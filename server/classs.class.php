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

      if (!$this->db->sql_command("INSERT INTO class (teahcer_rowid, subject_rowid, `name`) VALUES ('$this->teacher_rowid', '$this->subject_rowid', '$this->name')")) {
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

      if ($this->is_class_student($this->rowid, $this->student_rowid)) { throw new Exception("[Error] student already join this class"); }
      
      if (!$this->db->sql_command("INSERT INTO class_student (class_rowid, student_rowid,) VALUES ('$this->rowid','$this->student_rowid')")) {
        throw new Exception("[Error] failed to insert table class_student");
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
      ."</tr>";
    try {
      foreach ($this->db->sql_select("SELECT A.rowid, A.name, C.name, CONCAT(B.userid, ' - ', B.fullname) AS teacher FROM class A LEFT JOIN user B ON A.teacher=B.rowid LEFT JOIN `subject` C ON A.subject_rowid=C.rowid") as $val) {
        $ret_html .= "<tr>"
          .  "<td>" . $val['rowid'] . "</td>"
          .  "<td>" . $val['name'] . "</td>"
          .  "<td>" . $val['subject'] . "</td>"
          .  "<td>" . $val['teacher'] . "</td>"
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