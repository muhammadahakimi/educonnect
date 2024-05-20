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

    .btn_remove {
      border: 2px solid red;
      background: red;
      color: white;
    }

    .btn_remove:hover {
      background: white;
      color: red;
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
  </style>
</head>
<body>
  <div>
    <button onclick="page('index')">&#8592; Home</button>
    <br><br>
    <div>
      <label>Invite Student: </label>
      <select id="input_student"><?php print $user->option_student(); ?></select>
      <button onclick="add_class_student()">Invite</button>
    </div>
    <h3 align="center">Student List</h3>
    <table id="table_student" class="table2"><?php print $class->tc_student_list($_GET['class_rowid']); ?></table>
  </div>
</body>
<script>
  var rowid = "<?php print$_GET['class_rowid']; ?>";

  function add_class_student() {
    if ($("#input_student").val() == "") { $("#input_student").focus(); return false; }
    var data = {};
    data['rowid'] = rowid;
    data['student_rowid'] = $("#input_student").val();
    console.table(data);
    $.ajax({
      url: 'server/add_class_student.php',
      type: 'post',
      data: data,
      dataType: 'JSON',
      success: function (response) {
        console.log(response);
        if (response.result) {
          alert("Successful");
          $("#table_student > tbody").html(response.gui);
        } else {
          alert(response.reason);
        }
      }
    });
  }

  function remove_class_student(class_student_rowid) {
    var data = {};
    data['rowid'] = rowid;
    data['class_student_rowid'] = class_student_rowid;
    console.table(data);
    $.ajax({
      url: 'server/remove_class_student.php',
      type: 'post',
      data: data,
      dataType: 'JSON',
      success: function (response) {
        console.log(response);
        if (response.result) {
          alert("Successful");
          $("#table_student > tbody").html(response.gui);
        } else {
          alert(response.reason);
        }
      }
    });
  }
</script>
</html>