<?php
  include 'server/user.class.php';
  $user = new user();

  if (!$user->is_login()) { header("Location: login.php"); }
  if ($user->role != "teacher") { header("Location: access_denied.php"); }
?>
<html>
<head>
  <title>Manage User</title>
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
  </style>
</head>
<body>
  <div>
    <button onclick="page('manage_user')">&#8592; Manage User</button>
    <h3 align="center">User Deactive List</h3>
    <table id="table_deactive" class="table2"><?php print $user->tc_deactive_list(); ?></table>
  </div>
</body>
<script>
  function activate_user(rowid) {
    var data = {};
    data['rowid'] = rowid;
    console.table(data);
    $.ajax({
      url: 'server/activate_user.php',
      type: 'post',
      data: data,
      dataType: 'JSON',
      success: function (response) {
        console.log(response);
        if (response.result) {
          alert("Successful");
          $("#table_deactive > tbody").html(response.gui);
        } else {
          alert(response.reason);
        }
      }
    });
  }
</script>
</html>