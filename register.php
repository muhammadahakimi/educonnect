<html>
<head>
  <title>Register</title>
  <link rel="stylesheet" href="css/setup.css">
  <script src="jquery/jquery.js"></script>
  <style>
    body {
      display: flex;
      align-items: center;
      justify-content: center;
      background: lightgray;
    }

    body>div {
      display: flex;
      flex-direction: column;
      width: 250px;
      padding: 20px;
      border-radius: 10px;
      background: white;
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

    button {
      margin-top: 10px;
      font-weight: bold;
      padding: 7px;
      border-radius: 5px;
      border: 2px solid dodgerblue;
      color: white;
      background: dodgerblue;
    }

    button:hover {
      color: dodgerblue;
      background: white;
    }

    button:last-child {
      color: dodgerblue;
      background: white;
    }

    button:last-child:hover {
      color: white;
      background: dodgerblue;
    }
  </style>
</head>
<body>
  <div>
    <h3>Register</h3>
    <label>User ID</label>
    <input id="input_userid" autocomplete="off">
    <label>Role</label>
    <select id="input_role">
      <option value="teacher">Teacher</option>
      <option value="student">Student</option>
      <option value="heir">Heir</option>
    </select>
    <label>Password</label>
    <input type="password" id="input_password">
    <label>Confirm Password</label>
    <input type="password" id="input_password_confirm">
    <button onclick="register()">Register</button>
    <button onclick="login()">Login</button>
  </div>
</body>
<script>
  function register() {
    if ($("#input_userid").val() == "") { $("#input_userid").focus(); return false; }
    if ($("#input_role").val() == "") { $("#input_role").focus(); return false; }
    if ($("#input_password").val() == "") { $("#input_password").focus(); return false; }
    if ($("#input_password").val() != $("#input_password_confirm").val()) { $("#input_password_confirm").focus(); return false; }
    console.log('start loading');
    var data = {};
    data['userid'] = $("#input_userid").val();
    data['password'] = $("#input_password").val();
    data['role'] = $("#input_role").val();
    console.table(data);
    $.ajax({
      url: 'server/register.php',
      type: 'post',
      data: data,
      dataType: 'JSON',
      success: function (response) {
        console.log(response);
        if (response.result) {
          alert("Please wait to getting account activation by teacher");
          //location.replace("index.php");
        } else {
          alert(response.reason);
        }
      }
    });
  }

  function login() {
    location.replace("login.php");
  }
</script>
</html>