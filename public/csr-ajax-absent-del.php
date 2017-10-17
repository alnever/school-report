<?php

if (! class_exists('AJAX_Handler'))
 require_once(dirname(__FILE__) . "/class-school-report-ajax.php");

 if (! class_exists('School_Report_Db_Table'))
  require_once(dirname(dirname(__FILE__)) . "/includes/class-school-report-db-table.php");


class SR_AJAX_Delete_Absent extends AJAX_Handler {
    function callback() {
      $tab_reports = (new School_Report_Db_Table)->get_table("absent_students");
      $res = $tab_reports->delete($_POST["id_absent"]);
      wp_send_json_success(json_encode(array("result" => 1)));
    }
}

new SR_AJAX_Delete_Absent('del_absent');

?>
