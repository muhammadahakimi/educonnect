<?php

class exam {
  //Table exam
  public $rowid = ''; 
  public $class_rowid = '';
  public $student_rowid = '';
  public $mark = '';
  public $create_date = '';
  public $create_user = '';

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

  function set_details($rowid = '') {
    try {
      $this->rowid = $rowid == "" ? $rowid : $this->rowid;
      if ($this->rowid == "") { throw new Exception("[Error] rowid not assigned"); }

      $data = $this->db->sql_select("SELECT * FROM exam WHERE rowid='$this->rowid'");

      if (count($data) == 0) { throw new Exception("[Error] rowid: $this->rowid not found in table exam"); }
      $data = $data[0];
      $this->name = $data['name'];
      $this->date = $data['date'];
      $this->subject_rowid = $data['subject_rowid'];
      $this->create_date = $data['create_date'];
      $this->create_user = $data['create_user'];

      return false;
    } catch (Exception $e) {
      $this->add_error_msg($e->getMessage());
      return false;
    }
  }

  function set_score($class_rowid, $student_rowid, $mark) {
    try {
      if (!$this->user->is_login()) { throw new Exception("[Warning] Please login first"); }
      if ($this->user->role != 'teacher') { throw new Exception("[Warning] just teacher can update exam score"); }
      $this->create_user = $this->user->rowid;
      
      if ($this->exists($class_rowid, $student_rowid)) {
        if (!$this->db->sql_command("UPDATE exam SET mark='$mark', create_user='$this->create_user', create_date=NOW() WHERE rowid='$this->rowid'")) {
          throw new Exception("[Error] failed to update table exam");
        }
      } else {
        if (!$this->db->sql_command("INSERT INTO exam (class_rowid, student_rowid, mark, create_user) VALUES ('$class_rowid', '$student_rowid', '$mark', '$this->create_user')")) {
          throw new Exception("[Error] failed to insert table exam");
        }
      }      

      return true;
    } catch (Exception $e) {
      $this->add_error_msg($e->getMessage());
      return false;
    }
  }

  function exists($class_rowid, $student_rowid) {
    try {
      if ($class_rowid == "") { throw new Exception("[Error] rowid not assigned"); }
      if ($student_rowid == "") { throw new Exception("[Error] studen_rowid not assigend"); }
      
      $data = $this->db->sql_select("SELECT rowid FROM exam WHERE class_rowid='$class_rowid' AND student_rowid='$student_rowid'");
      if (count($data) > 0) {
        $this->rowid = $data[0]['rowid'];
        return true;
      } else {
        return false;
      }
    } catch (Exception $e) {
      $this->add_error_msg($e->getMessage());
      return false;
    }
  }

  function form_score($class_rowid ) {
    $ret_html = "";
    try {
      $this->class_rowid = $class_rowid;
      if ($this->class_rowid == "") { throw new Exception("[Error] rowid not assigned"); }

      foreach ($this->db->sql_select("SELECT A.student_rowid, B.userid, B.fullname, C.mark FROM class_student A LEFT JOIN user B ON A.student_rowid=B.rowid LEFT JOIN exam C ON C.class_rowid='$this->class_rowid' AND C.student_rowid=A.student_rowid WHERE A.class_rowid='$this->class_rowid' ORDER BY B.userid;") as $val) {
        $ret_html .= "<tr>"
          .  "<td>User ID: " . $val['userid'] . "</td>"
          .  "<td>Fullname: " . $val['fullname'] . "</td>"
          .  "<td><input type='hidden' name='student_rowid' value='" . $val['student_rowid'] . "'>Mark: <input type='number' name='mark' value='" . $val['mark'] . "'</td>"
          ."</tr>";
      }
    } catch (Exception $e) {
      $this->add_error_msg($e->getMessage());
      $ret_html .= "<tr><td colspan='20'>No Candidate</td></tr>";
    }

    return $ret_html;
  }

  function tc_list() {
    $ret_html = "<tr>"
      .  "<th>Class ID</th>"
      .  "<th>Class</th>"
      .  "<th>Subject</th>"
      .  "<th>Mark</th>"
      .  "<th>Create Date</th>"
      .  "<th>Create By</th>"
      ."</tr>";
    try {
      if (!$this->user->is_login()) { throw new Exception("[Warning] Please login first"); }
      $user = $this->user->rowid;
      foreach ($this->db->sql_select("SELECT C.rowid AS rowid, C.name AS `class`, D.name AS `name`, A.mark, CONCAT(DATE_FORMAT(A.create_date,'%d %b %y'), ' - ',TIME_FORMAT(A.create_date, '%h:%i %p')) AS create_date, B.userid AS create_user FROM exam A LEFT JOIN user B ON A.create_user=B.rowid LEFT JOIN `class` C ON A.class_rowid=C.rowid LEFT JOIN subject D ON C.subject_rowid-D.rowid WHERE A.student_rowid='$user'") as $val) {
        $ret_html .= "<tr>"
          .  "<td>" . $val['rowid'] . "</td>"
          .  "<td>" . $val['class'] . "</td>"
          .  "<td>" . $val['name'] . "</td>"
          .  "<td>" . $val['mark'] . "</td>"
          .  "<td>" . $val['create_date'] . "</td>"
          .  "<td>" . $val['create_user'] . "</td>"
          ."</tr>";
      }
    } catch (Exception $e) {
      $this->add_error_msg($e->getMessage());
      $ret_html .= "<tr><td colspan='20'>No Candidate</td></tr>";
    }

    return $ret_html;
  }

  function tc_candidate($rowid = '') {
    $ret_html = "<tr>"
      .  "<th>No</th>"
      .  "<th>User ID</th>"
      .  "<th>Fullname</th>"
      .  "<th>Remove</th>"
      ."</tr>";
    try {
      $this->rowid = $rowid;
      if ($this->rowid == "") { throw new Exception("[Error] rowid not assigned"); }
      $no = 1;
      foreach ($this->db->sql_select("SELECT A.rowid, B.userid, B.fullname, A.mark FROM exam_score A LEFT JOIN user B ON A.student_rowid=B.rowid WHERE A.exam_rowid='$this->rowid' ORDER BY B.userid") as $val) {
        $ret_html .= "<tr>"
          .  "<td>" . $no . "</td>"
          .  "<td>" . $val['userid'] . "</td>"
          .  "<td>" . $val['fullname'] . "</td>"
          .  "<td><button onclick=\"remove_candidate('" . $val['rowid'] . "')\">Remove</button></td>"
          ."</tr>";
        $no++;
      }
    } catch (Exception $e) {
      $this->add_error_msg($e->getMessage());
      $ret_html .= "<tr><td colspan='20'>No Candidate</td></tr>";
    }
    return $ret_html;
  }

  function tc_ranking($rowid = '') {
    $ret_html = "<tr>"
      .  "<th>No</th>"
      .  "<th>User ID</th>"
      .  "<th>Fullname</th>"
      .  "<th>Mark</th>"
      ."</tr>";
    try {
      $this->rowid = $rowid;
      if ($this->rowid == "") { throw new Exception("[Error] rowid not assigned"); }
      $no = 1;
      foreach ($this->db->sql_select("SELECT B.userid, B.fullname, A.mark FROM exam_score A LEFT JOIN user B ON A.student_rowid=B.rowid WHERE A.exam_rowid='$this->rowid' ORDER BY A.mark DESC") as $val) {
        $ret_html .= "<tr>"
          .  "<td>" . $no . "</td>"
          .  "<td>" . $val['userid'] . "</td>"
          .  "<td>" . $val['fullname'] . "</td>"
          .  "<td>" . $val['mark'] . "</td>"
          ."</tr>";
        $no++;
      }
    } catch (Exception $e) {
      $this->add_error_msg($e->getMessage());
      $ret_html .= "<tr><td colspan='20'>No Candidate</td></tr>";
    }
    return $ret_html;
  }

  function add_error_msg($msg) {
    $br = $this->error_msg == "" ? "<br>" : "";
    $this->error_msg .= $br . $msg;
    return true;
  }
}