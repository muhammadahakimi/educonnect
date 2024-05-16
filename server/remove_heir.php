<?php
include 'user.class.php';
$user = new user();

try {
  if (!isset($_POST['rowid'])) { throw new Exception("[Error] rowid not found"); }

  if (!$user->remove_heir($_POST['rowid'])) {
    throw new Exception($user->error_msg);
  }

  $response['gui'] = $user->tc_user_relate_list();
  $response['result'] = true;
} catch (Exception $e) {
  $response['result'] = false;
  $response['reason'] = $e->getMessage();
}

print json_encode($response);