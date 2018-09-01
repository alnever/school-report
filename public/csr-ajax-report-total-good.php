<?php

if (! class_exists('AJAX_Handler'))
 require_once(dirname(__FILE__) . "/class-school-report-ajax.php");

 if (! class_exists('School_Report_Db_Table'))
  require_once(dirname(dirname(__FILE__)) . "/includes/class-school-report-db-table.php");


class SR_AJAX_Report_Total_Good extends AJAX_Handler {
    function callback() {
      $t =  new School_Report_Db_Table;
      $tab = $t->get_table("good");

      $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
      $per_page = isset($_POST['rows']) ? intval($_POST['rows']) : 1;
      $sort = isset($_POST['sort']) ? strval($_POST['sort']) : '';
      $order = isset($_POST['order']) ? strval($_POST['order']) : '';

      $where = array("id_report" => $_POST["id_report"]);

      $res = $tab->get_list($where, $sort, $order, $per_page, $page);
      wp_send_json_success(json_encode($res));
    }
}

new SR_AJAX_Report_Total_Good('report_total_good');

?>
