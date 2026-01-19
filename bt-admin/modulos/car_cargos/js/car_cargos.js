jQuery(document).ready(function(){
	listar();
	$(document).on('click', '#btnBuscar', function () {
		gridReload(1);
	});
	$(document).on('click', '#btnLimpiar', function () {
		Resetear();
	});
	$(document).on('click', '.btnEliminar', function () {
		let id = $(this).data('id');
		Eliminar(id);
	});
	$(document).on('change', '.btnActivar', function () {
		let id = $(this).data('id');
		ActivarDesactivar(id,5);
	});
	$(document).on('change', '.btnDesactivar', function () {
		let id = $(this).data('id');
		ActivarDesactivar(id,4);
	});

	$('input').keypress(function(e){
		if(e.which == 13){//Enter key pressed
			gridReload(1);
		}
	});
});

var timeoutHnd;
function doSearch(ev){
	if(timeoutHnd)
		clearTimeout(timeoutHnd)
	timeoutHnd = setTimeout(function() {gridReload(1);},500)
}

function gridReload(page){
	var datos = $("#formbusqueda").serializeObject();
	jQuery("#listarDatos").jqGrid('setGridParam', {url:"car_cargos_lst_ajax.php?rand="+Math.random(), postData: datos,page:page}).trigger("reloadGrid");
}
function Resetear(){
	$("#IdCargo").val("");
	$("#IdTipoCargo").val("");
	$("#Codigo").val("");
	$("#Descripcion").val("");
	$("#Esdeno").val("");
	$("#EquivalenciaHs").val("");
	$("#Jerarquico").val("");
	$("#IdRegimenSalarial").val("");
    $("#IdEscalafon").val("");
    $("#DesempenoLugar").val("");
	timeoutHnd = setTimeout(function() {gridReload(1);},500);
}

function listar(){
	var datos = $("#formbusqueda").serializeObject();
	jQuery("#listarDatos").jqGrid(
	{
		url:'car_cargos_lst_ajax.php?rand='+Math.random(),
		postData: datos,
		datatype: "json",
		colNames:['Id','C\u00f3digo','IdExterno','Tipo','Descripci\u00f3n','Reg. Salarial','Admite suplente', 'Escalaf&oacute;n', 'Lugar desempe&ntilde;o', 'Estado','Editar'],
		colModel:[
			{name:'IdCargo',index:'IdCargo', width:20, align:'center'},
			{name:'Codigo',index:'Codigo', align:'center', width:40},
			{name:'IdExterno',index:'IdExterno', width:15, align:'center'},
			{name:'IdTipoCargo',index:'IdTipoCargo', align:'left', width:40},
			{name:'Descripcion',index:'Descripcion', align:'left', width:40},
			{name:'IdRegimenSalarial',index:'IdRegimenSalarial', align:'center', width:40},
			{name:'AdmiteSuplente',index:'AdmiteSuplente', align:'center', width:30},
            {name:'escalafon',index:'escalafon', align:'center', width:30},
            {name:'desempenolugar',index:'desempenolugar', align:'center', width:30},
			{name:'act',index:'act', width:35,  align:'center', sortable:false},
			{name:'edit',index:'edit', width:35,  align:'center', sortable:false}		],
		rowNum:20,
		ajaxGridOptions: {cache: false},
		rowList:[20,40,60],
		mtype: "POST",
		pager: '#pager2',
		sortname: 'IdCargo',
		viewrecords: true,
		sortorder: "DESC",
		styleUI:'Bootstrap4',
		iconSet:'fontAwesome',
		height:390,
		caption:"",
        responsive:true,
        autowidth: true,  // set 'true' here
		emptyrecords: "Sin datos para mostrar.",
		loadError : function(xhr,st,err) {
		}
	});

	$(window).bind('resize', function() {
		$("#listarDatos").setGridWidth($("#LstDatos").width());
	}).trigger('resize');

	jQuery("#listarDatos").jqGrid('navGrid','#pager2',{edit:false,add:false,del:false,search:false,refresh:false});

}

function ActivarDesactivar(codigo,tipo){
	var param;
	$.blockUI({ message: '<div style="font-size:20px; font-weight:bold"><div class="load-sistema">Actualizando...</h1>',baseZ: 9999999999 })
	param = "IdCargo="+codigo;
	param += "&accion="+tipo;
	EnviarDatos(param);

}

function EnviarDatos(param){
	$.ajax({
		type: "POST",
		url: "car_cargos_upd.php",
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
