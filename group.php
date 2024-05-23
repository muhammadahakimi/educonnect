<?php
  include 'server/user.class.php';
  include 'server/group.class.php';
  $user = new user();
  $group = new group();

  if (!$user->is_login()) { header("Location: login.php"); }
  if ($user->role != "teacher") { header("Location: access_denied.php"); }
?>
<html>
<head>
  <title>Group</title>
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

    input {
      padding: 7px;
      border-radius: 5px;
      border: 1px solid gray;
    }
  </style>
</head>
<body>
  <div>
    <button onclick="page('index')">&#8592; Home</button>
    <br><br>
    <div>
      <label>Create Group</label>
      <input id="input_name">
      <button onclick="create()">Create</button>
    </div>
    <h3 align="center">Class List</h3>
    <table id="table_group" class="table2"><?php print $group->tc_list(); ?></table>
  </div>
</body>
<script>
  function open_chat(target, to, name) {
    location.replace("chat.php?target="+target+"&to="+to+"&name="+name);
  }

  function manage_group(rowid) {
    location.replace("manage_group.php?rowid=" + rowid);
  }
  
  function create(rowid) {
    if ($("#input_name").val() == "") { $("#input_name").focus(); return false; }
    var data = {};
    data['name'] = $("#input_name").val();
    console.table(data);
    $.ajax({
      url: 'server/create_group.php',
      type: 'post',
      data: data,
      dataType: 'JSON',
      success: function (response) {
        console.log(response);
        if (response.result) {
          alert("Successful");
          $("#table_group > tbody").html(response.gui);
          $("#input_name").val("");
        } else {
          alert(response.reason);
        }
      }
    });
  }
</script>
</html>