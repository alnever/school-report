<?php
/**
 * Extends school_report_db_table class to work with school-subjects
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    school-report
 * @subpackage school-report/includes
 */

/**
 * Extends school_report_db_table class to work with school-subjects
 *
 * This class extends school_report_db_table class to work with school-subjects
 *
 * @since      1.0.0
 * @package    school-report
 * @subpackage school-report/includes
 * @author     Alex Neveroc <al_neverov@live.ru>
 */

 if (! class_exists('School_Report_Db_Table'))
  require_once(dirname(__FILE__) . "/class-school-report-db-table.php");

class School_Report_Table_Subjects extends School_Report_Db_Table{

  protected $table_name = 'school_report_subjects';

  protected $fields = array(
    "id_subject" => array( "name" => "id_subject", "type" => "int", "constraint" => "not null auto_increment", "default" => 0, "format" => '%d', "caption" => "ID",
                       "show_function" => null, "select_function" => null, "sortable" => false, "display" => false, "required" => false),
    "subject_name" => array( "name" => "subject_name", "type" => "varchar(100)", "constraint" => "not null", "default" => "", "format" => '%s', "caption" => "Название предмета",
                       "show_function" => null, "select_function" => null, "sortable" => true, "display" => true, "required" => true),
    "deleted" => array( "name" => "deleted", "type" => "int", "constraint" => "not null default 0", "default" => 0, "format" => '%d', "caption" => "deleted",
                      "show_function" => null, "select_function" => null, "sortable" => false, "display" => false, "required" => false)
  );

  protected $id_field = "id_subject";
  protected $name_field = "subject_name";
  protected $delete = false;
  protected $delete_before = array();


}
