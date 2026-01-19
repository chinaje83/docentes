

jQuery(document).ready(function(){
	listar();
	$( "#UltimaModificacionFechaDesde" ).datepicker( {format:"dd/mm/yy",language: 'es'});
	$( "#UltimaModificacionFechaHasta" ).datepicker( {format:"dd/mm/yy",language: 'es'});
});

var timeoutHnd;
function doSearch(ev){ 
	if(timeoutHnd) 
		clearTimeout(timeoutHnd) 
	timeoutHnd = setTimeout(gridReload,500)
}

function gridReload(){
	var datos = $("#formbusqueda").serializeObject();
	jQuery("#listarDatos").jqGrid('setGridParam', {url:"car_cargos_auditoria_lst_ajax.php?rand="+Math.random(), postData: datos,page:1}).trigger("reloadGrid");
}
function Resetear(){
	$("#UltimaModificacionFechaDesde").val("");
	$("#UltimaModificacionFechaHasta").val("");
	timeoutHnd = setTimeout(gridReload,500);
}

function listar(){
	var datos = $("#formbusqueda").serializeObject();
	jQuery("#listarDatos").jqGrid(
	{
		url:'car_cargos_auditoria_lst_ajax.php?rand='+Math.random(),
		postData: datos,
		datatype: "json", 
		colNames:['Tipo','Codigo','Admite suplente','Descripcion','Esdeno','EquivalenciaHs','Accion','Fecha Modificaci\u00F3n',' '],
		colModel:[
			{name:'IdTipoCargo',index:'IdTipoCargo', align:'left'},
			{name:'Codigo',index:'Codigo', align:'left'},
			{name:'AdmiteSuplente',index:'AdmiteSuplente', align:'center'},
			{name:'Descripcion',index:'Descripcion', align:'left'},
			{name:'Esdeno',index:'Esdeno', align:'left'},
			{name:'EquivalenciaHs',index:'EquivalenciaHs', align:'left'},
			{name:'Accion',index:'Accion', width:70,  align:'center', sortable:false},
			{name:'UltimaModificacionFecha',index:'UltimaModificacionFecha', width:100,  align:'center', sortable:false},
			{name:'view',index:'view', width:70,  align:'center', sortable:false},
		], 
		styleUI:'Bootstrap4', 
		iconSet:'fontAwesome', 
		rowNum:20,
		ajaxGridOptions: {cache: false},
		rowList:[20,40,60],
		mtype: "POST",
		pager: '#pager2',
		sortname: 'IdFilaLog',
		viewrecords: true,
		sortorder: "DESC", 
		height:390, 
		caption:"", 
		emptyrecords: "Sin datos para mostrar.", 
		loadError : function(xhr,st,err) { 
		} 
	});

	$(window).bind('resize', function() {
		$("#listarDatos").setGridWidth($("#LstDatos").width());
	}).trigger('resize');

	jQuery("#listarDatos").jqGrid('navGrid','#pager2',{edit:false,add:false,del:false,search:false,refresh:false});

}

function VisualizarLog(codigo){
	$.blockUI({ message: '<div style="font-size:20px; font-weight:bold"><div class="load-sistema"></div>Cargando...</h1>',baseZ: 9999999999 });
	var param;
	$("#cargando").show();
	param = "IdFilaLog="+codigo;
	$.ajax({
		 type: "POST",
		url: "car_cargos_auditoria_data.php",
		 data: param,
		 success: function(msg){
			$("#DataAuditoria").html(msg)
			$("#ModalData").modal('show');
			$.unblockUI();
		}
	});
	return true;
}