<?php
class classs{
  //Table class
  public $rowid = '';
  public $teacher_rowid = '';
  public $subject_rowid = '';
  public $name = '';
  public $create_date = '';

  //Table class_student
  public $rowid_student = ''; //class_student.rowid
  //public $class_rowid = ''; //same as rowid
  public $student_rowid = '';
  public $create_date_student = ''; //class.student.create_date


  //Common
  private $db;
  private $user;
  public $error_msg = '';
  function __construct() {
    if (!class_exists('db')) {
      if (file_exists('db.class.php')) {
        include 'db.class.php';
      }
      if (file_exists('server/db.class.php')) {
        include 'server/db.class.php';
      }
    }

    $this->db = new db();

    if (!class_exists('user')) {
      if (file_exists('user.class.php')) {
        include 'user.class.php';
      }
      if (file_exists('server/user.class.php')) {
        include 'server/user.class.php';
      }
    }

    $this->user = new user();
  }

  function access() {
    try {
      if (!$this->user->is_login()) { throw new Exception("[Warning] Please login first"); }
      if ($this->user->role != "teacher") { throw new Exception("[Warning] Your role is not teacher"); }
      $this->teacher_rowid = $this->user->rowid;
      return true;
    } catch (Exception $e) {
      $this->add_error_msg($e->getMessage());
      return false;
    }
  }

  function create($subject_rowid, $name) {
    try {
      if (!$this->access()) { throw new Exception("[Error] Access Denied"); }
      $this->subject_rowid = $subject_rowid;
      $this->name = $name;
      if ($this->name == "") { throw new Exception("[Error] name not assigned"); }
      if ($this->subject_rowid == "") { throw new Exception("[Error] subject_rowid not assigend"); }
      if (!is_numeric($this->subject_rowid)) { throw new Exception("[Error] Invalid subject_rowid"); }

      if (!$this->db->sql_command("INSERT INTO `class` (teacher_rowid, subject_rowid, `name`) VALUES ('$this->teacher_rowid', '$this->subject_rowid', '$this->name')")) {
        throw new Exception("[Error] failed to insert table class");
      }

      return true;
    } catch (Exception $e) {
      $this->add_error_msg($e->getMessage());
      return false;
    }
  }

  function add_student($student_rowid, $rowid = '') {
    try {
      if (!$this->access()) { throw new Exception("[Error] Access Denied"); }
      $this->rowid = $rowid != "" ? $rowid : $this->rowid;
      $this->student_rowid = $student_rowid;
      if ($this->rowid == "") { throw new Exception("[Error] rowid not assigned"); }
      if ($this->student_rowid == "") { throw new Exception("[Error] student_rowid not assigend"); }
      if (!is_numeric($this->rowid)) { throw new Exception("[Error] Invalid rowid"); }
      if (!is_numeric($this->student_rowid)) { throw new Exception("[Error] Invalid student_rowid"); }

      if ($this->is_class_student($this->rowid, $this->student_rowid)) { throw new Exception("[Error] student already join this class"); }
      
      if (!$this->db->sql_command("INSERT INTO class_student (class_rowid, student_rowid) VALUES ('$this->rowid','$this->student_rowid')")) {
        throw new Exception("[Error] failed to insert table class_student");
      }

      return true;
    } catch (Exception $e) {
      $this->add_error_msg($e->getMessage());
      return false;
    }
  }

  function remove_student($rowid) {
    try {
      if (!$this->access()) { throw new Exception("[Error] Access Denied"); }
      if ($rowid == "") { throw new Exception("[Error] rowid not assigned"); }
      if (!is_numeric($rowid)) { throw new Exception("[Error] Invalid rowid"); }
      
      if (!$this->db->sql_command("DELETE FROM class_student WHERE rowid='$rowid'")) {
        throw new Exception("[Error] failed to delete record table class_student");
      }

      return true;
    } catch (Exception $e) {
      $this->add_error_msg($e->getMessage());
      return false;
    }
  }

  function tc_list() {
    $ret_html = "<tr>"
      .  "<th>ID</th>"
      .  "<th>Name</th>"
      .  "<th>Subject</th>"
      .  "<th>Teacher</th>"
      .  "<th>Total Student</th>"
      .  "<th>Manage</th>"
      .  "<th>Homework</th>"
      .  "<th>Exam</th>"
      ."</tr>";
    try {
      foreach ($this->db->sql_select("SELECT A.rowid, A.name, CONCAT(B.userid, ' - ', B.fullname) AS teacher, C.name AS `subject`, (SELECT COUNT(*) FROM class_student WHERE class_rowid=A.rowid) AS student FROM class A LEFT JOIN user B ON A.teacher_rowid=B.rowid LEFT JOIN `subject` C ON A.subject_rowid=C.rowid") as $val) {
        $ret_html .= "<tr>"
          .  "<td>" . $val['rowid'] . "</td>"
          .  "<td>" . $val['name'] . "</td>"
          .  "<td>" . $val['subject'] . "</td>"
          .  "<td>" . $val['teacher'] . "</td>"
          .  "<td>" . $val['student'] . "</td>"
          .  "<td><button onclick=\"manage_class('" . $val['rowid'] . "')\">Manage</button></td>"
          .  "<td><button onclick=\"manage_homework('" . $val['rowid'] . "')\">Homework</button></td>"
          .  "<td><button onclick=\"manage_exam('" . $val['rowid'] . "')\">Exam</button></td>"
          ."</tr>";
      }
    } catch (Exception $e) {
      $this->add_error_msg($e->getMessage());
      $ret_html .= "<tr><td colspan='20'>No Class</td></tr>";
    }

    return $ret_html;
  }

  function tc_student_list($rowid) {
    $ret_html = "<tr>"
      .  "<th>User ID</th>"
      .  "<th>Fullname</th>"
      .  "<th>Gender</th>"
      .  "<th>IC</th>"
      .  "<th>Birthday</th>"
      .  "<th>Join Date</th>"
      .  "<th>Remove</th>"
      ."</tr>";
    try {
      foreach ($this->db->sql_select("SELECT A.rowid, B.userid, B.fullname, B.gender, B.ic, DATE_FORMAT(B.birthday,'%d %b %Y') AS birthday, CONCAT(DATE_FORMAT(A.create_date,'%d %b %y'), ' - ',TIME_FORMAT(A.create_date, '%h:%i %p')) AS create_date FROM class_student A LEFT JOIN user B ON A.student_rowid=B.rowid WHERE A.class_rowid='$rowid'") as $val) {
        $ret_html .= "<tr>"
          .  "<td>" . $val['userid'] . "</td>"
          .  "<td>" . $val['fullname'] . "</td>"
          .  "<td>" . ucwords($val['gender']) . "</td>"
          .  "<td>" . $val['ic'] . "</td>"
          .  "<td>" . $val['birthday'] . "</td>"
          .  "<td>" . $val['create_date'] . "</td>"
          .  "<td><button class='btn_remove' onclick=\"remove_class_student('" . $val['rowid'] ."')\">Remove</button></td>"
          ."</tr>";
      }
    } catch (Exception $e) {
      $this->add_error_msg($e->getMessage());
      $ret_html .= "<tr><td colspan='20'>No Class</td></tr>";
    }

    return $ret_html;
  }

  function name_is_taken($name = '') {
    try {
      $this->name = $name != "" ? $name : $this->name;
      if ($this->name == "") { throw new Exception("[Error] name not assigend"); }

      $data = $this->db->sql_select("SELECT rowid FROM class WHERE name='$this->name'");
      if (!$this->db->sql_select_result) { throw new Exception($this->db->sql_select_reason); }
      if (count($data) > 0) {
        return true;
      } else {
        return false;
      }
    } catch (Exception $e) {
      $this->add_error_msg($e->getMessage());
      return false;
    }
  }

  function is_class_student($rowid, $student_rowid) {
    try {
      if ($rowid == "") { throw new Exception("[Error] rowid not assigned"); }
      if ($student_rowid == "") { throw new Exception("[Error] student_rowid not assigend"); }
      
      $data = $this->db->sql_select("SELECT rowid FROM class_student WHERE class_rowid='$rowid' AND student_rowid='$student_rowid'");
      if (!$this->db->sql_select_result) { throw new Exception($this->db->sql_select_reason); }
      if (count($data) > 0) {
        return true;
      } else {
        return false;
      }
    } catch (Exception $e) {
      $this->add_error_msg($e->getMessage());
      return false;
    }
  }

  function add_error_msg($msg) {
    $br = $this->error_msg == "" ? "<br>" : "";
    $this->error_msg .= $br . $msg;
    return true;
  }
}