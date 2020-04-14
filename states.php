<?php

include_once('connection.php');

$choice = $_GET['choice'];
	
$query = "SELECT DISTINCT state_alpha FROM features ORDER BY state_alpha asc;";
	
$result = mysqli_query($mysqli, $query);
		
while ($row = mysqli_fetch_assoc($result)) {
      echo "<option>" . $row{'state_alpha'} . "</option>";
}

?>