<?php

include_once('connection.php');

// Collection of attributes for query
$min_elev = $_GET['min_elev'];
$numbs_of_markers = $_GET['numbs_of_markers'];
$class_of_markers = $_GET['class_of_markers'];

// Build query
$sql = "SELECT * FROM FEATURES" .
     " WHERE FEATURE_CLASS = '" . $class_of_markers . "'" .
     " AND ELEV_IN_FT > " . $min_elev .
     " ORDER BY ELEV_IN_FT DESC" .
     " LIMIT " . $numbs_of_markers . ";" ;

// Ask the database
$result = mysqli_query($mysqli, $sql);

//
// Build GeoJSON feature collection array
//

$geojson = array(
    'type'      => 'FeatureCollection',
    'features'  => array()
);

// Loop through results to add features to feature collection
while ($row = mysqli_fetch_assoc($result)){
    $feature = array(
        'type' => 'Feature',
        'geometry' => array(
            'type' => 'Point',
            'coordinates' => array(
                $row['PRIM_LAT_DEC'],
                $row['PRIM_LONG_DEC']
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
