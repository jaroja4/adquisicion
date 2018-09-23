class Device {
    // Constructor
    constructor(map, positions, mapProp) {
        this.map = map || null;
        this.positions = positions || null;
        this.mapProp = mapProp || null;
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
                mymap.drawPanel(e);
            })
            .fail(function (e) {
                // alert("Error", "No se pudo cargar!");
            });
    };

    loadStatus(e) {
        // var e = JSON.parse(e);
        $.ajax({
            type: "POST",
            url: "class/device.php",
            data: {
                action: "LoadStatus",
                obj: JSON.stringify(e)
            }
        })
            .done(function (e) {
                mymap.drawEstado(e);
            })
            .fail(function (e) {
                alert("Error", "No se pudo cargar!");
            });
    }
}

let device = new Device();

$(document).ready(function () {
    device.loadMyDevices();
    // $('#dispositivos').DataTable();
    // $('#estados').DataTable();
});

function tiempoReal() {
    device.loadMyDevices();
}
setInterval(tiempoReal, 300000);

