<?php

if (! class_exists('AJAX_Handler'))
 require_once(dirname(__FILE__) . "/class-school-report-ajax.php");

 if (! class_exists('School_Report_Db_Table'))
  require_once(dirname(dirname(__FILE__)) . "/includes/class-school-report-db-table.php");


class SR_AJAX_Update_Bad extends AJAX_Handler {
    function callback() {
      $tab_reports = (new School_Report_Db_Table)->get_table("bad_students");
      $res = $tab_reports->update($_POST);
      wp_send_json_success(json_encode(array("result" => $res)));
    }
}

new SR_AJAX_Update_Bad('upd_bad_student');

?>
