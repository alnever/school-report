<?php
/**
 * Extends school_report_db_table class to work with school-teachers
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    school-report
 * @subpackage school-report/includes
 */

/**
 * Extends school_report_db_table class to work with school-teachers
 *
 * This class extends school_report_db_table class to work with school-teachers
 *
 * @since      1.0.0
 * @package    school-report
 * @subpackage school-report/includes
 * @author     Alex Neveroc <al_neverov@live.ru>
 */

 if (! class_exists('School_Report_Db_Table'))
  require_once(dirname(__FILE__) . "/class-school-report-db-table.php");

class School_Report_Table_Teachers extends School_Report_Db_Table{

  protected $table_name = 'school_report_teachers';

  protected $fields = array(
    "id_teacher" => array( "name" => "id_teacher", "type" => "int", "constraint" => "not null auto_increment", "default" => 0, "format" => '%d', "caption" => "ID",
                       "show_function" => null, "select_function" => null, "sortable" => false, "display" => false, "required" => false),
    "teacher_family" => array( "name" => "teacher_family", "type" => "varchar(30)", "constraint" => "not null", "default" => "", "format" => '%s', "caption" => "Фамилия",
                       "show_function" => null, "select_function" => null, "sortable" => true, "display" => true, "required" => true),
    "teacher_name" => array( "name" => "teacher_name", "type" => "varchar(30)", "constraint" => "not null", "default" => "", "format" => '%s', "caption" => "Имя",
                       "show_function" => null, "select_function" => null, "sortable" => true, "display" => true, "required" => true),
    "teacher_surname" => array( "name" => "teacher_surname", "type" => "varchar(30)", "constraint" => "", "default" => "", "format" => '%s', "caption" => "Отчество",
                      "show_function" => null, "select_function" => null, "sortable" => true, "display" => true, "required" => false),
    "teacher_comment" => array( "name" => "teacher_comment", "type" => "varchar(512)", "constraint" => "", "default" => "", "format" => '%s', "caption" => "Комментарий",
                        "show_function" => null, "select_function" => null, "sortable" => true, "display" => true, "required" => false),
    "deleted" => array( "name" => "deleted", "type" => "int", "constraint" => "not null default 0", "default" => 0, "format" => '%d', "caption" => "deleted",
                      "show_function" => null, "select_function" => null, "sortable" => false, "display" => false, "required" => false)
  );

  protected $id_field = "id_teacher";
  protected $name_field = "teacher_family";
  protected $delete = false;
  protected $delete_before = array();

  public function get_ext_list()
  {
    $sql = "select id_teacher,
            concat(teacher_family,' ', teacher_name,' ', ifnull(teacher_surname,''),' - ',ifnull(teacher_comment,'')) as teacher_family
            from ".$this->table_name." ".
            " where deleted = 0
              order by teacher_family, teacher_name, teacher_surname
            ";
    return  $this->connection->get_results($sql, 'ARRAY_A');
  }

  public function get_ext_item($id)
  {
    $sql = "select id_teacher, teacher_family, teacher_name, teacher_surname,
            concat(teacher_family,' ', teacher_name,' ', ifnull(teacher_surname,'')) as teacher_combo_name
            from ".$this->table_name." ".
            " where deleted = 0 and id_teacher = $id
            ";
    return $this->connection->get_row($sql, 'ARRAY_A');
  }

}
