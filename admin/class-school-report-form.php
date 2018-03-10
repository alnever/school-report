<?php
/**
 * The edit form's class
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    school-report
 * @subpackage school-report/admin
 */

/**
 * The  edit form's class
 *
 * Defines a form for adding and editing records
 *
 * @package    school-report
 * @subpackage school-report/admin
 * @author     Alex Neveroc <al_neverov@live.ru>
 */

if (! class_exists('School_Report_Db'))
  require_once(dirname(dirname(__FILE__)) . "/includes/class-school-report-db.php");

if (! class_exists('School_Report_Db_Table'))
 require_once(dirname(dirname(__FILE__)) . "/includes/class-school-report-db-table.php");


class School_Report_Form
{
  private $db_table_info;
  private $db_table;
  private $table_id;
  private $id_field;

  private $item;
  private $insert_id;
  private $notice;
  private $message;

  public function __construct($table_id)
  {
    $this->set_db_options($table_id);
  }

  private function set_db_options($table_id)
  {
    $this->table_id = $table_id;
    $tabs = School_Report_Db::get_instance()->get_tables();
    $this->db_table_info = $tabs[$table_id];
    $tab = new School_Report_Db_Table;
    $this->db_table = $tab->get_table($table_id);
    $this->id_field = $this->db_table->get_id_field();
  }

  public function get_table_name()
  {
    return $this->db_table_info["singular"];
  }

  public function get_id_field()
  {
    return $this->id_field;
  }

  public function get_table_key()
  {
    return $this->table_id;
  }

  private function set_defaults()
  {
    $defaults = array();

    foreach ($this->db_table->get_fields() as $key => $value) {
      $defaults[$key] = $value["default"];
    }

    return $defaults;
  }

  public function get($id)
  {
    return $this->db_table->get($id);
  }

 /*
  * Insert a new grade into the table
 */
  public function insert()
  {
    $result =  $this->db_table->insert($this->item);
    $this->insert_id = $this->db_table->get_insert_id();
    return $result;
  }

	/*
	* Update information about the grade
	*/
  public function update()
  {
    return $this->db_table->update($this->item);
  }

  /*
	* Delete the grade
	*/
  public function delete()
  {
    return $this->db_table->delete($this->item[$this->id_field]);
  }


  public function form_handler()
  {
    $this->message = '';
    $this->notice = '';

    // this is default $item which will be used for new records
    $default = $this->set_defaults();

    // here we are verifying does this request is post back and have correct nonce
    // if (wp_verify_nonce($_REQUEST['nonce'], basename(__FILE__))) {
    if(isset($_REQUEST['nonce']))
    {
      // combine our default item with request params
      $this->item = shortcode_atts($default, $_REQUEST);
      // validate data, and if all ok save item to database
      // if id is zero insert otherwise update
      $item_valid = $this->validate($this->item);
      if ($item_valid === true) {
          if ($this->item[$this->id_field] == 0) {

              $result = $this->insert();

              $this->item[$this->id_field] = $this->insert_id;
              if ($result) {
                  $this->message = __('Запись успешно добавлена', 'school-report-form');
              } else {
                  $this->notice = __('Во время добавления записи возникли ошибки!', 'school-report-form');
              }
          } else {
              $result = $this->update();

              if ($result) {
                  $this->message = __('Запись успешно изменена', 'school-report-form');
              } else {
                  $this->notice = __('Во время изменения записи возникли ошибки!', 'school-report-form');
              }
          }
      } else {
          // if $item_valid not true it contains error message(s)
          $this->notice = $item_valid;
      }
  }
  else {
      // if this is not post back we load item to edit or give new one to create
      $this->item = $this->set_defaults();
      if (isset($_REQUEST[$this->id_field])) {
          $this->item = $this->get($_REQUEST[$this->id_field]);
          if (!$this->item) {
              $this->item = $this->set_defaults();
              $this->notice = __('Запись не найдена', 'school-report-form');
          }
      }
    }
  }

  private function validate($item)
  {
      $messages = array();

      foreach ($this->db_table->get_fields() as $key => $value) {
        if ($value["display"] && $value["required"])
        {
          if (empty($item[$key]))
          {
            $messages[] = __("Поле необходимо заполнить ".$key." ".$value["caption"], 'school-report-form');
          }
        }
      }

      if (empty($messages)) return true;
      return implode('<br />', $messages);
  }

  public function get_item()
  {
    return $this->item;
  }

  public function get_item_id()
  {
    return $this->item[$this->id_field];
  }

  public function show_input_fields()
  {
    foreach ($this->db_table->get_fields() as $key => $field) {
      $field_type = preg_replace("/\(\d+\)$/","",$field["type"]);
      switch ($field_type) {
        case 'char':
        case 'varchar': {
          $this->show_input_string($field);
          break;
        }
        case 'text': {
          $this->show_input_text($field);
          break;
        }
        case 'int': {
          $this->show_input_int($field);
          break;
        }
        case 'date': {
          $this->show_input_date($field);
          break;
        }
        default:{
          echo "";
        }
      }
    }
  }

  public function show_input_text($field)
  {
    $f_name = $field["name"];
    $f_value = $this->item[$field["name"]];
    echo '<tr class="form-field">';
    echo '<th valign="top" scope="row">';
    echo sprintf('<label for="%s">', $field["name"]);
      _e($field["caption"], 'micro-tags-'.$field["name"]);
    echo "</label>
          </th>
          <td>
          <textarea name='$f_name' style='width:100%;height:300px;padding:20px'>
          $f_value
          </textarea>
        </td>
    </tr>";
  }

  public function show_input_string($field)
  {
    echo '<tr class="form-field">';
    echo '<th valign="top" scope="row">';
    echo sprintf('<label for="%s">', $field["name"]);
      _e($field["caption"], 'school-report-'.$field["name"]);
    echo '</label>
          </th>
          <td>';
    echo sprintf('<input id="%s" name="%s" type="text" value="%s"
                   size="50" class="code" placeholder="%s" %s />',
                   $field["name"],
                   $field["name"],
                   $this->item[$field["name"]],
                   $field["caption"],
                   ($field["required"] ? "required" : "")
                 );
    echo "
        </td>
    </tr>";
  }

  public function show_input_int_field($field)
  {
    echo sprintf('<input id="%s" name="%s" type="text" value="%d" class="esayui-numberbox" style="width:400px
                   size="50" class="code" placeholder="%s" %s />',
                   $field["name"],
                   $field["name"],
                   $this->item[$field["name"]],
                   $field["caption"],
                   ($field["required"] === true ? "required" : "")
                 );
  }

  public function show_input_int_combo($field)
  {
    $fun = $field["select_function"];
    // print_r($fun);
    //if (is_string($this->db_table->$fun)) {
    $list_source = $this->db_table->$fun();

    echo sprintf('<select id="%s" name="%s" class="easyui-combobox" placeholder="%s" %s style="width:400px">',
                   $field["name"],
                   $field["name"],
                   $field["caption"],
                   ($field["required"] === true ? "required" : "")
                 );
                 echo "<option value='0'>(не выбрано)</option>";
     if ($field["name"] == "id_teacher") {
       $list_data = $list_source->get_ext_list();
     }
     else {
       $list_data = $list_source->get_list(array(),$list_source->get_name_field(),"",1000);
     }

     foreach($list_data as $list_field) {
        echo sprintf('<option value="%d" %s>%s</option>',
                     $list_field[$list_source->get_id_field()],
                     ($list_field[$list_source->get_id_field()] == $this->item[$field["name"]] ? "selected" : ""),
                     $list_field[$list_source->get_name_field()]
             );
     }


    echo '</select>';
  /*}
  else {
    print_r($field);
    print_r($this->db_table->$field);
    print_r($this->db_table->$field["select_function"]);
  }*/
  }


  public function show_input_int($field)
  {
    if ($field["display"] === true)
    {
      echo '<tr class="form-field">';
      echo '<th valign="top" scope="row">';
      echo sprintf('<label for="%s">', $field["name"]);
        _e($field["caption"], 'school-report-'.$field["name"]);
      echo '</label>
            </th>
            <td>';
      if ($field["select_function"] === null) {
        $this->show_input_int_field($field);
      }
      else {
        $this->show_input_int_combo($field);
      }


      echo "
          </td>
      </tr>";
    }
    else{
      // hidden field
      echo sprintf(
        '<input type="hidden" name="%s" value="%d" />',
        $field["name"],
        $this->item[$field["name"]]
      );
    }
  }

  public function show_input_date($field)
  {
    echo '<tr class="form-field">';
    echo '<th valign="top" scope="row">';
    echo sprintf('<label for="%s">', $field["name"]);
      _e($field["caption"], 'school-report-'.$field["name"]);
    echo '</label>
          </th>
          <td>';
    echo sprintf('<input id="%s" name="%s" type="text" value="%s"
                   size="50" class="code" placeholder="%s" %s />',
                   $field["name"],
                   $field["name"],
                   $this->item[$field["name"]],
                   $field["caption"],
                   ($field["required"] ? "required" : "")
                 );
    echo "
        </td>
    </tr>";
  }

  public function get_notice()
  {
    return $this->notice;
  }

  public function get_message()
  {
    return $this->message;
  }

}

?>
