<?php

if (! class_exists('AJAX_Handler'))
 require_once(dirname(__FILE__) . "/class-school-report-ajax.php");

 if (! class_exists('School_Report_Db_Table'))
  require_once(dirname(dirname(__FILE__)) . "/includes/class-school-report-db-table.php");


class SR_AJAX_New_Absent extends AJAX_Handler {
    function callback() {
      $tab_reports = (new School_Report_Db_Table)->get_table("absent_students");
      $res = $tab_reports->insert($_POST);
      $insert_id = $tab_reports->get_insert_id();
      wp_send_json_success(json_encode(array("result" => $res, "id_absent" => $insert_id)));
    }
}

new SR_AJAX_New_Absent('new_absent');

?>
