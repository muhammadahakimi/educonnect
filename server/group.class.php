<?php
class group {
  //Table group
  public $rowid = '';
  public $name = '';
  public $description = '';
  public $create_date = '';
  public $create_user = '';

  //Table group_member
  public $member_rowid = '';
  public $user_rowid = '';
  public $join_date = '';
  public $invite_user = '';

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

  function create($name, $description = '') {
    try {
      $this->name = $name;
      $this->description = $description;
      if ($this->name == "") { throw new Exception("[Error] name not assigned"); }
      if (!$this->access()) { return false; }
      $this->create_user = $this->user->rowid;

      if (!$this->db->sql_command("INSERT INTO group (`name`, `description`, create_user) VALUES ('$this->name', '$this->description', '$this->create_user')"))
      return true;
    } catch (Exception $e) {
      $this->add_error_msg($e->getMessage());
      return false;
    }
  }

  function add_member($user_rowid, $rowid = '') {
    try {
      if (!$this->access()) { return false; }
      $this->invite_user = $this->user->rowid;-
      $this->user_rowid = $user_rowid;
      $this->rowid = $rowid != "" ? $rowid : $this->rowid;
      if ($this->user_rowid == "") { throw new Exception("[Error] user_rowid not assigned"); }
      if ($this->rowid == "") { throw new Exception("[Error] rowid not assigned"); }
      if ($this->member_exists($this->rowid, $this->user_rowid)) { throw new Exception("[Error] this user is already invite"); }
      if (!$this->db->sql_command("INSERT INTO group_member (user_rowid, group_rowid, invite_user) VALUES ('$this->user_rowid', '$this->rowid', '$this->invite_user')")) {
        throw new Exception("[Error] failed to insert table group_member");
      }

      return true;
    } catch (Exception $e) {
      $this->add_error_msg($e->getMessage());
      return false;
    }
  }

  function option() {
    try {
      $ret_html = "";
      foreach ($this->db->sql_select("SELECT rowid, `name` FROM group") as $val) {
        $ret_html .= "<option value='" . $val['rowid'] . "'>" . $val['name'] . "</option>";
      }

      return $ret_html;
    } catch (Exception $e) {
      $this->add_error_msg($e->getMessage());
      return "";
    }
  }

  function member_exists($rowid, $user_rowid) {
    try {
      $data = $this->db->sql_select("SELECT rowid FROM group_member WHERE group_rowid='$rowid' AND user_rowid='$this->user_rowid'");
      if (count($data) > 0) {
        $this->member_rowid = $data[0]['rowid'];
        return true;
      } else {
        return false;
      }
    } catch (Exception $e) {
      $this->add_error_msg($e->getMessage());
      return false;
    }
  }

  function access() {
    try {
      if (!$this->user->is_login()) { throw new Exception("[Warning] Please login first"); }
      if ($this->user->role != "teacher") { throw new Exception("[Warning] your role is not teacher"); }

      return false;
    } catch (Exception $e) {
      $this->add_error_msg($e->getMessage());
      return false;
    }
  }

  function add_error_msg($msg) {
    $br = $this->error_msg == "" ?  "<br>" : "";
    $this->error_msg .= $br . $msg;
    return true;
  }
}