<?php
class subject {
  //Table subject
  public $rowid = '';
  public $name = '';
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

  function create($name) {
    try {
      if (!$this->access()) { throw new Exception("[Error] Access Denied"); }
      if ($name == "") { throw new Exception("[Error] name not assigned"); }
      if ($this->name_taken()) { throw new Exception("[Warning] name is taken"); }
      $this->create_user = $this->user->rowid;

      if (!$this->db->sql_command("INSERT INTO `subject` (`name`, create_user) VALUES ('$this->name', '$this->create_user')")) {
        throw new Exception("[Error] failed to insert table subject");
      }

      return true;
    } catch (Exception $e) {
      $this->add_error_msg($e->getMessage());
      return false;
    }
  }

  function name_taken() {
    try {
      if ($this->name == "") { throw new Exception("[Error] name not assigned"); }
      $data = $this->db->sql_select("SELECT rowid FROM `subject` WHERE name='$this->name' AND rowid<>'$this->rowid'");

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

  function option() {
    try {
      $ret_html = '';
      foreach ($this->db->sql_select("SELECT rowid, `name` FROM `subject`") as $val) {
        $ret_html .= "<option value='" . $val['rowid'] . "'>" . $val['name'] . "</option>";
      }

      return $ret_html;
    } catch (Exception $e) {
      $this->add_error_msg($e->getMessage());
      return "";
    }
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

  function add_error_msg($msg) {
    $br = $this->error_msg == "" ? "<br>" : "";
    $this->error_msg .= $br . $msg;
    return true;
  }
}