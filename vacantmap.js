map = new OpenLayers.Map("mapdiv");
map.addLayer(new OpenLayers.Layer.OSM());

var zoom=12;

var markers = new OpenLayers.Layer.Markers( "Markers" );
map.addLayer(markers);

var center = new OpenLayers.LonLat( -84.38798240 ,33.74899540 )
					.transform(
						new OpenLayers.Projection("EPSG:4326"), // transform from WGS 1984
						map.getProjectionObject() // to Spherical Mercator Projection
					);
					
map.setCenter (center, zoom);

var xhr = new XMLHttpRequest();
xhr.open('GET', 'properties.php');
xhr.onload = function() {
	// map the data
	var jsonProperties = $.parseJSON(xhr.responseText);
	vacantProp = jsonProperties[0];
	console.log(vacantProp); // Works now!
	
	$.each(jsonProperties, function(i, item) {
		var lonLat = new OpenLayers.LonLat( item.lon, item.lat )
							.transform(
								new OpenLayers.Projection("EPSG:4326"), // transform from WGS 1984
								map.getProjectionObject() // to Spherical Mercator Projection
							);

		markers.addMarker(new OpenLayers.Marker(lonLat));
	});
};
xhr.send();

// REAL TIME DATA MAP UPDATE
var socket = io.connect('http://ec2-23-23-39-12.compute-1.amazonaws.com:8080/');
socket.on('news', function (data) {
  console.log(data);
  socket.emit('my other event', { my: 'data' });

	var lonLat = new OpenLayers.LonLat( data[0].lon, data[0].lat )
						.transform(
							new OpenLayers.Projection("EPSG:4326"), // transform from WGS 1984
							map.getProjectionObject() // to Spherical Mercator Projection
						);

	markers.addMarker(new OpenLayers.Marker(lonLat));
});