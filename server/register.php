<?php

try {
  if (!isset($_POST['userid'])) { throw new Exception("[Error] POST data not found: userid"); }
  if (!isset($_POST['password'])) { throw new Exception("[Error] POST data not found: password"); }
  if (!isset($_POST['role'])) { throw new Exception("[Error] POST data not found: role"); }

  if ($_POST['userid'] == "") { throw new Exception("[Error] POST data not assigned: userid"); }
  if ($_POST['password'] == "") { throw new Exception("[Error] POST data not assigned: password"); }
  if ($_POST['role'] == "") { throw new Exception("[Error] POST data not assigned: role"); }

  include 'user.class.php';
  $user = new user();
  
  if (!$user->register($_POST['userid'], $_POST['password'], $_POST['role'])) {
    throw new Exception($user->error_msg);
  }

  $response['result'] = true;
} catch (Exception $e) {
  $response['result'] = false;
  $response['reason'] = $e->getMessage();
}

echo json_encode($response);