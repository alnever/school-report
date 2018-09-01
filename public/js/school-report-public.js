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

		 jQuery(function($){
		     $.ajax({
		         type: "GET",
		         url: window.wp_data.ajax_url,
		         data: {
		             action : 'user_reports'
		         },
		         success: function (responce) {
		             console.log('AJAX response : ',responce.data);
								 $('#dg').datagrid({
									   data: $.parseJSON(responce.data),
										 loadMsg: "Идет загрузка данных...",
										 emptyMsg: "Нет данных",
								     columns:[[
								         {field:'id_report',title:'ID',width:30},
								         {field:'create_date',title:'Дата создания',width:100},
								         {field:'type_name',title:'Type',width:100,align:'left'},
								         {field:'class_name',title:'Class',width:100,align:'left'},
								         {field:'year_name',title:'Year',width:100,align:'left'},
								         {field:'report_status',title:'Status',width:30,align:'left'},
								     ]],
										 onLoadError: function(){
											 $.messager.alert('Ошибка загрузки',$('#dg').datagrid('options').url,'info');
										 },
										 onLoadSuccess: function(data){
											 $.messager.alert('Загрузка успешна',data,'info');
										 }
								 }); // data grid definition
		         } // success function
		     }); // ajax
		 }); // jQuery function
	 }); // doc ready end

})( jQuery );
