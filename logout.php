<?php

session_start();
$_SESSION['login'] = null;
header("Location: ../register.php");
exit();