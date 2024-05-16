<?php
  include 'server/user.class.php';
  $user = new user();

  if (!$user->is_login()) { header("Location: login.php"); }
  $user->set_details();
?>
<html>
<head>
  <title>My Profile</title>
  <link rel="stylesheet" href="css/setup.css">
  <script src="jquery/jquery.js"></script>
  <script src="js/setup.js"></script>
  <style>
    body > div {
      padding: 20px;
    }

    button {
      padding: 7px;
      margin-top: 10px;
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

    #container {
      display: flex;
      width: 500px;
      margin: auto;
      flex-direction: column;
    }
  </style>
</head>
<body>
  <div>
    <button onclick="page('index')">&#8592; Home</button>
    
    <div id="container">
      <h3 align="center">My Profile</h3>
      <label>User ID</label>
      <input id="input_userid" value="<?php print $user->userid; ?>" autocomplete="off">
      <label>Fullname</label>
      <input id="input_fullname" value="<?php print $user->fullname; ?>" autocomplete="off">
      <label>Gender</label>
      <select id="input_gender">
        <option value="male">Male</option>
        <option value="female">Female</option>
      </select>
      <label>IC No</label>
      <input id="input_ic" maxlength="12" value="<?php print $user->ic; ?>" autocomplete="off">
      <label>Birthday</label>
      <input type="date" id="input_birthday" value="<?php print $user->birthday; ?>" autocomplete="off">
      <button onclick="update()">Save Changes</button>

      <br><br>
      <h4>Reset Password</h4>
      <label>New Password</label>
      <input type="password" id="input_password">
      <label>Confirm New Password</label>
      <input type="password" id="input_password_confirm">
      <button onclick="reset_password()">Reset</button>
    </div>
    
  </div>
</body>
<script>
  $("#input_gender").val("<?php print $user->gender; ?>");

  function update() {
    if ($("#input_userid").val() == "") { $("#input_userid").focus(); return false; }
    var data = {};
    data['userid'] = $("#input_userid").val();
    data['fullname'] = $("#input_fullname").val();
    data['gender'] = $("#input_gender").val();
    data['ic'] = $("#input_ic").val();
    data['birthday'] = $("#input_birthday").val();
    console.table(data);
    $.ajax({
      url: 'server/update_profile.php',
      type: 'post',
      data: data,
      dataType: 'JSON',
      success: function (response) {
        console.log(response);
        if (response.result) {
          alert("Successful");
        } else {
          alert(response.reason);
        }
      }
    });
  }

  function reset_password() {
    if ($("#input_password").val() == "") { $("#input_password").focus(); return false; }
    if ($("#input_password_confirm").val() == "") { $("#input_password_confirm").focus(); return false; }
    if ($("#input_password").val() != $("#input_password_confirm").val()) { $("#input_password_confirm").focus(); return false; }

    var data = {};
    data['password'] = $("#input_password").val();
    console.table(data);
    $.ajax({
      url: 'server/reset_password.php',
      type: 'post',
      data: data,
      dataType: 'JSON',
      success: function (response) {
        console.log(response);
        if (response.result) {
          alert("Successful");
        } else {
          alert(response.reason);
        }
      }
    });
  }
</script>
</html>