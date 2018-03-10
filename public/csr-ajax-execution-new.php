<?php

if (! class_exists('AJAX_Handler'))
 require_once(dirname(__FILE__) . "/class-school-report-ajax.php");

 if (! class_exists('School_Report_Db_Table'))
  require_once(dirname(dirname(__FILE__)) . "/includes/class-school-report-db-table.php");


class SR_AJAX_New_Execution extends AJAX_Handler {
    function callback() {
      $t =  new School_Report_Db_Table;     
      $tab_reports = $t->get_table("classes_execution");
      $res = $tab_reports->insert($_POST);
      $insert_id = $tab_reports->get_insert_id();
      wp_send_json_success(json_encode(array("result" => $res, "id_execution" => $insert_id)));
    }
}

new SR_AJAX_New_Execution('new_execution');

?>
