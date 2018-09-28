class Device {
    // Constructor
    constructor(map, positions, mapProp, nombre, id, imei) {
        this.map = map || null;
        this.positions = positions || null;
        this.mapProp = mapProp || null;
        this.nombre = nombre || null;
        this.id = id || null;
        this.imei = imei || null;
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
                device.drawPanelDevices(e);
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
                device.drawEstado(e);
            })
            .fail(function (e) {
                //alert("Error", "No se pudo cargar!");
            });
    }

    drawPanelDevices(e) {
        var dataDevice = JSON.parse(e);
        this.tb_device = $('#dispositivos').DataTable({
            data: dataDevice,
            destroy: true,
            "paging": false,
            "ordering": false,
            "responsive": true,
            "info": false,
            "searching": false,
            "scrollX": false,
            "scrollY": false,
            "scrollCollapse": true,
            "language": {
                "infoEmpty": "No Existen Elementos",
                "emptyTable": "No Existen Elementos"
            },
            "order": [[1, "desc"]],
            columns: [
                {
                    title: "imei",
                    data: "imei",
                    visible: false
                },
                {
                    title: "Nombre",
                    data: "nombre"
                },
                {
                    title: "Fecha",
                    data: "fecha",
                    "searchable": false
                }
            ]
        });
        $("#dispositivos").css("font-size", 10);
        $("#dispositivos thead th").css("color", "white");
        this.tb_device.draw();


        $('#dispositivos tbody').on('click', 'tr', function () {
            if ($(this).hasClass('selected')) {
                $(this).removeClass('selected');
            }
            else {
                device.tb_device.$('tr.selected').removeClass('selected');
                $(this).addClass('selected');
            }
        });
    };

    drawEstado(e) {
        var dataEstados = JSON.parse(e);
        this.tb_estados = $('#estados').DataTable({
            data: dataEstados,
            destroy: true,
            "paging": false,
            "ordering": false,
            "responsive": true,
            "info": false,
            "searching": false,
            "scrollX": false,
            "scrollY": false,
            "scrollCollapse": true,
            "language": {
                "infoEmpty": "No Existen Estados",
                "emptyTable": "No Existen Estados"
            },
            "order": [[0, "desc"]],
            columns: [
                {
                    title: "Tipo",
                    data: "tipo"
                },
                {
                    title: "Valor",
                    data: "valor"
                }
            ]
        });

        //////////////////////////////////////////////////////////////////////////////
        $("#dispositivos").css("font-size", 10);
        $("#dispositivos thead th").css("color", "white");
        this.tb_estados.draw();
    };
}

let device = new Device();

$(document).ready(function () {
    device.loadMyDevices();
});

function tiempoReal() {
    device.loadMyDevices();
}
setInterval(tiempoReal, 30000);


$('#dispositivos').on('click', 'tr', function () {
    device.loadStatus(device.tb_device.row(this).data());
});


$("#addDeviceModal").click(function () {
    // modal-header     modal-body    modal-footer
    $('.modal-header').html(`
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Agregar nuevo dispositivo</h4>
    `);

    $('.modal-body').html(`
        <div class="input-group deviceinput">
            <span class="input-group-addon" id="basic-addon1">Nombre:&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp</span>
            <input type="text" class="form-control inp_deviceName" placeholder="Nombre" aria-describedby="basic-addon1">
        </div>
        <br>
        <div class="input-group deviceinput">
            <span class="input-group-addon" id="basic-addon1">Identificador:</span>
            <input type="text" class="form-control inp_deviceID" placeholder="IMEI" aria-describedby="basic-addon1">
        </div>
        <br>
        <div class="input-group deviceinput">
            <span class="input-group-addon" id="basic-addon1">Numero Tel.:</span>
            <input type="text" class="form-control inp_deviceNum" placeholder="Numero Tel" aria-describedby="basic-addon1">
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-primary" onclick="btn_addDevice()">Agregar</button>
        </div>
    `);


    $(".device-modal-lg").modal("show");
});

function btn_addDevice() {
    device.id = $('.inp_deviceName').val();
    device.nombre = $('.inp_deviceID').val();
    device.imei = $('.inp_deviceNum').val();

    $(".device-modal-lg").modal("hide");
    $.ajax({
        type: "POST",
        url: "class/device.php",
        data: {
            action: "Create",
            obj: JSON.stringify(device)
        }
    })
        .done(function (e) {
            mymap.drawMarcadores(e);
            mymap.drawStatus(e);
        })
        .fail(function (e) {
            // alert("Error", "No se pudo cargar!");
        });
}


