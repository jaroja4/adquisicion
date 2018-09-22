class MyMap {
    // Constructor
    constructor(map, positions, mapProp) {
        this.map = map || null;
        this.positions = positions || null;
        this.mapProp = mapProp || null;
    };

    loadmyMap() {
        this.mapProp = {
            center: new google.maps.LatLng(9.936516, -84.098354),
            zoom: 15
        };

        this.map = new google.maps.Map(document.getElementById("googleMap"), this.mapProp);
    };
}

let mymap = new MyMap();

$(document).ready(function () {
    mymap.loadmyMap();
    $('#dispositivos').DataTable();
    $('#estados').DataTable();
});

$("#mas").click(function () {

    var marcadores = [
        ['Pozuelo', 9.953127, -84.111272],
        ['Temp: 04C \n Sensor 01', 9.926180, -84.088934],
        ['AV.10', 9.932922, -84.097657]
    ];
    var infowindow = new google.maps.InfoWindow();
    var marker, i;
    for (i = 0; i < marcadores.length; i++) {
        marker = new google.maps.Marker({
            position: new google.maps.LatLng(marcadores[i][1], marcadores[i][2]),
            animation: google.maps.Animation.DROP,
            map: mymap.map
        });

        google.maps.event.addListener(marker, 'click', (function (marker, i) {
            return function () {
                infowindow.setContent(marcadores[i][0]);
                infowindow.open(mymap.map, marker);
                if (marker.getAnimation() !== null) {
                    marker.setAnimation(null);
                } else {
                    marker.setAnimation(google.maps.Animation.BOUNCE);
                }
            }
        })(marker, i));

        var newPoint = new google.maps.LatLng(marcadores[marcadores.length - 1][1],
            marcadores[marcadores.length - 1][2]);
        mymap.map.setCenter(newPoint);
    }


});
