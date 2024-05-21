<?php
include 'chat.class.php';
$chat = new chat();

try {
  if (!isset($_POST['target'])) { throw new Exception("[Error] POST data assigned"); }
  if (!isset($_POST['to'])) { throw new Exception("[Error] POST data assigned"); }
  if (!isset($_POST['type'])) { throw new Exception("[Error] POST data assigned"); }
  if (!isset($_POST['data']) && !isset($_FILES['file'])) { throw new Exception("[Error] POST data assigned"); }
  $data = isset($_POST['data']) ? $_POST['data'] : $_FILES['file'];
  if (!$chat->send($_POST['target'], $_POST['to'], $_POST['type'], $data)) {
    throw new Exception($chat->error_msg);
  }

  $response['gui'] = $chat->html_chat_list($_POST['target'], $_POST['to']);
  $response['result'] = true;
} catch (Exception $e) {
  $response['result'] = false;
  $response['reason'] = $e->getMessage();
}

print json_encode($response);