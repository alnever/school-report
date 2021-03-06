<?php

if (! class_exists('AJAX_Handler'))
 require_once(dirname(__FILE__) . "/class-school-report-ajax.php");

 if (! class_exists('School_Report_Db_Table'))
  require_once(dirname(dirname(__FILE__)) . "/includes/class-school-report-db-table.php");


class SR_AJAX_User_Reports extends AJAX_Handler {
    function callback() {
      $tab = new School_Report_Db_Table;
      $tab_reports = $tab->get_table("reports");
      $user = new WP_User(get_current_user_id());
      if (! in_array("administrator", (array) $user->roles)) {
        $id_creator = $user->ID;
        $where = array("id_creator" => $id_creator);
      }

      $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
      $per_page = isset($_POST['rows']) ? intval($_POST['rows']) : 10;
      $sort = isset($_POST['sort']) ? strval($_POST['sort']) : '';
      $order = isset($_POST['order']) ? strval($_POST['order']) : '';

      if (isset($_POST["class_name"]) && $_POST["class_name"] != "")
        $where["class_name"] = $_POST["class_name"];
      if (isset($_POST["year_name"]) && $_POST["year_name"] != "")
        $where["year_name"] = $_POST["year_name"];

      $reports = $tab_reports->get_reports_by_user($where, $sort, $order, $per_page, $page);
      $reports_count = $tab_reports->count_reports_by_user($where);

      $result = array();
      $result["total"] = $reports_count;
      $result["rows"] = $reports;
      wp_send_json_success(json_encode($result));
    }
}

new SR_AJAX_User_Reports('user_reports');

?>
