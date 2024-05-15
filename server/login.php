<?php

try {
  if (!isset($_POST['userid'])) { throw new Exception("[Error] POST data not found: userid"); }
  if (!isset($_POST['password'])) { throw new Exception("[Error] POST data not found: password"); }
  if ($_POST['userid'] == "") { throw new Exception("[Error] POST data not assigned: userid"); }
  if ($_POST['password'] == "") { throw new Exception("[Error] POST data not assigned: password"); }
  include 'user.class.php';
  $user = new user();
  if (!$user->login($_POST['userid'], $_POST['password'])) { throw new Exception($user->error_msg); }
  $response['result'] = true;
} catch (Exception $e) {
  $response['result'] = false;
  $response['reason'] = $e->getMessage();
}

echo json_encode($response);