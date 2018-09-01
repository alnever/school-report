<?php

/**
 * Fired during plugin activation
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    school-report
 * @subpackage school-report/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    school-report
 * @subpackage school-report/includes
 * @author     Alex Neveroc <al_neverov@live.ru>
 */

 if (! class_exists('School_Report_Db_Creator'))
  require_once(dirname(__FILE__) . "/class-school-report-db-creator.php");
if (! class_exists('School_Report_Roles'))
   require_once(dirname(__FILE__) . "/class-school-report-roles.php");

class School_Report_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
			School_Report_Db_Creator::get_instance()->create_db();
      School_Report_Roles::get_instance()->create_roles();
	}

}
