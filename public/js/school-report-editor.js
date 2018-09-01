(function( $ ) {
	'use strict';

	/**
	 * All of the code for your admin-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */
	 $(document).ready(function(){

		 /*
		 Data grid for user reports list
		 */
		 $('#user_reports').datagrid({
				 // data: $.parseJSON(responce.data),

				 url:  window.wp_data.ajax_url,
				 method: 'post',
				 queryParams: {
					 action: 'user_reports'
				 },

				 loadMsg: "Идет загрузка данных...",
				 emptyMsg: "Нет данных",
				 pagination: true,
				 singleSelect: true,
				 columns:[[
						 {field:'id_report',title:'ID',width:30, hidden: true},
						 {field:'create_date',title:'Дата создания', sortable: true},
						 {field:'type_name',title:'Период отчета',align:'left', sortable: true},
						 {field:'class_name',title:'Класс',align:'left', sortable: true},
						 {field:'year_name',title:'Учебный год',align:'left', sortable: true},
						 {field:'students_count',title:'Количество учеников',align:'center', sortable: true},
						 {field:'report_status',title:'Статус отчета',width:50,align:'left',hidden: true},
						 {field:'id_creator',title:'ID',width:30, hidden: true},
						 {field:'id_report_type',title:'ID',width:30, hidden: true},
						 {field:'id_class',title:'ID',width:30, hidden: true},
						 {field:'id_year',title:'ID',width:30, hidden: true},
				 ]],
				 title: "Ваши отчеты",
				 // toolbar: "#user_reports_tb",

				 onLoadError: function(){
				 },
				 onLoadSuccess: function(data){
					 // console.log(data);
				 },
				 // loader for reports data grid
				 loader: function(param, success, error){
			 		var opts = $(this).datagrid('options');
			 		if (!opts.url) return false;
			 		$.ajax({
			 			type: opts.method,
			 			url: opts.url,
			 			data: param,
			 			dataType: 'json',
			 			success: function(data){
			 				if (data.isError){
								//console.log(data);
			 					// error($.parseJSON(data));
			 				} else {
								//console.log(data.data);
			 					success($.parseJSON(data.data));
			 				}
			 			},
			 			error: function(){
			 				error.apply(this, arguments);
			 			}
			 		});
				 }, // end of loader function fo data grid
				/*
	 		 Reports Data grid OnSelect
	 		 Show the report content below the main grid
	 		 */
				onSelect: function(index, row) {
					$("#absent_panel").panel('expand');
					$("#absent_students_panel").panel('expand');
					$("#bad_students_panel").panel('expand');
					$("#good_students_panel").panel('expand');
					$("#execution_panel").panel('expand');
					// get students count
					/*$.ajax({
						url:  window.wp_data.ajax_url,
						method: 'POST',
						data:{
							action: 'students_count',
							id_class: row.id_class,
						},
						dataType: 'json',
						success: function(data){
							var arr = $.parseJSON(data.data);
							$("span.pupil_number").html(arr["students_count"]);
							$("div.students_count_div").css("visibility","visible");
						}
					}); // end of students count
					*/
					// get absent for current reports
					$.ajax({
						url:  window.wp_data.ajax_url,
						method: 'POST',
						data:{
							action: 'report_absent',
							id_report: row.id_report,
						},
						dataType: 'json',
						cache: false,
						success: function(data){
							// settle absent_panel fields with token values
							var arr = $.parseJSON(data.data);
							// console.log(data.data);
							if (arr.length > 0){
								$("#days_all").textbox('setValue', parseInt(arr[0].days_all));
								$("#days_ill").numberbox('setValue', arr[0].days_ill);
								$("#classes_all").numberbox('setValue', arr[0].classes_all);
								$("#classes_ills").numberbox('setValue', arr[0].classes_ills);
								//$("#without_reason").numberbox('setValue', arr[0].without_reason);
								$('input[name=id_absent]').val(arr[0].id_absent);
								$('input[id=id_report_2]').val(arr[0].id_report);
							}
							else{
								$("#days_all").numberbox('setValue', 0);
								$("#days_ill").numberbox('setValue', 0);
								$("#classes_all").numberbox('setValue', 0);
								$("#classes_ills").numberbox('setValue', 0);
								//$("#without_reason").numberbox('setValue', 0);
								$('input[name=id_absent]').val(0);
								$('input[id=id_report_2]').val(row.id_report);
							}
						},
						error: function()
						{
							$("#days_all").numberbox('setValue', '0');
							$("#days_ill").numberbox('setValue', '0');
							$("#classes_all").numberbox('setValue', '0');
							$("#classes_ills").numberbox('setValue', '0');
							//$("#without_reason").numberbox('setValue', '0');
							$('input[name=id_absent]').val(0);
							$('input[id=id_report_2]').val(0);
						}
					}); // end of absent form fill

					// get total good for current reports
					$.ajax({
						url:  window.wp_data.ajax_url,
						method: 'POST',
						data:{
							action: 'report_total_good',
							id_report: row.id_report,
						},
						cache: false,
						dataType: 'json',
						success: function(data){
							// settle absent_panel fields with token values
							var arr = $.parseJSON(data.data);
							console.log(data.data);
							if (arr.length > 0){
								$("#good_total").textbox('setValue', parseInt(arr[0].good_total));
								$('input[id=id_good_total]').val(arr[0].id_good_total);
								$('input[id=id_report_7]').val(arr[0].id_report);
							}
							else{
								$("#good_total").numberbox('setValue', 0);
								$('input[id=id_good_total]').val(0);
								$('input[id=id_report_7]').val(row.id_report);
							}
						},
						error: function()
						{
							$("#good_total").numberbox('setValue', '0');
							//$("#without_reason").numberbox('setValue', '0');
							$('input[id=id_good_total]').val(0);
							$('input[id=id_report_7]').val(0);
						}
					}); // end of absent form fill

					// student comboboxes
					// ... absent students
					$("#rep_id_absent_student").combobox({
						url:  window.wp_data.ajax_url,
						method: 'POST',
						data:{
							action: 'report_students',
							id_year: $('#user_reports').datagrid('getSelected').id_year,
							id_class: $('#user_reports').datagrid('getSelected').id_class,
						},
						required: true,
						valueField: 'id_student',
						textField: 'combo_name',
						loader: function(param, success, error){
			 				var opts = $(this).combobox('options');
			 				if (!opts.url) return false;
			 				$.ajax({
			 					type: 'post',
			 					url: window.wp_data.ajax_url,
			 					data: {
									action: 'report_students',
									id_year: $('#user_reports').datagrid('getSelected').id_year,
									id_class: $('#user_reports').datagrid('getSelected').id_class,
								},
			 					dataType: 'json',
			 					success: function(data){
			 						if (data.isError){
			 							//console.log(data);
			 						} else {
			 							// console.log(data.data);
			 							success($.parseJSON(data.data));
			 						}
			 					},
			 					error: function(){
			 						error.apply(this, arguments);
			 					}
			 				});
		 			  }
					}); // ... end of absent students combobox

					// ... bad students combobox
					$("#rep_id_bad_student").combobox({
						url:  window.wp_data.ajax_url,
						method: 'POST',
						data:{
							action: 'report_students',
							id_year: $('#user_reports').datagrid('getSelected').id_year,
							id_class: $('#user_reports').datagrid('getSelected').id_class,
						},
						required: true,
						valueField: 'id_student',
						textField: 'combo_name',
						loader: function(param, success, error){
			 				var opts = $(this).combobox('options');
			 				if (!opts.url) return false;
			 				$.ajax({
			 					type: 'post',
			 					url: window.wp_data.ajax_url,
			 					data: {
									action: 'report_students',
									id_year: $('#user_reports').datagrid('getSelected').id_year,
									id_class: $('#user_reports').datagrid('getSelected').id_class,
								},
			 					dataType: 'json',
			 					success: function(data){
			 						if (data.isError){
			 							//console.log(data);
			 						} else {
			 							// console.log(data.data);
			 							success($.parseJSON(data.data));
			 						}
			 					},
			 					error: function(){
			 						error.apply(this, arguments);
			 					}
			 				});
		 			  }
					}); // ... end of bad students combobox

					// ... good students combobox
					$("#rep_id_good_student").combobox({
						url:  window.wp_data.ajax_url,
						method: 'POST',
						data:{
							action: 'report_students',
							id_year: $('#user_reports').datagrid('getSelected').id_year,
							id_class: $('#user_reports').datagrid('getSelected').id_class,
						},
						required: true,
						valueField: 'id_student',
						textField: 'combo_name',
						loader: function(param, success, error){
			 				var opts = $(this).combobox('options');
			 				if (!opts.url) return false;
			 				$.ajax({
			 					type: 'post',
			 					url: window.wp_data.ajax_url,
			 					data: {
									action: 'report_students',
									id_year: $('#user_reports').datagrid('getSelected').id_year,
									id_class: $('#user_reports').datagrid('getSelected').id_class,
								},
			 					dataType: 'json',
			 					success: function(data){
			 						if (data.isError){
			 							//console.log(data);
			 						} else {
			 							// console.log(data.data);
			 							success($.parseJSON(data.data));
			 						}
			 					},
			 					error: function(){
			 						error.apply(this, arguments);
			 					}
			 				});
		 			  }
					}); // ... end of good students combobox

					// Combobox for subjects in bad form
					$("#rep_id_bad_subject").combobox({
						url:  window.wp_data.ajax_url,
						method: 'POST',
						data:{
							action: 'report_subjects',
						},
						required: true,
						valueField: 'id_subject',
						textField: 'subject_name',
						loader: function(param, success, error){
			 				var opts = $(this).combobox('options');
			 				if (!opts.url) return false;
			 				$.ajax({
			 					type: 'post',
			 					url: window.wp_data.ajax_url,
			 					data: {
									action: 'report_subjects',
								},
			 					dataType: 'json',
			 					success: function(data){
			 						if (data.isError){
			 							// console.log(data);
			 						} else {
			 							// console.log(data.data);
			 							success($.parseJSON(data.data));
			 						}
			 					},
			 					error: function(){
			 						error.apply(this, arguments);
			 					}
			 				});
		 			  }
					}); // ... end of bad subjects combobox

					// Combobox for subjects in plan execution form
					$("#rep_id_subject").combobox({
						url:  window.wp_data.ajax_url,
						method: 'POST',
						data:{
							action: 'report_subjects',
						},
						required: true,
						valueField: 'id_subject',
						textField: 'subject_name',
						loader: function(param, success, error){
			 				var opts = $(this).combobox('options');
			 				if (!opts.url) return false;
			 				$.ajax({
			 					type: 'post',
			 					url: window.wp_data.ajax_url,
			 					data: {
									action: 'report_subjects',
								},
			 					dataType: 'json',
			 					success: function(data){
			 						if (data.isError){
			 							// console.log(data);
			 						} else {
			 							// console.log(data.data);
			 							success($.parseJSON(data.data));
			 						}
			 					},
			 					error: function(){
			 						error.apply(this, arguments);
			 					}
			 				});
		 			  }
					}); // ... end of Combobox for subjects in plan execution form

					// Combobox for teachers
					$("#rep_id_teacher").combobox({
						url:  window.wp_data.ajax_url,
						method: 'POST',
						data:{
							action: 'report_teachers',
						},
						required: true,
						valueField: 'id_teacher',
						textField: 'combo_name',
						loader: function(param, success, error){
			 				var opts = $(this).combobox('options');
			 				if (!opts.url) return false;
			 				$.ajax({
			 					type: 'post',
			 					url: window.wp_data.ajax_url,
			 					data: {
									action: 'report_teachers',
								},
			 					dataType: 'json',
			 					success: function(data){
			 						if (data.isError){
			 							// console.log(data);
			 						} else {
			 							// console.log(data.data);
			 							success($.parseJSON(data.data));
			 						}
			 					},
			 					error: function(){
			 						error.apply(this, arguments);
			 					}
			 				});
		 			  }
					}); // ... end of Combobox for subjects in plan execution form
					// data grid for absent students

					$("#absent_students_list").datagrid({
						url:  window.wp_data.ajax_url,
						method: 'POST',
						queryParams:{
							action: 'student_absent',
							id_report: $('#user_reports').datagrid('getSelected').id_report
							/*
							,
							scn: $('#student_absent_search').val()
							*/
						},
						loadMsg: "Идет загрузка данных...",
 	 	   			emptyMsg: "Нет данных",
		 				pagination: true,
		 				singleSelect: true,
		 				columns:[[
		 						 {field:'id_report',title:'ID',width:30, hidden: true},
								 {field:'id_absent',title:'ID',width:30, hidden: true},
								 {field:'id_student',title:'ID',width:30, hidden: true},
								 {field:'student_combo_name',title:'Фамилия, имя и отчество ученика', width: 300, sortable: true},
		 						 {field:'hours_all',title:'Пропущено часов', sortable: true, width: 300, align: 'right'}
		 				 ]],
		 				title: "Пропуски учеников",
		 				// toolbar: "#student_absent_tb",
		 				 // loader for reports data grid
		 				loader: function(param, success, error){
		 			 		$.ajax({
								type: 'post',
			 					url: window.wp_data.ajax_url,
			 					data: param,
								/*{
									action: 'student_absent',
									id_report: $('#user_reports').datagrid('getSelected').id_report
								},*/
		 			 			dataType: 'json',
		 			 			success: function(data){
		 			 				if (data.isError){
		 								// console.log(data);
		 			 				} else {
		 								// console.log(data.data);
		 			 					success($.parseJSON(data.data));
		 			 				}
		 			 			},
		 			 			error: function(){
									alert("loader error");
		 			 				error.apply(this, arguments);
		 			 			}
		 			 		});
		 				}, // end of loader function for student absent datagrid
					}); // end of student absent datagrid

					// Data grid for bad students

					$("#bad_students_list").datagrid({
						url:  window.wp_data.ajax_url,
						method: 'POST',
						queryParams:{
							action: 'report_bad',
							id_report: $('#user_reports').datagrid('getSelected').id_report
						},
						loadMsg: "Идет загрузка данных...",
 	 	   			emptyMsg: "Нет данных",
		 				pagination: true,
		 				singleSelect: true,
		 				columns:[[
		 						 {field:'id_report',title:'ID',width:30, hidden: true},
								 {field:'id_bad',title:'ID',width:30, hidden: true},
								 {field:'id_student',title:'ID',width:30, hidden: true},
								 {field:'id_subject',title:'ID',width:30, hidden: true},
								 {field:'id_teacher',title:'ID',width:30, hidden: true},
								 {field:'student_combo_name',title:'Ученик',width: 300, sortable: true},
								 {field:'subject_name',title:'Предмет',width: 200,sortable: true},
								 {field:'teacher_combo_name',title:'Учитель', width: 300,sortable: true}
		 				 ]],
		 				title: "Неуспевающие",
		 				// toolbar: "#student_bad_tb",
		 				 // loader for reports data grid
		 				loader: function(param, success, error){
		 			 		$.ajax({
								type: 'post',
			 					url: window.wp_data.ajax_url,
			 					data: param,
		 			 			dataType: 'json',
		 			 			success: function(data){
		 			 				if (data.isError){
		 								// console.log(data);
		 			 				} else {
		 								//console.log(data.data);
		 			 					success($.parseJSON(data.data));
		 			 				}
		 			 			},
		 			 			error: function(){
									alert("bad loader error");
		 			 				error.apply(this, arguments);
		 			 			}
		 			 		});
		 				}, // end of loader function for bad students datagrid
					}); // end of bad students datagrid

					// Data grid for good students

					$("#good_students_list").datagrid({
						url:  window.wp_data.ajax_url,
						method: 'POST',
						queryParams:{
							action: 'report_good',
							id_report: $('#user_reports').datagrid('getSelected').id_report
						},
						loadMsg: "Идет загрузка данных...",
 	 	   			emptyMsg: "Нет данных",
		 				pagination: true,
		 				singleSelect: true,
		 				columns:[[
		 						 {field:'id_report',title:'ID',width:30, hidden: true},
								 {field:'id_good',title:'ID',width:30, hidden: true},
								 {field:'id_student',title:'ID',width:30, hidden: true},
								 {field:'student_combo_name',title:'Фамилия, имя и отчество ученика', width: 300,sortable: true},
								 {field:'student_status',title:'Успеваемость', sortable: true, width: 300, formatter: function(val,row) {
									  	return (val == 0 ? "Хорошист" : "Отличник");
										}
							   }
		 				 ]],
		 				title: "Отличники и хорошисты",
		 				// toolbar: "#student_good_tb",
		 				 // loader for reports data grid
		 				loader: function(param, success, error){
		 			 		$.ajax({
								type: 'post',
			 					url: window.wp_data.ajax_url,
			 					data: param,
		 			 			dataType: 'json',
		 			 			success: function(data){
		 			 				if (data.isError){
		 								// console.log(data);
		 			 				} else {
										// console.log(data);
		 								// console.log(data.data);
		 			 					success($.parseJSON(data.data));
		 			 				}
		 			 			},
		 			 			error: function(){
									alert("good loader error");
		 			 				error.apply(this, arguments);
		 			 			}
		 			 		});
		 				}, // end of loader function for student absent datagrid
					}); // end of student absent datagrid

					// Data grid for good students

					$("#execution_list").datagrid({
						url:  window.wp_data.ajax_url,
						method: 'POST',
						queryParams:{
							action: 'report_execution',
							id_report: $('#user_reports').datagrid('getSelected').id_report
						},
						loadMsg: "Идет загрузка данных...",
 	 	   			emptyMsg: "Нет данных",
		 				pagination: true,
		 				singleSelect: true,
		 				columns:[[
		 						 {field:'id_report',title:'ID',width:30, hidden: true},
								 {field:'id_execution',title:'ID',width:30, hidden: true},
								 {field:'id_subject',title:'ID',width:30, hidden: true},
								 {field:'subject_name',title:'Предмет', width: 300,sortable: true},
								 {field:'classes_executed',title:'Проведено часов', width: 300, sortable: true}
		 				 ]],
		 				title: "Проведено часов",
		 				// toolbar: "#execution_tb",
		 				 // loader for reports data grid
		 				loader: function(param, success, error){
		 			 		$.ajax({
								type: 'post',
			 					url: window.wp_data.ajax_url,
			 					data: param,
		 			 			dataType: 'json',
		 			 			success: function(data){
		 			 				if (data.isError){
		 								// console.log(data);
		 			 				} else {
		 								//console.log(data.data);
		 			 					success($.parseJSON(data.data));
		 			 				}
		 			 			},
		 			 			error: function(){
									alert("execution loader error");
		 			 				error.apply(this, arguments);
		 			 			}
		 			 		});
		 				}, // end of loader function for student absent datagrid
					}); // end of student absent datagrid

					// TODO: other datagrids
					// ...
					// ...


				} // end of onSelect for report data grid
		 }); // data grid definition

		 /*
		 Search in user reports action
		 */
		 $('#user_report_search_btn').bind('click', function() {
			 $('#user_reports').datagrid('load',{
				   action: 'user_reports',
	         year_name: $('#year_name_search').val(),
	         class_name: $('#class_name_search').val()
	     });
	   });

		 /*
		 New report form show
		 */
		 $("#create_new_report_btn").bind('click', function() {
			  $('input[id=action_1]').val('new_report');
				$('input[id=id_report_1]').val('0');

			 	$("#create_new_report_panel").panel('expand');

				$("#rep_id_year").combobox('reload');
				$("#rep_id_year").combobox('clear');
				$("#rep_id_class").combobox('reload');
				$("#rep_id_class").combobox('clear');
				$("#rep_id_type").combobox('reload');
				$("#rep_id_type").combobox('clear');
				$("#rep_students_count").numberbox('setValue',0);

		 });

		 /*
		 Load report years
		 */
		 $("#rep_id_year").combobox({
			 url:  window.wp_data.ajax_url,
			 method: 'post',
			 queryParams: {
				 action: 'report_years'
			 },
			 required: true,
			 valueField: 'id_year',
			 textField: 'year_name',
			 loader: function(param, success, error){
				var opts = $(this).combobox('options');
				if (!opts.url) return false;
				$.ajax({
					type: opts.method,
					url: opts.url,
					data: param,
					dataType: 'json',
					success: function(data){
						if (data.isError){
							// console.log(data);
							// error($.parseJSON(data));
						} else {
							// console.log(data.data);
							success($.parseJSON(data.data));
						}
					},
					error: function(){
						error.apply(this, arguments);
					}
				});
			},
			onChange: function(newValue,oldValue) {
				if (newValue != oldValue) {
					$("input[id=tmp_id_year]").val(newValue);
					$("#rep_id_class").combobox('reload');
				}
			}
		}); // end of year combobox define

		/*
		Load report classes
		*/
		$("#rep_id_class").combobox({
			url:  window.wp_data.ajax_url,
			method: 'post',
			queryParams: {
				action: 'report_classes',
				id_year: $("input[id=tmp_id_year]").val()
			},
			required: true ,
			valueField: 'id_class',
			textField: 'class_name',
			loader: function(param, success, error){
			 var opts = $(this).combobox('options');
			 if (!opts.url) return false;
			 $.ajax({
				 type: opts.method,
				 url: opts.url,
				 data: {
	 				action: 'report_classes',
	 				id_year: $("input[id=tmp_id_year]").val()
	 			 },
				 dataType: 'json',
				 success: function(data){
					 if (data.isError){
						 // console.log(data);
						 // error($.parseJSON(data));
					 } else {
						 // console.log(data.data);
						 success($.parseJSON(data.data));
					 }
				 },
				 error: function(){
					 error.apply(this, arguments);
				 }
			 });
		 },
		 onChange: function(newValue, oldValue)
		 {
			 // if ($('#user_reports').datagrid('getSelected').students_count == 0){
				 $.ajax({
					 url:  window.wp_data.ajax_url,
					 method: 'POST',
					 data:{
						 action: 'students_count',
						 id_class: newValue,
					 },
					 dataType: 'json',
					 success: function(data){
						 var arr = $.parseJSON(data.data);
						 $("#rep_students_count").numberbox('setValue',arr["students_count"]);
					 }
				 }); // end of students count
			 // }
		 }
	 }); // end of classes combobox definition

	 /*
	 Load report types
	 */
	 $("#rep_id_type").combobox({
		 url:  window.wp_data.ajax_url,
		 method: 'post',
		 queryParams: {
			 action: 'report_types'
		 },
		 required: true,
		 valueField: 'id_report_type',
		 textField: 'type_name',
		 loader: function(param, success, error){
			var opts = $(this).combobox('options');
			if (!opts.url) return false;
			$.ajax({
				type: opts.method,
				url: opts.url,
				data: param,
				dataType: 'json',
				success: function(data){
					if (data.isError){
						// console.log(data);
						// error($.parseJSON(data));
					} else {
						// console.log(data.data);
						success($.parseJSON(data.data));
					}
				},
				error: function(){
					error.apply(this, arguments);
				}
			});
		 }
		}); // end of classes combobox definition

		/*
		New report submit
		*/
		$("#create_new_report_form").form({
  		url:  window.wp_data.ajax_url,
			method: 'post',
			onSubmit: function(){
				if (isNaN(parseInt($("input[id=rep_id_year]").val()))) {
					$.messager.alert("Ошибка ввода",'Выберите учебный год из списка!');
					return false;
				}
				if (isNaN(parseInt($("input[id=rep_id_class]").val()))) {
					$.messager.alert("Ошибка ввода",'Выберите класс из списка!');
					return false;
				}
				if (isNaN(parseInt($("input[id=rep_id_type]").val()))) {
					$.messager.alert("Ошибка ввода",'Выберите период отчета из списка!');
					return false;
				}
			},
			success:function(data){
				$("#create_new_report_panel").panel('collapse');
				/*
					TODO : handle id_report = -1 => the same report exists
				*/
				;
				var responce = $.parseJSON(data);
				responce = $.parseJSON(responce.data);
				if (responce["id_report"] == -1){
						$.messager.alert("Ошибка создания отчета","Отчет для этого класса за указанный период уже создан!");
				}
				else {
					$('#user_reports').datagrid('load',{
	 				   action: 'user_reports',
	 	         year_name: $('#year_name_search').val(),
	 	         class_name: $('#class_name_search').val()
	 	     });
			 }
			}
		}) // end of new report submit

		$("#cancel_edit_btn").bind('click', function() {
			$("#create_new_report_panel").panel('collapse');
		});

		/*
		Delete selected report
		*/
		$("#delete_report_btn").bind('click', function() {
			$.messager.confirm('Удаление','Вы уврены, что хотите удалить выделенный отчет?',function(r){
			    if (r){
			        $.ajax({
								type: "POST",
								url: window.wp_data.ajax_url,
								data: {
									action: "delete_report",
									id_report: $('#user_reports').datagrid('getSelected').id_report
								},
								success: function(){
									$("#absent_panel").panel('collapse');
									$("#absent_students_panel").panel('collapse');
									$("#bad_students_panel").panel('collapse');
									$("#good_students_panel").panel('collapse');
									$("#execution_panel").panel('collapse');
									$('#user_reports').datagrid('load',{
										action: "user_reports",
										year_name: $('#year_name_search').val(),
 			  	          class_name: $('#class_name_search').val()
									});
								}
							});
			    }
			});
		});

		/*
		Update report form show
		*/
		$("#update_report_btn").bind('click', function() {
			$('input[id=action_1]').val('update_report');
			$('input[id=id_report_1]').val($('#user_reports').datagrid('getSelected').id_report);

			$("#rep_id_year").combobox('reload');
			$("#rep_id_year").combobox('select', $('#user_reports').datagrid('getSelected').id_year);
			$("#rep_id_class").combobox('reload');
			$("#rep_id_class").combobox('select', $('#user_reports').datagrid('getSelected').id_class);
			$("#rep_id_type").combobox('reload');
			$("#rep_id_type").combobox('select', $('#user_reports').datagrid('getSelected').id_report_type);
			$("#rep_students_count").numberbox('setValue',$('#user_reports').datagrid('getSelected').students_count);

			$("#create_new_report_panel").panel('expand');
		});

		/*
		Submit absent form
		*/
		$("#absent_form").form({
  		url:  window.wp_data.ajax_url,
			method: 'post',
			onSubmit: function(){
				$('input[id=action_2]').val("edit_absent");
			},
			success:function(data){
				// get absent for current reports
				$.ajax({
					url:  window.wp_data.ajax_url,
					method: 'POST',
					data:{
						action: 'report_absent',
						id_report: $('#user_reports').datagrid('getSelected').id_report
					},
					dataType: 'json',
					success: function(data){
						// settle absent_panel fields with token values
						var arr = $.parseJSON(data.data);
						// console.log(data.data);
						if (arr.length > 0){
							$("#days_all").textbox('setValue', parseInt(arr[0].days_all));
							$("#days_ill").numberbox('setValue', arr[0].days_ill);
							$("#classes_all").numberbox('setValue', arr[0].classes_all);
							$("#classes_ills").numberbox('setValue', arr[0].classes_ills);
							//$("#without_reason").numberbox('setValue', arr[0].without_reason);
							$('input[name=id_absent]').val(arr[0].id_absent);
							$('input[id=id_report_2]').val(arr[0].id_report);
						}
						else{
							$("#days_all").numberbox('setValue', 0);
							$("#days_ill").numberbox('setValue', 0);
							$("#classes_all").numberbox('setValue', 0);
							$("#classes_ills").numberbox('setValue', 0);
							//$("#without_reason").numberbox('setValue', 0);
							$('input[name=id_absent]').val(0);
							$('input[id=id_report_2]').val($('#user_reports').datagrid('getSelected').id_report);
						}
						},
						error: function()
						{
							$("#days_all").numberbox('setValue', '0');
							$("#days_ill").numberbox('setValue', '0');
							$("#classes_all").numberbox('setValue', '0');
							$("#classes_ills").numberbox('setValue', '0');
							//$("#without_reason").numberbox('setValue', '0');
							$('input[name=id_absent]').val(0);
							$('input[id=id_report_2]').val(0);
						}
				}); // end of absent form fill
			} // end of submit success handler
		}); // end of new report submit

		/*
			Absent students buttons
		*/

		// ... on Add btn click
		$("#new_absent_btn").bind('click', function() {
			$('input[id=action_3]').val('new_absent');
			$('input[id=id_report_3]').val($('#user_reports').datagrid('getSelected').id_report);

			$("#rep_id_absent_student").combobox('reload');
			$("#rep_id_absent_student").combobox('clear');
			$("#hours_all").numberbox('setValue', 0);

			$("#new_absent_panel").panel('expand');
		});

		// ... on Edit btn click
		$("#update_absent_btn").bind('click', function() {
			$('input[id=action_3]').val('upd_absent');
			$('input[id=id_report_3]').val($('#user_reports').datagrid('getSelected').id_report);

			$('input[id=id_absent_student]').val($('#absent_students_list').datagrid('getSelected').id_absent);
			$("#rep_id_absent_student").combobox('reload');
			$("#rep_id_absent_student").combobox('select', $('#absent_students_list').datagrid('getSelected').id_student);
			$("#hours_all").numberbox('setValue', $('#absent_students_list').datagrid('getSelected').hours_all);

			$("#new_absent_panel").panel('expand');
		});

		// ... on Delete btn click
		$("#delete_absent_btn").bind('click', function() {
			$.messager.confirm('Удаление','Вы уврены, что хотите удалить информацию о прпусках выделенного ученика?',function(r){
			    if (r){
			        $.ajax({
								type: "POST",
								url: window.wp_data.ajax_url,
								data: {
									action: "del_absent",
									id_absent: $('#absent_students_list').datagrid('getSelected').id_absent
								},
								success: function(){
									$('#absent_students_list').datagrid('load',{
					 				   action: 'student_absent',
										 id_report: $('#user_reports').datagrid('getSelected').id_report
					 	     });
								}
							});
			    }
			});
		});

		// ... on Cansel btn click
		$("#cancel_absent_btn").bind('click', function() {
			$("#new_absent_panel").panel('collapse');
		});

		// ... on Edit form submit
		$("#new_absent_form").form({
  		url:  window.wp_data.ajax_url,
			method: 'post',
			onSubmit: function(){
				if (isNaN(parseInt($("input[id=rep_id_absent_student]").val()))) {
					$.messager.alert("Ошибка ввода",'Выберите студента из списка!');
					return false;
				}
			},
			success:function(data){
				$("#new_absent_panel").panel('collapse');
				$('#absent_students_list').datagrid('load',{
 				   action: 'student_absent',
					 id_report: $('#user_reports').datagrid('getSelected').id_report
 	     });
			}
		}) // end of new report submit

		// ... on Search btn click
		$('#student_absent_search_btn').bind('click', function() {
			// console.log($('#student_absent_search').val());
			$('#absent_students_list').datagrid('load',{
					action: 'student_absent',
					id_report: $('#user_reports').datagrid('getSelected').id_report,
					scn: $('#student_absent_search').val()
			});
		});


		/*
			Bad students buttons
		*/

		// ... on Add btn click
		$("#new_bad_btn").bind('click', function() {
			$('input[id=action_4]').val('new_bad_student');
			$('input[id=id_report_4]').val($('#user_reports').datagrid('getSelected').id_report);

			$("#rep_id_bad_student").combobox('reload');
			$("#rep_id_bad_student").combobox('clear');
			$("#rep_id_bad_subject").combobox('reload');
			$("#rep_id_bad_subject").combobox('clear');
			$("#rep_id_teacher").combobox('reload');
			$("#rep_id_teacher").combobox('clear');

			$("#new_bad_panel").panel('expand');
		});

		// ... on Edit btn click
		$("#update_bad_btn").bind('click', function() {
			$('input[id=action_4]').val('upd_bad_student');
			$('input[id=id_report_4]').val($('#user_reports').datagrid('getSelected').id_report);

			$('input[id=id_bad_student]').val($('#bad_students_list').datagrid('getSelected').id_bad);
			$("#rep_id_bad_student").combobox('reload');
			$("#rep_id_bad_student").combobox('select', $('#bad_students_list').datagrid('getSelected').id_student);
			$("#rep_id_bad_subject").combobox('reload');
			$("#rep_id_bad_subject").combobox('select', $('#bad_students_list').datagrid('getSelected').id_subject);
			$("#rep_id_teacher").combobox('reload');
			$("#rep_id_teacher").combobox('select', $('#bad_students_list').datagrid('getSelected').id_teacher);

			$("#new_bad_panel").panel('expand');
		});

		// ... on Delete btn click
		$("#delete_bad_btn").bind('click', function() {
			$.messager.confirm('Удаление','Вы уврены, что хотите удалить информацию о выделенном ученике?',function(r){
			    if (r){
			        $.ajax({
								type: "POST",
								url: window.wp_data.ajax_url,
								data: {
									action: "del_bad_student",
									id_bad: $('#bad_students_list').datagrid('getSelected').id_bad
								},
								success: function(){
									$('#bad_students_list').datagrid('load',{
					 				   action: 'report_bad',
										 id_report: $('#user_reports').datagrid('getSelected').id_report
					 	     });
								}
							});
			    }
			});
		});

		// ... on Cansel btn click
		$("#cancel_bad_btn").bind('click', function() {
			$("#new_bad_panel").panel('collapse');
		});

		// ... on Edit form submit
		$("#new_bad_form").form({
  		url:  window.wp_data.ajax_url,
			method: 'post',
			onSubmit: function(){
				if (isNaN(parseInt($("input[id=rep_id_bad_student]").val())))
				{
					$.messager.alert("Ошибка ввода",'Выберите студента из списка!');
					return false;
				}
				if (isNaN(parseInt($("input[id=rep_id_bad_subject]").val())))
				{
					$.messager.alert("Ошибка ввода",'Выберите предмет из списка!');
					return false;
				}
				if (isNaN(parseInt($("input[id=rep_id_teacher]").val())))
				{
					$.messager.alert("Ошибка ввода",'Выберите учителя из списка!');
					return false;
				}
			},
			success:function(data){
				$("#new_bad_panel").panel('collapse');
				$('#bad_students_list').datagrid('load',{
 				   action: 'report_bad',
					 id_report: $('#user_reports').datagrid('getSelected').id_report
 	     });
			}
		}) // end of new report submit

		// ... on Search btn click
		$('#bad_search_btn').bind('click', function() {
			$('#bad_students_list').datagrid('load',{
					action: 'report_bad',
					id_report: $('#user_reports').datagrid('getSelected').id_report,
					student_combo_name: $('#bad_student_search').val(),
					teacher_combo_name: $('#bad_teacher_search').val(),
					subject_name: $('#bad_subject_search').val()
			});
		});

		/*
			Good students buttons
		*/

		// ... on Add btn click
		$("#new_good_btn").bind('click', function() {
			$('input[id=action_5]').val('new_good_student');
			$('input[id=id_report_5]').val($('#user_reports').datagrid('getSelected').id_report);

			$("#rep_id_good_student").combobox('reload');
			$("#rep_id_good_student").combobox('clear');
			$("#student_status").combobox('select', -1);

			$("#new_good_panel").panel('expand');
		});

		// ... on Edit btn click
		$("#update_good_btn").bind('click', function() {
			$('input[id=action_5]').val('upd_good_student');
			$('input[id=id_report_5]').val($('#user_reports').datagrid('getSelected').id_report);

			$('input[id=id_good_student]').val($('#good_students_list').datagrid('getSelected').id_good);
			$("#rep_id_good_student").combobox('reload');
			$("#rep_id_good_student").combobox('select', $('#good_students_list').datagrid('getSelected').id_student);
			$("#student_status").combobox('select', $('#good_students_list').datagrid('getSelected').student_status);

			$("#new_good_panel").panel('expand');
		});

		// ... on Delete btn click
		$("#delete_good_btn").bind('click', function() {
			$.messager.confirm('Удаление','Вы уврены, что хотите удалить информацию о выделенном ученике?',function(r){
			    if (r){
			        $.ajax({
								type: "POST",
								url: window.wp_data.ajax_url,
								data: {
									action: "del_good_student",
									id_good: $('#good_students_list').datagrid('getSelected').id_good
								},
								success: function(){
									$('#good_students_list').datagrid('load',{
					 				   action: 'report_good',
										 id_report: $('#user_reports').datagrid('getSelected').id_report
					 	     });
								}
							});
			    }
			});
		});

		// ... on Cansel btn click
		$("#cancel_good_btn").bind('click', function() {
			$("#new_good_panel").panel('collapse');
		});

		// ... on Edit form submit
		$("#new_good_form").form({
  		url:  window.wp_data.ajax_url,
			method: 'post',
			onSubmit: function(){
				if (isNaN(parseInt($("input[id=rep_id_good_student]").val())))
				{
					$.messager.alert("Ошибка ввода",'Выберите студента из списка!');
					return false;
				}
				if (isNaN(parseInt($("input[name=student_status]").val())))
				{
					$.messager.alert("Ошибка ввода",'Выберите статус ученика из списка!');
					return false;
				} else {
					if ($("input[name=student_status]").val() == -1) {
						$.messager.alert("Ошибка ввода",'Укажите, является ученик отличником или хорошистом!');
						return false;
					}
				}
			},
			success:function(data){
				$("#new_good_panel").panel('collapse');
				$('#good_students_list').datagrid('load',{
 				   action: 'report_good',
					 id_report: $('#user_reports').datagrid('getSelected').id_report
 	     });
			}
		}) // end of new report submit

		// ... on Search btn click
		$('#good_search_btn').bind('click', function() {
			$('#good_students_list').datagrid('load',{
					action: 'report_good',
					id_report: $('#user_reports').datagrid('getSelected').id_report,
					student_combo_name: $('#good_student_search').val()
			});
		});

		/*
		Submit total good form
		*/
		$("#total_good_form").form({
  		url:  window.wp_data.ajax_url,
			method: 'post',
			onSubmit: function(){
				$('input[id=action_7]').val("edit_total_good");
			},
			success:function(data){
				// get total good for current reports
				$.ajax({
					url:  window.wp_data.ajax_url,
					method: 'POST',
					data:{
						action: 'report_total_good',
						id_report: $('#user_reports').datagrid('getSelected').id_report,
						good_total: $('#good_total').textbox('getValue')
					},
					dataType: 'json',
					success: function(data){
						// settle absent_panel fields with token values
						var arr = $.parseJSON(data.data);
						console.log(data.data);
						if (arr.length > 0){
							$("#good_total").textbox('setValue', parseInt(arr[0].good_total));
							$('input[id=id_good_total]').val(arr[0].id_good_total);
							$('input[id=id_report_7]').val(arr[0].id_report);
						}
						else{
							$("#good_total").numberbox('setValue', 0);
							$('input[id=id_good_total]').val(0);
							$('input[id=id_report_7]').val($('#user_reports').datagrid('getSelected').id_report);
						}
						},
						error: function()
						{
							$("#good_total").numberbox('setValue', '0');
							$('input[id=id_good_total]').val(0);
							$('input[id=id_report_7]').val(0);
						}
				}); // end of absent form fill
			} // end of submit success handler
		}); // end of new report submit



		/*
			Execution buttons
		*/

		// ... on Add btn click
		$("#new_execution_btn").bind('click', function() {
			$('input[id=action_6]').val('new_execution');
			$('input[id=id_report_6]').val($('#user_reports').datagrid('getSelected').id_report);

			$("#rep_id_subject").combobox('reload');
			$("#rep_id_subject").combobox('clear');
			$("#classes_executed").numberbox('setValue', 0);

			$("#new_execution_panel").panel('expand');
		});

		// ... on Edit btn click
		$("#update_execution_btn").bind('click', function() {
			$('input[id=action_6]').val('upd_execution');
			$('input[id=id_report_6]').val($('#user_reports').datagrid('getSelected').id_report);

			$('input[id=id_execution]').val($('#execution_list').datagrid('getSelected').id_execution);
			$("#rep_id_subject").combobox('reload');
			$("#rep_id_subject").combobox('select', $('#execution_list').datagrid('getSelected').id_subject);
			$("#classes_executed").numberbox('setValue', $('#execution_list').datagrid('getSelected').classes_executed);

			$("#new_execution_panel").panel('expand');
		});

		// ... on Delete btn click
		$("#delete_execution_btn").bind('click', function() {
			$.messager.confirm('Удаление','Вы уврены, что хотите удалить информацию о выделенном предмете?',function(r){
			    if (r){
			        $.ajax({
								type: "POST",
								url: window.wp_data.ajax_url,
								data: {
									action: "del_execution",
									id_execution: $('#execution_list').datagrid('getSelected').id_execution
								},
								success: function(){
									$('#execution_list').datagrid('load',{
					 				   action: 'report_execution',
										 id_report: $('#user_reports').datagrid('getSelected').id_report
					 	     });
								}
							});
			    }
			});
		});

		// ... on Cansel btn click
		$("#cancel_execution_btn").bind('click', function() {
			$("#new_execution_panel").panel('collapse');
		});

		// ... on Edit form submit
		$("#new_execution_form").form({
  		url:  window.wp_data.ajax_url,
			method: 'post',
			onSubmit: function(){
				if (isNaN(parseInt($("input[id=rep_id_subject]").val())))
				{
					$.messager.alert("Ошибка ввода",'Выберите предмет из списка!');
					return false;
				}
			},
			success:function(data){
				$("#new_execution_panel").panel('collapse');
				$('#execution_list').datagrid('load',{
 				   action: 'report_execution',
					 id_report: $('#user_reports').datagrid('getSelected').id_report
 	     });
			}
		}) // end of new report submit

		// ... on Search btn click
		$('#execution_search_btn').bind('click', function() {
			$('#execution_list').datagrid('load',{
					action: 'report_execution',
					id_report: $('#user_reports').datagrid('getSelected').id_report,
					subject_name: $('#execution_subject_search').val()
			});
		});

		/*
		linkbuttons to format reports
		*/

		$("#pdf_report").bind("click", function() {
			window.location.href = window.location.href + "?id_report="+
														 $('#user_reports').datagrid('getSelected').id_report;
		});

		/*
		reports ready check
		*/
		$("#reports-ready-btn").bind("click", function() {
			$.ajax({
				url:  window.wp_data.ajax_url,
				type: 'POST',
				data: {
					action: 'ready_reports',
					id_year: $("input[id=rep_id_year]").val(),
					id_type: $("input[id=rep_id_type]").val()
				},
				dataType: 'json',
				success: function(data){
					var reports = $.parseJSON(data.data);
					var div_content = "<h3>Готовые отчеты:</h3><h4>";
					for(var i=0;i<reports.length;i++)
					{
						var report = reports[i];
						if (report["id_report"] == 0) {
							div_content = div_content + "<span style='color:#999999;'>" + report["class_name"] +"</span>&nbsp;";
						}
						else {
							div_content = div_content + "<a href='?id_report="+ report["id_report"]
												+"&id_year="+ $("input[id=rep_id_year]").val()
												+"&id_report_type="+ $("input[id=rep_id_type]").val()
												+"'>" + report["class_name"] + "</a>&nbsp;";
						}
					}
					div_content = div_content + "</h4>";
					$("div.ready-reports").html(div_content);
				}
			});
		});


	}); // doc ready end

})( jQuery );
