<?php
  include 'server/user.class.php';
  include 'server/classs.class.php';
  include 'server/homework.class.php';
  $user = new user();
  $class = new classs();
  $homework = new homework();

  if (!$user->is_login()) { header("Location: login.php"); }
  if ($user->role == "teacher") { header("Location: access_denied.php"); }
?>
<html>
<head>
  <title>My Homework</title>
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

    .btn_close {
      padding: 7px;
      border-radius: 5px;
      border: 2px solid tomato;
      background: tomato;
      color: white;
    }

    .btn_close:hover {
      background: white;
      color: tomato;
    }
  </style>
</head>
<body>
  <div>
    <button onclick="page('index')">&#8592; Home</button>
    <br><br>
    <h3 align="center">Homework List</h3>
    <br>
    <table id="table_homework" class="table2"><?php print $homework->tc_my_homework(); ?></table>
  </div>
  <div id="div_submit" class="overlay">
    <div>
      <label> File</label>
      <input type="file" id="input_file">
      <button onclick="submit()">Submit</button>
      <button class="btn_close" onclick="close_submit()">Close</button>
    </div>
  </div>
</body>
<script>
  var homework_rowid = '';
  function submit() {
    if ($("#input_file").val() == '') { $("#input_file").focus(); return false; }
    var formData = new FormData(); // Create FormData object
    var file = document.getElementById('input_file').files[0];
    formData.append('rowid', homework_rowid);
    formData.append('file', file);
    console.table(formData);
    $.ajax({
      url: 'server/submit_homework.php',
      type: 'post',
      data: formData,
      processData: false,
      contentType: false,
      dataType: 'JSON',
      success: function (response) {
        console.log(response);
        if (response.result) {
          $("#table_homework").html(response.gui);
          $("#input_file").val("");
          close_submit();
        } else {
          alert(response.reason);
        }
      }
    });
  }

  function open_submit(rowid) {
    homework_rowid = rowid;
    $("#div_submit").css('display','flex');
  }

  function close_submit() {
    $("#div_submit").css('display','none');
  }

</script>
</html>