class Device {
    // Constructor
    constructor(map, positions, mapProp) {
        this.map = map || null;
        this.positions = positions || null;
        this.mapProp = mapProp || null;
    };

    loadDevice() {
        $.ajax({
            type: "POST",
            url: "class/Device.php",
            data: {
                action: "ReadAll"
            }
        })
        .done(function (e) {
            DrawDevice(e);
        })
        .fail(function (e) {
            showError(e);
        });
    };
}

let device = new Device();

$(document).ready(function () {
    device.loadDevice();
    $('#dispositivos').DataTable();
    $('#estados').DataTable();
});

function tiempoReal()
    {
        alert("ok");
        // var tabla = $.ajax({
        //         url:'tablaConsulta.php',
        //         dataType:'text',
        //         async:false
        // }).responseText;
    document.getElementById("miTabla").innerHTML = tabla;
    }
    setInterval(tiempoReal, 3000);