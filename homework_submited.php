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
    <button onclick="back()">&#8592; Homework List</button>
    <br><br>
    
    <h3 align="center">Submit List</h3>
    <br>
    <table id="table_homework" class="table2"><?php print $homework->tc_submit($_GET['homework']); ?></table>
  </div>
</body>
<script>
  function back() {
    location.replace("homework_list.php?class=<?php print $_GET['class']; ?>");
  }

</script>
</html>