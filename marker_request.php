<?php

include_once('connection.php');

// Collection of attributes for query
$min_elev = $_GET['min_elev'];
$numbs_of_markers = $_GET['numbs_of_markers'];
$class_of_markers = $_GET['class_of_markers'];
$how_select = $_GET['how_select'];

if ($how_select == "bystate") {

   // Build query
   $sql = "SELECT * FROM FEATURES" .
   " WHERE FEATURE_CLASS = '" . $class_of_markers . "'" .
   " AND ELEV_IN_FT > " . $min_elev .
   " ORDER BY ELEV_IN_FT DESC" .
   " LIMIT " . $numbs_of_markers . ";" ;

}
if ($how_select == "bycounty") {

$sql = "SELECT * FROM (SELECT row_number() over (PARTITION by COUNTY_NUMERIC ORDER BY ELEV_IN_FT desc) as topn, " . 
     "FEATURE_NAME, FEATURE_CLASS, COUNTY_NAME, COUNTY_NUMERIC, ELEV_IN_FT, PRIM_LAT_DEC, PRIM_LONG_DEC " .
     "FROM FEATURES " .
     "WHERE FEATURE_CLASS ='" . $class_of_markers . "') as x " .
     "WHERE (x.topn <= " . $numbs_of_markers . ") " .
     "ORDER BY COUNTY_NUMERIC, ELEV_IN_FT;";
}

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
