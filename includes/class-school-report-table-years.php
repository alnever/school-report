<?php
/**
 * Extends school_report_db_table class to work with school-years
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    school-report
 * @subpackage school-report/includes
 */

/**
 * Extends school_report_db_table class to work with school-years
 *
 * This class extends school_report_db_table class to work with school-years
 *
 * @since      1.0.0
 * @package    school-report
 * @subpackage school-report/includes
 * @author     Alex Neveroc <al_neverov@live.ru>
 */

 if (! class_exists('School_Report_Db_Table'))
  require_once(dirname(__FILE__) . "/class-school-report-db-table.php");

class School_Report_Table_Years extends School_Report_Db_Table{

  protected $table_name = 'school_report_years';

  protected $fields = array(
    "id_year" => array( "name" => "id_year", "type" => "int", "constraint" => "not null auto_increment", "default" => 0, "format" => '%d', "caption" => "ID",
                       "show_function" => null, "select_function" => null, "sortable" => false, "display" => false, "required" => false),
    "year_name" => array( "name" => "year_name", "type" => "varchar(30)", "constraint" => "not null", "default" => "", "format" => '%s', "caption" => "Учебный год",
                       "show_function" => null, "select_function" => null, "sortable" => true, "display" => true, "required" => true),
    "deleted" => array( "name" => "deleted", "type" => "int", "constraint" => "not null default 0", "default" => 0, "format" => '%d', "caption" => "deleted",
                      "show_function" => null, "select_function" => null, "sortable" => false, "display" => false, "required" => false)
  );

  protected $id_field = "id_year";
  protected $name_field = "year_name";
  protected $delete = false;
  protected $delete_before = array();

  public function delete($value)
  {
    $num_classes = count($this->get_table("classes")->get_all_list(array("id_year" => $value)));
    if ($num_classes == 0) {
      return $this->connection->delete($this->table_name, array("id_year" => $value), array("%d"));
    }
    else {
      return parent::delete($value);
    }
  }


}
