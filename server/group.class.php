<?php
class group {
  //Table group
  public $rowid = '';
  public $name = '';
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

  function create($name) {
    try {
      $this->name = $name;
      if ($this->name == "") { throw new Exception("[Error] name not assigned"); }
      if (!$this->access()) { return false; }
      $this->create_user = $this->user->rowid;

      if (!$this->db->sql_command("INSERT INTO `group` (`name`, create_user) VALUES ('$this->name', '$this->create_user')")) { throw new Exception("[Error] failed to insert table group"); }
      if (!$this->add_member($this->create_user, $this->db->lastInsertId)) { throw new Exception("[Error] failed to invite you into group"); }

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

  function remove_member($rowid) {
    try {
      if (!$this->access()) { return false; }
      if ($rowid == "") { throw new Exception("[Error] rowid not assigned"); }
      if (!$this->db->sql_command("DELETE FROM group_member WHERE rowid='$rowid'")) {
        throw new Exception("[Error] failed to delete record table group_member");
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

  function tc_list() {
    $ret_html = "<tr>
        <th>ID</th>
        <th>Name</th>
        <th>Member</th>
        <th>Create By</th>
        <th>Create On</th>
        <th>Manage</th>
        <th>Chat</th>
      </tr>";
    try {
      foreach ($this->db->sql_select("SELECT *, A.rowid, CONCAT(DATE_FORMAT(A.create_date,'%d %b %y'), ' - ',TIME_FORMAT(A.create_date, '%h:%i %p')) AS create_date, (SELECT COUNT(*) FROM group_member WHERE group_rowid=A.rowid) AS member, B.userid AS create_user FROM `group` A LEFT JOIN user B ON A.create_user=B.rowid ORDER BY A.name") as $val) {

        $ret_html .= "<tr>"
          .  "<td>" . $val['rowid'] . "</td>"
          .  "<td>" . $val['name'] . "</td>"
          .  "<td>" . $val['member'] . "</td>"
          .  "<td>" . $val['create_user'] . "</td>"
          .  "<td>" . $val['create_date'] . "</td>"
          .  "<td align='center'><button onclick=\"manage_group('" . $val['rowid'] . "')\">Manage</button></td>"
          .  "<td align='center'><button onclick=\"open_chat('group','" . $val['rowid'] . "','" . $val['name'] . "')\">Chat</button></td>"
          ."</tr>";
      }
    } catch (Exception $e) {
      $this->add_error_msg($e->getMessage());
    }
    return $ret_html;
  }

  function tc_member_list($group_rowid) {
    $ret_html = "<tr>
        <th>User ID</th>
        <th>Name</th>
        <th>Join Date</th>
        <th>Invited By</th>
        <th>Remove</th>
      </tr>";
    try {
      if ($group_rowid == "") { throw new Exception("[Error] group_rowid not assigned"); }
      if (!is_numeric($group_rowid)) { throw new Exception("[Error] Invalid group_rowid"); }
      foreach ($this->db->sql_select("SELECT A.rowid, B.userid, B.fullname, C.userid AS invite_user, CONCAT(DATE_FORMAT(A.join_date,'%d %b %y'), ' - ',TIME_FORMAT(A.join_date, '%h:%i %p')) AS join_date FROM group_member A LEFT JOIN user B ON A.user_rowid=B.rowid LEFT JOIN user C ON A.invite_user=C.rowid WHERE group_rowid='$group_rowid'") as $val) {

        $ret_html .= "<tr>"
          .  "<td>" . $val['userid'] . "</td>"
          .  "<td>" . $val['fullname'] . "</td>"
          .  "<td>" . $val['join_date'] . "</td>"
          .  "<td>" . $val['invite_user'] . "</td>"
          .  "<td align='center'><button class='btn_remove' onclick=\"remove_member('" . $val['rowid'] . "')\">Remove</button></td>"
          ."</tr>";
      }
    } catch (Exception $e) {
      $this->add_error_msg($e->getMessage());
    }
    return $ret_html;
  }

  function access() {
    try {
      if (!$this->user->is_login()) { throw new Exception("[Warning] Please login first"); }
      if ($this->user->role != "teacher") { throw new Exception("[Warning] your role is not teacher"); }

      return true;
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