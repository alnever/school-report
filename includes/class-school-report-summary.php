<?php
/**
 * Class provides queries for aggregative reports
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    school-report
 * @subpackage school-report/includes
 */

/**
 * Class provides queries for aggregative reports
 *
 * This Class provides queries for aggregative reports
 *
 * @since      1.0.0
 * @package    school-report
 * @subpackage school-report/includes
 * @author     Alex Neveroc <al_neverov@live.ru>
 */

 if (! class_exists('School_Report_Db'))
  require_once(dirname(__FILE__) . "/class-school-report-db.php");

class School_Report_Summary {

  protected $connection;

  public function __construct()
  {
    $this->connection = School_Report_Db::get_instance();
  }

  /*
    Ready reports
  */
  public function get_ready_reports($id_year, $id_report_type)
  {
    $sql = "select c.id_class, c.class_name,
                   ifnull(r.id_report, 0) as id_report
            from school_report_classes c
                 left join school_report_reports r on c.id_class = r.id_class
                           and c.id_year = $id_year
                           and r.id_year = $id_year
                           and id_report_type = $id_report_type
            order by c.class_name
    ";

    return $this->connection->get_results($sql, 'ARRAY_A');
  }

  public function get_report_ids($id_year, $id_report_type)
  {
    $tab = (new School_Report_Db_Table)->get_table("reports");
    $reports = $tab->get_list(array("id_year" => $id_year, "id_report_type" => $id_report_type));
    // get all report IDs
    $report_ids = "";
    $i = 0;
    foreach ($reports as $report) {
      if ($i == 0)
      {
        $report_ids = sprintf("%d", $report["id_report"]);
      }
      else {
        $report_ids = sprintf("%s,%d", $report_ids, $report["id_report"]);
      }
      $i++;
    }

    return $report_ids;
  }

  public function get_executed_subjects($id_year, $id_report_type, $id_subgrade)
  {
    $report_ids = $this->get_report_ids($id_year, $id_report_type);
    $sql = "select distinct s.id_subject, s.subject_name
              from school_report_reports r
              join school_report_classes_execution e on r.id_report = e.id_report
              join school_report_classes c on r.id_class = c.id_class
              join school_report_subjects s on e.id_subject = s.id_subject
              where r.id_report in ($report_ids)
              and c.id_subgrade = $id_subgrade
              order by s.subject_name
    ";
    return $this->connection->get_results($sql, "ARRAY_A");
  }


  public function get_execution_by_classes($id_year, $id_report_type, $id_class, $id_subject)
  {

    $report_ids = $this->get_report_ids($id_year, $id_report_type);

    $sql = "select sum(e.classes_executed)
                from school_report_classes_execution e
                join school_report_reports r on r.id_report = e.id_report
                where r.id_report in ($report_ids)
                and r.id_class = $id_class
                and e.id_subject = $id_subject
    ";

    return $this->connection->get_var($sql);

  }

  public function get_execution_by_grade($id_year, $id_report_type, $id_subgrade, $id_subject)
  {

    $report_ids = $this->get_report_ids($id_year, $id_report_type);

    $sql = "select avg(e.classes_executed)
                from school_report_reports r
                join school_report_classes_execution e on r.id_report = e.id_report
                join school_report_classes c on r.id_class = c.id_class
                join school_report_subjects s on e.id_subject = s.id_subject
                where r.id_report in ($report_ids)
                and c.id_subgrade = $id_subgrade
                and e.id_subject = $id_subject
    ";

    return $this->connection->get_var($sql);

  }

  public function get_lacks_by_grade($id_year, $id_report_type, $id_grade)
  {

    $report_ids = $this->get_report_ids($id_year, $id_report_type);

    $sql = "select c.class_name, sum(a.days_all) as days_all,
                sum(a.days_ill) as days_ill, sum(a.classes_all) as classes_all,
                sum(a.classes_ills) as classes_ills
                from school_report_reports r
                join school_report_absent a on r.id_report = a.id_report
                join school_report_classes c on r.id_class = c.id_class
                where r.id_report in ($report_ids)
                and c.id_grade = $id_grade
                group by c.class_name
                order by c.class_name
    ";

    return $this->connection->get_results($sql, "ARRAY_A");
  }

  public function get_negatives_by_student($id_year, $id_report_type)
  {

    $report_ids = $this->get_report_ids($id_year, $id_report_type);

    $sql = "select distinct c.class_name,
                   s.id_student,
                   concat(s.student_family,' ', s.student_name,' ', ifnull(s.student_surname,'')) as student_combo_name
                from school_report_reports r
                join school_report_bad_students b on r.id_report = b.id_report
                join school_report_classes c on r.id_class = c.id_class
                join school_report_students s on b.id_student = s.id_student
                where r.id_report in ($report_ids)
                order by c.class_name, student_combo_name
    ";

    $subsql = "select s.subject_name
                  from school_report_bad_students b
                  join school_report_subjects s on b.id_subject = s.id_subject
                  where b.id_report in ($report_ids)
                    and b.id_student = %d
                  order by s.subject_name
    ";

    $students = $this->connection->get_results($sql, "ARRAY_A");
    $result = array();

    foreach($students as $student){
      $subjects_list = '';
      $i = 0;
      $esql = sprintf($subsql,$student["id_student"]);
      $subjects = $this->connection->get_results($esql, "ARRAY_A");
      foreach ($subjects as $subject) {
        if ($i == 0)
          $subjects_list = $subject["subject_name"];
        else {
          $subjects_list = $subjects_list .",". $subject["subject_name"];
        }
        $i++;
      }
      array_push($result,array("class_name" => $student["class_name"],
                       "student_combo_name" => $student["student_combo_name"],
                       "subjects" => $subjects_list)
      );
    }

    return $result;
  }

  public function get_negative_by_subject($id_year, $id_report_type)
  {
    $report_ids = $this->get_report_ids($id_year, $id_report_type);

    $sql = "select s.id_subject, s.subject_name, count(b.id_bad) as count_neg
              from school_report_bad_students b
              join school_report_subjects s on b.id_subject = s.id_subject
              where b.id_report in ($report_ids)
              group by s.id_subject, s.subject_name
              order by s.subject_name
    ";

    return $this->connection->get_results($sql, "ARRAY_A");
  }

  public function get_negative_by_subject_by_student($id_year, $id_report_type, $id_subject)
  {
    $report_ids = $this->get_report_ids($id_year, $id_report_type);

    $sql = "select distinct c.class_name,
                   concat(s.student_family,' ', s.student_name,' ', ifnull(s.student_surname,'')) as student_combo_name,
                   concat(t.teacher_family,' ', t.teacher_name,' ', ifnull(t.teacher_surname,'')) as teacher_combo_name
             from school_report_bad_students b
             join school_report_students s on b.id_student = s.id_student
             join school_report_teachers t on b.id_teacher = t.id_teacher
             join school_report_classes c on s.id_class = c.id_class
             where b.id_report in ($report_ids)
             and b.id_subject = $id_subject
    ";

    return $this->connection->get_results($sql, "ARRAY_A");
  }

  public function get_quality($id_year, $id_report_type, $id_grade)
  {
    $report_ids = $this->get_report_ids($id_year, $id_report_type);

    $sql = "select c.class_name, count(distinct g.id_student)/r.students_count as quality,
                   (r.students_count  - count(distinct b.id_student))/r.students_count  as achievement,
                   sum(r.students_count)/count(r.id_report) as students_count
            from school_report_reports r
            join school_report_classes c on r.id_class = c.id_class
            left join school_report_good_students g on r.id_report = g.id_report
            left join school_report_bad_students b on r.id_report = b.id_report
            where r.id_report in ($report_ids)
            and c.id_grade = $id_grade
            group by c.class_name
            order by c.id_class
    ";

    /*
    join (select id_class, count(id_student) as students from school_report_students
            where deleted = 0
            group by id_class
         ) x on x.id_class = c.id_class
    */

    return $this->connection->get_results($sql, "ARRAY_A");
  }

  public function get_positives($id_year, $id_report_type)
  {
    $report_ids = $this->get_report_ids($id_year, $id_report_type);

    $sql = "select distinct c.id_class, c.class_name,
                   concat(s.student_family,' ', s.student_name,' ', ifnull(s.student_surname,'')) as student_combo_name
                from school_report_reports r
                join school_report_good_students g on r.id_report = g.id_report
                join school_report_classes c on r.id_class = c.id_class
                join school_report_students s on g.id_student = s.id_student
                where r.id_report in ($report_ids)
                and g.student_status = 1
                order by c.id_class, c.class_name, student_combo_name
                ";
    return $this->connection->get_results($sql, "ARRAY_A");
  }

  public function get_good_by_grade($id_year, $id_report_type)
  {
    $report_ids = $this->get_report_ids($id_year, $id_report_type);

    $sql = "select g.grade_name, count(distinct a.id_student) as good_students
                from school_report_reports r
                join school_report_good_students a on r.id_report = a.id_report
                join school_report_classes c on r.id_class = c.id_class
                join school_report_grades g on c.id_grade = g.id_grade
                where r.id_report in ($report_ids)
                group by g.grade_name
                order by g.grade_name
    ";

    return $this->connection->get_results($sql, "ARRAY_A");
  }

  public function get_outstanding_by_grade($id_year, $id_report_type)
  {
    $report_ids = $this->get_report_ids($id_year, $id_report_type);

    $sql = "select g.grade_name, count(distinct a.id_student) as good_students
                from school_report_reports r
                join school_report_good_students a on r.id_report = a.id_report
                join school_report_classes c on r.id_class = c.id_class
                join school_report_grades g on c.id_grade = g.id_grade
                where r.id_report in ($report_ids)
                and a.student_status = 1
                group by g.grade_name
                order by g.grade_name
    ";

    return $this->connection->get_results($sql, "ARRAY_A");
  }

}

?>
