<?php
include 'classs.class.php';
$class = new classs();

try {
  if (!isset($_POST['name'])) { throw new Exception("[Error] POST data assigned"); }
  if (!isset($_POST['subject_rowid'])) { throw new Exception("[Error] POST data assigned"); }
  if (!$class->create($_POST['subject_rowid'], $_POST['name'])) {
    throw new Exception($class->error_msg);
  }

  $response['gui'] = $class->tc_list();
  $response['result'] = true;
} catch (Exception $e) {
  $response['result'] = false;
  $response['reason'] = $e->getMessage();
}

print json_encode($response);