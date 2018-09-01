<?php
/**
 * Extends school_report_db_table class to work with school-report-absent
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    school-report
 * @subpackage school-report/includes
 */

/**
 * Extends school_report_db_table class to work with school-report-absent
 *
 * This class extends school_report_db_table class to work with absent days
 *
 * @since      1.0.0
 * @package    school-report
 * @subpackage school-report/includes
 * @author     Alex Neveroc <al_neverov@live.ru>
 */

 if (! class_exists('School_Report_Db_Table'))
  require_once(dirname(__FILE__) . "/class-school-report-db-table.php");

class School_Report_Table_Report_Absent extends School_Report_Db_Table{

  protected $table_name = 'school_report_absent';

  protected $fields = array(
    "id_absent" => array( "name" => "id_absent", "type" => "int", "constraint" => "not null auto_increment", "default" => 0, "format" => '%d', "caption" => "ID"),
    "id_report" => array( "name" => "id_report", "type" => "int", "constraint" => "not null", "default" => 0, "format" => '%d', "caption" => "Отчет"),
    "days_all" => array( "name" => "days_all", "type" => "int", "constraint" => "not null", "default" => 0, "format" => '%d', "caption" => "Дней всего"),
    "days_ill" => array( "name" => "days_ill", "type" => "int", "constraint" => "not null", "default" => 0, "format" => '%d', "caption" => "Дней по болезни"),
    "classes_all" => array( "name" => "classes_all", "type" => "int", "constraint" => "not null", "default" => 0, "format" => '%d', "caption" => "Уроков всего"),
    "classes_ills" => array( "name" => "classes_ills", "type" => "int", "constraint" => "not null", "default" => 0, "format" => '%d', "caption" => "Уроков по болезни"),
    "without_reason" => array( "name" => "without_reason", "type" => "int", "constraint" => "not null", "default" => 0, "format" => '%d', "caption" => "Без уважительной причины")
  );

  protected $id_field = "id_absent";
  protected $name_field = "days_all";
  protected $delete = true;
  protected $delete_before = array();
  

}
