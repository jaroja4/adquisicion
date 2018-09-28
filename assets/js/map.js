class MyMap {
    // Constructor
    constructor(map, positions, mapProp, tb_device, tb_estados) {
        this.map = map || null;
        this.positions = positions || null;
        this.mapProp = mapProp || null;
        this.tb_device = tb_device || null;
        this.tb_estados = tb_estados || null;
    };

    loadmyMap() {
        this.mapProp = {
            center: new google.maps.LatLng(9.936516, -84.098354),
            zoom: 16
        };

        this.map = new google.maps.Map(document.getElementById("googleMap"), this.mapProp);
    };

    drawMarcadores(e) {
        // setMapOnAll(null);
        //markers = [];
        var Emarcadores = JSON.parse(e);

        var infowindow = new google.maps.InfoWindow();
        var marker;
        // 1 dejar de mostrar los marcadores
        //setMapOnAll(null);
        //2 eliminar toda referencia a los marcadores antiguos


        $.each(Emarcadores, function (item, value) {
            marker = new google.maps.Marker({
                position: new google.maps.LatLng(value.latitud, value.longitud),
                // animation: google.maps.Animation.DROP,
                map: mymap.map
            });

            google.maps.event.addListener(marker, 'click', (function (marker, item) {
                return function () {
                    infowindow.setContent(value.nombre);
                    infowindow.open(mymap.map, marker);
                    // if (marker.getAnimation() !== null) {
                    //     marker.setAnimation(null);
                    // } else {
                    //     marker.setAnimation(google.maps.Animation.BOUNCE);
                    // }
                }
            })(marker, item));

            var newPoint = new google.maps.LatLng(Emarcadores[Emarcadores.length - 1].latitud,
                Emarcadores[Emarcadores.length - 1].longitud);
            mymap.map.setCenter(newPoint);

        });
    }
}


let mymap = new MyMap();

$(document).ready(function () {
    mymap.loadmyMap();
});
