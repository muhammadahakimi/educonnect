<?php
class homework {
  //Table homework
  public $rowid = '';
  public $subject_rowid = '';
  public $name = '';
  public $duedate = '';
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

  function create($name, $duedate, $subject_rowid) {
    try {
      $this->name = $name;
      $this->duedate = $duedate;
      $this->subject_rowid = $subject_rowid;
      if ($this->name == "") { throw new Exception("[Error] name not assigned"); }
      if ($this->duedate == "") { throw new Exception("[Error] duedate not assigned"); }
      if ($this->subject_rowid == "") { throw new Exception("[Error] subject_rowid not assigned"); }
      if (!is_numeric($this->subject_rowid)) { throw new Exception("[Error] Invalid subject_rowid"); }

      if (!$this->db->sql_command("INSERT INTO homework (subject_rowid,`name`,dueadate) VALUES ('$this->subject_rowid','$this->name','$this->duedate')")) {
        throw new Exception("[Error] failed to insert table homework");
      }

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