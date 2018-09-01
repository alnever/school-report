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

require_once(dirname(__FILE__)."/csr-ajax-user-reports.php");
require_once(dirname(__FILE__)."/csr-ajax-report-classes.php");
require_once(dirname(__FILE__)."/csr-ajax-report-types.php");
require_once(dirname(__FILE__)."/csr-ajax-report-years.php");
require_once(dirname(__FILE__)."/csr-ajax-new-report.php");
require_once(dirname(__FILE__)."/csr-ajax-delete-report.php");
require_once(dirname(__FILE__)."/csr-ajax-update-report.php");
require_once(dirname(__FILE__)."/csr-ajax-report-absent.php");
require_once(dirname(__FILE__)."/csr-ajax-edit-absent.php");
require_once(dirname(__FILE__)."/csr-ajax-student-absent.php");
require_once(dirname(__FILE__)."/csr-ajax-absent-new.php");
require_once(dirname(__FILE__)."/csr-ajax-absent-del.php");
require_once(dirname(__FILE__)."/csr-ajax-absent-upd.php");
require_once(dirname(__FILE__)."/csr-ajax-report-students.php");
require_once(dirname(__FILE__)."/csr-ajax-report-teachers.php");
require_once(dirname(__FILE__)."/csr-ajax-report-subjects.php");
require_once(dirname(__FILE__)."/csr-ajax-report-bad.php");
require_once(dirname(__FILE__)."/csr-ajax-report-good.php");
require_once(dirname(__FILE__)."/csr-ajax-report-execution.php");
require_once(dirname(__FILE__)."/csr-ajax-bad-new.php");
require_once(dirname(__FILE__)."/csr-ajax-bad-del.php");
require_once(dirname(__FILE__)."/csr-ajax-bad-upd.php");
require_once(dirname(__FILE__)."/csr-ajax-good-new.php");
require_once(dirname(__FILE__)."/csr-ajax-good-del.php");
require_once(dirname(__FILE__)."/csr-ajax-good-upd.php");
require_once(dirname(__FILE__)."/csr-ajax-execution-new.php");
require_once(dirname(__FILE__)."/csr-ajax-execution-del.php");
require_once(dirname(__FILE__)."/csr-ajax-execution-upd.php");
require_once(dirname(__FILE__)."/csr-ajax-students-count.php");
require_once(dirname(__FILE__)."/csr-ajax-edit-total-good.php");
require_once(dirname(__FILE__)."/csr-ajax-report-total-good.php");

final class School_Report_Editor
{
  private $user;

  public function report_editor(){
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
        include_once(dirname(__FILE__)."/partials/school-report-editor-1.php");
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
