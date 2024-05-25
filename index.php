<?php
  include 'server/user.class.php';
  $user = new user();

  if (!$user->is_login()) { header("Location: login.php"); }
?>
<html>
<head>
  <title>Home</title>
  <link rel="stylesheet" href="css/setup.css">
  <script src="jquery/jquery.js"></script>
  <script src="js/setup.js"></script>
  <style>
    body {
      display: flex;
      gap: 10px;
      padding: 50px;
    }
    button {
      display: block;
      padding: 20px;
      font-size: 20px;
      height: fit-content;
      border-radius: 5px;
      border: 2px solid dodgerblue;
      background: dodgerblue;
      color: white;
    }

    button:hover {
      background: white;
      color: dodgerblue;
    }

    img {
      display: block;
      width: 100%;
    }
  </style>
</head>
<body>
  <h1>EduConnect</h1>
  <button onclick="page('logout')">Logout</button>
  <button onclick="page('my_profile')">My Profile</button>
  <button onclick="page('manage_user')">Manage User</button>
  <button onclick="page('class')">Class</button>
  <button onclick="page('result_exam')">Result Exam</button>
  <button onclick="page('group')">Group</button>
  <?php if ($user->role == "student") { print "<button onclick=\"page('my_homework')\">My Homework</button>"; } ?>
  <button onclick="page('chat')">Chat</button>
  <img src="resources/images/background.jpg">
</body>
<script>
  

</script>
</html>