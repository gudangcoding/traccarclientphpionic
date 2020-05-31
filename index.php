<!DOCTYPE html>
<html> 
<head> 
  <meta http-equiv="content-type" content="text/html; charset=UTF-8" /> 
  <title>Google Maps</title> 
  <script src="http://maps.google.com/maps/api/js?key=AIzaSyCrt5zvXeDb6aUrSqbD39vt9oJfAamY1nI"></script>
  <script src="jquery.js"></script>
  <script src="marker-animasi.js"></script>
</head> 
<body>
  <div id="map" style="width: 500px; height: 400px;"></div>

  <script type="text/javascript">
    var numDeltas = 100;
    var delay = 10; //milliseconds
    var i = 0;
    var deltaLat;
    var deltaLng;

    function transition(result){
        i = 0;
        deltaLat = (result[0] - position[0])/numDeltas;
        deltaLng = (result[1] - position[1])/numDeltas;
        moveMarker();
    }

    function moveMarker(){
        position[0] += deltaLat;
        position[1] += deltaLng;
        var latlng = new google.maps.LatLng(position[0], position[1]);
        marker.setTitle("Latitude:"+position[0]+" | Longitude:"+position[1]);
        marker.setPosition(latlng);
        if(i!=numDeltas){
            i++;
            setTimeout(moveMarker, delay);
        }
    }

    var marker, i;
    var map = new google.maps.Map(document.getElementById('map'), {
      zoom: 19,
      center: new google.maps.LatLng(-6.2960674,106.9324442),
      mapTypeId: google.maps.MapTypeId.ROADMAP,
      
    });
    
    google.maps.event.addListener(map, 'click', function (event) {
        marker.animateTo(event.latLng, 500);
    });

    var infowindow = new google.maps.InfoWindow();
        
    $(document).ready(function() {

      $.ajax({
          url: "http://192.168.43.45:8082/api/session",
          //url: "http://demo.traccar.org/api/session",
          dataType: "json",
          type: "POST",
          data: {
              email: "admin",
              password: "admin"
          },
          success: function(sessionResponse){
              console.log(sessionResponse);
              openWebsocket();
          }
      });
    }); //ready

    var openWebsocket = function(){
    var socket;
    socket = new WebSocket('ws://192.168.43.45:8082/api/socket');

    socket.onclose = function (event) {
        console.log("WebSocket closed");
    };

    socket.onmessage = function (event) {
        var i, j, store, data, array, entity, device, typeKey, alarmKey, text, geofence;
        //console.log(event);
        var data = JSON.parse(event.data);
        
        if (marker ) {
            marker.setMap(null);
        }

        const icon = { // car icon
            path: 'M29.395,0H17.636c-3.117,0-5.643,3.467-5.643,6.584v34.804c0,3.116,2.526,5.644,5.643,5.644h11.759   c3.116,0,5.644-2.527,5.644-5.644V6.584C35.037,3.467,32.511,0,29.395,0z M34.05,14.188v11.665l-2.729,0.351v-4.806L34.05,14.188z    M32.618,10.773c-1.016,3.9-2.219,8.51-2.219,8.51H16.631l-2.222-8.51C14.41,10.773,23.293,7.755,32.618,10.773z M15.741,21.713   v4.492l-2.73-0.349V14.502L15.741,21.713z M13.011,37.938V27.579l2.73,0.343v8.196L13.011,37.938z M14.568,40.882l2.218-3.336   h13.771l2.219,3.336H14.568z M31.321,35.805v-7.872l2.729-0.355v10.048L31.321,35.805',
            scale: 0.6,
            fillColor: "#427af4", //<-- Car Color, you can change it 
            fillOpacity: 1,
            strokeWeight: 1,
            //anchor: new google.maps.Point(0, 5),
            rotation: 270 //<-- Car angle
        };

         
          if (data.positions) {
              for (i = 0; i < data.positions.length; i++) {
                  var position = data.positions[i];
                  //console.log(position); 
                  
                  marker = new google.maps.Marker({
                  position: new google.maps.LatLng(data.positions[i].latitude, data.positions[i].longitude),
                  map: map,
                  icon:icon
                });

                  //markers.push(marker);
                  console.log(position);
                 //transition(result);
                  
                console.log(data.positions[i].latitude+","+data.positions[i].longitude)
                //var newposition = data.positions[i].latitude,data.positions[i].longitude;
                //marker.animateTo(marker, 500);

                google.maps.event.addListener(marker, 'click', (function(marker, i) {
                  return function() {
                    infowindow.setContent('tes');
                    infowindow.open(map, marker);
                    //marker.animateTo(marker, 500);
                  }
                })(marker, i));                       
              }
          }
    };
};



  </script>
</body>
</html>