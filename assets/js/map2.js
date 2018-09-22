function MapViewModel() {
    var self = this;
    
    self.showMap = function() {
        window.loadGoogleMaps();
    };
}

ViewModel = {
    Map: new MapViewModel()
};

ko.applyBindings(ViewModel);

var gMapsLoaded = false;

window.gMapsCallback = function () {
    gMapsLoaded = true;
    $(window).trigger('gMapsLoaded');
}

window.loadGoogleMaps = function () {
    if (gMapsLoaded) return window.gMapsCallback();
    var script_tag = document.createElement('script');
    script_tag.setAttribute("type", "text/javascript");
    script_tag.setAttribute("src", "http://maps.google.com/maps/api/js?sensor=false&callback=gMapsCallback");
    if (script_tag != null) {
        (document.getElementsByTagName("head")[0] || document.documentElement).appendChild(script_tag);
    }
}

var address = "Blumenau";

window.initialize = function () {
    geocoder = new google.maps.Geocoder();
    var mapDiv = document.getElementsByClassName("map_canvas")[0];
    var latlng = new google.maps.LatLng(-34.397, 150.644);
    var myOptions = {
        zoom: 15,
        center: latlng,
        mapTypeControl: true,
        mapTypeControlOptions:
        { style: google.maps.MapTypeControlStyle.DROPDOWN_MENU },
        navigationControl: true,
        mapTypeId: google.maps.MapTypeId.ROADMAP
    };
    
    map = new google.maps.Map(mapDiv, myOptions);
    
    if (geocoder) {
        geocoder.geocode({ 'address': address }, function (results, status) {
            if (status == google.maps.GeocoderStatus.OK) {
                if (status != google.maps.GeocoderStatus.ZERO_RESULTS) {
                    map.setCenter(results[0].geometry.location);
                    
                    var infowindow = new google.maps.InfoWindow(
                        {
                            content: '<b>' + address + '</b>',
                            size: new google.maps.Size(150, 50)
                        });
                    
                    var marker = new google.maps.Marker({
                        position: results[0].geometry.location,
                        map: map,
                        title: address
                    });
                    
                    google.maps.event.addListener(marker, 'click', function () {
                        infowindow.open(map, marker);
                    });
                    
                } else {
                    alert("No results found");
                }
            } else {
                alert("Geocode was not successful for the following reason: " + status);
            }
        });
    }
};

$(window).on('gMapsLoaded', initialize);