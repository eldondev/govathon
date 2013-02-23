var xhr = new XMLHttpRequest();
xhr.open('GET', 'vacant.json');
xhr.onload = function() {
	// map the data
	// var response = $.parseJSON(xhr.responseText);
	console.log(xhr.responseText); // Works now!
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