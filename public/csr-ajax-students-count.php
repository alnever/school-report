<?php

if (! class_exists('AJAX_Handler'))
 require_once(dirname(__FILE__) . "/class-school-report-ajax.php");

 if (! class_exists('School_Report_Db_Table'))
  require_once(dirname(dirname(__FILE__)) . "/includes/class-school-report-db-table.php");


class SR_AJAX_Students_Count extends AJAX_Handler {
    function callback() {
      $tab = (new School_Report_Db_Table)->get_table("classes");

      $res = $tab->get_students_count($_POST["id_class"]);
      wp_send_json_success(json_encode(array("result" => true, "students_count" => $res)));
    }
}

new SR_AJAX_Students_Count('students_count');

?>
