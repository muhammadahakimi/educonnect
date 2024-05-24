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
  <title>Homework</title>
  <link rel="stylesheet" href="css/setup.css">
  <script src="jquery/jquery.js"></script>
  <script src="js/setup.js"></script>
  <style>
    body > div {
      padding: 20px;
    }

    button, .btn {
      padding: 7px;
      border-radius: 5px;
      border: 2px solid dodgerblue;
      background: dodgerblue;
      color: white;
    }

    button:hover, .btn:hover {
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
      <label>Create Homework: </label>
      <input id="input_name" autocomplete="off" placeholder="Homework Name">
      <label> Duedate</label>
      <input type="date" id="input_duedate" autocomplete="off">
      <label> File</label>
      <input type="file" id="input_file">
      <button onclick="create()">Create</button>
    </div>
    <br>
    <h3 align="center">Homework List</h3>
    <br>
    <table id="table_homework" class="table2"><?php print $homework->tc_html($_GET['class']); ?></table>
  </div>
</body>
<script>
  var class_rowid = "<?php print $_GET['class']; ?>";

  function create() {
    if ($("#input_name").val() == '') { $("#input_name").focus(); return false; }
    if ($("#input_duedate").val() == '') { $("#input_duedate").focus(); return false; }
    if ($("#input_file").val() == '') { $("#input_file").focus(); return false; }
    var formData = new FormData(); // Create FormData object
    var file = document.getElementById('input_file').files[0];
    formData.append('name', $("#input_name").val());
    formData.append('duedate', $("#input_duedate").val());
    formData.append('class_rowid', class_rowid);
    formData.append('file', file);
    console.table(formData);
    $.ajax({
      url: 'server/create_homework.php',
      type: 'post',
      data: formData,
      processData: false,
      contentType: false,
      dataType: 'JSON',
      success: function (response) {
        console.log(response);
        if (response.result) {
          alert("Succesful");
          $("#table_homework").html(response.gui);
          $("#input_name").val("");
          $("#input_duedate").val("");
          $("#input_file").val("");
        } else {
          alert(response.reason);
        }
      }
    });
  }

  function view_submited_homework(rowid) {
    location.replace("homework_submited.php?class=" + class_rowid + "&homework=" + rowid);
  }
</script>
</html>