<?php
session_start();
require_once("../db/mysqlcon.php");
@$_SESSION['UID']="";
session_destroy();
@header("location:../common/index.php");
?>