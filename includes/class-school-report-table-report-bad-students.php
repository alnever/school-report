<?php
/**
 * Extends school_report_db_table class to work with school-report-bad-students
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    school-report
 * @subpackage school-report/includes
 */

/**
 * Extends school_report_db_table class to work with school-report-bad-students
 *
 * This class extends school_report_db_table class to work with students with negative marks
 *
 * @since      1.0.0
 * @package    school-report
 * @subpackage school-report/includes
 * @author     Alex Neveroc <al_neverov@live.ru>
 */

 if (! class_exists('School_Report_Db_Table'))
  require_once(dirname(__FILE__) . "/class-school-report-db-table.php");

class School_Report_Table_Report_Bad_Students extends School_Report_Db_Table{

  protected $table_name = 'school_report_bad_students';

  protected $fields = array(
    "id_bad" => array( "name" => "id_bad", "type" => "int", "constraint" => "not null auto_increment", "default" => 0, "format" => '%d', "caption" => "ID"),
    "id_report" => array( "name" => "id_report", "type" => "int", "constraint" => "not null", "default" => 0, "format" => '%d', "caption" => "Отчет"),
    "id_student" => array( "name" => "id_student", "type" => "int", "constraint" => "not null", "default" => 0, "format" => '%d', "caption" => "Ученик"),
    "id_subject" => array( "name" => "id_subject", "type" => "int", "constraint" => "not null", "default" => 0, "format" => '%d', "caption" => "Предмет"),
    "id_teacher" => array( "name" => "id_teacher", "type" => "int", "constraint" => "not null", "default" => 0, "format" => '%d', "caption" => "Учитель")
  );

  protected $id_field = "id_bad";
  protected $name_field = "id_student";
  protected $delete = true;
  protected $delete_before = array();


  public function get_bad_by_report($where, $orderby = "", $order = "", $per_page = 10, $page_number = 1)
  {
    $sql = "select a.id_bad, a.id_report, a.id_student, a.id_teacher, a.id_subject,
                   s.student_family, s.student_name, s.student_surname,
                   concat(s.student_family,' ', s.student_name,' ', ifnull(s.student_surname,'')) as student_combo_name,
                   t.teacher_family, t.teacher_name, t.teacher_surname,
                   concat(t.teacher_family,' ', t.teacher_name,' ', ifnull(t.teacher_surname,'')) as teacher_combo_name,
                   p.subject_name
            from school_report_bad_students a
                 join school_report_students s on a.id_student = s.id_student
                 join school_report_teachers t on a.id_teacher = t.id_teacher
                 join school_report_subjects p on a.id_subject = p.id_subject
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

  public function count_bad_by_report($where){
    $sql = "select count(distinct id_student)
            from school_report_bad_students
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
