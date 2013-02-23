var xhr = new XMLHttpRequest();
xhr.open('GET', 'properties.php');
xhr.onload = function() {
	// map the data
	var jsonProperties = $.parseJSON(xhr.responseText);
	vacantProp = jsonProperties[0];
	console.log(vacantProp); // Works now!
};
xhr.send();