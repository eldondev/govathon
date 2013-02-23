var io = require('socket.io').listen(8080);
var mysql      = require('mysql');
var connection = mysql.createConnection({
  host     : 'localhost',
  user     : 'root',
  password : 'hello',
  database : 'teamvacant'
});

connection.connect();
var lastid = 0;
querfun = function() {
connection.query('select * from properties where address is not null order by id desc limit 1;', function(err, rows, fields) {
	if(rows[0].id == lastid) {
		return;
	}
	lastid = rows[0].id;
	console.log(rows);
	io.sockets.emit('news', rows[0]);
});
};

setInterval(querfun, 1000);
io.sockets.on('connection', function (socket) {
  socket.on('my other event', function (data) {
    console.log(data);
  });
});
