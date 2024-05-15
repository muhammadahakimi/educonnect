<?php

class user {
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

      $this->rowid = $_SESSION['rowid'];
      $this->userid = $_SESSION['userid'];
      $this->otp = $_SESSION['otp'];

      $data = $this->db->sql_select("SELECT rowid FROM user WHERE rowid='$this->rowid' AND userid='$this->userid' AND otp='$this->otp'");

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
      
      if (!$this->db->sql_command("UPDATE user SET userid='$this->userid' WHERE rowid='$this->rowid'")) {
        throw new Exception("[Error] failed to update table");
      }

      return true;
    } catch (Exception $e) {
      $this->add_error_msg($e->getMessage());
      return false;
    }
  }

  function change_password($password) {
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
      if ($this->userid_exists()) { throw new Exception("[Error] userid is taken"); }

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

  function userid_exists() {
    try {
      if ($this->userid == "") { throw new Exception("[Error] userid not assigned"); }
    $data = $this->db->sql_select("SELECT rowid FROM user WHERE userid='$this->userid'");

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