<!DOCTYPE html>
<html>
  <head>

    <title> USGS Names Database Interface </title>

    <!--- references for Leaflet library and jQuery -->
    
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.6.0/dist/leaflet.css" integrity="sha512-xwE/Az9zrjBIphAcBb3F6JVqxf46+CDLwfLMHloNu6KEQCAWi6HcDUbeOfBIptF7tcCzusKFjFw2yuvEpDL9wQ==" crossorigin=""/>

    <script src="https://unpkg.com/leaflet@1.6.0/dist/leaflet.js" integrity="sha512-gZwIG9x3wUXg2hdXF6+rVkLF/0Vi9U8D2Ntg4Ga5I5BZpVkVxlJWbSQtXPSiUTtC0TjtGOmxa1AJPuV0CPthew==" crossorigin=""></script>

    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>

  </head>

  <style>

    .container{
	display: flex;
    }
    .sidenav{
	width: 200px;
    }
    .flex-item{
	flex-grow: 1;
	height: 700px;
    }
    
  </style>

  <body>
    
    <div class="container">
      <div class="sidenav">
	<form>

	  Statewide or by county:
	  <select name='how'>
	    <option value='bystate'>By State</option>
	    <option value='bycounty'>By County</option>
	  </select>
	  <br><br>
	  
	  Elevation (in feet) >:
	  <input type="text" name="elev">
	  <br><br>

	  Max number of markers:
	  <input type="text" name="num_of_markers">
	  <br><br>
	  
	  Class of features to display:
	  <select name='feat_class'>

	    <?php
	     
	     include_once('connection.php');

	     $sql = mysqli_query($mysqli, "SELECT DISTINCT FEATURE_CLASS FROM FEATURES;");

	     while ($row = $sql->fetch_assoc()){
	    
	    echo "<option value='" . $row['FEATURE_CLASS'] . "'>" . $row['FEATURE_CLASS'] . "</option>"; 
	    }

	    ?>

	  </select>
	  <br><br>
	  
	  <input type="submit" value="Submit" name="class" id="sub_button">

	</form>

	<script>
	  //
	  // go get the markers we want
	  //

	  // make submit button do stuff
	  var sub_botton = document.getElementById("sub_botton");
	  sub_button.addEventListener("click", getMarkers, false);

	  // setup marker layer
	  var markerArray = [];
	  var group = L.featureGroup(markerArray);
	  function getMarkers(event){

	      // get info for query
	      var first_form = document.querySelector("form");
	      var how_select = first_form.elements.how.value;
	      var elevation = first_form.elements.elev.value;
	      var number_of_markers = first_form.elements.num_of_markers.value;
	      var class_of_markers = first_form.elements.feat_class.value;

	      // setup map for new markers
	      mymap.removeLayer(group);
	      event.preventDefault(); // page refreshes otherwise

	      // request and add markers
	      $.getJSON("http://gussies.website/marker_request.php?min_elev=" + elevation + "&numbs_of_markers=" + number_of_markers + "&class_of_markers=" + class_of_markers + "&how_select=" + how_select, function (data) {

		  markerArray = [];
		  
		  // build an array of markers
		  $.each(data.features, function(index, d){
		      var mama = new L.marker(d.geometry.coordinates);
		      mama.bindPopup(
			  d.properties.FEATURE_NAME + '<br>' +
			      d.properties.FEATURE_CLASS + '<br>' +
			      d.properties.ELEV_IN_FT + ' ft<br>' +
			      d.properties.COUNTY_NAME + ' County'
		      );
		      markerArray.push(mama);
		  });

		  // add the array to the map
		  group = L.featureGroup(markerArray).addTo(mymap);

		  // zoom to fit markers
		  mymap.fitBounds(group.getBounds());
		  
	      });
	  }
	  </script>

	<br><br> Goal: Create a web interface for querying a MYSQL database of USGS names with results to be displayed via the Leaflet JavaScript library. Currently restricted to only Utah features.
      </div>

      <div class="flex-item" id="mapid">

	<script>

	  //
	  // Build map
	  //
	  
	  // map layer
	  var mymap = L.map('mapid').setView([39.760833, -111.891111], 7);

	  // tile layer
	  var tile_layer = L.tileLayer( 'https://tile.opentopomap.org/{z}/{x}/{y}.png', {
	      attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
	  }).addTo(mymap);

	  </script>

      </div>

    </div>
    
  </body>
  
</html>
