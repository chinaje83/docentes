let listados = {
	tabObligatorios: false,
	tabCertificados: false,
	tabOpcionales: false,
	tabRectificados: false,
	tabApCondicional: false,
};
jQuery(document).ready(function () {

	const lnkSC = $('#formSolicitud').attr('action');
	$('.chzn-select').chosen({search_contains: true});

	listar();
	//listar2();

	$(document).on('click', '#btnBuscar', function () {
		if (listados['tabObligatorios']) {
			gridReload(1);
		}
		if (listados['tabOpcionales']) {
			timeoutHnd = setTimeout(function () {
				gridReload2(1);
			}, 500);
		}
		if (listados['tabCertificados']) {
			timeoutHnd = setTimeout(function () {
				gridReload3(1);
			}, 1000);
		}
		if (listados['tabRectificados']) {
			timeoutHnd = setTimeout(function () {
				gridReload4(1);
			}, 1500);
		}
		if (listados['tabApCondicional']) {
			timeoutHnd = setTimeout(function () {
				gridReload5(1);
			}, 2000);
		}
	});

	$(document).on('click', '#btnLimpiar', function () {
		Resetear();
	});

	$(document).on('click', '.btnSolicitud', function () {
		const id = $('#Id').val();
		const escuela = $(this).data('escuela');
		const puesto = $(this).data('puesto');
		const form = $('#formSolicitud');
		const idSC = $(this).data('id');
		let href = lnkSC;
		form.find('#IdEscuela').val(escuela);
		form.find('#IdPuesto').val(puesto);
		form.find('#IdLicencia').val(id);
		if ('' !== idSC)
			href += '?Id=' + idSC;
		// console.log('escuela ', escuela,  ' puesto ', puesto);
		form.attr('action', href);
		console.log(form.serialize());

		form.submit();
	});

	$("#Inicio").datepicker({format: "dd/mm/yyyy", language: 'es'});
	$("#Fin").datepicker({format: "dd/mm/yyyy", language: 'es'});

	/*$(document).on('keyup', '#formbusqueda #Nombre', function () {
		let nombre = $(this).val();
		if (nombre.length >= 3) {
			let param = 'Nombre=' + nombre;
			param += '&tipo=1';
			$.ajax({
				type: "POST",
				url: "/autocompletar_ajax.php",
				data: param,
				dataType: "json",
				success: function (msg) {
					if (msg.success) {
						console.log(msg.contenido);
					} else {
						console.log(msg.error_description);
						// alert(msg.error_description);
					}
				}
			});
		}
	});*/

	$(document).on('click', '.nav-link', function () {
        for(const key in listados) listados[key] = false;
        const id = this.id;
		if (!listados[id]) {
			switch (id) {
				case 'tabObligatorios':
					setTimeout(()=>listar(), 500);
					break;
				case 'tabOpcionales':
					setTimeout(()=>listar2(), 500);
					break;
				case 'tabCertificados':
					setTimeout(()=>listar3(), 500);
					break;
				case 'tabRectificados':
					setTimeout(()=>listar4(), 500);
					break;
				case 'tabApCondicional':
					setTimeout(()=>listar5(), 500);
					break;
			}
		} else {
			switch (id) {
				case 'tabObligatorios':
					setTimeout(()=>jQuery("#listarDatos").setGridWidth($('#LstDatos').width()), 500);
					break;
				case 'tabOpcionales':
					setTimeout(()=>jQuery("#listarDatos2").setGridWidth($('#LstDatos2').width()), 500);
					break;
				case 'tabCertificados':
					setTimeout(()=>jQuery("#listarDatos3").setGridWidth($('#LstDatos3').width()), 500);
					break;
				case 'tabRectificados':
					setTimeout(()=>jQuery("#listarDatos4").setGridWidth($('#LstDatos4').width()), 500);
					break;
				case 'tabApCondicional':
					setTimeout(()=>jQuery("#listarDatos5").setGridWidth($('#LstDatos5').width()), 500);
					break;
			}
		}
		const selector = $('#selectorEstados');
		const mostrar = parseInt($(this).data('mostrar')) === 1;
		$('#IdEstado').val('').trigger("chosen:updated");
		selector.addClass('hide');
		if (mostrar)
			selector.removeClass('hide');
	});
});

let timeoutHnd;

function doSearch(ev) {
	if (timeoutHnd)
		clearTimeout(timeoutHnd);
	timeoutHnd = setTimeout(function () {
		gridReload(1);
	}, 500);
}

function gridReload(page) {
	const grid = jQuery("#listarDatos");
	grid.setGridParam({postData: null});
	const form = $("#formbusqueda");
	let datos = form.serializeObject();
	datos['claseEstados'] = 'danger,warning';
	grid.jqGrid('setGridParam', {
		url: "/licencias/maternidad/revision/lst?rand=" + Math.random(),
		postData: datos,
		page: page
	}).trigger("reloadGrid");
}

function gridReload2(page) {
	const grid = jQuery("#listarDatos2");
	grid.setGridParam({postData: null});
	const form = $("#formbusqueda");
	let datos = form.serializeObject();
	datos['claseEstados'] = 'default,success';

	grid.jqGrid('setGridParam', {
		url: "/licencias/maternidad/revision/lst?rand=" + Math.random(),
		postData: datos,
		page: page
	}).trigger("reloadGrid");
}

function gridReload3(page) {
	const grid = jQuery("#listarDatos3");
	grid.setGridParam({postData: null});
	const form = $("#formbusqueda");
	let datos = form.serializeObject();
	datos['claseEstados'] = 'light';
	datos['IdEstado'] = 12;

	grid.jqGrid('setGridParam', {
		url: "/licencias/maternidad/revision/lst?rand=" + Math.random(),
		postData: datos,
		page: page
	}).trigger("reloadGrid");
}
function gridReload4(page) {
	const grid = jQuery("#listarDatos4");
	grid.setGridParam({postData: null});
	const form = $("#formbusqueda");
	let datos = form.serializeObject();
	datos['claseEstados'] = 'info';

	grid.jqGrid('setGridParam', {
		url: "/licencias/maternidad/revision/lst?rand="  + Math.random(),
		postData: datos,
		page: page
	}).trigger("reloadGrid");
}
function gridReload5(page) {
	const grid = jQuery("#listarDatos5");
	grid.setGridParam({postData: null});
	const form = $("#formbusqueda");
	let datos = form.serializeObject();
	datos['claseEstados'] = 'secondary';
	// datos['IdEstado'] = 17;

	grid.jqGrid('setGridParam', {
		url: "/licencias/maternidad/revision/lst?rand=" + Math.random(),
		postData: datos,
		page: page
	}).trigger("reloadGrid");
}

function Resetear() {
	$('input[type!=hidden]').val('');
	$('select').val('').trigger("chosen:updated");

	if (listados['tabObligatorios']) {
		timeoutHnd = setTimeout(function () {
			gridReload(1);
		}, 500);
	}
	if (listados['tabOpcionales']) {
		timeoutHnd = setTimeout(function () {
			gridReload2(1);
		}, 1000);
	}
	if (listados['tabCertificados']) {
		timeoutHnd = setTimeout(function () {
			gridReload3(1);
		}, 1500);
	}
	if (listados['tabRectificados']) {
		timeoutHnd = setTimeout(function () {
			gridReload4(1);
		}, 2000);
	}
	if (listados['tabApCondicional']) {
		timeoutHnd = setTimeout(function () {
			gridReload5(1);
		}, 2500);
	}
}

function listar() {
    listados['tabObligatorios'] = true;
	let datos = $("#formbusqueda").serializeObject();
	datos['claseEstados'] = 'danger, warning';
	armarGrilla(datos, jQuery("#listarDatos"), '#pager2', '#LstDatos');

}


function listar2() {
    listados['tabOpcionales'] = true;
	let datos = $("#formbusqueda").serializeObject();
	datos['claseEstados'] = 'default,success';
	armarGrilla(datos, jQuery("#listarDatos2"), '#pager22', '#LstDatos2');


}

function listar3() {
	listados['tabCertificados'] = true;
	let datos = $("#formbusqueda").serializeObject();
	datos['claseEstados'] = 'light';
	datos['IdEstado'] = 12;
	const grilla = jQuery("#listarDatos3");
	armarGrilla(datos, grilla, '#pager23', '#LstDatos3');
}

function listar4() {
	listados['tabRectificados'] = true;
	let datos = $("#formbusqueda").serializeObject();
	datos['claseEstados'] = 'info';
	datos['IdEstado'] = null;
	const grilla = jQuery("#listarDatos4");
	armarGrilla(datos, grilla, '#pager24', '#LstDatos4');


}
function listar5() {
	listados['tabApCondicional'] = true;
	let datos = $("#formbusqueda").serializeObject();
	datos['claseEstados'] = 'secondary';
	/*datos['IdEstado'] = 17;*/
	const grilla = jQuery("#listarDatos5");
	armarGrilla(datos, grilla, '#pager25', '#LstDatos5');


}

function armarGrilla(datos, grilla, pager, referencia) {
	grilla.jqGrid(
		{
			url: '/licencias/maternidad/revision/lst?rand=' + Math.random(),
			postData: datos,
			datatype: "json",
			colNames: ['Id', 'Agente','Escuela', 'Inicio', 'Fin', 'Duraci\u00f3n', 'Motivo', 'Art\u00edculos', 'Estado p\u00FAblico', 'Fecha de env\u00edo', 'Junta', 'Editar', 'Auditoria'],
			colModel: [
				{name: 'Id', index: 'Id', width: 10, align: 'center'},
				{name: 'Persona.NombreCompleto.raw', index: 'Persona.NombreCompleto.raw', width: 35, align: 'left'},
				{name: 'Cargos.Escuela.Id', index: 'Cargos.Escuela.Id', width: 20, align: 'center',sortable: false},
				{name: 'Inicio', index: 'Inicio', width: 20, align: 'center'},
				{name: 'Fin', index: 'Fin', width: 20, align: 'center'},
				{name: 'Duracion', index: 'Duracion', width: 20, align: 'center'},
				{name: 'Motivo.Id', index: 'Motivo.Id', width: 50, align: 'left'},
				{name: 'Articulo.Id', index: 'Articulo.Id', width: 50, align: 'left', sortable: false},
				{name: 'Estado.NombrePublico', index: 'Estado.NombrePublico', width: 30, align: 'center', sortable: false},
				{name: 'FechaEnvio', index: 'FechaEnvio', width: 30, align: 'center', sortable: false},
				{name: 'Junta', index: 'Junta', width: 20, align: 'center', sortable: false},
				{name: 'Editar', index: 'Editar', width: 20, align: 'center', sortable: false},
				{name: 'Auditoria', index: 'Auditoria', width: 20, align: 'center', sortable: false}
			],
			rowNum: 20,
			ajaxGridOptions: {cache: false},
			mtype: "POST",
			sortname: 'Id',
			viewrecords: true,
			sortorder: "DESC",
			styleUI: 'Bootstrap4',
			iconSet: 'fontAwesome',
			pager: pager,
			height: 390,
			caption: "",
            responsive:true,
            autowidth: true,  // set 'true' here
			emptyrecords: "Sin datos para mostrar.",
			loadError: function (xhr, st, err) {
			},
			loadComplete: function (data) {
				let rows = $(this).find('tbody').children('tr[class!=jqgfirstrow]');
				let rowsData = data.rows;
				// console.log(rows);
				// console.log(rowsData);
				rows.each(function (rowId, row) {
					$(row).addClass(rowsData[rowId].class);
				});
			}
		});

	$(window).bind('resize', function () {
		grilla.setGridWidth($(referencia).width());
	}).trigger('resize');
}
