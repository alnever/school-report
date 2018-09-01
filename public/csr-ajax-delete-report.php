<?php

if (! class_exists('AJAX_Handler'))
 require_once(dirname(__FILE__) . "/class-school-report-ajax.php");

 if (! class_exists('School_Report_Db_Table'))
  require_once(dirname(dirname(__FILE__)) . "/includes/class-school-report-db-table.php");


class SR_AJAX_Delete_Report extends AJAX_Handler {
    function callback() {
      $t =  new School_Report_Db_Table;     
      $tab_reports = $t->get_table("reports");
      $res = $tab_reports->delete($_POST["id_report"]);
      wp_send_json_success(json_encode(array("result" => 1)));
    }
}

new SR_AJAX_Delete_Report('delete_report');

?>