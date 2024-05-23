<?php
  include 'server/user.class.php';
  include 'server/classs.class.php';
  include 'server/homework.class.php';
  $user = new user();
  $class = new classs();
  $homework = new homework();

  if (!$user->is_login()) { header("Location: login.php"); }
  if ($user->role != "teacher") { header("Location: access_denied.php"); }
?>
<html>
<head>
  <title>Manage Homework</title>
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

    input, select {
      padding: 7px;
      border-radius: 5px;
      border: 1px solid gray;
    }
  </style>
</head>
<body>
  <div>
    <button onclick="page('subject')">&#8592; Subject</button>
    <br><br>
    <div>
      <label>Create Subject: </label>
      <input id="input_name" autocomplete="off" autocomplete="off">
      <label> Date</label>
      <input type="date" id="input_date" autocomplete="off">
      <button onclick="create()">Create</button>
    </div>
    <br>
    <h3 align="center">Homework List</h3>
    <table id="table_homework" class="table2"><?php print $homework->tc_list($_GET['subject']); ?></table>
  </div>
</body>
<script>
  var subject_rowid = "<?php print $_GET['subject'] ?>"; 
  function create() {
    if ($("#input_name").val() == "") { $("#input_name").focus(); return false; }
    if ($("#input_date").val() == "") { $("#input_date").focus(); return false; }
    var data = {};
    data['subject_rowid'] = subject_rowid;
    data['name'] = $("#input_name").val();
    data['date'] = $("#input_date").val();
    console.table(data);
    $.ajax({
      url: 'server/create_exam.php',
      type: 'post',
      data: data,
      dataType: 'JSON',
      success: function (response) {
        console.log(response);
        if (response.result) {
            $("#input_subject").val("");
          alert("Successful");
          $("#table_exam > tbody").html(response.gui);
        } else {
          alert(response.reason);
        }
      }
    });
  }
</script>
</html>