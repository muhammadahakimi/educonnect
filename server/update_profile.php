<?php
include 'user.class.php';
$user = new user();

try {
  if (!isset($_POST['userid'])) { throw new Exception("[Error] userid not found"); }
  if (!isset($_POST['fullname'])) { throw new Exception("[Error] fullname not found"); }
  if (!isset($_POST['gender'])) { throw new Exception("[Error] gender not found"); }
  if (!isset($_POST['ic'])) { throw new Exception("[Error] ic not found"); }
  if (!isset($_POST['birthday'])) { throw new Exception("[Error] birthday not found"); }
  
  $user->userid = $_POST['userid'];
  $user->fullname = $_POST['fullname'];
  $user->gender = $_POST['gender'];
  $user->ic = $_POST['ic'];
  $user->birthday = $_POST['birthday'];

  if (!$user->update_details()) {
    throw new Exception($user->error_msg);
  }

  $response['result'] = true;
} catch (Exception $e) {
  $response['result'] = false;
  $response['reason'] = $e->getMessage();
}

print json_encode($response);