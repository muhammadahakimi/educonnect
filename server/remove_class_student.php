<?php
include 'classs.class.php';
$class = new classs();

try {
  if (!isset($_POST['rowid'])) { throw new Exception("[Error] POST data assigned"); }
  if (!isset($_POST['class_student_rowid'])) { throw new Exception("[Error] POST data assigned"); }
  if (!$class->remove_student($_POST['class_student_rowid'])) {
    throw new Exception($class->error_msg);
  }

  $response['gui'] = $class->tc_student_list($_POST['rowid']);
  $response['result'] = true;
} catch (Exception $e) {
  $response['result'] = false;
  $response['reason'] = $e->getMessage();
}

print json_encode($response);