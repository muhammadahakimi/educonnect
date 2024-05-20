<?php
  include 'server/chat.class.php';
  $chat = new chat();
  $chat->set_lastupdate();
?>
<html>
<head>
  <title>Chat</title>
  <link rel="stylesheet" href="css/setup.css">
  <script src="jquery/jquery.js"></script>
  <script src="js/setup.js"></script>
  <style>
    body {
      background: lightgray;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    body > div {
      display: flex;
      gap: 10px;
      width: 800px;
      height: 80%;
      padding: 20px;
      border-radius: 10px;
      background: white;
    }

    #list {
      border-right: 1px solid black;
      width: 270px;
      padding-right: 20px; 
    }

    #list > div > div {
      display: flex;
      gap: 10px;
      padding: 10px 0px;
      border-top: 1px solid lightgray;
      border-bottom: 1px solid lightgray;
    }

    #list > div > div:hover {
      background: #e4e4e4;
    }

    #list > div > div > img {
      height: 50px;
      border-radius: 50%;
      
    }

    #list > div > div > div {
      flex: 1;
      display: flex;
      flex-direction: column;
      justify-content: center;
      gap: 5px;
    }

    #list > div > div > div > p {
      margin: 0px;
    }

    #list > div > div > div > p:last-child {
      font-size: 14px;
      text-overflow: hide;
    }

     #list > div > div > p {
      font-weight: bold;
      font-size: 14px;
      color: blue;
     }

    #box {
      flex: 1;
      display: flex;
      flex-direction: column;
    }

    #chat {
      flex: 1;
      display: flex;
      gap: 10px;
      flex-direction: column;
    }

    #input {
      display: flex;
      gap: 5px;
      margin: 5px;
      padding: 5px 13px;
      border: 1px solid black;
      border-radius: 18px;;
    }

    #input_text {
      font-size: 16px;
      flex: 1;
      border: none
    }

    #input_text:focus {
      outline: none;
    }

    #input > img {
      opacity: 0.6;
    }

    #input > img:hover {
      opacity: 1;
    }

    #chat {
      overflow-y: auto;
      padding: 20px 0px;
      padding-right: 10px;
    }

    .chat_you {
      display: block;
      width: fit-content;
      max-width: 90%;
      padding: 7px;
      background: lightblue;
      border-radius: 5px;
      margin-left: auto;
    }

    .chat_you > p {
      text-align: right;
      margin: 0px;
    }

    .chat_you > p:nth-child(1) {
      font-size: 12px;
    }

    .chat_you > p:nth-child(2) {
      font-size: 14px;
    }

    .chat_other {
      width: fit-content;
      width: 90%;
      padding: 7px;
      background: lightgray;
      border-radius: 5px;
    }

    .chat_other > p {
      margin: 0px;
    }

    .chat_other > p:nth-child(1) {
      font-size: 12px;
    }

    .chat_other > p:nth-child(2) {
      font-size: 14px;
    }

    #details {
      background: skyblue;
      padding: 10px;
    }

    #details > p {
      margin: 0px;
    }
  </style>
</head>
<body>
  <div>
    <div id="list">
      <div>
        <img src="resources/icons/home.svg" onclick="page('index')">
      </div>
      <div>
        <?php print $chat->html_list(); ?>
      </div>
    </div>
    <div id="box">
      <div id="details">
        <p>Please select contact</p>
      </div>
      <div id="chat">
        <?php print $chat->html_chat_list('user', '3'); ?>
        <!--<div class="chat_you">
          <p>You</p>
          <p>message be here</p>
        </div>
        <div class="chat_you">
          <p>You</p>
          <p>message be here message be here message be here message be here message be here message be here message be here message be here message be here message be here message be here message be here </p>
        </div>
        <div class="chat_other">
          <p>Other</p>
          <p>message be here message be here message be here message be here message be here message be here message be here message be here message be here message be here message be here message be here </p>
        </div>
        <div class="chat_you">
          <p>You</p>
          <p>message be here</p>
        </div>-->
      </div>
      <div id="input">
        <input id="input_text" type="text" autocomplete="off">
        <img src="resources/icons/image.svg">
        <img src="resources/icons/attach.svg">
        <img src="resources/icons/assignment.svg">
        <img src="resources/icons/send.svg" onclick="send_text()">
      </div>
    </div>
  </div>
</body>
<script>
  var lastupdate = "<?php print $chat->lastupdate; ?>";
  var target = '';
  var to = '';
  function open_chat(ptarget, pto, chat_name) {
    $("#details > p").html(chat_name);
    target = ptarget;
    to = pto;
    console.log("set chat target:" + target + " to:" + to);
    var data = {};
    data['target'] = target;
    data['to'] = to;
    console.table(data);
    $.ajax({
      url: 'server/gui_chat.php',
      type: 'post',
      data: data,
      dataType: 'JSON',
      success: function (response) {
        console.log(response);
        if (response.result) {
          $("#chat").html(response.gui);
        } else {
          alert(response.reason);
        }
      }
    });
  }
  function send_text() {
    if ($("#input_text").val() == '') { $("#input_text").focus(); return false; }
    if (target == '' && to == '') { alert("Please select contact"); return false;}
    var data = {};
    data['target'] = target;
    data['to'] = to;
    data['type'] = 'text';
    data['data'] = $("#input_text").val();
    console.table(data);
    $.ajax({
      url: 'server/send_chat.php',
      type: 'post',
      data: data,
      dataType: 'JSON',
      success: function (response) {
        console.log(response);
        if (response.result) {
          //$("#table_deactive > tbody").html(response.gui);
          $("#input_text").val("");
        } else {
          alert(response.reason);
        }
      }
    });
  }
</script>
</html>