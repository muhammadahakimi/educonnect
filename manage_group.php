<?php
  include 'server/user.class.php';
  include 'server/group.class.php';
  $user = new user();
  $group = new group();

  if (!isset($_GET['rowid'])) { header("Location: group.php?alert=not_found"); }
  if ($_GET['rowid'] == "" || !is_numeric($_GET['rowid'])) { header("Location: group.php?alert=not_found"); }

  if (!$user->is_login()) { header("Location: login.php"); }
  if ($user->role != "teacher") { header("Location: access_denied.php"); }
?>
<html>
<head>
  <title>Manage Group</title>
  <link rel="stylesheet" href="css/setup.css">
  <script src="jquery/jquery.js"></script>
  <script src="js/setup.js"></script>
  <style>
    body > div {
      padding: 20px;
    }

    button {
      padding: 7px;
      border-radius: 5px;
      border: 2px solid dodgerblue;
      background: dodgerblue;
      color: white;
    }

    button:hover {
      background: white;
      color: dodgerblue;
    }

    label {
      font-size: 14px;
      margin-top: 10px;
    }

    select {
      padding: 7px;
      border-radius: 5px;
      border: 1px solid gray;
    }

    .btn_remove {
      border: 2px solid red;
      background: red;
      color: white;
    }

    .btn_remove:hover {
      background: white;
      color: red;
    }
  </style>
</head>
<body>
  <div>
    <button onclick="page('group')">&#8592; Group</button>
    <br><br>
    <div>
      <label>Add Group Member</label>
      <select id="input_user"><?php print $user->option_user(); ?></select>
      <button onclick="add_member()">Add</button>
    </div>
    <h3 align="center">Class Member List</h3>
    <table id="table_group" class="table2"><?php print $group->tc_member_list($_GET['rowid']); ?></table>
  </div>
</body>
<script>
  var group_rowid = "<?php print $_GET['rowid']; ?>";

  function open_chat(target, to, name) {
    location.replace("chat.php?target="+target+"&to="+to+"&name="+name);
  }
  
  function add_member() {
    if ($("#input_user").val() == "") { $("#input_user").focus(); return false; }
    var data = {};
    data['rowid'] = group_rowid;
    data['user'] = $("#input_user").val();
    console.table(data);
    $.ajax({
      url: 'server/add_member.php',
      type: 'post',
      data: data,
      dataType: 'JSON',
      success: function (response) {
        console.log(response);
        if (response.result) {
          alert("Successful");
          $("#table_group > tbody").html(response.gui);
        } else {
          alert(response.reason);
        }
      }
    });
  }

  function remove_member(rowid) {
    var data = {};
    data['group_rowid'] = group_rowid;
    data['rowid'] = rowid;
    console.table(data);
    $.ajax({
      url: 'server/remove_member.php',
      type: 'post',
      data: data,
      dataType: 'JSON',
      success: function (response) {
        console.log(response);
        if (response.result) {
          alert("Successful");
          $("#table_group > tbody").html(response.gui);
        } else {
          alert(response.reason);
        }
      }
    });
  }
</script>
</html>