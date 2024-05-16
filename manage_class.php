<?php
  include 'server/user.class.php';
  include 'server/classs.class.php';
  $user = new user();
  $class = new classs();

  if (!$user->is_login()) { header("Location: login.php"); }
  if ($user->role != "teacher") { header("Location: access_denied.php"); }
?>
<html>
<head>
  <title>Manage Class</title>
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
    <button onclick="page('index')">&#8592; Home</button>
    <button onclick="page('')">Manage Subject</button>
    <h3 align="center">Class List</h3>
    <table id="table_deactive" class="table1"><?php print $class->tc_list(); ?></table>
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