<?php
include 'chat.class.php';
$chat = new chat();

try {
  if (!isset($_POST['target'])) { throw new Exception("[Error] POST data incomplete"); }
  if (!isset($_POST['to'])) { throw new Exception("[Error] POST data incomplete"); }
  
  $response['gui'] = $chat->html_chat_list($_POST['target'], $_POST['to']);
  $response['gui_list'] = $chat->html_list();
  $response['result'] = true;
} catch (Exception $e) {
  $response['result'] = false;
  $response['reason'] = $e->getMessage();
}

echo json_encode($response);