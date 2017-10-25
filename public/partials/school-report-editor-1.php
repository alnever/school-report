<!-- -->
<?php

wp_enqueue_style( 'easyui-default', plugin_dir_url( __FILE__ ) . '../../js/easyui/themes/default/easyui.css', 9999, $this->version, 'all' );
wp_enqueue_style( 'easyui-icons', plugin_dir_url( __FILE__ ) . '../../js/easyui/themes/icon.css', array('easyui-default'), $this->version, 'all' );
wp_enqueue_style( 'school-report-editor-common-css', plugin_dir_url( __FILE__ ) . '../css/school-report-editor-common.css', array('easyui-default', 'easyui-icons'), $this->version, 'all' );
wp_enqueue_style( 'school-report-editor-css', plugin_dir_url( __FILE__ ) . '../css/school-report-editor.css', array('easyui-default', 'easyui-icons'), $this->version, 'all' );

wp_enqueue_script( 'easyui', plugin_dir_url( __FILE__ ) . '../../js/easyui/jquery.easyui.min.js', 9999, $this->version, false );
wp_enqueue_script( 'school-report-editor', plugin_dir_url( __FILE__ ) . '../js/school-report-editor.js', array( 'jquery' , 'easyui'), $this->version, false );


  if (!isset($_GET["id_report"])):
?>
<!-- Title -->
<div class="entry-meta post-info">
  <span class="theauthor">
    Добро пожаловать, <?php _e($this->user->display_name); ?>!
  </span>
</div>
<!-- Add new report section -->
<div class="easyui-panel">
  <input id="create_new_report_btn"  type="button" class="easyui-button" data-options="iconCls:'icon-add'" value="Создать новый отчет" />
  <input id="delete_report_btn"  type="button" class="easyui-button" data-options="iconCls:'icon-remove'" value="Удалить отчет" />
  <input id="update_report_btn"  type="button" class="easyui-button" data-options="iconCls:'icon-edit'" value="Редактировать шапку отчета" />
  <input id="pdf_report" type="button" class="easyui-button" data-options="iconCls:'icon-edit'" value="Просмотр" />
  <div id="create_new_report_panel" class="easyui-panel"
                style="width:100%;height:auto;padding:10px;background:#fafafa;"
                data-options="closable:true,
                collapsible:true, minimizable:true, maximizable:true, collapsed: true">
    <form id="create_new_report_form" method="post">
        <input type="hidden" id="action_1" name="action" value="new_report" />
        <input type="hidden" name="id_creator" value="<?php echo $this->user->ID; ?>" />
        <input type="hidden" name="report_status" value="0" />
        <input type="hidden" id="id_report_1" name="id_report" value="0" />
        <input type="hidden" id="tmp_id_year" name="tmp_id_year" value="0" />
        <input type="hidden" name="create_date" value="<?php echo date("Y-m-d"); ?>" />
        <div>
            <label for="id_year">Учебный год:</label>
            <input id="rep_id_year" class="easyui-combobox" name="id_year" style="width:100%" />
        </div>
        <div>
            <label for="id_class">Класс:</label>
            <input id="rep_id_class" class="easyui-combobox" name="id_class" style="width:100%" />
        </div>
        <div>
            <label for="id_report_type">Период отчета:</label>
            <input id="rep_id_type" class="easyui-combobox" name="id_report_type" style="width:100%" />
        </div>
        <div>
            <label for="id_report_type">Количество учеников в классе на дату составления отчета:</label>
            <input id="rep_students_count" name="students_count" class="easyui-numberbox" data-options="required:true,min:0,precision:0,value:0" style="width:100%"/>
        </div>
        <div>
          <input type="submit" class="easyui-submit" value="Сохранить" />
          <input id="cancel_edit_btn" type="button" class="easyui-button" value="Отмена" />
        </div>
    </form>
  </div>
  <div id="students_count_div" class="students_count_div" style="visibility:hidden">
    <p><strong>Количество человек в классе:&nbsp;<span class="pupil_number"></span></strong></p>
  </div>

  <!-- Reports toolbar
  <div id="user_reports_tb" style="padding:3px">
      <span>Учебный год:</span>
      <input id="year_name_search" style="line-height:26px;border:1px solid #ccc">
      <span>Класс:</span>
      <input id="class_name_search" style="line-height:26px;border:1px solid #ccc">
      <input id="user_report_search_btn"  type="button" class="easyui-button" data-options="iconCls:'icon-search'" value="Найти" />
  </div>
  -->
  <!-- Reports list -->

  <table id="user_reports"></table>
</div>



<!-- BIG TABLE -->

<table class="report_editor_table">
<tr>
  <td class="report_editor_td">


<!-- Report absent -->
<div id="absent_panel" class="easyui-panel"
              style="width:100%;height:auto;padding:10px;background:#fafafa;"
              data-options="closable:true,
              collapsible:true, minimizable:true, maximizable:true, collapsed: true">
  <h3>Сводная информация по пропускам</h3>
  <form id="absent_form" method="post">
    <div>
        <label for="days_all">Дней всего:</label>
        <input id="days_all" name="days_all" class="easyui-numberbox" data-options="required:true,min:0,precision:0,value:0" style="width:100%"  />
    </div>
    <div>
        <label for="days_ill">Дней по болезни:</label>
        <input id="days_ill" name="days_ill" class="easyui-numberbox" data-options="required:true,min:0,precision:0,value:0" style="width:100%" />
    </div>
    <div>
        <label for="classes_all">Уроков всего:</label>
        <input id="classes_all" name="classes_all" class="easyui-numberbox" data-options="required:true,min:0,precision:0,value:0" style="width:100%" />
    </div>
    <div>
        <label for="classes_ills">Уроков по болезни:</label>
        <input id="classes_ills" name="classes_ills" class="easyui-numberbox" data-options="required:true,min:0,precision:0,value:0" style="width:100%" />
    </div>
    <!-- div>
        <label for="without_reason">Без уважительной причины:</label>
        <intput id="without_reason" name="without_reason" class="easyui-numberbox" data-options="required:true,min:0,precision:0,value:0" style="width:100%" />
    </div -->

    <input type="hidden" id="action_2" name="action" value="edit_absent" />
    <input type="hidden" id="id_report_2" name="id_report" value="0" />
    <input type="hidden" name="id_absent" value="0" />
    <input type="submit" class="easyui-submit" value="Сохранить" />
  </form>
</div>

<!-- big table -->
</td>
<td class="report_editor_td">
<!-- big table -->

<!-- Report absent students -->
<div id="absent_students_panel" class="easyui-panel"
              style="width:100%;height:auto;padding:10px;background:#fafafa;"
              data-options="closable:true,
              collapsible:true, minimizable:true, maximizable:true, collapsed: true">
  <h3>Поименная информация по пропускам</h3>
  <input id="new_absent_btn"  type="button" class="easyui-button" data-options="iconCls:'icon-add'" value="Добавить" />
  <input id="delete_absent_btn"  type="button" class="easyui-button" data-options="iconCls:'icon-remove'" value="Удалить" />
  <input id="update_absent_btn"  type="button" class="easyui-button" data-options="iconCls:'icon-edit'" value="Редактировать" />
  <div id="new_absent_panel" class="easyui-panel"
                style="width:100%;height:auto;padding:10px;background:#fafafa;"
                data-options="closable:true,
                collapsible:true, minimizable:true, maximizable:true, collapsed: true">
    <form id="new_absent_form" method="post">
      <div>
          <label for="id_student">Ученик:</label>
          <input id="rep_id_absent_student" class="easyui-combobox" name="id_student" style="width:100%" />
      </div>
      <div>
          <label for="hours_all">Всего пропущено часов:</label>
          <input id="hours_all" name="hours_all" class="easyui-numberbox" data-options="required:true,min:0,precision:0,value:0" style="width:100%" />
      </div>
      <input type="hidden" id="id_absent_student" name="id_absent" value="0" />
      <input type="hidden" id="action_3" name="action" value="new_absent_student" />
      <input type="hidden" id="id_report_3" name="id_report" value="0" />
      <input type="submit" class="easyui-submit" value="Сохранить" />
      <input id="cancel_absent_btn" type="button" class="easyui-button" value="Отмена" />
    </form>
  </div>

  <table id="absent_students_list"></table>
<!--
  <div id="student_absent_tb" style="padding:3px">
      <span>Ученик:</span>
      <input id="student_absent_search" style="line-height:26px;border:1px solid #ccc">
      <input id="student_absent_search_btn"  type="button" class="easyui-button" data-options="iconCls:'icon-search'" value="Найти" />
  </div>
-->
</div>

<!-- big table -->
</td>
</tr>
<tr>
  <td class="report_editor_td">

<!-- Report bad students -->
<div id="bad_students_panel" class="easyui-panel"
              style="width:100%;height:auto;padding:10px;background:#fafafa;"
              data-options="closable:true,
              collapsible:true, minimizable:true, maximizable:true, collapsed: true">
  <h3>Неуспевающие</h3>
  <input id="new_bad_btn"  type="button" class="easyui-button" data-options="iconCls:'icon-add'" value="Добавить" />
  <input id="delete_bad_btn"  type="button" class="easyui-button" data-options="iconCls:'icon-remove'" value="Удалить" />
  <input id="update_bad_btn"  type="button" class="easyui-button" data-options="iconCls:'icon-edit'" value="Редактировать" />
  <div id="new_bad_panel" class="easyui-panel"
                style="width:100%;height:auto;padding:10px;background:#fafafa;"
                data-options="closable:true,
                collapsible:true, minimizable:true, maximizable:true, collapsed: true">
    <form id="new_bad_form" method="post">
      <div>
          <label for="id_student">Ученик:</label>
          <input id="rep_id_bad_student" class="easyui-combobox" name="id_student" style="width:100%" />
      </div>
      <div>
          <label for="id_subject">Предмет:</label>
          <input id="rep_id_bad_subject" class="easyui-combobox" name="id_subject" style="width:100%" />
      </div>
      <div>
          <label for="id_teacher">Учитель:</label>
          <input id="rep_id_teacher" class="easyui-combobox" name="id_teacher" style="width:100%" />
      </div>
      <input type="hidden" id="id_bad_student" name="id_bad" value="0" />
      <input type="hidden" id="action_4" name="action" value="new_bad_student" />
      <input type="hidden" id="id_report_4" name="id_report" value="0" />
      <input type="submit" class="easyui-submit" value="Сохранить" />
      <input id="cancel_bad_btn" type="button" class="easyui-button" value="Отмена" />
    </form>
  </div>

  <table id="bad_students_list"></table>
<!--
  <div id="student_bad_tb" style="padding:3px">
      <span>Ученик:</span>
      <input id="bad_student_search" style="line-height:26px;border:1px solid #ccc">
      <span>Предмет:</span>
      <input id="bad_subject_search" style="line-height:26px;border:1px solid #ccc">
      <span>Учитель:</span>
      <input id="bad_teacher_search" style="line-height:26px;border:1px solid #ccc">
      <input id="bad_search_btn"  type="button" class="easyui-button" data-options="iconCls:'icon-search'" value="Найти" />
  </div>
-->
</div>


</td>
<td class="report_editor_td">


<!-- Report good students -->
<div id="good_students_panel" class="easyui-panel"
              style="width:100%;height:auto;padding:10px;background:#fafafa;"
              data-options="closable:true,
              collapsible:true, minimizable:true, maximizable:true, collapsed: true">
  <h3>Отличники/хорошисты</h3>
  <input id="new_good_btn"  type="button" class="easyui-button" data-options="iconCls:'icon-add'" value="Добавить" />
  <input id="delete_good_btn"  type="button" class="easyui-button" data-options="iconCls:'icon-remove'" value="Удалить" />
  <input id="update_good_btn"  type="button" class="easyui-button" data-options="iconCls:'icon-edit'" value="Редактировать" />
  <div id="new_good_panel" class="easyui-panel"
                style="width:100%;height:auto;padding:10px;background:#fafafa;"
                data-options="closable:true,
                collapsible:true, minimizable:true, maximizable:true, collapsed: true">
    <form id="new_good_form" method="post">
      <div>
          <label for="id_student">Ученик:</label>
          <input id="rep_id_good_student" class="easyui-combobox" name="id_student" style="width:100%" />
      </div>
      <div>
          <label for="student_status">Отличник:</label>
          <select id="student_status" name="student_status" class="easyui-combobox" style="width:100%">
            <option value="-1">(не выбрано)</option>
            <option value="0">Хорошист</option>
            <option value="1">Отличник</option>
          </select>
      </div>
      <input type="hidden" id="id_good_student" name="id_good" value="0" />
      <input type="hidden" id="action_5" name="action" value="new_good_student" />
      <input type="hidden" id="id_report_5" name="id_report" value="0" />
      <input type="submit" class="easyui-submit" value="Сохранить" />
      <input id="cancel_good_btn" type="button" class="easyui-button" value="Отмена" />
    </form>
  </div>

  <table id="good_students_list"></table>
<!--
  <div id="student_good_tb" style="padding:3px">
      <span>Ученик:</span>
      <input id="bad_student_search" style="line-height:26px;border:1px solid #ccc">
      <input id="good_search_btn"  type="button" class="easyui-button" data-options="iconCls:'icon-search'" value="Найти" />
  </div>
-->
</div>

</td>
</tr>
<tr>
  <td class="report_editor_td">

<!-- Report plan execution -->
<div id="execution_panel" class="easyui-panel"
              style="width:100%;height:auto;padding:10px;background:#fafafa;"
              data-options="closable:true,
              collapsible:true, minimizable:true, maximizable:true, collapsed: true">
  <h3>Проведено часов</h3>
  <input id="new_execution_btn"  type="button" class="easyui-button" data-options="iconCls:'icon-add'" value="Добавить" />
  <input id="delete_execution_btn"  type="button" class="easyui-button" data-options="iconCls:'icon-remove'" value="Удалить" />
  <input id="update_execution_btn"  type="button" class="easyui-button" data-options="iconCls:'icon-edit'" value="Редактировать" />
  <div id="new_execution_panel" class="easyui-panel"
                style="width:100%;height:auto;padding:10px;background:#fafafa;"
                data-options="closable:true,
                collapsible:true, minimizable:true, maximizable:true, collapsed: true">
    <form id="new_execution_form" method="post">
      <div>
          <label for="id_subject">Предмет:</label>
          <input id="rep_id_subject" class="easyui-combobox" name="id_subject" style="width:100%" />
      </div>
      <div>
          <label for="classes_executed">Проведено часов:</label>
          <input id="classes_executed" name="classes_executed" class="easyui-numberbox" data-options="required:true,min:0,precision:0,value:0" style="width:100%" />
      </div>
      <input type="hidden" id="id_execution" name="id_execution" value="0" />
      <input type="hidden" id="action_6" name="action" value="new_execution" />
      <input type="hidden" id="id_report_6" name="id_report" value="0" />
      <input type="submit" class="easyui-submit" value="Сохранить" />
      <input id="cancel_execution_btn" type="button" class="easyui-button" value="Отмена" />
    </form>
  </div>

  <table id="execution_list"></table>
<!--
  <div id="execution_tb" style="padding:3px">
      <span>Предмет:</span>
      <input id="execution_subject_search" style="line-height:26px;border:1px solid #ccc">
      <input id="execution_search_btn"  type="button" class="easyui-button" data-options="iconCls:'icon-search'" value="Найти" />
  </div>
-->
</div>

</td>
  <td class="report_editor_td">
  </td>
</tr>
</table>

<!-- end of BIG TABLE -->

<?php
endif // end of report editor form
?>
<!-- PDF output -->
<?php
  if (isset($_GET["id_report"])) {
    include(dirname(__FILE__)."/school-report-view-1.php");
  }
?>
