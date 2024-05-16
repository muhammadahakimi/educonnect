<?php
include 'user.class.php';
$user = new user();

try {
  if (!isset($_POST['password'])) { throw new Exception("[Error] password not found"); }

  if (!$user->reset_password($_POST['password'])) {
    throw new Exception($user->error_msg);
  }

  $response['result'] = true;
} catch (Exception $e) {
  $response['result'] = false;
  $response['reason'] = $e->getMessage();
}

print json_encode($response);