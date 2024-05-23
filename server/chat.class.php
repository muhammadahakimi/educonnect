<?php
class chat {
  //Table chat
  public $rowid = '';
  public $from = '';
  public $to_user = '';
  public $to_group = '';
  public $type = '';
  public $text = NULL;
  public $image = NULL;
  public $file = NULL;
  public $homework_rowid = NULL;
  public $create_date = '';

  public $resource = '';
  

  //Common
  public $lastupdate = '';
  private $db;
  private $user;
  private $htmtemp;
  private $dir = '../resources/files/';
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

    if (!class_exists('htmtemp')) {
      if (file_exists('htmtemp.class.php')) {
        include 'htmtemp.class.php';
      }
      if (file_exists('server/htmtemp.class.php')) {
        include 'server/htmtemp.class.php';
      }
    }

    $this->htmtemp = new htmtemp();

    $this->set_dir();
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

  function send($target, $to, $type, $data) {
    try {
      if (!$this->user->is_login()) { throw new Exception("[Warning] Please login first"); }
      $this->from = $this->user->rowid;
      $this->type = $type;
      if ($this->type == "") { throw new Exception("[Error] type not assinged"); }

      if ($target == "user") {
        $this->to_user = $to;
        $this->to_group = NULL;
      } else if ($target == "group") {
        $this->to_group = $to;
        $this->to_user = NULL;
      }

      if ($this->to_user == "" && $this->to_group == "") { throw new Exception("[Error] not found send to who"); }
      if ($this->to_user != "" && $this->to_group != "") { throw new Exception("[Error] not found send to who"); }
      switch ($this->type) {
        case "text" : 
          $this->text = $data;
          if ($this->text == "") { throw new Exception("[Error] text not assigned"); }
          break;
        case "image" :
          $this->image = $data;
          if ($this->image == "") { throw new Exception("[Error] image not assigned"); }
          if (!$this->upload_image($this->image)) { throw new Exception("[Error] failed to upload image to server"); }
          break;
        case "file" :
          $this->file = $data;
          if ($this->file == "") { throw new Exception("[Error] file not assigned"); }
          if (!$this->upload_file($this->file)) { throw new Exception("[Error] failed to upload file to server"); }
          break;
        default :
          throw new Exception("[Error] Invalid type");
      }

      if (!$this->db->sql_command("INSERT INTO chat (`from`, `to_user`, `to_group`, `type`, `text`, `image`, `file`) VALUES ('$this->from', '$this->to_user', '$this->to_group', '$this->type', '$this->text', '$this->image', '$this->file')")) {
        throw new Exception("[Error] failed to insert table chat");
      }

      if (!$this->update_unread($target, $to)) {
        throw new Exception("[Error] failed to udpate_unread");
      }

      return true;
    } catch (Exception $e) {
      $this->add_error_msg($e->getMessage());
      return false;
    }
  }

  function upload_image($image) {
    try {
      if ($image['error'] !== 0 ) { throw new Exception("[Error] this image have error"); }
      date_default_timezone_set('Asia/Kuala_Lumpur');
      $this->image = sha1(date('Y-m-d H:i:s')) . "." . pathinfo($image['name'], PATHINFO_EXTENSION);
      if (!move_uploaded_file($image['tmp_name'], $this->dir.$this->image)) { throw new Exception("[Error] failed to move file: " . $this->dir.$this->image); }

      return true;
    } catch (Exception $e) {
      $this->add_error_msg($e->getMessage());
      return false;
    }
  }

  function upload_file($file) {
    try {
      if ($file['error'] !== 0 ) { throw new Exception("[Error] this file have error"); }
      date_default_timezone_set('Asia/Kuala_Lumpur');
      $this->file = sha1(date('Y-m-d H:i:s')) . "." . pathinfo($file['name'], PATHINFO_EXTENSION);
      if (!move_uploaded_file($file['tmp_name'], $this->dir.$this->file)) { throw new Exception("[Error] failed to move file: " . $this->dir.$this->file); }

      return true;
    } catch (Exception $e) {
      $this->add_error_msg($e->getMessage());
      return false;
    }
  }

  function html_list () {
    try {
      if (!$this->user->is_login()) { throw new Exception("[Warning] Please login first"); }
      $user = $this->user->rowid;
      $dir = "";
      if (is_file("template/chat_contact.html")) {
        $dir = "template/chat_contact.html";
      }
      if (is_file("../template/chat_contact.html")) {
        $dir = "../template/chat_contact.html";
      }
      $ret_html = "";
      $sql = "SELECT DISTINCT 
        CASE
          WHEN A.to_group<>0 THEN 'group'
          ELSE 'user'
        END AS `target`,
         CASE 
          WHEN A.to_group<>0 THEN A.to_group
          WHEN A.to_user='$user' THEN A.from
          ELSE A.to_user
        END AS `to`,
        CASE
          WHEN A.to_user='$user' THEN A.from
          ELSE A.to_user
        END AS to_user,
        CASE 
          WHEN A.to_group<>0 THEN D.name
          WHEN A.to_user='$user' THEN C.userid
          ELSE B.userid
        END AS `name`,
        CASE
          WHEN A.to_group <>0 THEN (SELECT `text` FROM chat WHERE to_group=A.to_group AND `text`<>'' ORDER BY create_date DESC LIMIT 1)
          ELSE NULL
        END AS lastchat
        FROM chat A 
        LEFT JOIN user B ON to_user=B.rowid 
        LEFT JOIN user C ON A.from=C.rowid 
        LEFT JOIN `group` D ON A.to_group=D.rowid
        LEFT JOIN group_member E ON D.rowid=E.group_rowid
        LEFT JOIN chat_unread F ON F.user_rowid=''
        WHERE A.from='$user' OR A.to_user='$user' OR E.user_rowid='$user'
        ORDER BY A.rowid DESC";
      foreach ($this->db->sql_select($sql) as $val) {
        //echo json_encode($val);
        $val['unread'] = $this->unread($val['target'], $val['to']);
        if ($val['lastchat'] == "") { $val['lastchat'] = $this->get_lastchat($val['to_user']); }
        $ret_html .= $this->htmtemp->gen($dir, $val);
      }

      return $ret_html;
    } catch (Exception $e) {
      $this->add_error_msg($e->getMessage());
      return "";
    }
  }

  function option_user() {
    try {
      $this->user->is_login();
      $ret_html = "";
      foreach ($this->db->sql_select("SELECT rowid, userid, fullname FROM user ORDER BY userid") as $val) {
        if ($val['rowid'] != $this->user->rowid) {
          $fullname = $val['fullname'] != "" ? " - " . $val['fullname'] : "";
          $ret_html .= "<option value=\"" . $val['rowid'] . "," . $val['userid'] . "\">" . $val['userid'] . "$fullname</option>";
        }
      }

      return $ret_html;
    } catch (Exception $e) {
      $this->add_error_msg($e->getMessage());
      return "";
    }
  }

  function html_chat_list ($target, $to) {
    try {
      if (!$this->user->is_login()) { throw new Exception("[Warning] Please login first"); }

      if ($target == "user") {
        $from_user = $to;
        $from_group = "-1";
      } else if ($target == "group") {
        $from_user = "-1";
        $from_group = $to;
      }

      $user = $this->user->rowid;
      $dir = "";
      if (is_file("template/chatbox.html")) {
        $dir = "template/chatbox.html";
      }
      if (is_file("../template/chatbox.html")) {
        $dir = "../template/chatbox.html";
      }

      if (!$this->clear_unread($target, $to)) {
        throw new Exception("[Error] failed to clear unread");
      }

      $ret_html = "";
      $sql = "SELECT
        CASE
          WHEN A.from='$user' THEN 'You'
          ELSE B.userid
        END AS `name`,
        CASE 
          WHEN A.from='$user' THEN 'chat_you'
          ELSE 'chat_other'
        END AS class,
        CASE 
          WHEN A.type='text' THEN A.text
          WHEN A.type='image' THEN A.image
          WHEN A.type='file' THEN A.file
          ELSE ''
        END AS content,
        A.type
        FROM chat A 
        LEFT JOIN user B ON A.from=B.rowid 
        WHERE (A.from='$from_user' AND A.to_user='$user') OR (A.from='$user' AND A.to_user='$from_user') OR A.to_group='$from_group' ORDER BY A.rowid";
      $this->dir = "resources/files/";
      foreach ($this->db->sql_select($sql) as $val) {
        //echo json_encode($val);
        if ($val['type'] == "image") {
          $val['content'] = "<img class='img_chat' src='$this->dir" . $val['content'] . "'>";
        }
        if ($val['type'] == "file") {
          $val['content'] = "<a style='font-size:16px;' href='$this->dir" . $val['content'] . "' target='_blank'>" . $val['content'] . "</a>";
        }
        $ret_html .= $this->htmtemp->gen($dir, $val);
      }

      return $ret_html;
    } catch (Exception $e) {
      $this->add_error_msg($e->getMessage());
      return "";
    }
  }

  function unread($target, $to) {
    try {
      if (!$this->user->is_login()) { throw new Exception("[Warning] Please login first"); }
      if ($target == "") { throw new Exception("[Error] target not assigned"); }
      if ($to == "") { throw new Exception("[Error] to not assigend"); }
      if (!is_numeric($to)) { throw new Exception("[Error] Invalid to"); }
      $user = $this->user->rowid;
      if ($target == 'user') {
        $data = $this->db->sql_select("SELECT qty FROM chat_unread WHERE user_rowid='$user' AND from_rowid='$to'");
      } else if ($target == 'group') {
        $data = $this->db->sql_select("SELECT qty FROM chat_unread WHERE user_rowid='$user' AND group_rowid='$to'");
      } else {
        throw new Exception("[Error] Invalid target");
      }

      if (count($data) == 0) {
        return '';
      } else {
        return $data[0]['qty'] == 0 ? '' : $data[0]['qty'];
      }
    } catch (Exception $e) {
      $this->add_error_msg($e->getMessage());
      return '';
    }
  }

  function update_unread($target, $to) {
    try {
      if (!$this->user->is_login()) { throw new Exception("[Warning] Please login first"); }
      if ($target == "") { throw new Exception("[Error] target not assigned"); }
      if ($to == "") { throw new Exception("[Error] to not assigend"); }
      if (!is_numeric($to)) { throw new Exception("[Error] Invalid to"); }
      if ($target == 'user') {
        $from_rowid = $this->user->rowid;
        $data = $this->db->sql_select("SELECT rowid FROM chat_unread WHERE user_rowid='$to' AND from_rowid='$from_rowid'");
        if (count($data) == 0) {
          if (!$this->db->sql_command("INSERT INTO chat_unread (user_rowid, from_rowid) VALUES ('$to', '$from_rowid')")) {
            throw new Exception("[Error] failed to insert table chat_unread");
          }
          $data = $this->db->sql_select("SELECT rowid FROM chat_unread WHERE user_rowid='$to' AND from_rowid='$from_rowid'");
        }
        $rowid = $data[0]['rowid'];
        if (!$this->db->sql_command("UPDATE chat_unread SET qty=qty+1, `date`=NOW() WHERE rowid='$rowid'")) {
          throw new Exception("[Error] failed to update table chat_unread");
        }
      } else if ($target == 'group') {
        foreach ($this->db->sql_select("SELECT user_rowid FROM group_member WHERE group_rowid='$to'") as $val) {
          $user_rowid = $val['user_rowid'];
          $data = $this->db->sql_select("SELECT rowid FROM chat_unread WHERE user_rowid='$user_rowid' AND group_rowid='$to'");
          if (count($data) == 0) {
            if (!$this->db->sql_command("INSERT INTO chat_unread (user_rowid, group_rowid) VALUES ('$user_rowid', '$to')")) {
              throw new Exception("[Error] failed to insert table chat_unread");
            }
            $data = $this->db->sql_select("SELECT rowid FROM chat_unread WHERE user_rowid='$user_rowid' AND group_rowid='$to'");
          }
          $rowid = $data[0]['rowid'];
          if (!$this->db->sql_command("UPDATE chat_unread SET qty=qty+1, `date`=NOW() WHERE rowid='$rowid'")) {
            throw new Exception("[Error] failed to update table chat_unread");
          }
        }
        
      } else {
        throw new Exception("[Error] Invalid target");
      }

      return true;
    } catch (Exception $e) {
      $this->add_error_msg($e->getMessage());
      return false;
    }
  }

  function clear_unread($target, $to) {
    try {
      if (!$this->user->is_login()) { throw new Exception("[Warning] Please login first"); }
      if ($target == "") { throw new Exception("[Error] target not assigned"); }
      if ($to == "") { throw new Exception("[Error] to not assigend"); }
      if (!is_numeric($to)) { throw new Exception("[Error] Invalid to"); }
      $user = $this->user->rowid;

      if ($target == 'user') {
        if (!$this->db->sql_command("UPDATE chat_unread SET qty=0, `date`=NOW() WHERE user_rowid='$user' AND from_rowid='$to'")) {
          throw new Exception("[Error] failed to update table chat_unread");
        }
      } else if ($target == 'group') {
        if (!$this->db->sql_command("UPDATE chat_unread SET qty=0, `date`=NOW() WHERE user_rowid='$user' AND group_rowid='$to'")) {
          throw new Exception("[Error] failed to update table chat_unread");
        }
      } else {
        throw new Exception("[Error] Invalid Target");
      }

      return true;
    } catch (Exception $e) {
      $this->add_error_msg($e->getMessage());
      return false;
    }
  }

  function set_lastupdate() {
    try {
      $user = $this->user->rowid;
      $data = $this->db->sql_select("SELECT A.rowid FROM chat A LEFT JOIN `group` B ON A.to_group=B.rowid LEFT JOIN group_member C ON A.to_group=C.group_rowid AND C.user_rowid='$user' WHERE A.from='$user' OR A.to_user='$user' OR B.create_user='$user' OR C.user_rowid='$user' ORDER BY A.rowid DESC LIMIT 1");
      if (count($data) > 0) {
        $this->lastupdate = $data[0]['rowid'];
      } else {
        $this->lastupdate = 0;
      }

      return true;
    } catch (Exception $e) {
      $this->add_error_msg($e->getMessage());
      return false;
    }
  }

  function get_lastchat($to_user) {
    try {
      $user = $this->user->rowid;
      $sql = "SELECT `text` FROM chat WHERE ((`from`='$user' AND to_user='$to_user') OR (`from`='$to_user' AND to_user='$user')) AND `text`<>'' ORDER BY create_date DESC LIMIT 1";
      $data = $this->db->sql_select($sql);
      if (count($data) > 0) {
        return $data[0]['text'];
      } else {
        return 'No chat yet';
      }
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