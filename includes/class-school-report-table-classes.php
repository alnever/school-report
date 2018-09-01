<?php
/**
 * Extends school_report_db_table class to work with school-classes
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    school-report
 * @subpackage school-report/includes
 */

/**
 * Extends school_report_db_table class to work with school-classes
 *
 * This class extends school_report_db_table class to work with school-classes
 *
 * @since      1.0.0
 * @package    school-report
 * @subpackage school-report/includes
 * @author     Alex Neveroc <al_neverov@live.ru>
 */

 if (! class_exists('School_Report_Db_Table'))
  require_once(dirname(__FILE__) . "/class-school-report-db-table.php");

class School_Report_Table_Classes extends School_Report_Db_Table{

  protected $table_name = 'school_report_classes';

  protected $fields = array(
    "id_class" => array( "name" => "id_class", "type" => "int", "constraint" => "not null auto_increment", "default" => 0, "format" => '%d', "caption" => "ID",
                       "show_function" => null, "select_function" => null, "sortable" => false, "display" => false, "required" => false),
    "id_grade" => array( "name" => "id_grade", "type" => "int", "constraint" => "not null", "default" => 0, "format" => '%d', "caption" => "Уровень класса",
                       "show_function" => 'get_grade', "select_function" => 'get_grades', "sortable" => true, "display" => true, "required" => true),
    "id_subgrade" => array( "name" => "id_subgrade", "type" => "int", "constraint" => "not null", "default" => 0, "format" => '%d', "caption" => "Параллель",
                       "show_function" => 'get_subgrade', "select_function" => 'get_subgrades', "sortable" => true, "display" => true, "required" => true),
    "id_year" => array( "name" => "id_year", "type" => "int", "constraint" => "not null", "default" => 0, "format" => '%d', "caption" => "Учебный год",
                        "show_function" => 'get_year', "select_function" => 'get_years', "sortable" => true, "display" => true, "required" => true),
    "class_name" =>array( "name" => "class_name", "type" => "varchar(30)", "constraint" => "not null", "default" => "", "format" => '%s', "caption" => "Класс",
                       "show_function" => null, "select_function" => null, "sortable" => true, "display" => true, "required" => true),
    "students" =>array( "name" => "students", "type" => "int", "constraint" => "not null", "default" => 0, "format" => '%d', "caption" => "Количество учеников",
                      "show_function" => null, "select_function" => null, "sortable" => false, "display" => false, "required" => false),
    "id_teacher" => array( "name" => "id_teacher", "type" => "int", "constraint" => "not null", "default" => 0, "format" => '%d', "caption" => "Классный руководитель",
                        "show_function" => 'get_leiter', "select_function" => 'get_leiters', "sortable" => true, "display" => true, "required" => true),
    "deleted" => array( "name" => "deleted", "type" => "int", "constraint" => "not null default 0", "default" => 0, "format" => '%d', "caption" => "deleted",
                      "show_function" => null, "select_function" => null, "sortable" => false, "display" => false, "required" => false)
  );

  protected $id_field = "id_class";
  protected $name_field = "class_name";
  protected $delete = false;
  protected $delete_before = array();

  public function delete($value)
  {
    $num_students = count($this->get_table("students")->get_all_list(array("id_class" => $value)));
    if ($num_students == 0) {
      return $this->connection->delete($this->table_name, array("id_class" => $value), array("%d"));
    }
    else {
      return parent::delete($value);
    }
  }

  public function get_grade($id)
  {
    $tab = new School_Report_Table_Grades;
    $f = $tab->get($id);
    return $f[$tab->get_name_field()];
  }

  public function get_grades()
  {
    return new School_Report_Table_Grades;
  }

  public function get_subgrade($id)
  {
    $tab = new School_Report_Table_Subgrade;
    $f = $tab->get($id);
    return $f[$tab->get_name_field()];
  }

  public function get_subgrades()
  {
    return new School_Report_Table_Subgrade;
  }

  public function get_year($id)
  {
    $tab = new School_Report_Table_Years;
    $f = $tab->get($id);
    return $f[$tab->get_name_field()];
  }

  public function get_years()
  {
    return new School_Report_Table_Years;
  }

  public function get_leiter($id)
  {
    $tab = new School_Report_Table_Teachers;
    $f = $tab->get_ext_item($id);
    return $f["teacher_combo_name"];
  }

  public function get_leiters()
  {
    return new School_Report_Table_Teachers;
  }

  public function get_students_count($id)
  {
    $sql = "select count(id_student) from school_report_students
              where deleted = 0
              and id_class = $id
    ";

    return $this->connection->get_var($sql);
  }

}
