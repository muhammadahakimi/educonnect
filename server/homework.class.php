<?php
class homework {
  //Table homework
  public $rowid = '';
  public $class_rowid = '';
  public $name = '';
  public $duedate = '';
  public $file = '';
  public $create_date = '';
  public $create_user = '';

  //Common
  public $dir = '';
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

  function set_dir() {
    try {
      if (is_dir("../resources/files")) { $this->dir = "../resources/files/"; }
      if (is_dir("resources/files")) { $this->dir = "resources/files/"; }
      return true;
    } catch (Exception $e) {
      $this->add_error_msg($e->getMessage());
      return false;
    }
  }

  function create($class_rowid, $name, $duedate, $file) {
    try {
      if (!$this->user->is_login()) { throw new Exception("[Warning] Please login first"); }
      $this->create_user = $this->user->rowid;
      $this->name = $name;
      $this->duedate = $duedate;
      $this->file = $file;
      $this->class_rowid = $class_rowid;
      if ($this->name == "") { throw new Exception("[Error] name not assigned"); }
      if ($this->duedate == "") { throw new Exception("[Error] duedate not assigned"); }
      if ($this->class_rowid == "") { throw new Exception("[Error] class_rowid not assigned"); }
      if (!is_numeric($this->class_rowid)) { throw new Exception("[Error] Invalid subject_rowid"); }

      if (!$this->set_dir()) { throw new Exception("[Error] failed to set dir"); }
      if ($file['error'] !== 0 ) { throw new Exception("[Error] this file have error"); }
      date_default_timezone_set('Asia/Kuala_Lumpur');
      $this->file = sha1(date('Y-m-d H:i:s')) . "." . pathinfo($file['name'], PATHINFO_EXTENSION);
      if (!move_uploaded_file($file['tmp_name'], $this->dir.$this->file)) { throw new Exception("[Error] failed to move file: " . $this->dir.$this->file); }

      if (!$this->db->sql_command("INSERT INTO homework (class_rowid,`name`,duedate, `file`, create_user) VALUES ('$this->class_rowid','$this->name','$this->duedate', '$this->file', '$this->create_user')")) {
        throw new Exception("[Error] failed to insert table homework");
      }
      $this->rowid = $this->db->lastInsertId;
      foreach ($this->db->sql_select("SELECT student_rowid AS student FROM class_student WHERE class_rowid='$this->rowid'") as $val) {
        $student = $val['student'];
        if (!$this->db->sql_command("INSERT INTO class_homework (student_rowid,`homework_rowid`) VALUES ('$student','$this->rowid')")) {
          throw new Exception("[Error] failed to insert table class_homework");
        }
      }

      return true;
    } catch (Exception $e) {
      $this->add_error_msg($e->getMessage());
      return false;
    }
  }

  function submit($rowid, $file) {
    try {
      if (!$this->user->is_login()) { throw new Exception("[Warning] Please login first"); }
      $this->create_user = $this->user->rowid;
      if ($rowid == "") { throw new Exception("[Error] rowid not assigned"); }
      if (!is_numeric($rowid)) { throw new Exception("[Error] Invalid rowid"); }

      if (!$this->set_dir()) { throw new Exception("[Error] failed to set dir"); }
      if ($file['error'] !== 0 ) { throw new Exception("[Error] this file have error"); }
      date_default_timezone_set('Asia/Kuala_Lumpur');
      $this->file = sha1(date('Y-m-d H:i:s')) . "." . pathinfo($file['name'], PATHINFO_EXTENSION);
      if (!move_uploaded_file($file['tmp_name'], $this->dir.$this->file)) { throw new Exception("[Error] failed to move file: " . $this->dir.$this->file); }

      if (!$this->db->sql_command("UPDATE class_homework SET file='$this->file', submit_date=NOW(), `status`='submited' WHERE rowid='$rowid'")) {
        throw new Exception("[Error] failed to update table class_homework");
      }

      return true;
    } catch (Exception $e) {
      $this->add_error_msg($e->getMessage());
      return false;
    }
  }

  function tc_html($class_rowid) {
    $ret_html = "<tr>"
      .  "<th>ID</th>"
      .  "<th>Name</th>"
      .  "<th>Duedate</th>"
      .  "<th>Create Date</th>"
      .  "<th>Create By</th>"
      .  "<th>Submit List</th>"
      .  "<th>File</th>"
      ."</tr>";
    try {
      foreach ($this->db->sql_select("SELECT A.rowid, A.name, A.file, DATE_FORMAT(A.duedate, '%d %b %Y') AS `duedate`, CONCAT(DATE_FORMAT(A.create_date,'%d %b %y'), ' - ',TIME_FORMAT(A.create_date, '%h:%i %p')) AS create_date, B.userid AS create_user FROM homework A LEFT JOIN user B ON A.create_user=B.rowid WHERE class_rowid='$class_rowid'") as $val) {
        $ret_html .= "<tr>"
          .  "<td>" . $val['rowid'] . "</td>"
          .  "<td>" . $val['name'] . "</td>"
          .  "<td>" . $val['duedate'] . "</td>"
          .  "<td>" . $val['create_date'] . "</td>"
          .  "<td>" . $val['create_user'] . "</td>"
          .  "<td><button onclick=\"view_submited_homework('" . $val['rowid'] . "')\">View</button></td>"
          .  "<td><a class='btn' target='_blank' href='resources/files/" . $val['file'] . "'>Open</a></td>"
          ."</tr>";
      }
    } catch (Exception $e) {
      $this->add_error_msg($e->getMessage());
      $ret_html .= "<tr><td colspan='20'>No Class</td></tr>";
    }

    return $ret_html;
  }

  function tc_my_homework() {
    $ret_html = "<tr>"
      .  "<th>ID</th>"
      .  "<th>Subject</th>"
      .  "<th>Name</th>"
      .  "<th>Duedate</th>"
      .  "<th>Status</th>"
      .  "<th>Create Date</th>"
      .  "<th>Create By</th>"
      .  "<th>File Homework</th>"
      .  "<th>File Submit</th>"
      .  "<th>Submit</th>"
      ."</tr>";
    try {
      if (!$this->user->is_login()) { throw new Exception("[Warning] Please login first"); }
      $this->user = $this->user->rowid;
      foreach ($this->db->sql_select("SELECT E.status AS `status`, E.rowid AS rowid, A.name, E.file AS `file`, A.file AS file_homework, D.name AS `subject`, DATE_FORMAT(A.duedate, '%d %b %Y') AS `duedate`, CONCAT(DATE_FORMAT(A.create_date,'%d %b %y'), ' - ',TIME_FORMAT(A.create_date, '%h:%i %p')) AS create_date, B.userid AS create_user FROM class_homework E LEFT JOIN homework A ON E.homework_rowid=A.rowid LEFT JOIN user B ON A.create_user=B.rowid LEFT JOIN `class` C ON A.class_rowid=C.rowid LEFT JOIN `subject` D ON C.subject_rowid=D.rowid WHERE E.student_rowid='$this->user'") as $val) {
        $btn_submit = $val['status'] == 'pending' ? "<button onclick=\"open_submit('" . $val['rowid'] . "')\">Open</button>" : "";
        $btn_view = $val['file'] != '' ? "<a class='btn' target='_blank' href='resources/files/" . $val['file'] . "'>Open</a>" : "";
        $ret_html .= "<tr>"
          .  "<td>" . $val['rowid'] . "</td>"
          .  "<td>" . $val['subject'] . "</td>"
          .  "<td>" . $val['name'] . "</td>"
          .  "<td>" . $val['duedate'] . "</td>"
          .  "<td>" . ucwords($val['status']) . "</td>"
          .  "<td>" . $val['create_date'] . "</td>"
          .  "<td>" . $val['create_user'] . "</td>"
          .  "<td><a class='btn' target='_blank' href='resources/files/" . $val['file_homework'] . "'>Open</a></td>"
          .  "<td>$btn_view</td>"
          .  "<td>$btn_submit</td>"
          ."</tr>";
      }
    } catch (Exception $e) {
      $this->add_error_msg($e->getMessage());
      $ret_html .= "<tr><td colspan='20'>No Class</td></tr>";
    }

    return $ret_html;
  }

  function tc_submit($rowid) {
    $ret_html = "<tr>"
      .  "<th>ID</th>"
      .  "<th>Student</th>"
      .  "<th>Status</th>"
      .  "<th>Submit Date</th>"
      .  "<th>File Submit</th>"
      ."</tr>";
    try {
      foreach ($this->db->sql_select("SELECT A.rowid AS rowid,A.status, B.userid AS student, A.file, CONCAT(DATE_FORMAT(A.submit_date,'%d %b %y'), ' - ',TIME_FORMAT(A.submit_date, '%h:%i %p')) AS submit_date, B.userid AS create_user FROM class_homework A LEFT JOIN user B ON A.student_rowid=B.rowid WHERE A.homework_rowid='$rowid'") as $val) {
        $btn_view = $val['file'] != '' ? "<a class='btn' target='_blank' href='resources/files/" . $val['file'] . "'>Open</a>" : "";
        $ret_html .= "<tr>"
          .  "<td>" . $val['rowid'] . "</td>"
          .  "<td>" . $val['student'] . "</td>"
          .  "<td>" . ucwords($val['status']) . "</td>"
          .  "<td>" . $val['submit_date'] . "</td>"
          .  "<td>$btn_view</td>"
          ."</tr>";
      }
    } catch (Exception $e) {
      $this->add_error_msg($e->getMessage());
      $ret_html .= "<tr><td colspan='20'>No Class</td></tr>";
    }

    return $ret_html;
  }

  function add_error_msg($msg) {
    $br = $this->error_msg == "" ? "<br>" : "";
    $this->error_msg .= $br . $msg;
    return true;
  }
}