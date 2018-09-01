<?php

if (! class_exists('AJAX_Handler'))
 require_once(dirname(__FILE__) . "/class-school-report-ajax.php");

 if (! class_exists('School_Report_Db_Table'))
  require_once(dirname(dirname(__FILE__)) . "/includes/class-school-report-db-table.php");


class SR_AJAX_Edit_Total_Good extends AJAX_Handler {
    function callback() {
      $tmp = new School_Report_Db_Table;
      $tab = $tmp->get_table("good");

      $res = 0;
      if ( $_POST["id_good_total"] == '0' ) {
        // insert
        $res = $tab->insert($_POST);
      }
      else {
        // update
        $res = $tab->update($_POST);
      }
      wp_send_json_success(json_encode(array("result" => $res)));
    }
}

new SR_AJAX_Edit_Total_Good('edit_total_good');

?>
