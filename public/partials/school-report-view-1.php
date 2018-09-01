<?php

include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

if (! class_exists('School_Report_Db_Table'))
 require_once(dirname(dirname(__FILE__)) . "/includes/class-school-report-db-table.php");

wp_enqueue_style( 'school-report-viewer-css', plugin_dir_url( __FILE__ ) . '../css/school-report-viewer.css', array('easyui-default', 'easyui-icons'), $this->version, 'all' );

$id_report = $_GET["id_report"];

$rep_tab = new School_Report_Db_Table;
$tab = $rep_tab->get_table("reports");
$user_report = $tab->get_reports_by_user(array("id_report" => $id_report));
$rep_info = $user_report[0];
?>
<div class="report_preview">
<!-- converters -->
<span class="pdf_invisible">
  <?php echo do_shortcode('[dkpdf-button]'); ?>
</span>
<!-- header -->

<h2 align="center">Отчет</h2>
<table width="100%">
  <tr>
    <th width="30%">
      Дата создания
    </th>
    <td width="70%">
      <?php echo date_create($rep_info["create_date"])->format('d.m.Y'); ?>
    </td>
  </tr>
  <tr>
    <th width="30%">
      Учебный год
    </th>
    <td width="70%">
      <?php echo $rep_info["year_name"]; ?>
    </td>
  </tr>
  <tr>
    <th width="30%">
      Период отчета
    </th>
    <td width="70%">
      <?php echo $rep_info["type_name"]; ?>
    </td>
  </tr>
  <tr>
    <th width="30%">
      Класс
    </th>
    <td width="70%">
      <?php echo $rep_info["class_name"]; ?>
    </td>
  </tr>
  <tr>
    <th width="30%">
      Классный руководитель
    </th>
    <td width="70%">
      <?php
        // $tab = (new School_Report_Db_Table)->get_table("teachers");
        $tab = $rep_tab->get_table("teachers");
        $teacher = $tab->get_ext_item($rep_info["id_teacher"]);
        echo $teacher["teacher_combo_name"];
       ?>
    </td>
  </tr>
  <tr>
    <th width="70%">
      Количество человек в классе
    </th>
    <td width="30%">
      <?php
        // $tab = (new School_Report_Db_Table)->get_table("classes");
        $tab = $rep_tab->get_table("classes");
        echo $tab->get_students_count($rep_info["id_class"]);
        ?>
    </td>
  </tr>
</table>

<!-- lacks -->
<?php
  // $tab = (new School_Report_Db_Table)->get_table("absent");
  $tab = $rep_tab->get_table("absent");
  $tmp = $tab->get_list(array("id_report" => $id_report));
  $absents = $tmp[0];
?>

<h3 class="report_part_title">1. Пропуски</h3>
<table width="100%" cellspacing="0" cellspaddin="0" border="1px">
  <tr>
    <th colspan="2">Дни</th>
    <th colspan="2">Уроки</th>
  </tr>
  <tr>
    <th width="25%">Всего</th>
    <th width="25%">По болезни</th>
    <th width="25%">Всего</th>
    <th width="25%">По болезни</th>
  </tr>
  <tr>
    <td width="25%">
      <?php echo $absents["days_all"]; ?>
    </td>
    <td width="25%">
      <?php echo $absents["days_ill"]; ?>
    </td>
    <td width="25%">
      <?php echo $absents["classes_all"]; ?>
    </td>
    <td width="25%">
      <?php echo $absents["classes_ills"]; ?>
    </td>
  </tr>
</table>

<!-- students lacks -->
<?php
  // $tab = (new School_Report_Db_Table)->get_table("absent_students");
  $tab = $rep_tab->get_table("absent_students");
  $lacks = $tab->get_student_absent_by_report(array("id_report" => $id_report));
  $total_lacks = $tab->get_total_absent_by_report(array("id_report" => $id_report));
?>

<br />
<p>
  <strong>
    Пропуски без уважительной причины, часов всего:&nbsp;
  </strong>
    <?php echo (!isset($total_lacks)?0:$total_lacks);
    ?>
</p>

<table width="100%" cellspacing="0" cellspaddin="0" border="1px">
  <tr>
    <th width="25%">ФИО ученика</th>
    <th width="25%">Всего часов</th>
  </tr>
  <?php  foreach ($lacks as $lack): ?>
    <tr>
      <td> <?php echo $lack["student_combo_name"]; ?> </td>
      <td> <?php echo $lack["hours_all"]; ?> </td>
    </tr>
  <?php endforeach; ?>
</table>

<!-- negative notes -->
<?php
  // $tab = (new School_Report_Db_Table)->get_table("bad_students");
  $tab = $rep_tab->get_table("bad_students");
  $bads = $tab->get_bad_by_report(array("id_report" => $id_report), "student_combo_name");
  $bad_count = $tab->count_bad_by_report(array("id_report" => $id_report));
?>

<h3 class="report_part_title">2. Закончили на "2"</h3>
<p> Количество: <?php echo $bad_count; ?></p>
<table width="100%" cellspacing="0" cellspaddin="0" border="1px">
  <tr>
  </tr>
  <th width="33%">Ученик</th>
  <th width="33%">Предмет</th>
  <th width="33%">Учитель</th>
    <?php foreach ($bads as $bad): ?>
      <tr>
        <td width="33%"><?php echo $bad["student_combo_name"]?></td>
        <td width="33%"><?php echo $bad["subject_name"]?></td>
        <td width="33%"><?php echo $bad["teacher_combo_name"]?></td>
      </tr>
    <?php endforeach; ?>
</table>

<!-- superb -->
<?php
  // $tab = (new School_Report_Db_Table)->get_table("good_students");
  $tab = $rep_tab->get_table("good_students");
  $goods = $tab->get_good_by_report(array("id_report" => $id_report, "student_status" => 1), "student_combo_name");
  $good_count = $tab->count_good_by_report(array("id_report" => $id_report, "student_status" => 1));
?>

<h3 class="report_part_title">3. Закончили на "5"</h3>
<p> Количество человек: <?php echo $good_count; ?></p>
<ol>
    <?php foreach ($goods as $good): ?>
        <li><?php echo $good["student_combo_name"]?></li>
      </tr>
    <?php endforeach; ?>
</ol>

<!-- good -->
<?php
  $tab = $rep_tab->get_table("good");
  $goods = $tab->get_list(array("id_report" => $id_report));
?>

<h3 class="report_part_title">4. Закончили на "4" и "5"</h3>
<p> Количество человек: <?php echo $goods[0]["good_total"] ?></p>

<!-- plan execution -->
<?php
  // $tab = (new School_Report_Db_Table)->get_table("classes_execution");
  $tab = $rep_tab->get_table("classes_execution");
  $executions = $tab->get_execution_by_report(array("id_report" => $id_report), "subject_name");
  $exec_count = $tab->sum_execution_by_report(array("id_report" => $id_report));
?>

<h3 class="report_part_title">5. Проведено часов</h3>
<p> Количество: <?php echo $exec_count; ?></p>
<table width="100%" cellspacing="0" cellspaddin="0" border="1px">
  <tr>
    <th width="50%">Предмет</th>
    <th width="50%">Часы</th>
  </tr>
    <?php foreach ($executions as $execution): ?>
      <tr>
        <td width="50%"><?php echo $execution["subject_name"]?></td>
        <td width="50%"><?php echo $execution["classes_executed"]?></td>
      </tr>
    <?php endforeach; ?>
</table>
</div> <!-- report_preview -->
