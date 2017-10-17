<?php
/**
 * Class to get wpdb instance
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    school-report
 * @subpackage school-report/includes
 */

/**
 * Class to get wpdb instance
 *
 * This class provides access to $wpdb instance
 *
 * @since      1.0.0
 * @package    school-report
 * @subpackage school-report/includes
 * @author     Alex Neveroc <al_neverov@live.ru>
 */

 class School_Report_Db extends wpdb {
   protected static $instance = null;

   protected static $table_info = array(
     "years" => array("class_name" => 'School_Report_Table_Years',
                             "file_name" => "/class-school-report-table-years.php",
                             "admin_menu" => 1,
                             "caption" => 'Учебные годы',
                             "singular" => 'Учебный год'
     ),
     "grades" => array("class_name" => 'School_Report_Table_Grades',
                             "file_name" => "/class-school-report-table-grades.php",
                             "admin_menu" => 1,
                             "caption" => 'Уровни классов',
                             "singular" => 'Уровень классов'
     ),
     "subgrades" => array("class_name" => 'School_Report_Table_Subgrade',
                             "file_name" => "/class-school-report-table-subgrade.php",
                             "admin_menu" => 1,
                             "caption" => 'Параллели',
                             "singular" => 'Параллель'
     ),
     "classes" => array("class_name" => 'School_Report_Table_Classes',
                             "file_name" => "/class-school-report-table-classes.php",
                             "admin_menu" => 1,
                             "caption" => 'Классы',
                             "singular" => 'Класс'
     ),
     "students" => array("class_name" => 'School_Report_Table_Students',
                             "file_name" => "/class-school-report-table-students.php",
                             "admin_menu" => 1,
                             "caption" => 'Ученики',
                             "singular" => 'Ученик'
     ),
     "teachers" => array("class_name" => 'School_Report_Table_Teachers',
                             "file_name" => "/class-school-report-table-teachers.php",
                             "admin_menu" => 1,
                             "caption" => 'Учителя',
                             "singular" => 'Учитель'
     ),
     "subjects" => array("class_name" => 'School_Report_Table_Subjects',
                             "file_name" => "/class-school-report-table-subjects.php",
                             "admin_menu" => 1,
                             "caption" => 'Предметы',
                             "singular" => 'Предмет'
     ),
     "report_types" => array("class_name" => 'School_Report_Table_Report_Types',
                             "file_name" => "/class-school-report-table-report-types.php",
                             "admin_menu" => 1,
                             "caption" => 'Периоды отчетов',
                             "singular" => 'Период отчетов'
     ),
     "reports" => array("class_name" => 'School_Report_Table_Reports',
                             "file_name" => "/class-school-report-table-reports.php",
                             "admin_menu" => 0,
                             "caption" => 'Отчеты',
                             "singular" => 'Отчет'
     ),
     "absent" => array("class_name" => 'School_Report_Table_Report_Absent',
                             "file_name" => "/class-school-report-table-report-absent.php",
                             "admin_menu" => 0,
                             "caption" => 'Пропуски',
                             "singular" => 'Пропуск'
     ),
     "absent_students" => array("class_name" => 'School_Report_Table_Report_Absent_Students',
                             "file_name" => "/class-school-report-table-report-absent-students.php",
                             "admin_menu" => 0,
                             "caption" => 'Пропуски поименно',
                             "singular" => 'Пропуск'
     ),
     "bad_students" => array("class_name" => 'School_Report_Table_Report_Bad_Students',
                             "file_name" => "/class-school-report-table-report-bad-students.php",
                             "admin_menu" => 0,
                             "caption" => 'Неуспевающие',
                             "singular" => 'Неуспевающий',
     ),
     "good_students" => array("class_name" => 'School_Report_Table_Report_Good_Students',
                             "file_name" => "/class-school-report-table-report-good-students.php",
                             "admin_menu" => 0,
                             "caption" => 'Отличники/хорошисты',
                             "singular" => 'Отличник/хорошист'
     ),
     "classes_execution" => array("class_name" => 'School_Report_Table_Report_Classes_Execution',
                             "file_name" => "/class-school-report-table-report-classes-execution.php",
                             "admin_menu" => 0,
                             "caption" => 'Выполнение нагрузки',
                             "singular" => 'Выполнение нагрузки'
     )
   );


  /*
  * @return a single instance of the database connection object
  */
   public static function get_instance()
   {
     if (!self::$instance) {
       self::$instance = new School_Report_Db(DB_USER, DB_PASSWORD, DB_NAME, DB_HOST);
     }

     return self::$instance;
   }

   public static function get_tables()
   {
     return self::$table_info;
   }
 }


 ?>
