<?php
try {
  include 'exam.class.php';
  $exam = new exam();

  if (!isset($_POST['class_rowid'])) { throw new Exception("[Error] POST data class_rowid not found"); }
  if (!isset($_POST['list'])) { throw new Exception("[Error] POST data list not found"); }

  foreach ($_POST['list'] as $val) {
    if (!$exam->set_score($_POST['class_rowid'], $val['student_rowid'], $val['mark'])) {
      throw new Exception($exam->error_msg);
    }
  }

  $response['result'] = true;
} catch (Exception $e) {
  $response['result'] = false;
  $response['reason'] = $e->getMessage();
}

print json_encode($response);