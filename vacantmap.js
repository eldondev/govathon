var xhr = new XMLHttpRequest();
xhr.open('GET', 'vacant.json');
xhr.onload = function() {
	// map the data
	var jsonProperties = $.parseJSON(xhr.responseText);
	vacantProp = jsonProperties.per.mapRS[0];
	console.log(vacantProp); // Works now!
};
xhr.send();