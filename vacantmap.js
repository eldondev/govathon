var xhr = new XMLHttpRequest();
xhr.open('GET', 'vacant.json');
xhr.onload = function() {
	// map the data
	var response = $.parseJSON(xhr.responseText);
	vacantProp = response.per.mapRS[0];
	console.log(vacantProp); // Works now!
};

// if (tests.progress) {
//   xhr.upload.onprogress = function (event) {
//     if (event.lengthComputable) {
//       var complete = (event.loaded / event.total * 100 | 0);
//       progress.value = progress.innerHTML = complete;
//     }
//   }
// }

xhr.send();