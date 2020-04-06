<?php
  $servername = "localhost";
  $username = "gussduvw_joncheryl";
  $password = "iop[jkl;kl;'";
  $dbname = "gussduvw_usgsnames";

  // Create connection

$mysqli = new mysqli($servername, $username, $password, $dbname);

  if (mysqli_connect_errno()) {
printf("Connect failed: %s\n", mysqli_connect_error()); //this will print out the error while connecting to MySQL, if any
  exit();
  }

?>