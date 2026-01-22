jQuery(function($){
    if (typeof $.fn.serializeObject !== "function") {
        $.fn.serializeObject = function() {
            var obj = {};
            $.each(this.serializeArray(), function() {
                if (obj[this.name] !== undefined) {
                    if (!Array.isArray(obj[this.name])) {
                        obj[this.name] = [obj[this.name]];
                    }
                    obj[this.name].push(this.value || "");
                } else {
                    obj[this.name] = this.value || "";
                }
            });
            return obj;
        };
    }
});

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
    jQuery("#listarDatos").jqGrid('setGridParam', {url:"per_personasantigtipos_lst_ajax.php?rand="+Math.random(), postData: datos,page:page}).trigger("reloadGrid");
}
function Resetear(){    $("#IdAntiguedadTipo").val("");
    $("#Nombre").val("");
    $("#Estado").val("");
    $("#AltaFecha").val("");
    $("#AltaUsuario").val("");
    $("#UltimaModificacionesFecha").val("");
    $("#UltimaModificacionUsuario").val("");
    $("#SoloLiquidacion").val("");
    timeoutHnd = setTimeout(function() {gridReload(1);},500);
}

function listar(){
    var datos = $("#formbusqueda").serializeObject();
    jQuery("#listarDatos").jqGrid(
    {
        url:'per_personasantigtipos_lst_ajax.php?rand='+Math.random(),
        postData: datos,
        datatype: "json",
        colNames:[
            'IdAntiguedadTipo',
            'Nombre',
            'UltimaModificacionesFecha',
            'SoloLiquidacion',
            'Estado',
            'Editar'
        ],
        colModel:[
            {name:'IdAntiguedadTipo',index:'IdAntiguedadTipo', align:'left', width:40},
            {name:'Nombre',index:'Nombre', align:'left', width:40},
            {name:'UltimaModificacionesFecha',index:'UltimaModificacionesFecha', align:'left', width:40},
            {name:'SoloLiquidacion',index:'SoloLiquidacion', align:'left', width:40},
            {name:'act',index:'act', width:35, align:'center', sortable:false},
            {name:'edit',index:'edit', width:35, align:'center', sortable:false}
        ],
        rowNum:20,
        ajaxGridOptions: {cache: false},
        rowList:[20,40,60],
        mtype: "POST",
        pager: '#pager2',
        sortname: 'IdAntiguedadTipo',
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
    param = "IdAntiguedadTipo="+codigo;
    param += "&accion="+tipo;
    EnviarDatos(param);
}

function EnviarDatos(param){
    $.ajax({
        type: "POST",
        url: "per_personasantigtipos_upd.php",
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