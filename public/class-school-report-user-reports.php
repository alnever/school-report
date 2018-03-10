<?php

if (! class_exists('AJAX_Handler'))
 require_once(dirname(__FILE__) . "/class-school-report-ajax.php");

class School_Report_User_Reports extends AJAX_Handler {
    function callback() {
      global $wpdb;

      $id_user = (new WP_User(get_current_user_id()))->ID;

      $sql = "select r.id_report, r.create_date, a.id_report_type, a.type_name, b.id_class, b.class_name, c.id_year, c.year_name, r.report_status
                from school_report_reports r join school_report_report_types a on r.id_report_type = a.id_report_type
                                             join school_report_classes b on r.id_class = b.id_class
                                             join school_report_years c on r.id_year = c.id_year
                where id_creator = $id_user
                order by r.create_date
      ";

      wp_send_json_success(json_encode($wpdb->get_results($sql, 'ARRAY_A')));

    }
}

new School_Report_User_Reports('user_reports');

?>
