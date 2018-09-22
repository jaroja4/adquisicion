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
            zoom: 10
        };

        this.map = new google.maps.Map(document.getElementById("googleMap"), this.mapProp);
    };


    loadMyDevices() {
        $.ajax({
            type: "POST",
            url: "class/device.php",
            data: {
                action: "ReadAll"
            }
        })
            .done(function (e) {
                mymap.drawMarcadores(e);
            })
            .fail(function (e) {
                alert("Error", "No se pudo cargar!");
            });
    }
    

    drawMarcadores(e) {
        var Emarcadores = JSON.parse(e);
        var infowindow = new google.maps.InfoWindow();
        var marker;
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

            var newPoint = new google.maps.LatLng(Emarcadores[Emarcadores.length-1].latitud,
                Emarcadores[Emarcadores.length-1].longitud);
            mymap.map.setCenter(newPoint);
        
        });
/*
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
        }*/
    }
}

let mymap = new MyMap();

$(document).ready(function () {
    mymap.loadmyMap();
    mymap.loadMyDevices();
    $('#dispositivos').DataTable();
    $('#estados').DataTable();
});

function tiempoReal()
    {
       mymap.loadMyDevices();
    }
    setInterval(tiempoReal, 10000);

$("#mas").click(function () {

});
