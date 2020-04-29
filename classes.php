<?php

include_once('connection.php');

$state = $_GET['state'];
$county = $_GET['county'];

if (is_null($state))
{
    $query = "SELECT DISTINCT feature_class " .
           "FROM features " .
           "ORDER BY feature_class asc;";
} elseif (is_null($county))
{
    $query = "SELECT DISTINCT feature_class " .
           "FROM features " .
           "WHERE state_alpha LIKE '" . $state . "' " .
           "ORDER BY feature_class asc;";
}
else
{
    $query = "SELECT DISTINCT feature_class " .
           "FROM features " .
           "WHERE state_alpha LIKE '" . $state . "' " .
           "AND county_name LIKE '" . $county . "' " .
           "ORDER BY feature_class asc;";
}

$result = mysqli_query($mysqli, $query);

echo "<option>(any)</option>";

while ($row = mysqli_fetch_assoc($result)) {
    echo "<option>" . $row{'feature_class'} . "</option>";
}

?>
