<?php
include 'homework.class.php';
$homework = new homework();

try {
  if (!isset($_POST['name'])) { throw new Exception("[Error] POST data assigned"); }
  if (!isset($_POST['duedate'])) { throw new Exception("[Error] POST data assigned"); }
  if (!isset($_POST['class_rowid'])) { throw new Exception("[Error] POST data assigned"); }
  if (!isset($_FILES['file'])) { throw new Exception("[Error] POST data assigned"); }
  if (!$homework->create($_POST['class_rowid'], $_POST['name'], $_POST['duedate'], $_FILES['file'])) {
    throw new Exception($homework->error_msg);
  }

  $response['gui'] = $homework->tc_html($_POST['class_rowid']);
  $response['result'] = true;
} catch (Exception $e) {
  $response['result'] = false;
  $response['reason'] = $e->getMessage();
}

print json_encode($response);