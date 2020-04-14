<?php

include_once('connection.php');

// Collection of attributes for query
$numbs_of_markers = $_GET['numbs_of_markers'];
$class_of_markers = $_GET['class_of_markers'];
$how_choice = $_GET['how_choice'];
$state_choice = $_GET['state_choice'];
$county_choice = $_GET['county_choice'];

if ($how_choice == "withinnation")
{
   $sql = "SELECT * FROM features " .
   "WHERE feature_class = '" . $class_of_markers . "' " .
   "ORDER BY elev_in_ft DESC " .
   "LIMIT " . $numbs_of_markers . ";" ;

//$sql = "SELECT * FROM features WHERE feature_class = 'Summit' AND elev_in_ft > 19000 ORDER BY elev_in_ft DESC LIMIT 20";
}
elseif ($how_choice == "withinstate")
{
   $sql = "SELECT * " .
   	  "FROM features " .
 	  "WHERE feature_class = '" . $class_of_markers . "' " .
	  "AND state_alpha LIKE '" . $state_choice . "' " .
	  "ORDER BY elev_in_ft DESC " .
	  "LIMIT " . $numbs_of_markers . ";" ;
}
elseif ($how_choice == "withincounty")
{
   $sql = "SELECT * " .
   	  "FROM features " .
 	  "WHERE feature_class = '" . $class_of_markers . "' " .
	  "AND state_alpha LIKE '" . $state_choice . "' " .
	  "AND county_name LIKE '" . $county_choice . "' " .
	  "ORDER BY elev_in_ft DESC " .
	  "LIMIT " . $numbs_of_markers . ";" ;
}
elseif ($how_choice == "bystate")
{
   $sql = "SELECT * FROM (SELECT ROW_NUMBER() over (PARTITION by state_alpha order BY elev_in_ft DESC) AS topn, " . 
     "feature_name, feature_class, county_name, state_alpha, elev_in_ft, prim_lat_dec, prim_long_dec " .
     "FROM features " .
     "WHERE feature_class ='" . $class_of_markers . "') AS x " .
     "WHERE (x.topn <= " . $numbs_of_markers . ") " .
     "ORDER BY state_alpha, elev_in_ft;";
}
elseif ($how_choice == "bycounty")
{
   $sql = "SELECT * FROM (SELECT ROW_NUMBER() over (PARTITION by county_numeric order BY elev_in_ft DESC) AS topn, " . 
     "feature_name, feature_class, county_name, county_numeric, elev_in_ft, prim_lat_dec, prim_long_dec " .
     "FROM features " .
     "WHERE feature_class ='" . $class_of_markers . "' " .
     "AND state_alpha='" . $state_choice . "') AS x " .
     "WHERE (x.topn <= " . $numbs_of_markers . ") " .
     "ORDER BY county_numeric, elev_in_ft;";
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
