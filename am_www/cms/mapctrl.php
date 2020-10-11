	<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=ABQIAAAA33Kpf88ZOZV3A6GwEOexQRQhqVftajBWhnB0QeoWDTEzejszMhQOBqrAbVBk4Ev54LAvk2T9IS1QLg"  type="text/javascript"></script>
    <script type="text/javascript">
    //<![CDATA[
	var map;
	var last_point_lat="";
	var last_point_lon="";
	var complete_distance=0;
	var txt_mapctrl;
	var map_div;
    function load_map(map_div_id, txt_ctrl, point_lat, point_lng, map_zoom, msgstr) {
    	txt_mapctrl=txt_ctrl;
    	map_div=map_div_id;
      if (GBrowserIsCompatible()) {
        map = new GMap2(document.getElementById(map_div_id), {draggableCursor: 'crosshair', draggingCursor: 'pointer'});
        map.setCenter(new GLatLng(point_lat, point_lng), map_zoom);
        map.disableDoubleClickZoom();
        var mapControl = new GMapTypeControl();
		map.addControl(mapControl);
		map.addControl(new GLargeMapControl());
		map.setMapType(G_HYBRID_MAP);
		/* map.openInfoWindowHtml(map.getCenter(), ); */
		var point = new GLatLng(point_lat, point_lng);
		var markr = new GMarker(point);
		GEvent.addListener(markr, "click", function() {
            // markr.openInfoWindowHtml(msgstr);
        });
   		map.addOverlay(markr);
   		//markr.openInfoWindowHtml(msgstr);
   		GEvent.addListener(map,"dblclick", addMarker); 
      }
    }
    
    function get_map(map_div_id, point_lat, point_lng, map_zoom, msgstr) {
    	map_div=map_div_id;
      if (GBrowserIsCompatible()) {
        map = new GMap2(document.getElementById(map_div_id), {draggableCursor: 'pointer', draggingCursor: 'pointer'});
        map.setCenter(new GLatLng(point_lat, point_lng), map_zoom);
        var mapControl = new GMapTypeControl();
		map.addControl(mapControl);
		map.addControl(new GLargeMapControl());
		map.setMapType(G_HYBRID_MAP);
		/* map.openInfoWindowHtml(map.getCenter(), ); */
		var point = new GLatLng(point_lat, point_lng);
		var markr = new GMarker(point);
		GEvent.addListener(markr, "click", function() {
             markr.openInfoWindowHtml(msgstr);
        });
   		map.addOverlay(markr);
   		markr.openInfoWindowHtml(msgstr); 
      }
    }
    
    function addMarker(overlay, latlng) {
    	map.clearOverlays();
    	var marker = new GMarker(latlng);
    	map.addOverlay(marker);
    	last_point_lat=latlng.lat();
    	last_point_lon=latlng.lng();
    	document.getElementById(txt_mapctrl).value=last_point_lat + "-" + last_point_lon;
    	document.getElementById(map_div).style.innerHTML='';
    	document.getElementById(map_div).style.display='';
    }   
    //]]>
    </script>