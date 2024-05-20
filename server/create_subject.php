<?php
include 'subject.class.php';
$subject = new subject();

try {
  if (!isset($_POST['name'])) { throw new Exception("[Error] POST data assigned"); }
  if (!$subject->create($_POST['name'])) {
    throw new Exception($subject->error_msg);
  }

  $response['gui'] = $subject->tc_list();
  $response['result'] = true;
} catch (Exception $e) {
  $response['result'] = false;
  $response['reason'] = $e->getMessage();
}

print json_encode($response);