<?php

class user {
  //Table user
  public $rowid = '';
  public $userid = '';
  public $password = '';
  public $otp = '';
  public $role = '';
  public $fullname = '';
  public $gender = '';
  public $ic = '';
  public $birthday = '';
  public $lastlog = '';
  public $active = '';

  //Table user_relate
  public $relate_rowid = ''; //user_relate.rowid


  private $db;
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
  }

  function set_details($userid = '') {
    try {
      $this->userid = $userid != "" ? $userid : $this->userid;
      if ($this->userid == "") { throw new Exception("[Error] userid not assigned"); }

      $data = $this->db->sql_select("SELECT * FROM user WHERE userid='$this->userid'");
      if (count($data) == 0) { throw new Exception("[Error] userid not found"); }
      $this->rowid = $data[0]['rowid'];
      $this->otp = $data[0]['otp'];
      $this->role = $data[0]['role'];
      $this->fullname = $data[0]['fullname'];
      $this->gender = $data[0]['gender'];
      $this->ic = $data[0]['ic'];
      $this->birthday = $data[0]['birthday'];
      $this->lastlog = $data[0]['lastlog'];
      $this->active = $data[0]['active'];

      return true;
    } catch (Exception $e) {
      $this->add_error_msg($e->getMessage());
      return false;
    }
  }

  function login($userid = '', $password = '') {
    try {
      $this->userid = $userid != "" ? $userid : $this->userid;
      $this->password = $password != "" ? $password : $this->password;
      if ($this->userid == "") { throw new Exception("[Error] userid not assigned"); }
      if ($this->password == "") { throw new Exception("[Error] password not assigned"); }
      $this->userid = strtolower($this->userid);
      if (!$this->auth()) { throw new Exception("[Error] failed to authentication"); }
      if (!$this->set_details()) { throw new Exception("[Error] failed to set details"); }
      if (!$this->update_otp()) { throw new Exception("[Error] failed to update otp"); }
      if (!$this->set_session()) { throw new Exception("[Error] failed to set session"); }

      return true;
    } catch (Exception $e) {
      $this->add_error_msg($e->getMessage());
      return false;
    }
  }

  function auth($userid = '', $password = '') {
    try {
      $this->userid = $userid != "" ? $userid : $this->userid;
      $this->password = $password != "" ? $password : $this->password;
      if ($this->userid == "") { throw new Exception("[Error] userid not assigned"); }
      if ($this->password == "") { throw new Exception("[Error] password not assigned"); }
      $password_hash = sha1($this->password);
      $data = $this->db->sql_select("SELECT rowid FROM user WHERE `userid`='$this->userid' AND `password`='$password_hash' AND active='Y'");
  
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

  function is_login() {
    try {
      if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }
      if (!isset($_SESSION['rowid'])) { throw new Exception("[Error] rowid not assigned"); }
      if (!isset($_SESSION['userid'])) { throw new Exception("[Error] userid not assigned"); }
      if (!isset($_SESSION['otp'])) { throw new Exception("[Error] otp not assigned"); }
      if (!isset($_SESSION['role'])) { throw new Exception("[Error] otp not assigned"); }

      $this->rowid = $_SESSION['rowid'];
      $this->userid = $_SESSION['userid'];
      $this->otp = $_SESSION['otp'];
      $this->role = $_SESSION['role'];

      $data = $this->db->sql_select("SELECT rowid FROM user WHERE rowid='$this->rowid' AND userid='$this->userid' AND otp='$this->otp' AND `role`='$this->role'");

      if (count($data) == 0) { throw new Exception("[Error] session dat not macthing"); }

      return true;
    } catch (Exception $e) {
      $this->add_error_msg($e->getMessage());
      return false;
    }
  }

  function set_session() {
    try {
      if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }

      $_SESSION['rowid'] = $this->rowid;
      $_SESSION['userid'] = $this->userid;
      $_SESSION['otp'] = $this->otp;
      $_SESSION['role'] = $this->role;

      return true;
    } catch (Exception $e) {
      $this->add_error_msg($e->getMessage());
      return false;
    }
  }

  function logout() {
    try {
      if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }

      $_SESSION['rowid'] = $this->rowid;
      $_SESSION['userid'] = $this->userid;
      $_SESSION['otp'] = $this->otp;
      $_SESSION['role'] = $this->role;

      session_destroy();

      return true;
    } catch (Exception $e) {
      $this->add_error_msg($e->getMessage());
      return false;
    }
  }

  function update_otp() {
    try {
      if ($this->rowid == "") { throw new Exception("[Error] rowid not assigned"); }
      date_default_timezone_set("Asia/Kuala_Lumpur");
      $this->otp = sha1(date("Y-m-d H:i:s"));
      if (!$this->db->sql_command("UPDATE user SET otp='$this->otp', lastlog=NOW() WHERE rowid='$this->rowid'")) { throw new Exception("[Error] failed to update otp in database"); }
      if (!$this->db->sql_command("INSERT INTO userlog (`user_rowid`) VALUES ('$this->rowid') ")) { throw new Exception("[Error] failed to insert into table userlog"); }

      return true;
    } catch (Exception $e) {
      $this->add_error_msg($e->getMessage());
      return false;
    }
  }

  function update_details() {
    try {
      if (!$this->is_login()) { throw new Exception("[Warning] Please login first"); }
      if ($this->rowid == "") { throw new Exception("[Error] rowid not assigned"); }
      if ($this->userid == "") { throw new Exception("[Error] userid not assigned"); }
      if ($this->role == "") { throw new Exception("[Error] role not assigned"); }
      $this->fullname = ucwords($this->fullname);
      $this->userid = strtolower($this->userid);
      if ($this->userid_taken()) { throw new Exception("[Error] userid is taken"); }
      
      
      if (!$this->db->sql_command("UPDATE user SET userid='$this->userid', `role`='$this->role', fullname='$this->fullname', gender='$this->gender', ic='$this->ic', birthday='$this->birthday' WHERE rowid='$this->rowid'")) {
        throw new Exception("[Error] failed to update table");
      }

      return true;
    } catch (Exception $e) {
      $this->add_error_msg($e->getMessage());
      return false;
    }
  }

  function reset_password($password) {
    try {
      if (!$this->is_login()) { throw new Exception("[Warning] Please login first"); }
      if ($password == "") { throw new Exception("[Error] password not assigned"); }
      $password_hash = sha1($password);

      if (!$this->db->sql_command("UPDATE user SET password='$password_hash' WHERE rowid='$this->rowid'")) {
        throw new Exception("[Error] failed to update table");
      }

      return true;
    } catch (Exception $e) {
      $this->add_error_msg($e->getMessage());
      return false;
    }
  }

  function register($userid, $password, $role) {
    try {
      $this->userid = $userid;
      $this->password = $password;
      $this->role = $role;

      if ($this->userid == "") { throw new Exception("[Error] userid not assigned"); }
      if ($this->password == "") { throw new Exception("[Error] password not assigned"); }
      if ($this->role == "") { throw new Exception("[Error] role not assigned"); }
      if ($this->userid_taken()) { throw new Exception("[Error] userid is taken"); }

      $password_hash = sha1($password);

      if (!$this->db->sql_command("INSERT INTO user (userid, password, role) VALUES ('$this->userid','$password_hash', '$this->role')")) {
        throw new Exception("[Error] failed to insert into database");
      }

      if (!$this->set_details()) { throw new Exception("[Error] failed to set details"); }
      if (!$this->update_otp()) { throw new Exception("[Error] failed to update otp"); }
      if (!$this->set_session()) { throw new Exception("[Error] failed to set session"); }

      return true;
    } catch (Exception $e) {
      $this->add_error_msg($e->getMessage());
      return false;
    }
  }

  function set_heir($student, $heir, $type) {
    try {
      if (!$this->is_login()) { throw new Exception("[Warning] Please login first"); }
      if ($this->role != "teacher") { throw new Exception("[Warning] just teacher can set user_relate"); }
      if ($student == "") { throw new Exception("[Error] student not assigned"); }
      if ($heir == "") { throw new Exception("[Error] heir not assigned"); }
      if (!is_numeric($student)) { throw new Exception("[Error] Invalid student_rowid"); }
      if (!is_numeric($heir)) { throw new Exception("[Error] Invalid heir_rowid"); }
      if ($student == $heir) { throw new Exception("[Error] student and heir cannot be same person"); }

      if($this->heir_exists($student, $heir)) {
        if (!$this->db->sql_command("UPDATE user_relate SET `type`='$type', create_date=NOW(), create_user='$this->rowid' WHERE rowid='$this->relate_rowid'")) {
          throw new Exception("[Error] failed to update table user_relate");
        }
      } else {
        if (!$this->db->sql_command("INSERT INTO user_relate (student_rowid, heir_rowid, `type`, create_user) VALUES ('$student','$heir','$type','$this->rowid')")) {
          throw new Exception("[Error] failed to insert table user_relate");
        }
      }
     
      return true;
    } catch (Exception $e) {
      $this->add_error_msg($e->getMessage());
      return false;
    }
  }

  function remove_heir($rowid) {
    try {
      if (!$this->is_login()) { throw new Exception("[Warning] Please login first"); }
      if ($this->role != "teacher") { throw new Exception("[Warning] just teacher can set user_relate"); }
      if ($rowid == "") { throw new Exception("[Error] rowid not assigned"); }
      if (!$this->db->sql_command("DELETE FROM user_relate WHERE rowid='$rowid'")) {
        throw new Exception("[Error] failed to delete rowid: $rowid from table user_relate");
      }

      return true;
    } catch (Exception $e) {
      $this->add_error_msg($e->getMessage());
      return false;
    }
  }

  function userid_taken() {
    try {
      if ($this->userid == "") { throw new Exception("[Error] userid not assigned"); }
      $data = $this->db->sql_select("SELECT rowid FROM user WHERE userid='$this->userid' AND rowid<>'$this->rowid'");

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

  function heir_exists($student, $heir) {
    try {
      if ($student == "") { throw new Exception("[Error] student not assigned"); }
      if ($heir == "") { throw new Exception("[Error] heir not assigned"); }
      if (!is_numeric($student)) { throw new Exception("[Error] Invalid student_rowid"); }
      if (!is_numeric($heir)) { throw new Exception("[Error] Invalid heir_rowid"); }

      $data = $this->db->sql_select("SELECT rowid FROM user_relate WHERE student_rowid='$student' AND heir_rowid='$heir'");

      if (count($data) > 0) {
        $this->relate_rowid = $data[0]['rowid'];
        return true;
      } else {
        return false;
      }
    } catch (Exception $e) {
      $this->add_error_msg($e->getMessage());
      return false;
    }
  }

  function option_user() {
    try {
      $ret_html = "";
      foreach ($this->db->sql_select("SELECT rowid, userid, fullname FROM user ORDER BY userid") as $val) {
        $fullname = $val['fullname'] == "" ? " - " . $val['fullname'] : "";
        $ret_html .= "<option value='" . $val['rowid'] . "'>" . $val['userid'] . "$fullname<option>";
      }

      return $ret_html;
    } catch (Exception $e) {
      $this->add_error_msg($e->getMessage());
      return "";
    }
  }

  function activate($rowid) {
    try {
      if (!$this->is_login()) { throw new Exception("[Warning] Please login first"); }
      if ($this->role != "teacher") { throw new Exception("[Warning] just teacher can activate user"); }
      if ($rowid == "") { throw new Exception("[Error] rowid not assigned"); }
      if (!is_numeric($rowid)) { throw new Exception("[Error] Invalid rowid"); }
      
      if (!$this->db->sql_command("UPDATE user SET active='Y' WHERE rowid='$rowid'")) {
        throw new Exception("[Error] failed to update table user");
      }

      return true;
    } catch (Exception $e) {
      $this->add_error_msg($e->getMessage());
      return false;
    }
  }

  function deactivate($rowid) {
    try {
      if (!$this->is_login()) { throw new Exception("[Warning] Please login first"); }
      if ($this->role != "teacher") { throw new Exception("[Warning] just teacher can activate user"); }
      if ($rowid == "") { throw new Exception("[Error] rowid not assigned"); }
      if (!is_numeric($rowid)) { throw new Exception("[Error] Invalid rowid"); }
      
      if (!$this->db->sql_command("UPDATE user SET active='N' WHERE rowid='$rowid'")) {
        throw new Exception("[Error] failed to update table user");
      }

      return true;
    } catch (Exception $e) {
      $this->add_error_msg($e->getMessage());
      return false;
    }
  }

  function tc_list() {
    $ret_html = "<tr>
        <th>User ID</th>
        <th>Fullname</th>
        <th>Role</th>
        <th>Gender</th>
        <th>IC No</th>
        <th>Birthday</th>
        <th>Last Login</th>
        <th>Active</th>
      </tr>";
    try {
      foreach ($this->db->sql_select("SELECT *, date_format(birthday,'%d-%b-%y') AS birthday, CONCAT(DATE_FORMAT(lastlog,'%d %b %y'), ' - ',TIME_FORMAT(lastlog, '%h:%i %p')) AS lastlog FROM user ORDER BY userid") as $val) {
        $active = $val['active'] == "Y" ? "Yes" : "No";
        $ret_html .= "<tr>"
          .  "<td>" . $val['userid'] . "</td>"
          .  "<td>" . ucwords($val['fullname']) . "</td>"
          .  "<td align='center'>" . ucwords($val['role']) . "</td>"
          .  "<td align='center'>" . ucwords($val['gender']) . "</td>"
          .  "<td align='center'>" . $val['ic'] . "</td>"
          .  "<td align='center'>" . $val['birthday'] . "</td>"
          .  "<td align='center'>" . $val['lastlog'] . "</td>"
          .  "<td align='center'>$active</td>"
          ."</tr>";
      }
    } catch (Exception $e) {
      $this->add_error_msg($e->getMessage());
    }
    return $ret_html;
  }

  function tc_active_list() {
    $ret_html = "<tr>
        <th>User ID</th>
        <th>Role</th>
        <th>Fullname</th>
        <th>Student</th>
        <th>Activate</th>
      </tr>";
    try {
      foreach ($this->db->sql_select("SELECT A.rowid, A.userid, A.role, A.fullname, CONCAT(C.userid, ' - ', C.fullname) AS student FROM user A LEFT JOIN user_relate B ON A.rowid=B.heir_rowid LEFT JOIN user C ON B.student_rowid=C.rowid WHERE A.active='Y'") as $val) {
        $ret_html .= "<tr>"
          .  "<td>" . $val['userid'] . "</td>"
          .  "<td>" . $val['role'] . "</td>"
          .  "<td>" . $val['fullname'] . "</td>"
          .  "<td>" . $val['student'] . "</td>"
          .  "<td><button class='btn_remove' style='display:block;margin:auto;' onclick=\"deactivate_user('" . $val['rowid'] . "')\">Deactivate</button></td>"
          ."</tr>";
      }
    } catch (Exception $e) {
      $this->add_error_msg($e->getMessage());
    }
    return $ret_html;
  }

  function tc_deactive_list() {
    $ret_html = "<tr>
        <th>User ID</th>
        <th>Role</th>
        <th>Fullname</th>
        <th>Student</th>
        <th>Activate</th>
      </tr>";
    try {
      foreach ($this->db->sql_select("SELECT A.rowid, A.userid, A.role, A.fullname, CONCAT(C.userid, ' - ', C.fullname) AS student FROM user A LEFT JOIN user_relate B ON A.rowid=B.heir_rowid LEFT JOIN user C ON B.student_rowid=C.rowid WHERE A.active='N'") as $val) {
        $ret_html .= "<tr>"
          .  "<td>" . $val['userid'] . "</td>"
          .  "<td>" . $val['role'] . "</td>"
          .  "<td>" . $val['fullname'] . "</td>"
          .  "<td>" . $val['student'] . "</td>"
          .  "<td><button style='display:block;margin:auto;' onclick=\"activate_user('" . $val['rowid'] . "')\">Activate</button></td>"
          ."</tr>";
      }
    } catch (Exception $e) {
      $this->add_error_msg($e->getMessage());
    }
    return $ret_html;
  }

  function tc_user_relate_list() {
    $ret_html = "<tr>
        <th>Student</th>
        <th>Heir</th>
        <th>Relationship</th>
        <th>Create Date</th>
        <th>Create By</th>
        <th>Remove</th>
      </tr>";
    try {
      foreach ($this->db->sql_select("SELECT A.rowid, A.type, CONCAT(B.userid, ' - ', B.fullname) AS student, CONCAT(C.userid, ' - ', C.fullname) AS heir, CONCAT(D.userid, ' - ', D.fullname) AS create_user, date_format(A.create_date,'%d %b %y') AS create_date FROM user_relate A LEFT JOIN user B ON A.student_rowid=B.rowid LEFT JOIN user C ON A.heir_rowid=C.rowid LEFT JOIN user D ON A.create_user=D.rowid") as $val) {
        $ret_html .= "<tr>"
          .  "<td>" . $val['student'] . "</td>"
          .  "<td>" . $val['heir'] . "</td>"
          .  "<td align='center'>" . ucwords($val['type']) . "</td>"
          .  "<td align='center'>" . $val['create_date'] . "</td>"
          .  "<td>" . $val['create_user'] . "</td>"
          .  "<td><button class='btn_remove' style='display:block;margin:auto;' onclick=\"remove_heir('" . $val['rowid'] . "')\">Remove</button></td>"
          ."</tr>";
      }
    } catch (Exception $e) {
      $this->add_error_msg($e->getMessage());
    }
    return $ret_html;
  }

  function option_teacher() {
    try {
      $ret_html = "";
      foreach ($this->db->sql_select("SELECT rowid, userid, fullname FROM user WHERE role='teacher' ORDER BY userid") as $val) {
        $fullname = $val['fullname'] == "" ? " - " . $val['fullname'] : "";
        $ret_html .= "<option value='" . $val['rowid'] . "'>" . $val['userid'] . "$fullname</option>";
      }

      return $ret_html;
    } catch (Exception $e) {
      $this->add_error_msg($e->getMessage());
      return "";
    }
  }

  function option_student() {
    try {
      $ret_html = "";
      foreach ($this->db->sql_select("SELECT rowid, userid, fullname FROM user WHERE `role`='student' AND active='Y' ORDER BY userid") as $val) {
        $fullname = $val['fullname'] == "" ? " - " . $val['fullname'] : "";
        $ret_html .= "<option value='" . $val['rowid'] . "'>" . $val['userid'] . "$fullname</option>";
      }

      return $ret_html;
    } catch (Exception $e) {
      $this->add_error_msg($e->getMessage());
      return "";
    }
  }

  function option_heir() {
    try {
      $ret_html = "";
      foreach ($this->db->sql_select("SELECT A.rowid, A.userid, B.type, C.fullname AS student  FROM user A LEFT JOIN user_relate B ON A.rowid=B.heir_rowid LEFT JOIN user C ON B.student_rowid=C.rowid WHERE A.role='heir' AND active='Y' ORDER BY userid") as $val) {
        $remark = $val['type'] == "" ? " - " . $val['student'] . " " . $val['type'] : "";
        $ret_html .= "<option value='" . $val['rowid'] . "'>" . $val['userid'] . "$remark</option>";
      }

      return $ret_html;
    } catch (Exception $e) {
      $this->add_error_msg($e->getMessage());
      return "";
    }
  }

  function add_error_msg($msg) {
    $br = $this->error_msg == "" ? "<br>" : "";
    $this->error_msg .= $br . $msg;
    return true;
  }

}