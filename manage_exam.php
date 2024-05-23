<?php
  include 'server/user.class.php';
  include 'server/classs.class.php';
  include 'server/exam.class.php';
  $user = new user();
  $class = new classs();
  $exam = new exam();

  if (!$user->is_login()) { header("Location: login.php"); }
  if ($user->role != "teacher") { header("Location: access_denied.php"); }
?>
<html>
<head>
  <title>Manage Exam</title>
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
    <button onclick="page('class')">&#8592; Class</button>
    <br>
    <br>
    <h3 align="center">Exam Student List</h3><br>
    <table id="table_exam" class="table3"><?php print $exam->form_score($_GET['class']); ?></table>
    <br>
    <button onclick="save()">Save Changes</button>
  </div>
</body>
<script>
  var class_rowid = "<?php print $_GET['class'] ?>"; 

  function check() {
    var student_rowids = [];
    var marks = [];
    $('input[name="student_rowid"]').each(function(){
      student_rowids.push($(this).val());
    });
    $('input[name="mark"]').each(function(){
      marks.push($(this).val());
    });

    var list = [];

    for (var i = 0; i < marks.length; i++) {
      list.push({
        "student_rowid": student_rowids[i],
        "mark": marks[i]
      });
    }

    console.table(list);
    return list
  }
  function save() {
    var data = {};
    data['class_rowid'] = class_rowid;
    data['list'] = check();
    console.table(data);
    $.ajax({
      url: 'server/update_exam.php',
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