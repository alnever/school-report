<?php

/**
 * Base class for school-report db table
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    school-report
 * @subpackage school-report/includes
 */

/**
 * Base class for school-report db table
 *
 * This class defines all code necessary to handle the db table
 *
 * @since      1.0.0
 * @package    school-report
 * @subpackage school-report/includes
 * @author     Alex Neveroc <al_neverov@live.ru>
 */

 if (! class_exists('School_Report_Db'))
  require_once(dirname(__FILE__) . "/class-school-report-db.php");

 class School_Report_Db_Table{

   protected $connection;

   protected $insert_id;

   // This properties are individual for each table class
   protected $table_name;
   protected $fields = array();
   protected $default_values;
   protected $id_field;
   protected $name_field;
   protected $delete = false;
   protected $delete_before = array();

   /*
    * Get a db instance during the class creation
    */

   public function __construct()
   {
     $this->connection = School_Report_Db::get_instance();
     $this->load_requirements();
   }

   /*
    * Get a singleton instance of the class
    */

   private function load_requirements()
   {
     $table_info = $this->connection->get_tables();
     foreach($table_info as $key => $table)
     {
       if (! class_exists($table["class_name"]))
          require_once(dirname(__FILE__) . $table["file_name"]);
     }
   }

   public function get_table($table_id)
   {
     $table_info = $this->connection->get_tables();
     $class_name = $table_info[$table_id]["class_name"];
     if(class_exists($class_name)) return new $class_name();
   }

   /*
   * Create a table
   */

   public function create_table(){
     $sql = "create table ".$this->table_name."( ";

     foreach ($this->fields as $key => $field) {
       $sql .= ($field["name"]." ".$field["type"]." ".$field["constraint"].",");
     }

     $sql .= ("primary key(" . $this->fields[$this->id_field]["name"] . ")) ");

     $sql .= ($this->connection->get_charset_collate() . ";");

     $this->connection->query($sql);
   }

   public function drop_table()
   {
     $this->connection->sql("drop table ".$this->table_name.";");
   }

   public function insert($value)
   {
     $values = array();
     $format = array();

     // create values list
     foreach ($this->fields as $key => $field) {
       if (! empty($value[$field["name"]]) && $key !== $this->id_field)
       {
         $values[$field["name"]] = $value[$field["name"]];
         array_push($format, $field["format"]);
       }
     }

     $result = $this->connection->insert($this->table_name, $values, $format);
     $this->insert_id = $this->connection->insert_id;

     return $result;
   }

   public function insert_defaults()
   {
     if (isset($this->default_values) )
     {
       foreach ($this->default_values as $value) {
         if (count ($this->get_list($value)) == 0) {
           $this->insert($value);
         }
       }
     }
   }

   public function update($value)
   {
     $values = array();
     $format = array();

     // create values list
     foreach ($this->fields as $key => $field) {
       if (isset($value[$field["name"]]))
       {
         $values[$field["name"]] = $value[$field["name"]];
         array_push($format, $field["format"]);
       }
     }

     $result = $this->connection->update($this->table_name,
                                   $values,
                                   array($this->fields[$this->id_field]["name"] => $value[$this->fields[$this->id_field]["name"]]),
                                   $format,
                                   array($this->fields[$this->id_field]["format"])
                                 );
     return $result;
   }

   public function delete_where($cond)
   {
     return $this->connection->delete(
       $this->table_name,
       $cond
     );
   }

   public function delete($value)
   {
     if ($this->delete)
     {
       if (! empty($this->delete_before) )
       {
         foreach($this->delete_before as $table_key)
         {
           $tab = $this->get_table($table_key);
           $tab->delete_where(array($this->id_field => $value));
         }
       }

       return $this->connection->delete(
         $this->table_name,
         array($this->fields[$this->id_field]["name"] => $value),
         array($this->fields[$this->id_field]["format"])
       );
     }
     else {
       return $this->update(array($this->id_field => $value, "deleted" => 1));
     }
   }

   public function get($value)
   {
     $sql = "select * from " . $this->table_name . " where " .
                $this->fields[$this->id_field]["name"] .
                " = $value";
     return $this->connection->get_row($sql, 'ARRAY_A');
   }

   private function get_id_list($list_function, $value)
   {
     $tab = $this->$list_function();
     $elems = $tab->get_list(array($tab->name_field => $value));
     $ids = "";
     $i = 0;
     foreach($elems as $elem)
     {
       if ($i == 0) {
         $ids = sprintf("%d", $elem[$tab->id_field]);
       } else {
         $ids = sprintf("%s,%d", $ids, $elem[$tab->id_field]);
       }
     }
     return $ids;
   }

   public function get_list($where = array(), $orderby = "", $order = "", $per_page = 10, $page_number = 1){

     $sql = "select * from ". $this->table_name ." where 1=1 ";

     if (!$this->delete)
     {
       $sql .= " and deleted = 0 ";
     }

     if ( ! empty ($where) )
     {
       foreach($where as $field => $value)
       {
          if (strcmp($this->fields[$field]["type"], "int") == 0) {
            $k = preg_match("/[^0-9]/",$value, $matches);
            if ($this->fields[$field]["select_function"] === null || $k == 0) {
              $sql .= ' and '. esc_sql( $field ) . ' = ' .esc_sql( $value ) . ' ';
            }
            else {
              $ids = $this->get_id_list($this->fields[$field]["select_function"], $value);
              if ($ids !== "") {
                $sql .= ' and '. esc_sql( $field ) . ' in (' . $ids  . ') ';
              }
            }
          }
          else
            $sql .= ' and '. esc_sql( $field ) . " like '%".esc_sql( $value ) . "%' ";
       }
     }

     if ( ! empty( $orderby ) && $orderby != '' )
     {
       $sql .= ' ORDER BY ' . esc_sql( $orderby );
       $sql .= ! empty( $order ) && $order != '' ? ' ' . esc_sql( $order ) : ' ASC';
     }

     $sql .= " LIMIT $per_page";

     $sql .= ' OFFSET ' . ( $page_number - 1 ) * $per_page;

     $result = $this->connection->get_results( $sql, 'ARRAY_A' );

     return $result;
   }

   public function get_all_list($where = array(), $orderby = "", $order = "", $per_page = 10, $page_number = 1){

     $sql = "select * from ". $this->table_name ." where 1=1 ";

     if ( ! empty ($where) )
     {
       foreach($where as $field => $value)
       {
          if (strcmp($this->fields[$field]["type"], "int") == 0) {
            $k = preg_match("/[^0-9]/",$value, $matches);
            if ($this->fields[$field]["select_function"] === null || $k == 0) {
              $sql .= ' and '. esc_sql( $field ) . ' = ' .esc_sql( $value ) . ' ';
            }
            else {
              $ids = $this->get_id_list($this->fields[$field]["select_function"], $value);
              if ($ids !== "") {
                $sql .= ' and '. esc_sql( $field ) . ' in (' . $ids  . ') ';
              }
            }
          }
          else
            $sql .= ' and '. esc_sql( $field ) . " like '%".esc_sql( $value ) . "%' ";
       }
     }

     if ( ! empty( $orderby ) && $orderby != '' )
     {
       $sql .= ' ORDER BY ' . esc_sql( $orderby );
       $sql .= ! empty( $order ) && $order != '' ? ' ' . esc_sql( $order ) : ' ASC';
     }

     $sql .= " LIMIT $per_page";

     $sql .= ' OFFSET ' . ( $page_number - 1 ) * $per_page;

     $result = $this->connection->get_results( $sql, 'ARRAY_A' );

     return $result;
   }

   public function record_count($where = array())
   {
     $sql = "SELECT COUNT(*) FROM ". $this->table_name;

     if (!$this->delete)
      $sql .= " where deleted = 0 ";

      if ( ! empty ($where) )
      {
        foreach($where as $field => $value)
        {
           if (strcmp($this->fields[$field]["type"], "int") == 0) {
             $k = preg_match("/[^0-9]/",$value, $matches);
             if ($this->fields[$field]["select_function"] === null || $k == 0) {
               $sql .= ' and '. esc_sql( $field ) . ' = ' .esc_sql( $value ) . ' ';
             }
             else {
               $ids = $this->get_id_list($this->fields[$field]["select_function"], $value);
               if ($ids !== "") {
                 $sql .= ' and '. esc_sql( $field ) . ' in (' . $ids  . ') ';
               }
             }
           }
           else
             $sql .= ' and '. esc_sql( $field ) . " like '%".esc_sql( $value ) . "%' ";
        }
      }

     return $this->connection->get_var( $sql );
   }

   public function get_fields()
   {
     return $this->fields;
   }

   public function get_id_field()
   {
     return $this->id_field;
   }

   public function get_name_field()
   {
     return $this->name_field;
   }

   public function get_insert_id()
   {
     return $this->insert_id;
   }

 }


 ?>
