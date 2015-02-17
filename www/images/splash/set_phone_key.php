<?php

$link = mysql_connect("localhost", "root", "");
mysql_select_db("test", $link);
mysql_query("INSERT INTO `test`.`phone_key` (`regID`, `user`, `OS`) VALUES ('" .$_POST['regID'] ."', '" .$_POST['user'] ."', '" .$_POST['OS'] ."')", $link);
?>