var url;
var t= null;
var mouseX;
var mouseY;

$(document).ready(function () {        
    $('#btnLogin').click(function(e){
        // e.preventDefault();
        Login();
    });

    $(document).mousemove( function(e) {
        mouseX = e.pageX; 
        mouseY = e.pageY;
    }); 
   
});

function Login(){
    $('#btnLogin').attr("disabled", "disabled");
    $.ajax({
        type: "POST",
        url: "class/Usuario.php",
        data: {
            action: 'Login',               
            username:  $("#username").val(),
            password: $("#password").val(),
            beforeSend: function(){
                 $("#error").fadeOut();
            } 
        }        
    })
    .done(function( e ) {
        var data= JSON.parse(e);
        if(data.status=='login'){
            // si el usuario está relacionado con mas de una bodega debe seleccionarla en modal            
            location.href= data.url || 'index.html';
        }
        else if(data.status=='inactivo')
            $("#error").fadeIn(500, function(){
                $("#error").html(`                    
                    <div class="alert alert-danger alert-dismissible fade in" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                        Usuario <strong>INACTIVO</strong>.
                    </div>
                `);
            });  
        else if(data.status=='noexiste')
            $("#error").fadeIn(500, function(){
                $("#error").html(`                    
                    <div class="alert alert-danger alert-dismissible fade in" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                        Usuario <strong>NO EXISTE</strong>, favor registrarse.
                    </div>
                `);
            });
        else
            $("#error").fadeIn(500, function(){
                $("#error").html(`
                    <div class="alert alert-danger alert-dismissible fade in" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                        Usuario / Contraseña <strong>Inválidos</strong>.
                    </div>
                `);
            });        
    })    
    .fail(function( e ) {
        showError(e);
    })
    .always(function () {
        $("#btnLogin").removeAttr("disabled");
    });
};

function showError(e) {
    //$(".modal").css({ display: "none" });  
    var data = JSON.parse(e.responseText);
    swal({
        type: 'error',
        title: 'Oops...',
        text: 'Algo no está bien (' + data.code + '): ' + data.msg, 
        footer: '<a href>Contacte a Soporte Técnico</a>',
    })
};

function ShowAll(data) {
    // carga la tabla desde un array.
    t= $('#dsItemsBodega').DataTable ({
        responsive: true,      
        pagging: false,
        destroy: true,
        searching: false,
        bPaginate: false,
        bLengthChange: false,
        distroy: true,
        info:false,
        data : data.bodegas,              
        "language": {
            "infoEmpty": "Sin Productos Ingresados",
            "emptyTable": "Sin Productos Ingresados",
            "search": "Buscar",
            "zeroRecords":    "No hay resultados",
            "lengthMenu":     "Mostrar _MENU_ registros",
            "paginate": {
                "first":      "Primera",
                "last":       "Ultima",
                "next":       "Siguiente",
                "previous":   "Anterior"
            }
        },  
        columns : [
            { "data" : "idBodega" },
            { "data" : "nombre" }
        ],
        columnDefs: [
            {//id
            targets: 0,
            visible: false,
            searchable: false,
            className: "itemId"
            }
        ]
    });
};
