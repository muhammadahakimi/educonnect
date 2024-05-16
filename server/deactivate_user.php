<?php
include 'user.class.php';
$user = new user();

try {
  if (!isset($_POST['rowid'])) { throw new Exception("[Error] POST data assigned"); }
  if (!$user->deactivate($_POST['rowid'])) {
    throw new Exception($user->error_msg);
  }

  $response['gui'] = $user->tc_deactive_list();
  $response['result'] = true;
} catch (Exception $e) {
  $response['result'] = false;
  $response['reason'] = $e->getMessage();
}

print json_encode($response);