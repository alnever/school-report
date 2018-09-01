<?php

if (! class_exists('AJAX_Handler'))
 require_once(dirname(__FILE__) . "/class-school-report-ajax.php");

 if (! class_exists('School_Report_Db_Table'))
  require_once(dirname(dirname(__FILE__)) . "/includes/class-school-report-db-table.php");


class SR_AJAX_Report_Students extends AJAX_Handler {
    function callback() {
      $t =  new School_Report_Db_Table;    
      $tab = $t->get_table("students");

      $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
      $per_page = isset($_POST['rows']) ? intval($_POST['rows']) : 1000;
      $sort = isset($_POST['sort']) ? strval($_POST['sort']) : '';
      $order = isset($_POST['order']) ? strval($_POST['order']) : '';

      $where = array('id_class' => $_POST["id_class"]);

      $res = $tab->get_list($where, $sort, $order, $per_page, $page);

      $result = array();
      foreach($res as $r)
      {
        $r["combo_name"] = sprintf("%s %s",$r["student_family"], $r["student_name"]);
        if($r["student_surname"] !== null)
        {
          $r["combo_name"] .= (" ".$r["student_surname"]);
        }
        array_push($result, $r);
      }

      wp_send_json_success(json_encode($result));
    }
}

new SR_AJAX_Report_Students('report_students');

?>
