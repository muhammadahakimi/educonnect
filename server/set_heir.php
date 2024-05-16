<?php
include 'user.class.php';
$user = new user();

try {
  if (!isset($_POST['student'])) { throw new Exception("[Error] student not found"); }
  if (!isset($_POST['heir'])) { throw new Exception("[Error] heir not found"); }
  if (!isset($_POST['type'])) { throw new Exception("[Error] type not found"); }

  if (!$user->set_heir($_POST['student'], $_POST['heir'], $_POST['type'])) {
    throw new Exception($user->error_msg);
  }

  $response['gui'] = $user->tc_user_relate_list();
  $response['result'] = true;
} catch (Exception $e) {
  $response['result'] = false;
  $response['reason'] = $e->getMessage();
}

print json_encode($response);