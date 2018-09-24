class Device {
    // Constructor
    constructor(map, positions, mapProp, name, id) {
        this.map = map || null;
        this.positions = positions || null;
        this.mapProp = mapProp || null;
        this.name = name || null;
        this.id = id || null;
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
setInterval(tiempoReal, 1000);


$('#dispositivos').on('click', 'tr', function () {
    device.loadStatus(mymap.tb_device.row(this).data());
});

$('#dispositivos').on('click', 'tr', function () {
    device.loadStatus(mymap.tb_device.row(this).data());
});


$("#addDeviceModal").click(function(){
    // modal-header     modal-body    modal-footer
    $('.modal-header').html(`
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Agregar nuevo dispositivo</h4>
    `);

    $('.modal-body').html(`
        <div class="input-group deviceinput">
            <span class="input-group-addon" id="basic-addon1">Nombre:</span>
            <input type="text" class="form-control inp_deviceName" placeholder="Nombre" aria-describedby="basic-addon1">
        </div>
        <br>
        <div class="input-group deviceinput">
            <span class="input-group-addon" id="basic-addon1">Identificador:</span>
            <input type="text" class="form-control inp_deviceID" placeholder="Identificador" aria-describedby="basic-addon1">
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-primary" onclick="btn_addDevice()">Agregar</button>
        </div>
    `);


    $(".device-modal-lg").modal("show");
});

function btn_addDevice() {
    mymap.id = $('.inp_deviceName').val();    
    mymap.name = $('.inp_deviceID').val();
    
    $(".device-modal-lg").modal("hide");
    $.ajax({
        type: "POST",
        url: "class/device.php",
        data: {
            action: "Create"
        }
    })
        .done(function (e) {
            mymap.drawMarcadores(e);
            mymap.drawPanel(e);
        })
        .fail(function (e) {
            // alert("Error", "No se pudo cargar!");
        });
}


