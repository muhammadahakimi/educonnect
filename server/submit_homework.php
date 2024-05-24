<?php
include 'homework.class.php';
$homework = new homework();

try {
  if (!isset($_POST['rowid'])) { throw new Exception("[Error] POST data assigned"); }
  if (!isset($_FILES['file'])) { throw new Exception("[Error] POST data assigned"); }
  if (!$homework->submit($_POST['rowid'], $_FILES['file'])) {
    throw new Exception($homework->error_msg);
  }

  $response['gui'] = $homework->tc_my_homework();
  $response['result'] = true;
} catch (Exception $e) {
  $response['result'] = false;
  $response['reason'] = $e->getMessage();
}

print json_encode($response);