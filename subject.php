<?php
  include 'server/user.class.php';
  include 'server/classs.class.php';
  include 'server/subject.class.php';
  $user = new user();
  $class = new classs();
  $subject = new subject();

  if (!$user->is_login()) { header("Location: login.php"); }
  if ($user->role != "teacher") { header("Location: access_denied.php"); }
?>
<html>
<head>
  <title>Subject</title>
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
    <button onclick="page('class')">&#8592; Class</button>
    <br><br>
    <div>
      <label>Create Subject: </label>
      <input id="input_subject" autocomplete="off">
      <button onclick="create()">Create</button>
    </div>
    <h3 align="center">Subject List</h3>
    <table id="table_subject" class="table2"><?php print $subject->tc_list(); ?></table>
  </div>
</body>
<script>
  function create() {
    if ($("#input_subject").val() == "") { $("#input_subject").focus(); return false; }
    var data = {};
    data['name'] = $("#input_subject").val();
    console.table(data);
    $.ajax({
      url: 'server/create_subject.php',
      type: 'post',
      data: data,
      dataType: 'JSON',
      success: function (response) {
        console.log(response);
        if (response.result) {
            $("#input_subject").val("");
          alert("Successful");
          $("#table_subject > tbody").html(response.gui);
        } else {
          alert(response.reason);
        }
      }
    });
  }

  function manage_homework(subject_rowid) {
    location.replace("manage_homework.php?subject=" + subject_rowid);
  }

  function manage_exam(subject_rowid) {
    location.replace("manage_exam.php?subject=" + subject_rowid)
  }
</script>
</html>