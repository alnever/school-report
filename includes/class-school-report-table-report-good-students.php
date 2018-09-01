<?php
/**
 * Extends school_report_db_table class to work with school-report-good-students
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    school-report
 * @subpackage school-report/includes
 */

/**
 * Extends school_report_db_table class to work with school-report-good-students
 *
 * This class extends school_report_db_table class to work with students with just good marks
 *
 * @since      1.0.0
 * @package    school-report
 * @subpackage school-report/includes
 * @author     Alex Neveroc <al_neverov@live.ru>
 */

 if (! class_exists('School_Report_Db_Table'))
  require_once(dirname(__FILE__) . "/class-school-report-db-table.php");

class School_Report_Table_Report_Good_Students extends School_Report_Db_Table{

  protected $table_name = 'school_report_good_students';

  protected $fields = array(
    "id_good" => array( "name" => "id_good", "type" => "int", "constraint" => "not null auto_increment", "default" => 0, "format" => '%d', "caption" => "ID"),
    "id_report" => array( "name" => "id_report", "type" => "int", "constraint" => "not null", "default" => 0, "format" => '%d', "caption" => "Отчет"),
    "id_student" => array( "name" => "id_student", "type" => "int", "constraint" => "not null", "default" => 0, "format" => '%d', "caption" => "Ученик"),
    "student_status" => array( "name" => "student_status", "type" => "int", "constraint" => "not null", "default" => 0, "format" => '%d', "caption" => "Только 5")
  );

  protected $id_field = "id_good";
  protected $name_field = "id_student";
  protected $delete = true;
  protected $delete_before = array();


  public function get_good_by_report($where, $orderby = "", $order = "", $per_page = 10, $page_number = 1)
  {
    $sql = "select a.id_good, a.id_report, a.id_student, a.student_status,
                   s.student_family, s.student_name, s.student_surname,
                   concat(s.student_family,' ', s.student_name,' ', ifnull(s.student_surname,'')) as student_combo_name
            from school_report_good_students a
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

  public function count_good_by_report($where){
    $sql = "select count(*)
            from school_report_good_students
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
