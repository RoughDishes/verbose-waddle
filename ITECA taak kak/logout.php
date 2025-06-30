<?php
session_start(); 

$_SESSION = array();

session_destroy();

header("Location: Trade4US.html");
exit();
?>