<?php
/**
 * Extends school_report_db_table class to work with school-report-classes-execution
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    school-report
 * @subpackage school-report/includes
 */

/**
 * Extends school_report_db_table class to work with school-report-classes-execution
 *
 * This class extends school_report_db_table class to work with execution of the study plan
 *
 * @since      1.0.0
 * @package    school-report
 * @subpackage school-report/includes
 * @author     Alex Neveroc <al_neverov@live.ru>
 */

 if (! class_exists('School_Report_Db_Table'))
  require_once(dirname(__FILE__) . "/class-school-report-db-table.php");

class School_Report_Table_Report_Classes_Execution extends School_Report_Db_Table{

  protected $table_name = 'school_report_classes_execution';

  protected $fields = array(
    "id_execution" => array( "name" => "id_execution", "type" => "int", "constraint" => "not null auto_increment", "default" => 0, "format" => '%d', "caption" => "ID"),
    "id_report" => array( "name" => "id_report", "type" => "int", "constraint" => "not null", "default" => 0, "format" => '%d', "caption" => "Отчет"),
    "id_subject" => array( "name" => "id_subject", "type" => "int", "constraint" => "not null", "default" => 0, "format" => '%d', "caption" => "Предмет"),
    "classes_executed" => array( "name" => "classes_executed", "type" => "int", "constraint" => "not null", "default" => 0, "format" => '%d', "caption" => "Проведено часов")
  );

  protected $id_field = "id_execution";
  protected $name_field = "id_subject";
  protected $delete = true;
  protected $delete_before = array();


  public function get_execution_by_report($where, $orderby = "", $order = "", $per_page = 1000, $page_number = 1)
  {
    $sql = "select a.id_execution, a.id_report, a.id_subject, a.classes_executed,
                   s.subject_name
            from school_report_classes_execution a
                 join school_report_subjects s on a.id_subject = s.id_subject
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

  public function count_execution_by_report($where){
    $sql = "select count(*)
            from school_report_classes_execution
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

  public function sum_execution_by_report($where){
    $sql = "select sum(classes_executed)
            from school_report_classes_execution
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
