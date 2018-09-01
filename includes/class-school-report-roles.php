<?php
/*
 * Plugin Name: School report builder
 * Version: 0.1.0
 * Author: Alex Neverov
 * Author URI: http://alneverov.ru
 */

/*
* Class for creation and dropping specific roles for plugin
*/
final class School_Report_Roles{

  private static $instance;

  private function __construct()
  {

  }

  public static function get_instance()
  {
    if (!(self::$instance instanceof self)) {
                self::$instance = new self;
            }
            return self::$instance;
  }

  // This method creates additional roles for plugin
  public function create_roles()
  {
    // Add a teacher's role. Teacher can do the same operation, as an Author
    // In addition the teacher can create school reports
    // This role is used just within this plugin
    add_role('teacher', 'Учитель', array(
      'delete_posts' => true, // True allows that capability
      'delete_published_posts' => true,
      'edit_posts' => true,
      'edit_published_posts ' => true, // Use false to explicitly deny
      'publish_posts' => true,
      'read' => true,
      'upload_files' => true
    ));
    // Add a head teacher's role. Teacher can do the same operation, as an Author
    // In addition the head teacher can create school reports and form the aggregative reports
    // This role is used just within this plugin
    add_role('head_teacher', 'Завуч', array(
      'delete_posts' => true, // True allows that capability
      'delete_published_posts' => true,
      'edit_posts' => true,
      'edit_published_posts ' => true, // Use false to explicitly deny
      'publish_posts' => true,
      'read' => true,
      'upload_files' => true
    ));
  }

  // This method removes additional roles when the plugin is deactivated or removed
  public function drop_roles()
  {
    remove_role('teacher');
    remove_role('head_teacher');
  }
}

?>
