<!-- report select form -->
<?php

wp_enqueue_style( 'easyui-default', plugin_dir_url( __FILE__ ) . '../../js/easyui/themes/default/easyui.css', 9999, $this->version, 'all' );
wp_enqueue_style( 'easyui-icons', plugin_dir_url( __FILE__ ) . '../../js/easyui/themes/icon.css', array('easyui-default'), $this->version, 'all' );
wp_enqueue_style( 'school-report-editor-common-css', plugin_dir_url( __FILE__ ) . '../css/school-report-editor-common.css', array('easyui-default', 'easyui-icons'), $this->version, 'all' );
wp_enqueue_style( 'school-report-editor-css', plugin_dir_url( __FILE__ ) . '../css/school-report-editor.css', array('easyui-default', 'easyui-icons'), $this->version, 'all' );
wp_enqueue_style( 'school-report-editor-creator-css', plugin_dir_url( __FILE__ ) . '../css/school-report-editor-creator.css', array('easyui-default', 'easyui-icons'), $this->version, 'all' );
wp_enqueue_style( 'school-report-editor-viewer-css', plugin_dir_url( __FILE__ ) . '../css/school-report-viewer.css', array('easyui-default', 'easyui-icons'), $this->version, 'all' );

wp_enqueue_script( 'easyui', plugin_dir_url( __FILE__ ) . '../../js/easyui/jquery.easyui.min.js', 9999, $this->version, false );
wp_enqueue_script( 'school-report-common', plugin_dir_url( __FILE__ ) . '../js/school-report-editor.js', array( 'jquery' , 'easyui'), $this->version, false );



  $id_year = (isset($_GET["id_year"]) ? $_GET["id_year"] : "");
  $id_report_type = (isset($_GET["id_report_type"]) ? $_GET["id_report_type"] : "");
  if (!isset($_GET["ready"])):
?>
  <form action="#" method="get">
    <div>
        <label for="id_year">Учебный год:</label>
        <input id="rep_id_year" class="easyui-combobox" name="id_year" style="width:100%" value="<?php echo $id_year; ?>" />
    </div>
    <div>
        <label for="id_report_type">Период отчета:</label>
        <input id="rep_id_type" class="easyui-combobox" name="id_report_type" style="width:100%" value="<?php echo $id_report_type; ?>" />
    </div>
    <input type="button" id="reports-ready-btn" class="easyui-button" value="Проверить готовность отчетов" />
    <input type="hidden" name="ready" value="1" />
    <input type="submit" class="easyui-button" value="Сгенерировать отчет" />
  </form>
  <div id="ready-reports" class="ready-reports" style="font-size:larger;">
  </div>
<?php
  endif;
?>

<!-- single report -->
<?php
  if (!isset($_GET["ready"]) && isset($_GET["id_report"])):
    include(dirname(__FILE__)."/school-report-view-1.php");
  endif;
?>

<!-- aggregative report -->
<?php
  if (isset($_GET["ready"])):
?>

<div class="report_preview">

<?php
    // create temporary file
    $fname = "report-".date("Y-m-d")."-".date("H-i-s").".csv";

    $file_name = dirname(__FILE__)."/tmp/".$fname;
    $file_url = plugin_dir_url(__FILE__)."/tmp/".$fname;
    $f = fopen($file_name,"w");
?>
<!-- converters -->
<div style="display:flex; flex-direction:row; justify-content:flex-end;">
    <span style="padding-top:22px;padding-bottom:40px;padding-right:10px;">
      <a href="<?php echo $file_url; ?>">CSV</a>
    </span>
    <span class="pdf_invisible">
      <?php echo do_shortcode('[dkpdf-button]'); ?>
    </span>
</div>



<?php
  $id_report_type = $_GET["id_report_type"];
  $id_year        = $_GET["id_year"];

  $rep_tab = new School_Report_Db_Table;

  $tmp =   $rep_tab->get_table("years")->get($id_year);
  $year_name = $tmp["year_name"];
  $tmp = $rep_tab->get_table("report_types")->get($id_report_type);
  $type_name = $tmp["type_name"];
?>

<h2 align="center">Отчет</h2>
<table width="100%">
  <tr>
    <th width="30%">
      Дата создания
    </th>
    <td width="70%">
      <?php echo date('d.m.Y'); ?>
    </td>
  </tr>
  <tr>
    <th width="30%">
      Учебный год
    </th>
    <td width="70%">
      <?php echo $year_name; ?>
    </td>
  </tr>
  <tr>
    <th width="30%">
      Период отчета
    </th>
    <td width="70%">
      <?php echo $type_name; ?>
    </td>
  </tr>
</table>

<?php
fwrite($f,"Отчет\n");

fwrite($f, sprintf("%s;%s\n","Дата создания",date('d.m.Y')));
fwrite($f, sprintf("%s;%s\n","Учебный год",$year_name));
fwrite($f, sprintf("%s;%s\n","Период отчета",$type_name));

fwrite($f,"\n\n\n");

?>

<?php
$summary = new School_Report_Summary;
?>
<!-- plan execution -->
<h3 class="report_part_title"> 1. Проведено часов </h3>

<?php
fwrite($f,"1. Проведено часов\n");
?>

<table width="100%" >
<?php
  $subgrades = $rep_tab->get_table('subgrades')->get_list(array(), "id_subgrade","",1000);
  $i = 0;
  foreach ($subgrades as $subgrade) {
    $classes = $rep_tab->get_table('classes')->get_list(array("id_subgrade" => $subgrade["id_subgrade"]),"class_name","",1000);
    $subjects = $summary->get_executed_subjects($id_year, $id_report_type, $subgrade["id_subgrade"]);
    if ($i % 2 == 0) {
      echo "<tr><td width='50%' class='double_columns'>";
    }
    else {
      echo "<td width='50%' class='double_columns'>";
    }

?>
  <table width="100%" cellspacing="0" cellspaddin="0" border="1px">
    <!-- table header -->
    <tr>
      <th>Предмет</th>
      <?php
        fwrite($f, "\n");
        fwrite($f, "Предмет;");
      ?>
      <?php foreach($classes as $class): ?>
        <th>
          <?php echo $class["class_name"]; ?>

          <?php
            fwrite($f, sprintf("%s;",$class["class_name"]));
          ?>

        </th>
      <?php endforeach; ?>
      <th><?php echo $subgrade["subgrade_name"]; ?></th>

      <?php
        fwrite($f, sprintf("%s\n",$subgrade["subgrade_name"]));
      ?>

    </tr>
    <!-- table body  by subject-->
    <?php foreach ($subjects as $subject):?>
      <tr>
        <th><?php echo $subject["subject_name"]; ?></th>

        <?php
          fwrite($f, sprintf("%s;",$subject["subject_name"]));
        ?>

        <?php foreach($classes as $class): ?>
            <td><?php echo $summary->get_execution_by_classes($id_year, $id_report_type, $class["id_class"], $subject["id_subject"]); ?></td>
            <?php
              fwrite($f, sprintf("%s;",$summary->get_execution_by_classes($id_year, $id_report_type, $class["id_class"], $subject["id_subject"])));
            ?>
        <?php endforeach; ?>
        <td align="right"><?php echo sprintf("%01.2f",$summary->get_execution_by_grade($id_year, $id_report_type, $subgrade["id_subgrade"], $subject["id_subject"])); ?></td>
        <?php
          fwrite($f, sprintf("%s\n",
                        str_replace(".",",",
                          sprintf("%01.2f",$summary->get_execution_by_grade($id_year, $id_report_type, $subgrade["id_subgrade"], $subject["id_subject"]))
                        )
                    )
               );
        ?>
      </tr>
    <?php endforeach; ?>
  </table>
  <br />
<?php
    if ($i % 2 == 0) {
      echo "</td>";
    }
    else {
      echo "</td></tr>";
    }
    $i++;

  } // end of 1st foreach
?>
  </table>
</br>

<?php
  fwrite($f, "\n\n\n");
  fwrite($f, "2. Пропуски");
?>

<!-- lacks -->
<h3 class="report_part_title">2. Пропуски</h3>
<?php
  $grades = $rep_tab->get_table("grades")->get_list(array(),"id_grade","",1000);
  foreach($grades as $grade) {
?>
  <table width="100%" cellspacing="0" cellspaddin="0" border="1px">
    <tr>
      <th rowspan="3"><?php echo $grade["grade_name"]; ?></th>
      <th colspan="4"><?php echo $type_name; ?></th>
      <?php
        fwrite($f,"\n");
        fwrite($f, sprintf("%s;%s\n",$grade["grade_name"],$type_name ));
      ?>
    </tr>
    <tr>
      <th colspan="2">Дни</th>
      <th colspan="2">Уроки</th>
      <?php
        fwrite($f, sprintf(";%s;;%s\n","Дни","Уроки" ));
      ?>
    </tr>
    <tr>
      <th>всего</th>
      <th>по болезни</th>
      <th>всего</th>
      <th>по болезни</th>
      <?php
        fwrite($f, sprintf(";%s;%s;%s;%s\n","всего","по болезни","всего","по болезни" ));
      ?>
    </tr>
    <?php
      $lacks = $summary->get_lacks_by_grade($id_year, $id_report_type, $grade["id_grade"]);
      $total_days_all = 0;
      $total_days_ill = 0;
      $total_classes_all = 0;
      $total_classes_ill = 0;
      foreach($lacks as $lack):
        $total_days_all += $lack["days_all"];
        $total_days_ill += $lack["days_ill"];
        $total_classes_all += $lack["classes_all"];
        $total_classes_ill += $lack["classes_ills"];
    ?>
      <tr>
          <td><?php echo $lack["class_name"]; ?></td>
          <td><?php echo $lack["days_all"]; ?></td>
          <td><?php echo $lack["days_ill"]; ?></td>
          <td><?php echo $lack["classes_all"]; ?></td>
          <td><?php echo $lack["classes_ills"]; ?></td>
          <?php
          fwrite($f, sprintf("%s;%s;%s;%s;%s\n",$lack["class_name"],$lack["days_all"],$lack["days_ill"],$lack["classes_all"],$lack["classes_ills"]));
          ?>
      </tr>
    <?php
      endforeach;
    ?>
    <tr>
        <th>Всего</th>
        <td><?php echo $total_days_all; ?></td>
        <td><?php echo $total_days_ill; ?></td>
        <td><?php echo $total_classes_all; ?></td>
        <td><?php echo $total_classes_ill; ?></td>
        <?php
        fwrite($f, sprintf("%s;%s;%s;%s;%s\n","Всего",$total_days_all,$total_days_ill,$total_classes_all,$total_classes_ill));
        ?>
    </tr>
  </table>
  <br />
<?php
  } // end of grades foreach
?>

<?php
  fwrite($f, "\n\n\n");
  fwrite($f, "3. Закончили на 2\n");
?>

<!-- negative -->
<h3 class="report_part_title">3. Закончили на "2"</h3>
<?php
  $students = $summary->get_negatives_by_student($id_year, $id_report_type);
?>
  <p><strong>Всего человек: <?php echo count($students); ?></strong></p>
  <?php
    fwrite($f,"\n");
    fwrite($f,sprintf("%s;%s\n\n","Всего человек:",count($students)));
   ?>
  <table width="100%" cellspacing="0" cellspaddin="0" border="1px">
    <tr>
      <th width="20%">Класс</th>
      <th width="20%">ФИО</th>
      <th width="60%">Предметы</th>
      <?php
        fwrite($f, sprintf("%s;%s;%s\n","Класс","ФИО","Предметы"));
      ?>
    </tr>
    <?php foreach($students as $student): ?>
      <tr>
        <td align="center"><?php echo $student["class_name"]; ?></td>
        <td><?php echo $student["student_combo_name"]; ?></td>
        <td><?php echo $student["subjects"]; ?></td>
        <?php
        fwrite($f, sprintf("%s;%s;%s\n",$student["class_name"],$student["student_combo_name"],$student["subjects"]));
        ?>
      </tr>
    <?php endforeach; ?>
  </table>
  <br />
  <?php
    fwrite($f,"\n");
  ?>


<?php
 $neg_subject = $summary->get_negative_by_subject($id_year, $id_report_type);
?>
<table width="100%" cellspacing="0" cellspaddin="0" border="1px">
  <?php foreach ($neg_subject as $subject): ?>
    <tr>
      <td colspan="3" align="center">
        <strong>
          <?php echo $subject["subject_name"]; ?>
          :
          <?php echo $subject["count_neg"]; ?>
          чел.
      </td>
      <?php
        fwrite($f,"\n");
        fwrite($f,sprintf("%s:;%s;%s\n",$subject["subject_name"],$subject["count_neg"],"чел."));
      ?>
    </tr>
    <?php
      $students = $summary->get_negative_by_subject_by_student($id_year, $id_report_type, $subject["id_subject"]);
      foreach($students as $student):
    ?>
      <tr>
        <td align="center"><?php echo $student["class_name"]; ?></td>
        <td><?php echo $student["student_combo_name"]; ?></td>
        <td><?php echo $student["teacher_combo_name"]; ?></td>
        <?php
          fwrite($f,sprintf("%s;%s;%s\n",$student["class_name"],$student["student_combo_name"],$student["teacher_combo_name"]));
        ?>
      </tr>
    <?php
      endforeach;
    ?>

  <?php endforeach; ?>
</table>
<br />

<?php
  fwrite($f,"\n\n\n");
  fwrite($f,"4. Отличники\n");
  fwrite($f,"Отличники по звеньям\n");
?>

<!-- good students -->
<h3 class="report_part_title">4. Отличники</h3>
<p><strong>Отличники по звеньям</strong></p>
<?php
  $students = $summary->get_outstanding_by_grade($id_year, $id_report_type);
?>
  <table width="100%" cellspacing="0" cellspaddin="0" border="1px">
    <tr>
      <th width="50%">Звено</th>
      <th>Количество учеников</th>
      <?php
        fwrite($f, sprintf("%s;%s\n","Звено","Количество учеников"));
       ?>
    </tr>
    <?php foreach($students as $student): ?>
      <tr>
        <td><?php echo $student["grade_name"]; ?></td>
        <td><?php echo $student["good_students"]; ?></td>
        <?php
          fwrite($f, sprintf("%s;%s\n",$student["grade_name"],$student["good_students"]));
         ?>
      </tr>
    <?php endforeach; ?>
  </table>
  <br />

  <?php
    fwrite($f,"\n");
    fwrite($f,"Хорошисты по звеньям\n");
  ?>

  <p><strong>Хорошисты по звеньям</strong></p>
  <?php
    $students = $summary->get_good_by_grade($id_year, $id_report_type);
  ?>
    <table width="100%" cellspacing="0" cellspaddin="0" border="1px">
      <tr>
        <th width="50%">Звено</th>
        <th>Количество учеников</th>
        <?php
          fwrite($f, sprintf("%s;%s\n","Звено","Количество учеников"));
         ?>
      </tr>
      <?php foreach($students as $student): ?>
        <tr>
          <td><?php echo $student["grade_name"]; ?></td>
          <td><?php echo $student["good_students"]; ?></td>
          <?php
            fwrite($f, sprintf("%s;%s\n",$student["grade_name"],$student["good_students"]));
           ?>
        </tr>
      <?php endforeach; ?>
    </table>
    <br />

    <?php
      fwrite($f,"\n");
      fwrite($f,"Отличники по классам\n");
    ?>

<p><strong>Отличники по классам</strong></p>
<?php
  $students = $summary->get_positives($id_year, $id_report_type);
?>
  <table width="100%" cellspacing="0" cellspaddin="0" border="1px">
    <tr>
      <th width="10%">Класс</th>
      <th>ФИО</th>
      <?php
        fwrite($f, sprintf("%s;%s\n","Класс","ФИО"));
       ?>
    </tr>
    <?php foreach($students as $student): ?>
      <tr>
        <td><?php echo $student["class_name"]; ?></td>
        <td><?php echo $student["student_combo_name"]; ?></td>
        <?php
          fwrite($f, sprintf("%s;%s\n",$student["class_name"],$student["student_combo_name"]));
         ?>

      </tr>
    <?php endforeach; ?>
  </table>
  <br />

<!-- quality -->

<?php
  fwrite($f,"\n\n\n");
  fwrite($f,"5. Успеваемость и качество\n");
?>

<h3 class="report_part_title">5. Успеваемость и качество</h3>
<?php
  $grades = $rep_tab->get_table("grades")->get_list(array(),"id_grade","",1000);
  foreach($grades as $grade) {
?>
  <table width="100%" cellspacing="0" cellspaddin="0" border="1px">
    <tr>
      <th colspan="3"><?php echo $grade["grade_name"]; ?></th>
      <?php
        fwrite($f,sprintf("\n%s\n",$grade["grade_name"]));
       ?>
    </tr>
    <tr>
      <th>Класс</th>
      <th>Качество</th>
      <th>Успеваемость</th>
      <?php
        fwrite($f, sprintf("%s;%s;%s\n","Класс","Качество","Успеваемость"));
      ?>
    </tr>

    <?php
      $quality = $summary->get_quality($id_year, $id_report_type, $grade["id_grade"]);
      $sum_q = 0;
      $sum_a = 0;
      $sum_count = 0;
      $n = (count($quality) == 0 ? 1 : count($quality));
      foreach($quality as $q):
        $sum_q += ($q["quality"]*100 * $q["students_count"]);
        $sum_a += ($q["achievement"]*100 * $q["students_count"]);
        $sum_count += $q["students_count"];
    ?>
      <tr>
          <td><?php echo $q["class_name"]; ?></td>
          <td><?php echo sprintf("%01.2f",$q["quality"]*100); ?>%</td>
          <td><?php echo sprintf("%01.2f",$q["achievement"]*100); ?>%</td>
          <?php
            fwrite($f, sprintf("%s;%s;%s\n",$q["class_name"],
              str_replace(".",",",sprintf("%01.2f",$q["quality"]*100)),
              str_replace(".",",",sprintf("%01.2f",$q["achievement"]*100))));
          ?>
      </tr>
    <?php
      endforeach;
      if ($sum_count > 0):
    ?>

    <tr>
        <th>Сумма:</th>
        <td><?php echo sprintf("%01.2f",$sum_q/$sum_count); ?>%</td>
        <td><?php echo sprintf("%01.2f",$sum_a/$sum_count); ?>%</td>
        <?php
          fwrite($f, sprintf("%s;%s;%s\n","Сумма",
            str_replace(".",",",sprintf("%01.2f",$sum_q/$sum_count)),
            str_replace(".",",",sprintf("%01.2f",$sum_a/$sum_count))));
        ?>
    </tr>
  <?php
    endif;
   ?>
  </table>
  <br />
<?php
  } // end of grades foreach
?>

</div>

<?php
  fclose($f);
  // change encoding
  $conv = iconv('utf-8', 'windows-1251', file_get_contents($file_name));
  $f = fopen($file_name,"w");
  fwrite($f, $conv);
  fclose($f);
  endif;
?>
