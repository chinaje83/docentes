jQuery(document).ready(function(){
    listar();
    $(document).on('click', '#btnBuscar', function () {
        gridReload(1);
    });
    $(document).on('click', '#btnLimpiar', function () {
        Resetear();
    });
});

var timeoutHnd;
function gridReload(page){
    var datos = $("#formbusqueda").serializeObject();
    jQuery("#listarDatos").jqGrid('setGridParam', {url:"esc_escalafones_lst_ajax.php?rand="+Math.random(), postData: datos,page:page}).trigger("reloadGrid");
}
function Resetear(){    $("#IdEscalafon").val("");
    $("#IdEscalafonExterno").val("");
    $("#Nombre").val("");
    $("#Descripcion").val("");
    $("#IdRegimenSalarial").val("");
    $("#Estado").val("");
    $("#AltaFecha").val("");
    $("#AltaUsuario").val("");
    $("#UltimaModificacionUsuario").val("");
    $("#UltimaModificacionFecha").val("");
    timeoutHnd = setTimeout(function() {gridReload(1);},500);
}

function listar(){
    var datos = $("#formbusqueda").serializeObject();
    jQuery("#listarDatos").jqGrid(
    {
        url:'esc_escalafones_lst_ajax.php?rand='+Math.random(),
        postData: datos,
        datatype: "json",
        colNames:[
            Array
        ],
        colModel:[
            Array
        ],
        rowNum:20,
        ajaxGridOptions: {cache: false},
        rowList:[20,40,60],
        mtype: "POST",
        pager: '#pager2',
        sortname: 'IdEscalafon',
        viewrecords: true,
        sortorder: "DESC",
        styleUI:'Bootstrap4',
        iconSet:'fontAwesome',
        height:390,
        caption:"",
        responsive:true,
        autowidth: true,
        emptyrecords: "Sin datos para mostrar."
    });

    $(window).bind('resize', function() {
        $("#listarDatos").setGridWidth($("#LstDatos").width());
    }).trigger('resize');

    jQuery("#listarDatos").jqGrid('navGrid','#pager2',{edit:false,add:false,del:false,search:false,refresh:false});
}

function ActivarDesactivar(codigo,tipo){
    var param;
    $.blockUI({ message: '<div style="font-size:20px; font-weight:bold"><div class="load-sistema">Actualizando...</h1>',baseZ: 9999999999 })
    param = "IdEscalafon="+codigo;
    param += "&accion="+tipo;
    EnviarDatos(param);
}

function EnviarDatos(param){
    $.ajax({
        type: "POST",
        url: "esc_escalafones_upd.php",
        data: param,
        dataType:"json",
        success: function(msg){
            if (msg.IsSucceed==true)
            {
                swal({
                    title: msg.Msg,
                    text: "Operaci\u00F3n finalizada",
                    confirmButtonColor: "#8bc71b",
                    confirmButtonText: "Ok",
                    type: "success"
                });
                var currentPageVar = jQuery("#listarDatos").getGridParam('page');
                gridReload(currentPageVar);
                $.unblockUI();
            }
            else
            {
                alert(msg.Msg);
                $.unblockUI();
            }
        }
    });
}