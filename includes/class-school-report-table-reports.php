<?php
/**
 * Extends school_report_db_table class to work with school-reports
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    school-report
 * @subpackage school-report/includes
 */

/**
 * Extends school_report_db_table class to work with school-reports
 *
 * This class extends school_report_db_table class to work with school-reports
 *
 * @since      1.0.0
 * @package    school-report
 * @subpackage school-report/includes
 * @author     Alex Neveroc <al_neverov@live.ru>
 */

 if (! class_exists('School_Report_Db_Table'))
  require_once(dirname(__FILE__) . "/class-school-report-db-table.php");

class School_Report_Table_Reports extends School_Report_Db_Table{

  protected $table_name = 'school_report_reports';

  protected $fields = array(
    "id_report" => array( "name" => "id_report", "type" => "int", "constraint" => "not null auto_increment", "default" => 0, "format" => '%d', "caption" => "ID"),
    "id_report_type" => array( "name" => "id_report_type", "type" => "int", "constraint" => "not null", "default" => 0, "format" => '%d', "caption" => "Период отчета"),
    "id_creator" => array( "name" => "id_creator", "type" => "int", "constraint" => "not null", "default" => 0, "format" => '%d', "caption" => "Автор"),
    "id_class" => array( "name" => "id_class", "type" => "int", "constraint" => "not null", "default" => 0, "format" => '%d', "caption" => "Класс"),
    "id_year" => array( "name" => "id_year", "type" => "int", "constraint" => "not null", "default" => 0, "format" => '%d', "caption" => "Учебный год"),
    "create_date" => array( "name" => "create_date", "type" => "date", "constraint" => "not null", "default" => "", "format" => '%s', "caption" => "Дата создания"),
    "report_status" => array( "name" => "report_status", "type" => "int", "constraint" => "not null", "default" => 0, "format" => '%d', "caption" => "Статус отчета"),
    "students_count" => array( "name" => "students_count", "type" => "int", "constraint" => "not null", "default" => 0, "format" => '%d', "caption" => "Количество учеников")
  );

  protected $id_field = "id_report";
  protected $name_field = "id_class";
  protected $delete = true;
  protected $delete_before = array("absent","absent_students","bad_students",
                          "good_students","classes_execution");

  public function insert($value)
  {
    $reports = $this->get_all_list(array("id_year" => $value["id_year"],
                                         "id_class" => $value["id_class"],
                                         "id_report_type" => $value["id_report_type"]
                        ));
    $rep_count = count($reports);
    if ($rep_count == 0) {
      return parent::insert($value);
    }
    else {
        $this->insert_id = -1;
        return 0;
    }
  }

  public function insert_ext($value)
  {
    $res = $this->insert($value);
    if(isset($value["id_report_type"]))
    {
      $tab = $this->get_table("report_types");
      $row = $tab->get($value["id_report_type"]);
      $rtype = $row["aggregativ"];
      if ($rtype == 1)
      {
        $this->pred_insert($this->insert_id, $value["id_year"], $value["id_class"]);
      }
    }
    return $res;
  }

  public function pred_insert($id_report, $id_year, $id_class)
  {
      $sql = "select id_report from school_report_reports
                      where id_year = $id_year and id_class = $id_class
                        and id_report_type in (select id_report_type from school_report_report_types where aggregativ = 0)
      ";

      $reports = $this->connection->get_results($sql, 'ARRAY_A');

      $subreport_ids = "";
      $i = 0;
      foreach($reports as $report)
      {
        if ($i == 0)
          $subreport_ids = sprintf("%d", $report["id_report"]);
        else
          $subreport_ids = sprintf("%s,%d", $subreport_ids, $report["id_report"]);
        $i++;
      }

      # calculate absent
      $sql = "select sum(days_all) as days_all, sum(days_ill) as days_ill,
                     sum(classes_all) as classes_all, sum(classes_ills) as classes_ills,
                     sum(without_reason) as without_reason
                from school_report_absent
                where id_report in ($subreport_ids)";
      $absent = $this->connection->get_row($sql, "ARRAY_A");
      $tab = $this->get_table("absent");
      $param = array("days_all" => $absent["days_all"],
                     "days_ill" => $absent["days_ill"],
                     "classes_all" => $absent["classes_all"],
                     "classes_ills" => $absent["classes_ills"],
                     "without_reason" => $absent["without_reason"],
                     "id_report" => $id_report
                    );
      $tab->insert($param);

      # calculate lacks by students

      $sql = "select id_student, sum(hours_all) as hours_all
                 from school_report_absent_students
                 where id_report in ($subreport_ids)
                 group by id_student";
      $st_lacks = $this->connection->get_results($sql, "ARRAY_A");
      $tab = $this->get_table("absent_students");
      foreach($st_lacks as $lack)
      {
        $param = array("id_student" => $lack["id_student"],
                       "hours_all" => $lack["hours_all"],
                       "id_report" => $id_report
                      );
        $tab->insert($param);
      }
      # calculate plan execution
      $sql = "select id_subject, sum(classes_executed) as classes_executed
                from school_report_classes_execution
                where id_report in ($subreport_ids)
                group by id_subject
      ";
      $executions = $this->connection->get_results($sql, "ARRAY_A");
      $tab = $this->get_table("classes_execution");
      foreach ($executions as $execution)
      {
        $param = array("id_subject" => $execution["id_subject"],
                       "classes_executed" => $execution["classes_executed"],
                       "id_report" => $id_report
                      );
        $tab->insert($param);
      }
  }

  public function get_reports_by_user($where, $orderby = "", $order = "", $per_page = 10, $page_number = 1){
    $sql = "select r.id_report, r.id_creator, r.create_date, r.report_status,
                   c.id_class, c.class_name, c.id_teacher, r.students_count,
                   y.id_year, y.year_name,
                   t.id_report_type, t.type_name
            from school_report_reports r
                 join school_report_classes c on r.id_class = c.id_class
                 join school_report_years y on r.id_year = y.id_year
                 join school_report_report_types t on r.id_report_type = t.id_report_type
            where 1=1
    ";

    if ( ! empty ($where) )
    {
      foreach($where as $field => $value)
      {
        if (strcmp($this->fields[$field]["type"], "int") == 0)
          $sql .= ' and '. esc_sql( $field ) . ' = ' .esc_sql( $value ) . ' ';
        else
          $sql .= ' and '. esc_sql( $field ) . " like '%".esc_sql( $value ) . "%' ";
      }
    }

    if ( ! empty( $orderby ) && $orderby != '' )
    {
      $sql .= ' ORDER BY ' . esc_sql( $orderby );
      $sql .= ! empty( $order ) && $order != '' ? ' ' . esc_sql( $order ) : ' ASC';
    }

    $sql .= " LIMIT $per_page";

    $sql .= ' OFFSET ' . ( $page_number - 1 ) * $per_page;

    $result = $this->connection->get_results( $sql, 'ARRAY_A' );

    return $result;
  }

  public function count_reports_by_user($where){
    $sql = "select count(*)
            from school_report_reports r
                 join school_report_classes c on r.id_class = c.id_class
                 join school_report_years y on r.id_year = y.id_year
                 join school_report_report_types t on r.id_report_type = t.id_report_type
            where 1=1
    ";

    if ( ! empty ($where) )
    {
      foreach($where as $field => $value)
      {
        if (strcmp($this->fields[$field]["type"], "int") == 0)
          $sql .= ' and '. esc_sql( $field ) . ' = ' .esc_sql( $value ) . ' ';
        else
          $sql .= ' and '. esc_sql( $field ) . " like '%".esc_sql( $value ) . "%' ";
      }
    }

    $result = $this->connection->get_var( $sql );

    return $result;
  }


}
