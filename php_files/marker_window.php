<?php

include_once('connection.php');

// Collection of attributes for query
$numbs_of_markers = $_GET['markerN'];
$north = $_GET['north'];
$south = $_GET['south'];
$east = $_GET['east'];
$west = $_GET['west'];

$class_of_markers = ($class_of_markers == "(any)" ?  "%" : $class_of_markers);

$sql = "SELECT * FROM features " .
     " WHERE prim_lat_dec < " . $north .
     " AND prim_lat_dec > " . $south .
     " AND prim_long_dec < " . $east .
     " AND prim_long_dec > " . $west .
     " ORDER BY elev_in_ft DESC " .
     " LIMIT " . $numbs_of_markers . ";" ;

// Ask the database
$result = mysqli_query($mysqli, $sql);

$sql_two = "SELECT COUNT(*) OVER() FROM features " .
     " WHERE prim_lat_dec < " . $north .
     " AND prim_lat_dec > " . $south .
     " AND prim_long_dec < " . $east .
     " AND prim_long_dec > " . $west . ";" ;

$result_two = mysqli_query($mysqli, $sql_two);

//
// Build GeoJSON feature collection array
//

$geojson = array(
    'type'      => 'FeatureCollection',
    'window_N'  => mysqli_fetch_row($result_two),
    'features'  => array()
);

// Loop through results to add features to feature collection
while ($row = mysqli_fetch_assoc($result)){
    $feature = array(
        'type' => 'Feature',
        'geometry' => array(
            'type' => 'Point',
            'coordinates' => array(
                $row['prim_lat_dec'],
                $row['prim_long_dec']
            )
        ),
            'properties' => $row
    );
    // Add feature arrays to feature collection array
    array_push($geojson['features'], $feature);
}

// Return results
header('Content-type: application/json');

echo json_encode($geojson, JSON_NUMERIC_CHECK);
$conn = NULL;

?>
