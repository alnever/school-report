<?php

if (! class_exists('AJAX_Handler'))
 require_once(dirname(__FILE__) . "/class-school-report-ajax.php");

 if (! class_exists('School_Report_Db_Table'))
  require_once(dirname(dirname(__FILE__)) . "/includes/class-school-report-db-table.php");


class SR_AJAX_Delete_Execution extends AJAX_Handler {
    function callback() {
      $tab_reports = (new School_Report_Db_Table)->get_table("classes_execution");
      $res = $tab_reports->delete($_POST["id_execution"]);
      wp_send_json_success(json_encode(array("result" => 1)));
    }
}

new SR_AJAX_Delete_Execution('del_execution');

?>
