jQuery(document).ready(function(){
    $(document).on('click', '#BtnInsertar', function () {
        Insertar();
    });
    $(document).on('click', '#BtnModificar', function () {
        Modificar();
    });
    $(document).on('click', '#BtnEliminar', function () {
        var  Codigo = $("#formalta #IdAntiguedadTipo").val();
        Eliminar(Codigo);
    });
});

function Insertar(){
    var param;
    $.blockUI({ message: '<div style="font-size:20px; font-weight:bold"><div class="load-sistema">Agregando...</h1>',baseZ: 9999999999 });
    param = $("#formalta").serialize();
    param += "&accion=1";
    enviarDatosInsertarModificar(param,1);
    return true;
}

function Modificar(){
    var param;
    $.blockUI({ message: '<div style="font-size:20px; font-weight:bold"><div class="load-sistema">Actualizando...</h1>',baseZ: 9999999999 });
    param = $("#formalta").serialize();
    param += "&accion=2";
    enviarDatosInsertarModificar(param,2);
    return true;
}

function Eliminar(codigo){
    var param;
    swal({
        title: "Eliminar",
        text: "Est\u00E1 seguro que desea eliminar?",
        type: "warning",
        showCancelButton: true,
        confirmButtonText: "Si, eliminar!",
        confirmButtonColor: "#DD6B55",
        cancelButtonText: "No, cancelar!"
    }).then(result => {if (result.value) {
        param = "IdAntiguedadTipo="+ codigo;
        param += "&accion=3";
        $.blockUI({ message: '<div style="font-size:20px; font-weight:bold"><div class="load-sistema">Eliminando...</h1>',baseZ: 9999999999 });
        enviarDatosInsertarModificar(param,3);}
    });
}

function enviarDatosInsertarModificar(param,tipo){
    $.ajax({
        type: "POST",
        url: "per_personasantigtipos_upd.php",
        data: param,
        dataType:"json",
        success: function(msg){
            if (msg.IsSucceed==true)
            {
                $.unblockUI();
                if(tipo===1)
                {
                    swal({
                        title: "Ha generado correctamente",
                        text: "Aguarde unos segundos mientras lo redireccionamos.",
                        type: "success",
                        showCancelButton: false,
                        timer: 3000,
                        showConfirmButton: false
                    }).then(result => {
                        $.blockUI({ message: '<div style="font-size:20px; font-weight:bold"><div class="load-sistema">Recargando...</h1>',baseZ: 9999999999 });
                        window.location=msg.header;
                    });
                }
                if(tipo===2)
                {
                    swal({
                        title: "Sus datos han sido modificados con exito",
                        text: "Operaci\u00F3n finalizada",
                        confirmButtonColor: "#8bc71b",
                        confirmButtonText: "Ok",
                        type: "success"
                    });
                }
                if(tipo===3)
                {
                    swal({
                        title: "Ha eliminado correctamente",
                        type: "success",
                        showCancelButton: false,
                        confirmButtonColor: "#8bc71b",
                        confirmButtonText: "Ok"
                    }).then(result => {if (result.value) {
                        $.blockUI({ message: '<div style="font-size:20px; font-weight:bold"><div class="load-sistema">Recargando...</h1>',baseZ: 9999999999 });
                        window.location=msg.header;}
                    });
                }
            }
            else
            {
                swal({
                    title: "Error",
                    text: msg.Msg,
                    type: "error"
                });
                $.unblockUI();
            }
        }
    });
}