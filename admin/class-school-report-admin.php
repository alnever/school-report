<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    school-report
 * @subpackage school-report/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    school-report
 * @subpackage school-report/admin
 * @author     Alex Neveroc <al_neverov@live.ru>
 */

 if (! class_exists('School_Report_Db'))
  require_once(dirname(dirname(__FILE__)) . "/includes/class-school-report-db.php");

if (! class_exists('School_Report_Db_Table'))
 require_once(dirname(dirname(__FILE__)) . "/includes/class-school-report-db-table.php");

 if (! class_exists('School_Report_List_Table'))
  require_once(dirname(__FILE__) . "/class-school-report-list.php");

if (! class_exists('School_Report_Form'))
 require_once(dirname(__FILE__) . "/class-school-report-form.php");

class School_Report_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

  private $list_view;
  private $form_view;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

		add_action('admin_menu', array($this, 'admin_menu'));

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in School_Report_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The School_Report_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
    wp_enqueue_style( 'easyui-default', plugin_dir_url( __FILE__ ) . '../js/easyui/themes/default/easyui.css', array(), $this->version, 'all' );
    wp_enqueue_style( 'easyui-icons', plugin_dir_url( __FILE__ ) . '../js/easyui/themes/icon.css', array('easyui-default'), $this->version, 'all' );
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/school-report-admin.css', array('easyui-default', 'easyui-icons'), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in School_Report_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The School_Report_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

    wp_enqueue_script( 'easyui', plugin_dir_url( __FILE__ ) . '../js/easyui/jquery.easyui.min.js', array( 'jquery' ), $this->version, false );
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/school-report-admin.js', array( 'jquery' , 'easyui'), $this->version, false );

	}

	public function admin_menu()
	{
		$page_title = $this->plugin_name;
		$menu_title = $this->plugin_name;
		$capability = 'manage_options';
		$menu_slug  = $this->plugin_name;
		$function   = array($this,'admin_page');
		$icon_url   = 'dashicons-media-code';
		$position   = 4;

		// Add main menu link
		add_menu_page($page_title,
									$menu_title,
									$capability,
									$menu_slug,
									'',
									$icon_url,
									$position);

		// For each element of the menu add a link as a submenu element
		$parent_slug = $menu_slug;

		$tables = School_Report_Db::get_instance()->get_tables();
		foreach($tables as $key => $value)
		{
			if ( $value["admin_menu"] == 1) {
					$page_title = $value["caption"];
					$menu_title = $value["caption"];
					$capability = 'manage_options';
					$menu_slug  = $key;
					$function   = array($this, $key."_admin");
					$form_function   = array($this, $key."_add_new");

					$hook = add_submenu_page( $parent_slug, $page_title, $menu_title, $capability, $menu_slug, array($this, 'menu_handler'));
					$hook_form = add_submenu_page(null,  // instead of $parent_slug to hide item
										"Add ".$page_title,
										"Add ".$menu_title, $capability, $menu_slug."-form",
										array($this, 'add_form')
									);


					add_action( "load-$hook", [ $this, 'screen_options' ] );
					add_action( "load-$hook_form", [ $this, 'screen_options_form'] );
				}
		}
	} // end of menu function

  public function screen_options()
  {
    $option = 'per_page';
    $args   = [
      'default' => 10,
      'option'  => 'per_page'
    ];

    add_screen_option( $option, $args );
  }

  public function screen_options_form() {
    $args   = [];
  }

	public function menu_handler()
	{
		$this->list_view = new School_Report_List_Table($_REQUEST["page"]);
    include(dirname(__FILE__)."/partials/school-report-list.php");
	}

  public function add_form()
  {
    $table_id = str_replace("-form", "", $_REQUEST["page"]);
    $this->form_view = new School_Report_Form($table_id);
    include(dirname(__FILE__)."/partials/school-report-form.php");
  }





}
