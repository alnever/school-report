<?php
/**
 * The shortcode provider: [report-editor]
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    school-report
 * @subpackage school-report/public
 */
 if (! class_exists('School_Report_Summary'))
  require_once(dirname(dirname(__FILE__)) . "/includes/class-school-report-summary.php");

require_once(dirname(__FILE__)."/csr-ajax-report-ready.php");

final class School_Report_Creator
{
  private $user;

  public function report_creator(){
    $this->start_screen();
  }

  function is_user_in_role($role)
  {
    return in_array($role, (array) $this->user->roles);
  }

  function start_screen()
  {
    $this->user = new WP_User(get_current_user_id());
    if ($this->is_user_in_role("administrator") ||
        $this->is_user_in_role("teacher") ||
        $this->is_user_in_role("head_teacher")
      ) {
        include_once(dirname(__FILE__)."/partials/school-report-creator-1.php");
      }
    else {
      echo "<p>Извините, Вам запрещено просматривать эту страницу!</p>";
      # echo "<p><a href='wp-login.php'>Войти в систему</a></p>";
    }
  }


  public function gen_pdf_report($id_report)
  {
    // $pdf1 = new Zend_Pdf();
  }


}




?>
