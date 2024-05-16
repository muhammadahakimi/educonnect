<?php

class exam {
  //Table exam
  public $rowid = ''; //same as exam
  public $name = '';
  public $date = '';
  public $subject_rowid = '';
  public $create_date = '';
  public $create_user = '';

  //Table exam_score
  public $score_rowid = ''; //exam_score.rowid
  //public $exam_rowid = ''; //already declare as rowid
  public $student_rowid = '';
  public $mark = '';
  public $score_create_date = ''; //exam_score.create_date
  public $score_create_user = ''; //exam_score.create_user

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

  function create($name, $date, $subject_rowid ) {
    try {
      $this->name = $name;
      $this->date = $date;
      $this->subject_rowid = $subject_rowid;
      if (!$this->user->is_login()) { throw new Exception("[Warning] Please login first"); }
      $this->create_user = $this->user->rowid;

      if ($this->name == "") { throw new Exception("[Error] name not assigned"); }
      if ($this->date == "") { throw new Exception("[Error] date not assigned"); }
      if ($this->subject_rowid == "") { throw new Exception("[Error] subject_rowid not assigned"); }
      if (is_numeric($this->subject_rowid)) { throw new Exception("[Error] Invalid subject_rowid"); }

      if (!$this->db->sql_command("INSERT INTO exam (`name`,`date`,subject_rowid,create_user) VALUES ('$this->name','$this->date','$this->subject_rowid','$this->create_user')")) {
        throw new Exception("[Error] failed to insert table exam");
      }

      return true;
    } catch (Exception $e) {
      $this->add_error_msg($e->getMessage());
      return false;
    }
  }

  function update($rowid, $name, $date, $subject_rowid ) {
    try {
      $this->rowid = $rowid;
      $this->name = $name;
      $this->date = $date;
      $this->subject_rowid = $subject_rowid;
      if (!$this->user->is_login()) { throw new Exception("[Warning] Please login first"); }
      $this->create_user = $this->user->rowid;

      if ($this->rowid == "") { throw new Exception("[Error] rowid not assigned"); }
      if ($this->name == "") { throw new Exception("[Error] name not assigned"); }
      if ($this->date == "") { throw new Exception("[Error] date not assigned"); }
      if ($this->subject_rowid == "") { throw new Exception("[Error] subject_rowid not assigned"); }
      if (is_numeric($this->subject_rowid)) { throw new Exception("[Error] Invalid subject_rowid"); }

      if (!$this->db->sql_command("UPDATE exam SET `name`='$this->name', `date`='$this->date', subject_rowid='$this->subject_rowid', create_user='$this->create_user', create_date=NOW() WHERE rowid='$this->rowid'")) {
        throw new Exception("[Error] failed to update table exam");
      }

      return true;
    } catch (Exception $e) {
      $this->add_error_msg($e->getMessage());
      return false;
    }
  }

  function set_score($student_rowid, $mark, $rowid = '') {
    try {
      $this->rowid = $rowid != "" ? $rowid : $this->rowid;
      if (!$this->user->is_login()) { throw new Exception("[Warning] Please login first"); }
      $this->score_create_user = $this->user->rowid;
      
      if ($this->score_exists($this->rowid, $student_rowid)) {
        if (!$this->db->sql_command("UPDATE exaam_score SET mark='$mark', create_user='$this->score_create_user', create_date=NOW() WHERE rowid='$this->score_rowid'")) {
          throw new Exception("[Error] failed to update table exam_score");
        }
      } else {
        if (!$this->db->sql_command("INSERT INTO exam_score (exam_rowid, student_rowid, mark, create_user) VALUES ('$this->rowid', '$student_rowid', '$mark', '$this->score_create_user')")) {
          throw new Exception("[Error] failed to insert table exam_score");
        }
      }      

      return true;
    } catch (Exception $e) {
      $this->add_error_msg($e->getMessage());
      return false;
    }
  }

  function score_exists($rowid, $student_rowid) {
    try {
      if ($rowid == "") { throw new Exception("[Error] rowid not assigned"); }
      if ($student_rowid == "") { throw new Exception("[Error] studen_rowid not assigend"); }
      
      $data = $this->db->sql_select("SELECT rowid FROM exam_score WHERE exam_rowid='$rowid' AND student_rowid='$student_rowid'");
      if (count($data) > 0) {
        $this->score_rowid = $data[0]['rowid'];
        return true;
      } else {
        return false;
      }
    } catch (Exception $e) {
      $this->add_error_msg($e->getMessage());
      return false;
    }
  }

  function form_score($rowid = '') {
    $ret_html = "<tr>"
      .  "<th>User ID</th>"
      .  "<th>Fullname</th>"
      .  "<th>Mark</th>"
      ."</tr>";
    try {
      $this->rowid = $rowid;
      if ($this->rowid == "") { throw new Exception("[Error] rowid not assigned"); }

      foreach ($this->db->sql_select("SELECT B.userid, B.fullname, A.mark FROM exam_score A LEFT JOIN user B ON A.student_rowid=B.rowid WHERE A.exam_rowid='$this->rowid' ORDER BY B.userid") as $val) {
        $ret_html .= "<tr>"
          .  "<td>" . $val['userid'] . "</td>"
          .  "<td>" . $val['fullname'] . "</td>"
          .  "<td>" . $val['mark'] . "</td>"
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