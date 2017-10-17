<?php

if (! class_exists('AJAX_Handler'))
 require_once(dirname(__FILE__) . "/class-school-report-ajax.php");

 if (! class_exists('School_Report_Summary'))
  require_once(dirname(dirname(__FILE__)) . "/includes/class-school-report-summary.php");


class SR_AJAX_Report_Ready extends AJAX_Handler {
    function callback() {
      $result = (new School_Report_Summary)->get_ready_reports($_POST["id_year"], $_POST["id_type"]);

      wp_send_json_success(json_encode($result));
    }
}

new SR_AJAX_Report_Ready('ready_reports');

?>
