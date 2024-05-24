<?php
  include 'server/user.class.php';
  include 'server/classs.class.php';
  include 'server/subject.class.php';
  $user = new user();
  $class = new classs();
  $subject = new subject();

  if (!$user->is_login()) { header("Location: login.php"); }
?>
<html>
<head>
  <title>Class</title>
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
    <button onclick="page('index')">&#8592; Home</button>
    <button <?php if ($user->role != 'teacher') { print "style='display: none'"; } ?> onclick="page('subject')">Manage Subject</button>
    <br><br>
    <div <?php if ($user->role != 'teacher') { print "style='display: none'"; } ?>>
      <label>Create Class: </label>
      <input id="input_class" autocomplete="off">
      <label>Subject</label>
      <select id="input_subject"><?php print $subject->option(); ?></select>
      <button onclick="create()">Create</button>
    </div>
    <br>
    <h3 align="center">Class List</h3>
    <br>
    <table id="table_class" class="table2"><?php print $class->tc_list(); ?></table>
  </div>
</body>
<script>
  function create() {
    var data = {};
    data['name'] = $("#input_class").val();
    data['subject_rowid'] = $("#input_subject").val();
    console.table(data);
    $.ajax({
      url: 'server/create_class.php',
      type: 'post',
      data: data,
      dataType: 'JSON',
      success: function (response) {
        console.log(response);
        if (response.result) {
          alert("Successful");
          $("#input_class").val("");
          $("#table_class > tbody").html(response.gui);
        } else {
          alert(response.reason);
        }
      }
    });
  }

  function manage_class(class_rowid) {
    location.replace("manage_class.php?class_rowid=" + class_rowid);
  }
  
  function manage_homework(class_rowid) {
    location.replace("homework_list.php?class=" + class_rowid);
  }

  function manage_exam(class_rowid) {
    location.replace("manage_exam.php?class=" + class_rowid)
  }
</script>
</html>