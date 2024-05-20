<?php
include 'chat.class.php';
$chat = new chat();

try {
  if (!isset($_POST['target'])) { throw new Exception("[Error] POST data assigned"); }
  if (!isset($_POST['to'])) { throw new Exception("[Error] POST data assigned"); }
  if (!isset($_POST['type'])) { throw new Exception("[Error] POST data assigned"); }
  if (!isset($_POST['data'])) { throw new Exception("[Error] POST data assigned"); }
  if (!$chat->send($_POST['target'], $_POST['to'], $_POST['type'], $_POST['data'])) {
    throw new Exception($chat->error_msg);
  }

  //$response['gui'] = $chat->tc_student_list($_POST['rowid']);
  $response['result'] = true;
} catch (Exception $e) {
  $response['result'] = false;
  $response['reason'] = $e->getMessage();
}

print json_encode($response);