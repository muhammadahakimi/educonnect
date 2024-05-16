<?php
include 'server/user.class.php';

$user = new user();
$user->logout();

header("Location: login.php");