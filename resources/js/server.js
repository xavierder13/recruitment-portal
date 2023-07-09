let app = require('express')();
let http = require('http').Server(app);
const io = require("socket.io")(http, { cors: { origin: '*' } });

http.listen(3472, () => {
  console.log('Listening on port *: 3472');
});

io.on("connection", socket => {

  socket.on('sendData', function (data) { 
    io.emit('sendData', data);
    console.log(data); 
  }); // listen to the event

});