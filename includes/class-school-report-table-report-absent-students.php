<?php
/**
 * Extends school_report_db_table class to work with school-report-absent-students
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    school-report
 * @subpackage school-report/includes
 */

/**
 * Extends school_report_db_table class to work with school-report-absent-students
 *
 * This class extends school_report_db_table class to work with absent students
 *
 * @since      1.0.0
 * @package    school-report
 * @subpackage school-report/includes
 * @author     Alex Neveroc <al_neverov@live.ru>
 */

 if (! class_exists('School_Report_Db_Table'))
  require_once(dirname(__FILE__) . "/class-school-report-db-table.php");

class School_Report_Table_Report_Absent_Students extends School_Report_Db_Table{

  protected $table_name = 'school_report_absent_students';

  protected $fields = array(
    "id_absent" => array( "name" => "id_absent", "type" => "int", "constraint" => "not null auto_increment", "default" => 0, "format" => '%d', "caption" => "ID"),
    "id_report" => array( "name" => "id_report", "type" => "int", "constraint" => "not null", "default" => 0, "format" => '%d', "caption" => "Отчет"),
    "id_student" => array( "name" => "id_student", "type" => "int", "constraint" => "not null", "default" => 0, "format" => '%d', "caption" => "Ученик"),
    "hours_all" => array( "name" => "hours_all", "type" => "int", "constraint" => "not null", "default" => 0, "format" => '%d', "caption" => "Всего часов")
  );

  protected $id_field = "id_absent";
  protected $name_field = "id_student";
  protected $delete = true;
  protected $delete_before = array();


  public function get_student_absent_by_report($where, $orderby = "", $order = "", $per_page = 10, $page_number = 1)
  {
    $sql = "select a.id_absent, a.id_report, a.id_student,
                   s.student_family, s.student_name, s.student_surname,
                   concat(s.student_family,' ', s.student_name,' ', ifnull(s.student_surname,'')) as student_combo_name,
                   a.hours_all
            from school_report_absent_students a
                 join school_report_students s on a.id_student = s.id_student
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

  public function get_total_absent_by_report($where, $orderby = "", $order = "", $per_page = 10, $page_number = 1)
  {
    $sql = "select sum(hours_all) as total_lack
            from school_report_absent_students
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

    $result = $this->connection->get_var( $sql );
    
    return $result;
  }

  public function count_student_absent_by_report($where){
    $sql = "select count(*)
            from school_report_absent_students
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
