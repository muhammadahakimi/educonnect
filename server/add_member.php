<?php
  try {
    include 'group.class.php';
    $group = new group();

    if (!isset($_POST['user'])) { throw new Exception("[Error] POST key user not found"); }
    if (!isset($_POST['rowid'])) { throw new Exception("[Error] POST key rowid not found"); }
    if ($_POST['user'] == "") { throw new Exception("[Error] user not assigend"); }
    if ($_POST['rowid'] == "") { throw new Exception("[Error] rowid not assigend"); }
    
    if (!$group->add_member($_POST['user'], $_POST['rowid'])) { throw new Exception($group->error_msg); }

    $response['gui'] = $group->tc_member_list($_POST['rowid']);
    $response['result'] = true;
  } catch (Exception $e) {
    $response['result'] = false;
    $response['reason'] = $e->getMessage();
  }

  print json_encode($response);