<?php
  include 'server/user.class.php';
  $user = new user();

  if (!$user->is_login()) { header("Location: login.php"); }
  if ($user->role != "teacher") { header("Location: access_denied.php"); }
?>
<html>
<head>
  <title>Heir</title>
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

    #form {
      display: flex;
      gap: 10px;
      margin-top: 10px;
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
    <button onclick="page('manage_user')">&#8592; Manage User</button>
    <div id="form">
      <label>Student</label>
      <select id="input_student"><?php print $user->option_student(); ?></select> 
      <label>Heir</label>
      <select id="input_heir"><?php print $user->option_heir(); ?></select>
      <label>Type</label>
      <select id="input_type">
        <option value="father">Father</option>
        <option value="mother">Mother</option>
        <option value="sibling">Sibling</option>
        <option value="other">Other</option>
      </select>
      <button onclick="set_heir()">Set Heir</button>
    </div>
    <h3 align="center">Student Heir List</h3>
    <table id="table_relate" class="table2"><?php print $user->tc_user_relate_list(); ?></table>
  </div>
</body>
<script>
  function set_heir() {
    var data = {};
    data['student'] = $("#input_student").val();
    data['heir'] = $("#input_heir").val();
    data['type'] = $("#input_type").val();
    console.table(data);
    $.ajax({
      url: 'server/set_heir.php',
      type: 'post',
      data: data,
      dataType: 'JSON',
      success: function (response) {
        console.log(response);
        if (response.result) {
          alert("Successful");
          $("#table_relate > tbody").html(response.gui);
        } else {
          alert(response.reason);
        }
      }
    });
  }

  function remove_heir(rowid) {
    var data = {};
    data['rowid'] = rowid;
    console.table(data);
    $.ajax({
      url: 'server/remove_heir.php',
      type: 'post',
      data: data,
      dataType: 'JSON',
      success: function (response) {
        console.log(response);
        if (response.result) {
          alert("Successful");
          $("#table_relate > tbody").html(response.gui);
        } else {
          alert(response.reason);
        }
      }
    });
  }
</script>
</html>