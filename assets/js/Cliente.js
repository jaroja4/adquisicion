class Cliente {
    // Constructor
    constructor(id, name, password, tel, active) {
        this.id = id || "";
        this.name = name || "";
        this.password = password || "";
        this.tel = tel || 0;
        this.active = active || 0;
    };

    CargaClientes() {
        $.ajax({
            type: "POST",
            url: "class/Cliente.php",
            data: {
                action: "ReadAll"
            }
        })
            .done(function (e) {
                cliente.drawClientes(e)
            });
    };

    drawClientes(e) {
        var dataClientes = JSON.parse(e);

        this.tb_movimientosCaja = $('#tb_usuarios').DataTable({
            destroy: true,
            data: dataClientes,                               
            "language": {
                "infoEmpty":  "Sin Movimientos Ingresados",
                "emptyTable": "Sin Movimientos Ingresados",
                "search":     "Buscar",
                "zeroRecords": "No hay resultados",
                "lengthMenu":  "Mostar _MENU_ registros",
                "paginate": {
                    "first":   "Primera",
                    "last":    "Ultima",
                    "next":    "Siguiente",
                    "previous":"Anterior"
                }
            },
            "order": [[1, "desc"]],
            columns: [
                {
                    title: "ID Movimientos Caja",
                    data: "id",
                    visible: false
                },
                {
                    title: "Nombre",
                    data: "name"
                },
                {
                    title: "Telefono",
                    data: "tel"
                },
                {
                    title: "Empresa",
                    data: "company"
                },
                {
                    title: "Activo",
                    data: "active",
                    mRender: function ( e ) {
                        var resp="";
                        switch(e) {
                            case "0":
                            resp = '<i class="fa fa-times" style="font-size:25px;color:red;"></i>';
                                break;
                            case "1":
                            resp = '<i class="fa fa-check-square" style="font-size:25px;color:green;"></i>';
                                break;
                            default:
                                resp = "Error";
                        }
                        return resp
                    }
                }
            ]
        });
        cliente.CargaDispositivos();
    };


    CargaDispositivos() {
        $.ajax({
            type: "POST",
            url: "class/device.php",
            data: {
                action: "ReadAll"
            }
        })
            .done(function (e) {
                cliente.drawDispositivos(e)
            });
    };

    drawDispositivos(e) {
        var dataDispositivos = JSON.parse(e);

        var tbl_Dispositivos = $('#tb_dispositivos').DataTable({
            data: dataDispositivos,                               
            "language": {
                "infoEmpty":  "Sin Movimientos Ingresados",
                "emptyTable": "Sin Movimientos Ingresados",
                "search":     "Buscar",
                "zeroRecords": "No hay resultados",
                "lengthMenu":  "Mostar _MENU_ registros",
                "paginate": {
                    "first":   "Primera",
                    "last":    "Ultima",
                    "next":    "Siguiente",
                    "previous":"Anterior"
                }
            },
            "order": [[1, "desc"]],
            columns: [
                {
                    title: "Identificador",
                    data: "id"
                },
                {
                    title: "Nombre",
                    data: "nombre"
                },
                {
                    title: "Fecha",
                    data: "fecha"
                },
                {
                    title: "Estado",
                    data: null,
                    defaultContent: '<div><center><label><input type="checkbox" value=""></label></center></div>'
                }
            ]
        });
    };


}
//Class Instance
let cliente = new Cliente();

$(document).ready(function () {
    cliente.CargaClientes();
});


// $("#cerrarCaja").click(function () {
//     $.ajax({
//         type: "POST",
//         url: "class/CajaXBodega.php",
//         data: {
//             action: "ValidarCierreCaja"
//         }
//     })
//     .done(function (e) {
//         movimientosCaja.loadModalCierreCaja(e);
//     })
// });

// $('#tb_movimientosCaja tbody').on('click', 'tr', function () {
//     movimientosCaja.ReadbyID(movimientosCaja.tb_movimientosCaja.row(this).data());
// });

// $("#btnMontoCajas").click(function () {
//     movimientosCaja.montoAperturaDefault = $("#inpMontoAperturaDefault").val(); 
//     // No se puede enviar el objeto tabla xq provoca una recursividad 
//     // Entonces la tabla se guarda en z y luego se recupera cuando finaliza el ajax
//     var z =  movimientosCaja.tb_movimientosCaja;
//     movimientosCaja.tb_movimientosCaja = 0;
//     $.ajax({
//         type: "POST",
//         url: "class/CajaXBodega.php",
//         data: {
//             action: "UpdateMontoApertura",
//             obj: JSON.stringify(movimientosCaja)
//         }
//     })
//     .done(function (e) {
//         movimientosCaja.tb_movimientosCaja = z;
//         swal({
//             text:'Monto establecido correctamente.',
//             title: 'Monto Actualizado!',
//             type: 'success',
//             showConfirmButton: false,
//             timer: 1500
//         });
//     })
// });