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

class School_Report_Table_Report_Good extends School_Report_Db_Table{

  protected $table_name = 'school_report_good';

  protected $fields = array(
    "id_good_total" => array( "name" => "id_good_total", "type" => "int", "constraint" => "not null auto_increment", "default" => 0, "format" => '%d', "caption" => "ID"),
    "id_report" => array( "name" => "id_report", "type" => "int", "constraint" => "not null", "default" => 0, "format" => '%d', "caption" => "Отчет"),
    "good_total" => array( "name" => "good_total", "type" => "int", "constraint" => "not null", "default" => 0, "format" => '%d', "caption" => "Хорошисты")
  );

  protected $id_field = "id_good_total";
  protected $name_field = "good_total";
  protected $delete = true;
  protected $delete_before = array();


}
