<!DOCTYPE html>
<html>
  <head>

    <title> USGS Names Database Interface </title>

    <!--- references for Leaflet library and jQuery -->
    
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.6.0/dist/leaflet.css" integrity="sha512-xwE/Az9zrjBIphAcBb3F6JVqxf46+CDLwfLMHloNu6KEQCAWi6HcDUbeOfBIptF7tcCzusKFjFw2yuvEpDL9wQ==" crossorigin=""/>

    <script src="https://unpkg.com/leaflet@1.6.0/dist/leaflet.js" integrity="sha512-gZwIG9x3wUXg2hdXF6+rVkLF/0Vi9U8D2Ntg4Ga5I5BZpVkVxlJWbSQtXPSiUTtC0TjtGOmxa1AJPuV0CPthew==" crossorigin=""></script>

    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>

    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>

    <script>

      $(document).ready(function() {
	  $("#how").change(function() {

	      $("#state").empty();
	      $("#county").empty();

	      var selection_style = $("#how").val()

	      switch(selection_style) {
	      case "withinnation":
		  $("#state").append("<option>option not needed</option>");
		  $("#county").append("<option>option not needed</option>");
		  break;
	      case "withinstate":
		  $("#state").load("http://gussies.website/states.php");
		  $("#county").append("<option>option not needed</option>");
		  break;
	      case "withincounty":
		  $("#state").load("http://gussies.website/states.php");
		  $("#county").append("<option>select a state</option>");
		  break;
	      case "bystate":
		  $("#state").append("<option>option not needed</option>");
		  $("#county").append("<option>option not needed</option>");
		  break;
	      case "bycounty":
		  $("#state").load("http://gussies.website/states.php");
		  $("#county").append("<option>option not needed</option>");
		  break;
	      }	  
	  });

	  $("#state").change(function() {
	      if ($("#how").val() == "withincounty") {
		  $("#county").load("http://gussies.website/counties.php?choice=" + $("#state").val());
	      }
	  });
			     
      });
      
    </script>
    
  </head>

  <style>

    body{
	padding: 0;
	margin: 0;
    }
    .container{
	display: flex;
	height: 100vh;
    }
    .sidenav{
	width: 220px;
	padding: 10px;
    }
    .flex-item{
	flex-grow: 1;
    }
    
  </style>

  <body>

    <div class="container">
      <div class="sidenav">
	<h1>What are the highest things?</h1>
	You can select diffent ways to search. For example, search for the highest stuff within the country or search for the highest stuff within each county for a chosen state. The dataset is <a href="https://www.usgs.gov/core-science-systems/ngp/board-on-geographic-names/download-gnis-data">here</a>.
	<br>
	<hr>

  	How do you want to search?<br>
	
	<form>
	  <select name='how' id='how'>
	    <option value='withinnation'>Within the US of A</option>
	    <option value='withinstate'>Within a state</option>
	    <option value='withincounty'>Within a county</option>
	    <option value='bystate'>By states</option>
	    <option value='bycounty'>By counties within a state</option>

	  </select>
	  <br><br>

	  State:<br>
	  <select name='state' id='state'>
	    <option>option not needed</option>
	  </select><br>

	  County:<br>
	  <select name='county' id='county'>
	    <option>option not needed</option>
	  </select><br><br>

	  Class of features to display:
	  <select name='feat_class'>
	    <!-- get rid of this php -->
	    <?php
	     
	     include_once('connection.php');
	     $sql = mysqli_query($mysqli, "SELECT DISTINCT feature_class FROM features;");
	     while ($row = $sql->fetch_assoc()){
	    echo "<option value='" . $row['feature_class'] . "'>" . $row['feature_class'] . "</option>"; 
	    }

	    ?>
	  </select>
	  <br><br>
	  
	  # of markers per grouping:
	  <input type="text" name="num_of_markers" value="1">
	  <br><br>

	  <input type="submit" value="Fetch things" name="class" id="sub_button">
	</form>

	<script>
	  //
	  // get the markers we want
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
	      var how_choice = first_form.elements.how.value;
	      var state_choice = first_form.elements.state.value;
	      var county_choice = first_form.elements.county.value;
	      var number_of_markers = first_form.elements.num_of_markers.value;
	      var class_of_markers = first_form.elements.feat_class.value;

	      // setup map for new markers
	      mymap.removeLayer(group);
	      event.preventDefault(); // page refreshes otherwise

	      // request and add markers
	      $.getJSON("http://gussies.website/marker_request.php?numbs_of_markers=" + number_of_markers + "&class_of_markers=" + class_of_markers + "&how_choice=" + how_choice + "&state_choice=" + state_choice + "&county_choice=" + county_choice, function (data) {

		  markerArray = [];
		  
		  // build an array of markers
		  $.each(data.features, function(index, d){
		      var mama = new L.marker(d.geometry.coordinates);
		      mama.bindPopup(
			  d.properties.feature_name + '<br>' +
			      d.properties.feature_class + '<br>' +
			      d.properties.elev_in_ft + ' ft<br>' +
			      d.properties.county_name + ' County'
		      );
		      markerArray.push(mama);
		  });

		  // add the array to the map
		  group = L.featureGroup(markerArray).addTo(mymap);

		  // zoom to fit markers
		  mymap.fitBounds(group.getBounds(), {maxZoom: 15});
		  
	      });
	  }
	  </script>

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
