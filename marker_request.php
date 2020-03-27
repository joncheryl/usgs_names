<?php

include_once('connection.php');

$min_elev = $_GET['min_elev'];
$numbs_of_markers = $_GET['numbs_of_markers'];
$class_of_markers = $_GET['class_of_markers'];

$sql = "SELECT * FROM utah_names WHERE feature_class = '" . $class_of_markers . "' AND ELEV_IN_FT > " . $min_elev . " ORDER BY ELEV_IN_FT DESC LIMIT " . $numbs_of_markers;

$result = mysqli_query($mysqli, $sql);

# Build GeoJSON feature collection array
$geojson = array(
    'type'      => 'FeatureCollection',
    'features'  => array()
);

# Loop through rows to build feature arrays
while ($row = mysqli_fetch_assoc($result)){
    $properties = $row;
    # Remove x and y fields from properties (optional)
    #    unset($properties['PRIM_LAT_DEC']);
    #    unset($properties['PRIM_LONG_DEC']);
    $feature = array(
        'type' => 'Feature',
        'geometry' => array(
            'type' => 'Point',
            'coordinates' => array(
                $row['PRIM_LAT_DEC'],
                $row['PRIM_LONG_DEC']
            )
        ),
        'properties' => $properties
    );
    # Add feature arrays to feature collection array
    array_push($geojson['features'], $feature);
}

header('Content-type: application/json');
echo json_encode($geojson, JSON_NUMERIC_CHECK);
$conn = NULL;
    // print_r($geojson);

?>
