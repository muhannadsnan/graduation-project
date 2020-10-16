<!DOCTYPE html>
<html>
<head>
<script src="http://maps.googleapis.com/maps/api/js"></script>
<script>
function initialize() { // Draws map for our location
  var mapProp = {
    center:new google.maps.LatLng(33.5074755,36.2828954),
    zoom:10,
    mapTypeId:google.maps.MapTypeId.HYBRID
  };
  var map=new google.maps.Map(document.getElementById("googleMap"),mapProp);
}
google.maps.event.addDomListener(window, 'load', initialize);
</script>
</head>

<body>
<div id="googleMap" style="width:600px;height:600px;margin:auto;"></div>
</body>

</html>