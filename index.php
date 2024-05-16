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
  <button onclick="page('logout')">Logout</button>
  <button onclick="page('my_profile')">My Profile</button>
  <button onclick="page('manage_user')">Manage User</button>
  <button onclick="page('heir')">Heir</button>
  <button onclick="page('activate_user')">Activate User</button>
  <button onclick="page('subject')">Subject</button>
  <button onclick="page('class')">Class</button>
  <button onclick="page('exam')">Exam</button>
</body>
<script>
  

</script>
</html>