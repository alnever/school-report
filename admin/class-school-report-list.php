<?php
ini_set("display_errors",1);
/**
 * The list class
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    school-report
 * @subpackage school-report/admin
 */

/**
 * The list class
 *
 * Defines a list view for admin pages
 *
 * @package    school-report
 * @subpackage school-report/admin
 * @author     Alex Neveroc <al_neverov@live.ru>
 */

 if ( ! class_exists( 'WP_List_Table' ) )
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );

 if (! class_exists('School_Report_Db'))
  require_once(dirname(dirname(__FILE__)) . "/includes/class-school-report-db.php");

if (! class_exists('School_Report_Db_Table'))
 require_once(dirname(dirname(__FILE__)) . "/includes/class-school-report-db-table.php");

 class School_Report_List_Table extends WP_List_Table
 {
   protected $db_table_info;
   protected $db_table;
   protected $table_id;

   private $message;

   private $search_field = "";
   private $search_value = "";

   public function __construct($table_id)
   {
     $this->set_db_options($table_id);

     parent::__construct(
       array(
         'singular' => __($this->db_table_info["singular"],'sp'),
         'plural' => __($this->db_table_info["caption"],'sp'),
         'ajax' => false
       )
     );
   }

   private function set_db_options($table_id)
   {
     $this->table_id = $table_id;
     $this->db_table_info = School_Report_Db::get_instance()->get_tables[$table_id];
     $tab = new School_Report_Db_Table;
     $this->db_table = $tab->get_table($table_id);
   }

   public function get_table_key()
   {
     return $this->table_id;
   }

   public function get_list($where = array(), $per_page = 10, $page_number = 1)
   {

     $orderby = (!empty($_REQUEST["orderby"])?$_REQUEST["orderby"]:'');
     $order = (!empty($_REQUEST["order"])?$_REQUEST["order"]:'');
     return $this->db_table->get_list($where, $orderby, $order, $per_page, $page_number);
   }

   public function delete_record($id_item)
   {
     $this->db_table->delete($id_item);
   }

   public function record_count($where = array())
   {
     return $this->db_table->record_count($where);
   }

   public function no_items()
   {
     _e("Записи отсутствуют!",'sp');
   }

   protected function get_default_primary_column_name() {
 		return $this->db_table->get_name_field();
 	 }

   protected function handle_row_actions( $post, $column_name, $primary ) {
 		if ( $primary !== $column_name ) return ''; // только для одной ячейки

    $delete_nonce = wp_create_nonce('sp_delete_record');
    $edit_nonce = wp_create_nonce('sp_edit_record');

 		$actions = array();

    $actions = array(
      'edit' => sprintf('<a href = "?page=%s&action=%s&%s=%s&_wpnonce=%s">Изменить</a>',
                        $this->table_id . "-form",
                        "edit",
                        $this->db_table->get_id_field(),
                        absint($post[$this->db_table->get_id_field()]),
                        $edit_nonce
      ),
      'delete' => sprintf('<a href = "?page=%s&action=%s&%s=%s&_wpnonce=%s">Удалить</a>',
                        $this->table_id,
                        "delete",
                        $this->db_table->get_id_field(),
                        absint($post[$this->db_table->get_id_field()]),
                        $delete_nonce
      )
    );

 		return $this->row_actions( $actions );
 	}

   public function column_default($item, $column_name)
   {
       $fields = $this->db_table->get_fields();
     $column_type = $fields[$column_name]["type"];
     switch($column_name)
     {
       case 'date': {
         return date_create($item[ $column_name ])->format('d.m.Y');
       }
       case 'text': {
         return substr(strip_tags($item[ $column_name ]), 0, 100);;
       }
       default:{
          if ($fields[$column_name]["show_function"] === null) {
            return $item[$column_name];
          }
          else {
            $show_function = $fields[$column_name]["show_function"];
            return $this->db_table->$show_function($item[$column_name]);
          }
       }
     }
   }

   function column_cb($item)
   {
     return  sprintf(
       '<input type="checkbox" name="bulk-delete[]" value="%s" />', $item[$this->db_table->get_id_field()]
     );
   }

   public function get_columns()
   {

     $columns = array();
     $columns["cb"] = '<input type="checkbox" />';
     foreach ($this->db_table->get_fields() as $key => $field) {
       if ($field["display"])
       {
         $columns[$key] = __($field["caption"],'sp');
       }
     }

     return $columns;
   }

   public function get_sortable_columns()
   {
     $sortable_columns = array();

     foreach ($this->db_table->get_fields() as $key => $field) {
       if ($field["display"] && $field["sortable"]) {
         $sortable_columns[$key] = array($key, $field["sortable"]);
       }
     }

     return $sortable_columns;
   }

   public function get_bulk_actions(){
     $actions = array(
       'bulk-delete' => 'Удалить'
     );
     return $actions;
   }

   public function extra_tablenav($which) {
     global $wpdb, $testiURL, $tablename, $tablet;
     // $move_on_url = '&cat-filter=';
     if ($which == "top") {
       echo '<div class="alignleft actions bulkactions">';
       echo 'Поиск:';
       echo "<form action='#'>";
       echo "<input type='hidden' name='page' value='". $this->table_id ."' />";
       echo "<select id='search_field' name='search_field'>";
       foreach ($this->db_table->get_fields() as $key => $field) {
         if ($field["display"]) {
           $selected = ($this->search_field == $key ? "selected" : "");
           echo sprintf("<option value='%s' %s>%s</option>",$key,$selected,$field["caption"]);
         }
       }
       echo "</select>";
       echo sprintf("<input id='search_value' type='text' name='search_value' value='%s' />",$this->search_value);
       echo "<input type='submit' class='button action' value='Найти' />";
       echo '</form>';
       echo '</div>';

       echo '<div class="alignleft actions bulkactions">';
       echo "<form action='#'>";
       echo "<input type='hidden' name='page' value='". $this->table_id ."' />";
       echo "<input type='submit' class='button action' value='Сброс' />";
       echo '</form>';
       echo '</div>';

     }
   }

   public function prepare_items()
   {
     $this->_column_headers = $this->get_column_info();
     $this->_column_headers[0] = $this->get_columns();

     $this->search_field = "";
     $this->search_value = "";
     $where = array();

     if (isset($_REQUEST))
     {
       foreach ($_REQUEST as $key => $value) {
         if ($key == "search_field") {
           $this->search_field = $value;
         }
         if ($key == "search_value") {
           $this->search_value = $value;
         }
       }
     }

     if ($this->search_field == "" || $this->search_value == "") {
       $where = array();
     }
     else {
       $where = array($this->search_field => $this->search_value);
     }

     /** Process bulk action */
     $this->process_bulk_action();

     $per_page     = $this->get_items_per_page('per_page', 10 );
     $current_page = $this->get_pagenum();
     $total_items  = $this->record_count($where);

     $this->set_pagination_args( array(
       'total_items' => $total_items, //WE have to calculate the total number of items
       'per_page'    => $per_page //WE have to determine how many items to show on a page
     ) );

     $this->items = $this->get_list($where, $per_page, $current_page );
   }

   public function process_bulk_action() {

     //Detect when a bulk action is being triggered...
     if ( 'delete' === $this->current_action() ) {

       // In our file that handles the request, verify the nonce.
       $nonce = esc_attr( $_REQUEST['_wpnonce'] );

       if ( ! wp_verify_nonce( $nonce, 'sp_delete_record' ) ) {
         die( 'Go get a life script kiddies' );
       }
       else {
         $deleted = $this->delete_record( absint( $_GET[$this->db_table->get_id_field()] ) );
         $this->message = "Удалено $deleted записей";
       }

     }

     // If the delete bulk action is triggered
     if ( ( isset( $_POST['action'] ) && $_POST['action'] == 'bulk-delete' )
          || ( isset( $_POST['action2'] ) && $_POST['action2'] == 'bulk-delete' )
     ) {

       $delete_ids = esc_sql( $_POST['bulk-delete'] );

       // loop over the array of record IDs and delete them
       $deleted = 0;
       foreach ( $delete_ids as $id ) {
         $deleted += $this->delete_record( $id );
       }
       $this->message = "Удалено $deleted записей";
     }
   }

   public function get_message()
   {
     return $this->message;
   }

 }

 ?>
