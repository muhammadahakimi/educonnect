<?php
  include 'server/user.class.php';
  include 'server/exam.class.php';
  $user = new user();
  $exam = new exam();

  if (!$user->is_login()) { header("Location: login.php"); }
?>
<html>
<head>
  <title>Result Exam</title>
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
    <br>
    <h3 align="center">Result List</h3>
    <table id="table_class" class="table2"><?php print $exam->tc_list(); ?></table>
  </div>
</body>
<script>
  
</script>
</html>