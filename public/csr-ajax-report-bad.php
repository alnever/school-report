<?php

if (! class_exists('AJAX_Handler'))
 require_once(dirname(__FILE__) . "/class-school-report-ajax.php");

 if (! class_exists('School_Report_Db_Table'))
  require_once(dirname(dirname(__FILE__)) . "/includes/class-school-report-db-table.php");


class SR_AJAX_Report_Bad extends AJAX_Handler {
    function callback() {
      $tab = (new School_Report_Db_Table)->get_table("bad_students");

      $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
      $per_page = isset($_POST['rows']) ? intval($_POST['rows']) : 1;
      $sort = isset($_POST['sort']) ? strval($_POST['sort']) : '';
      $order = isset($_POST['order']) ? strval($_POST['order']) : '';

      $where = array("id_report" => $_POST["id_report"]);
      if (isset($_POST["student_combo_name"]) && $_POST["student_combo_name"] != "")
        $where["student_combo_name"] = $_POST["student_combo_name"];
      if (isset($_POST["teacher_combo_name"]) && $_POST["teacher_combo_name"] != "")
        $where["teacher_combo_name"] = $_POST["teacher_combo_name"];
      if (isset($_POST["subject_name"]) && $_POST["subject_name"] != "")
        $where["subject_name"] = $_POST["subject_name"];

      $res = $tab->get_bad_by_report($where, $sort, $order, $per_page, $page);
      $res_count = $tab->count_bad_by_report($where);

      $result = array();
      $result["total"] = $res_count;
      $result["rows"] = $res;

      wp_send_json_success(json_encode($result));
    }
}

new SR_AJAX_Report_Bad('report_bad');

?>
