<?php
  try {
    include 'group.class.php';
    $group = new group();

    if (!isset($_POST['rowid'])) { throw new Exception("[Error] POST key rowid not found"); }
    if (!isset($_POST['group_rowid'])) { throw new Exception("[Error] POST key group_rowid not found"); }
    if ($_POST['rowid'] == "") { throw new Exception("[Error] rowid not assigend"); }
    if ($_POST['group_rowid'] == "") { throw new Exception("[Error] group_rowid not assigend"); }
    
    if (!$group->remove_member($_POST['rowid'])) { throw new Exception($group->error_msg); }

    $response['gui'] = $group->tc_member_list($_POST['group_rowid']);
    $response['result'] = true;
  } catch (Exception $e) {
    $response['result'] = false;
    $response['reason'] = $e->getMessage();
  }

  print json_encode($response);