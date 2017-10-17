<?php
/**
 * Extends school_report_db_table class to work with grades
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    school-report
 * @subpackage school-report/includes
 */

/**
 * Extends school_report_db_table class to work with grades
 *
 * This class extends school_report_db_table class to work with grades
 *
 * @since      1.0.0
 * @package    school-report
 * @subpackage school-report/includes
 * @author     Alex Neveroc <al_neverov@live.ru>
 */

 if (! class_exists('School_Report_Db_Table'))
  require_once(dirname(__FILE__) . "/class-school-report-db-table.php");

class School_Report_Table_Grades extends School_Report_Db_Table{

  protected $table_name = 'school_report_grades';

  protected $fields = array(
    "id_grade" => array( "name" => "id_grade", "type" => "int", "constraint" => "not null auto_increment", "default" => 0, "format" => '%d', "caption" => "ID",
                       "show_function" => null, "select_function" => null, "sortable" => false, "display" => false, "required" => false),
    "grade_name" => array( "name" => "grade_name", "type" => "varchar(30)", "constraint" => "not null", "default" => "", "format" => '%s', "caption" => "Уровень класса",
                       "show_function" => null, "select_function" => null, "sortable" => true, "display" => true, "required" => true),
    "deleted" => array( "name" => "deleted", "type" => "int", "constraint" => "not null default 0", "default" => 0, "format" => '%d', "caption" => "deleted",
                      "show_function" => null, "select_function" => null, "sortable" => false, "display" => false, "required" => false)
  );

  protected $default_values = array(
    array("grade_name" => 'Начальная школа'),
    array("grade_name" => 'Средняя школа'),
    array("grade_name" => 'Старшая школа')
  );

  protected $id_field = "id_grade";
  protected $name_field = "grade_name";
  protected $delete = false;
  protected $delete_before = array();
  


}
