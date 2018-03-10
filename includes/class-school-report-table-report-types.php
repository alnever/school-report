<?php
/**
 * Extends school_report_db_table class to work with school-report-types
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    school-report
 * @subpackage school-report/includes
 */

/**
 * Extends school_report_db_table class to work with school-report-types
 *
 * This class extends school_report_db_table class to work with school-report-types
 *
 * @since      1.0.0
 * @package    school-report
 * @subpackage school-report/includes
 * @author     Alex Neveroc <al_neverov@live.ru>
 */

 if (! class_exists('School_Report_Db_Table'))
  require_once(dirname(__FILE__) . "/class-school-report-db-table.php");

class School_Report_Table_Report_Types extends School_Report_Db_Table{

  protected $table_name = 'school_report_report_types';

  protected $fields = array(
    "id_report_type" => array( "name" => "id_report_type", "type" => "int", "constraint" => "not null auto_increment", "default" => 0, "format" => '%d', "caption" => "ID",
                       "show_function" => null, "select_function" => null, "sortable" => false, "display" => false, "required" => false),
    "type_name" => array( "name" => "type_name", "type" => "varchar(50)", "constraint" => "not null", "default" => "", "format" => '%s', "caption" => "Период отчета",
                       "show_function" => null, "select_function" => null, "sortable" => true, "display" => true, "required" => true),
    "aggregativ" => array( "name" => "aggregativ", "type" => "int", "constraint" => "not null", "default" => 0, "format" => '%d', "caption" => "Суммарный",
                        "show_function" => null, "select_function" => null, "sortable" => false, "display" => false, "required" => false),
    "deleted" => array( "name" => "deleted", "type" => "int", "constraint" => "not null default 0", "default" => 0, "format" => '%d', "caption" => "deleted",
                      "show_function" => null, "select_function" => null, "sortable" => false, "display" => false, "required" => false)
  );

  protected $default_values = array(
    array("type_name" => 'Первая четверть', "aggregativ" => 0),
    array("type_name" => 'Вторая четверть', "aggregativ" => 0),
    array("type_name" => 'Третья четверть', "aggregativ" => 0),
    array("type_name" => 'Четвертая четверть', "aggregativ" => 0),
    array("type_name" => 'Годовой отчет', "aggregativ" => 1)
  );

  protected $id_field = "id_report_type";
  protected $name_field = "type_name";
  protected $delete = false;
  protected $delete_before = array();


}
