
// 구글지도
    function mw_google_map(mapid, addr) {
        var geocoder =  new google.maps.Geocoder();
        geocoder.geocode( {'address': addr }, function(results, status) {
            if (status == google.maps.GeocoderStatus.OK) {
                var map = new google.maps.Map(document.getElementById(mapid), {
                    zoom: 15,
                    center: results[0].geometry.location,
                    mapTypeId: google.maps.MapTypeId.ROADMAP
                });
                var marker = new google.maps.Marker({map: map, position: map.getCenter(), title:addr});
                //var infoWindow = new google.maps.InfoWindow({content:addr, position: map.getCenter()}).open(map);
            }
        });
    }

