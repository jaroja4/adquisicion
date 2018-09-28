class Rol {
    // Constructor
    constructor(id, name, password, tel, active, tb_dataRoles) {
        this.id = id || "";
        this.name = name || "";
        this.password = password || "";
        this.tel = tel || 0;
        this.active = active || 0;
        this.tb_dataRoles = tb_dataRoles || null;
    };

    CargaRoles() {
        $.ajax({
            type: "POST",
            url: "class/Roles.php",
            data: {
                action: "ReadAll"
            }
        })
            .done(function (e) {
                rol.drawRoles(e)
            });
    };


    drawRoles(e) {
        var dataRoles = JSON.parse(e);

        rol.tb_dataRoles = $('#tb_Roles').DataTable({
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
            data: dataRoles,                               
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
            columns: [
                {
                    title: "ID",
                    data: "id",
                    visible: false
                },
                {
                    title: "Nombre",
                    data: "nombre"
                },
                {
                title: "Editar",
                data: null,
                defaultContent: '<a>Editar</a>'
                },
                {
                title: "Eliminar",
                data: null,
                defaultContent: '<a>Eliminar</a>'
                }
            ]
        });
        rol.drawOptionRoles(e);
    };
    

    drawOptionRoles(e){
        var dataRoles = JSON.parse(e);
        var options = [];
        dataRoles.forEach(function (item) {
            var option = `<option value="${item.id}"> ${item.nombre}</option>`;
            options.push(option);
            });
            $('.selectpicker').html(options); 
            $('select[name=selValue]').val(1);
            $('.selectpicker').selectpicker('refresh');
            $('.selectpicker').selectpicker('val', ['b7ebe888-be81-11e8-a309-54ee75873a76']);
            $('select').addClass('selectwidthauto');

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
                rol.drawClientes(e);
                rol.CargaRoles();
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
                    title: "Roles",
                    data: null,                
                    "defaultContent": '<select name="selValue" class="selectpicker input-small" multiple data-live-search="true"></select>'

                }
            ]
        });
        // cliente.CargaDispositivos();
        // rol.drawOptionRoles(e)
        
        // $('.selectpicker').selectpicker('val', ['b7ebe888-be81-11e8-a309-54ee75873a76', 'b7ebe888-be81-11e8-a309-54ee75873a79']);
        // $('.selectpicker').selectpicker('refresh');
    };

    ///////////////////////////////////////////////////////////////////////


}
//Class Instance
let rol = new Rol();

$(document).ready(function () {
    rol.CargaClientes();
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