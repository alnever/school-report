<?php
/**
 * Extends school_report_db_table class to work with school-students
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    school-report
 * @subpackage school-report/includes
 */

/**
 * Extends school_report_db_table class to work with school-students
 *
 * This class extends school_report_db_table class to work with school-students
 *
 * @since      1.0.0
 * @package    school-report
 * @subpackage school-report/includes
 * @author     Alex Neveroc <al_neverov@live.ru>
 */

 if (! class_exists('School_Report_Db_Table'))
  require_once(dirname(__FILE__) . "/class-school-report-db-table.php");

class School_Report_Table_Students extends School_Report_Db_Table{

  protected $table_name = 'school_report_students';

  protected $fields = array(
    "id_student" => array( "name" => "id_student", "type" => "int", "constraint" => "not null auto_increment", "default" => 0, "format" => '%d', "caption" => "ID",
                       "show_function" => null, "select_function" => null, "sortable" => false, "display" => false, "required" => false),
    "student_family" => array( "name" => "student_family", "type" => "varchar(30)", "constraint" => "not null", "default" => "", "format" => '%s', "caption" => "Фамилия",
                       "show_function" => null, "select_function" => null, "sortable" => true, "display" => true, "required" => true),
    "student_name" => array( "name" => "student_name", "type" => "varchar(30)", "constraint" => "not null", "default" => "", "format" => '%s', "caption" => "Имя",
                       "show_function" => null, "select_function" => null, "sortable" => true, "display" => true, "required" => true),
    "student_surname" => array( "name" => "student_surname", "type" => "varchar(30)", "constraint" => "", "default" => "", "format" => '%s', "caption" => "Отчество",
                       "show_function" => null, "select_function" => null, "sortable" => true, "display" => true, "required" => false),
    "id_class" => array( "name" => "id_class", "type" => "int", "constraint" => "not null", "default" => 0, "format" => '%d', "caption" => "Класс",
                       "show_function" => 'get_student_class', "select_function" => 'get_student_classes', "sortable" => true, "display" => true, "required" => true),
    "deleted" => array( "name" => "deleted", "type" => "int", "constraint" => "not null default 0", "default" => 0, "format" => '%d', "caption" => "deleted",
                      "show_function" => null, "select_function" => null, "sortable" => false, "display" => false, "required" => false),
    "pseudo_id" => array( "name" => "pseudo_id", "type" => "int", "constraint" => "not null", "default" => 0, "format" => '%d', "caption" => "IDD",
                       "show_function" => null, "select_function" => null, "sortable" => false, "display" => false, "required" => false)
  );

  protected $id_field = "id_student";
  protected $name_field = "student_family";
  protected $delete = false;
  protected $delete_before = array();

  public function insert($value)
  {

    $res = parent::insert($value);
    if ($value["pseudo_id"] == 0) {

      $res = $this->connection->update($this->table_name,
        array("pseudo_id" => $pseudo_id),
        array("id_student" => $this->insert_id),
        array("%d"),
        array("%d")
      );
    }
    return $res;
  }

  public function update($value)
  {
    if (isset($value["deleted"]) && $value["deleted"] == 1) {
      return parent::update($value);
    }
    else {
      $this->delete($value["id_student"]);
      $value["id_student"] = 0;
      return $this->insert($value);
    }
  }


  public function get_student_class($id)
  {
    $tab = new School_Report_Table_Classes;
    return $tab->get($id)[$tab->get_name_field()];
  }

  public function get_student_classes()
  {
    return new School_Report_Table_Classes;
  }

  public function get_student_year($id)
  {
    $tab = new School_Report_Table_Years;
    return $tab->get($id)[$tab->get_name_field()];
  }

  public function get_student_years()
  {
    return new School_Report_Table_Years;
  }
}
