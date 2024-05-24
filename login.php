<html>
<head>
  <title>Login</title>
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

    input {
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
    <h3>Login</h3>
    <label>User ID</label>
    <input id="input_userid" autocomplete="off">
    <label>Password</label>
    <input type="password" id="input_password">
    <button onclick="login()">Login</button>
    <button onclick="register()">Register</button>
  </div>
</body>
<script>
  $("#input_userid").focus();
  $("#input_userid").keypress(function (event) {
    if (event.keyCode === 13) {
      $("#input_password").focus();
    }
  });

  $("#input_password").keypress(function (event) {
    if (event.keyCode === 13) {
      login();
    }
  });

  function login() {
    if ($("#input_userid").val() == "") { $("#input_userid").focus(); return false; }
    if ($("#input_password").val() == "") { $("#input_password").focus(); return false; }
    var data = {};
    data['userid'] = $("#input_userid").val();
    data['password'] = $("#input_password").val();
    console.table(data);
    $.ajax({
      url: 'server/login.php',
      type: 'post',
      data: data,
      dataType: 'JSON',
      success: function (response) {
        console.log(response);
        if (response.result) {
          location.replace("index.php");
        } else {
          alert(response.reason);
        }
      }
    });
  }

  function register() {
    location.replace("register.php");
  }
</script>
</html>