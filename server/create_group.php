<?php
  try {
    include 'group.class.php';
    $group = new group();

    if (!isset($_POST['name'])) { throw new Exception("[Error] POST key name not found"); }
    if ($_POST['name'] == "") { throw new Exception("[Error] name not assigend"); }
    
    if (!$group->create($_POST['name'])) { throw new Exception($group->error_msg); }

    $response['gui'] = $group->tc_list();
    $response['result'] = true;
  } catch (Exception $e) {
    $response['result'] = false;
    $response['reason'] = $e->getMessage();
  }

  print json_encode($response);