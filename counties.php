<?php

include_once('connection.php');

$choice = $_GET['choice'];
	
$query = "SELECT DISTINCT county_name " .
         "FROM features " .
	 "WHERE state_alpha LIKE '" . $choice . "' " .
	 "ORDER BY county_name asc;";
	
$result = mysqli_query($mysqli, $query);
		
while ($row = mysqli_fetch_assoc($result)) {
      echo "<option>" . $row{'county_name'} . "</option>";
}

?>