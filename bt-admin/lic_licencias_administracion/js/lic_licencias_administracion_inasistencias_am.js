let licenciasListadas = false;
const lnkSC = $('#formSolicitud').attr('action');
$.fn.serializeDisabledLicencias = function () {
    const disabledElements = this.find(':disabled');
    let resultados = {};
    for (let el of disabledElements) {
        resultados[el.id] = $(el).val();
    }
    if (resultados["FechaFinAbierta"]) {
        resultados["FechaFinAbierta"] = $("#FechaFinAbierta").is(':checked') ? "1" : "0";
    }
    return $.param(resultados);
}
jQuery(document).ready(function () {
    $('.carousel').carousel({interval: false});

    if (($('#IdMotivo').val() !== "") && ($('#IdArticulo').val() === "")) {
        let id = $('#IdMotivo').val();
        buscarArticulos(id, true);
    }

    $(document).on('change', '#IdMotivo', function () {
        let id = $("#IdMotivo").val();
        buscarArticulos(id, true);
    });

    $(document).on('change', '.articulos', function () {

        let idPuesto = $(this).data('id');
        let optionSelected = $('#' + $(this).attr('id')).val();

        $('#IdArticuloPuesto_' + idPuesto).remove();

        if (optionSelected !== "") {
            $('#ArticulosPuestos_'+idPuesto).append('<input type="hidden" id="IdArticuloPuesto_' + idPuesto + '" value="' + optionSelected + '" name="IdArticuloPuesto[' + idPuesto + ']">');
        }
    });

    buscarObservaciones();

    $("#Inicio").datepicker({format: "dd/mm/yyyy", language: 'es', todayHighlight: true});
    $("#Fin").datepicker({format: "dd/mm/yyyy", language: 'es', todayHighlight: true});
    $("#Fecha").datepicker({format: "dd/mm/yyyy", language: 'es', todayHighlight: true});

    $('.chzn-select').chosen({search_contains: true});
    $(document).on('click', '#BtnInsertar', function () {
        Insertar();
    });

    $(document).on('click', '#BtnInsertarEnviar', function () {
        InsertarEnviar();
    });

    $(document).on('click', '#btnModificar', function () {
        Modificar();
    });

    $(document).on('click', '#BtnModificarEnviar', function () {
        ModificarEnviar();
    });

    $(document).on('blur', '#Matricula', function () {
        $("#DatosMatricula").remove();
        BuscarAutorizante();
    });


    $(document).on('blur', '#FamiliarDni', function () {
        $("#DatosFamilia").remove();
        BuscarFamiliar();
    });

    $(document).on('submit', '#formBusquedaPersona', function () {
        $("#cardResultadoPersona").remove();
        BuscarPersona();
    });


    $(document).on('click', '#BtnBuscarPersona', function () {
        $("#cardResultadoPersona").remove();
        BuscarPersona();
    });

    checkFechaFinAbierta(true);
    $(document).on('change', '#FechaFinAbierta', checkFechaFinAbierta);

    $(document).on('click', '#BtnSiguiente', function () {
        $('#formResultadoPersona').submit();
    });

    $(document).on('change', '#Inicio', function () {

        setTimeout(
            $.blockUI({message: '<div style="font-size:20px; font-weight:bold"><div class="load-sistema">Recalculando cargos afectados...</h1>', baseZ: 9999999999}),
            100);
        $.unblockUI();

        buscarCargosAsociados();
    });

    $(document).on('change', '#Fin', function () {

        setTimeout(
            $.blockUI({message: '<div style="font-size:20px; font-weight:bold"><div class="load-sistema">Recalculando cargos afectados...</h1>', baseZ: 9999999999}),
            100);
        $.unblockUI();

        buscarCargosAsociados();
    });

    if ($("#Familiar").prop("checked") === true)
        $("#dataLicenciaFamiliar").css('display', 'block');


    $(document).on('click', '.btnMover', function () {
        cambiarEstado($(this).data('target'), $(this).data('nombre'), $(this).data('id'));
    });

    $(document).on('click', '#btnAprobar', function () {
        aprobarLicencia();
    });


    $(document).on('click', '#btnAnular', function () {
        anularLicencia();
    });


    $(document).on('click', '#btnDenegar', function () {
        denegarLicencia();
    });

    $(document).on('click', '#btnFechaJunta', function () {
        $('#ModalJunta').modal('show');
        if (licenciasListadas)
            gridReloadJuntas();
        else {
            licenciasListadas = true;
            listarJuntas();
        }
    });

    $(document).on('click', '#btnAgregarJunta', function () {
        const tipo = 7;
        const fecha = convertirFecha($('#Fecha').val(), 'dd/mm/aaaa', 'aaaa-mm-dd');
        let param = 'IdLicencia=' + $('#Id').val();
        param += '&Fecha=' + fecha + ' ' + $('#Hora').val() + ':00';
        param += '&IdRegion=' + $('#IdRegion').val();
        param += '&accion=' + tipo;

        enviarDatosInsertarModificar(param, tipo);
    });


    $(document).on('click', '.btnFinalizar', function () {
        const tipo = 8;
        let param = 'Id=' + $(this).data('id');
        param += '&IdLicencia=' + $('#Id').val();
        param += '&accion=' + tipo;

        confirmar('Ingrese una raz\u00f3n para la finalizaci\u00f3n')
            .then((resultado) => {
                if (resultado.hasOwnProperty('value')) {
                    param += '&Comentarios=' + escape(resultado.value);
                    enviarDatosInsertarModificar(param, tipo);
                }
            });
    });

    $(document).on('click', '.btnAusente', function () {
        const tipo = 9;
        let param = 'Id=' + $(this).data('id');
        param += '&IdLicencia=' + $('#Id').val();
        param += '&accion=' + tipo;
        confirmar('Ingrese un comentario')
            .then((resultado) => {
                if (resultado.hasOwnProperty('value')) {
                    param += '&Comentarios=' + escape(resultado.value);
                    enviarDatosInsertarModificar(param, tipo);
                }
            });
    });

    $(document).on('click', '.btnCancelar', function () {
        const tipo = 10;
        let param = 'Id=' + $(this).data('id');
        param += '&IdLicencia=' + $('#Id').val();
        param += '&accion=' + tipo;
        confirmar('Ingrese una raz\u00f3n para la cancelaci\u00f3n')
            .then((resultado) => {
                if (resultado.hasOwnProperty('value')) {
                    param += '&Comentarios=' + escape(resultado.value);
                    enviarDatosInsertarModificar(param, tipo);
                }
            });
    });

    $(document).on('click', '#BtnEnviarComentario', function () {
        insertarObservacion();
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


    $(document).on('click', '#btnEditar', function () {

        if (!$('#btnEditar').hasClass('hide')) {
            $('input[type=file]').prop('disabled', false);
            $('#Inicio').removeAttr('disabled');
            $('#Fin').removeAttr('disabled');
            $('#Horas').removeAttr('disabled');
            $('#Duracion').removeAttr('disabled');
            $('#Unidad').removeAttr('disabled');
            // $('#IdArticulo').prop('disabled', false).trigger("chosen:updated");
            $('#IdMotivo').prop('disabled', false).trigger("chosen:updated");
            $('#Familiar').removeAttr('disabled');
            $('#IdParentesco').removeAttr('disabled');
            $('#FamiliarDni').removeAttr('disabled');
            $('#FamiliarNombre').removeAttr('disabled');
            $('#FamiliarApellido').removeAttr('disabled');

            // $('#btnModificar').removeClass('hide');
            $('#btnEditar').addClass('hide');
            $('#btnCancelar').removeClass('hide');
            $('.btnEliminarCargoAfectado').removeClass('disabled');
            $('#FechaFinAbierta').removeAttr('disabled');
            /*if (buscarCargos)
                buscarCargosAsociados();*/
        }
    });

    $(document).on('click', '#btnCancelar', function () {

        if (!$('#btnCancelar').hasClass('hide')) {
            $('input[type=file]').prop('disabled', true);
            $('#IdPuesto').prop('disabled', true).trigger("chosen:updated");
            $('#Inicio').attr('disabled', 'disabled');
            $('#Fin').attr('disabled', 'disabled');
            $('#Horas').attr('disabled', 'disabled');
            $('#Duracion').attr('disabled', 'disabled');
            $('#Unidad').attr('disabled', 'disabled');
            // $('#IdArticulo').prop('disabled', true).trigger("chosen:updated");
            $('#IdMotivo').prop('disabled', true).trigger("chosen:updated");
            $('#Familiar').attr('disabled', 'disabled');
            $('#IdParentesco').attr('disabled', 'disabled');
            $('#FamiliarDni').attr('disabled', 'disabled');
            $('#FamiliarNombre').attr('disabled', 'disabled');
            $('#FamiliarApellido').attr('disabled', 'disabled');
            $('#FechaFinAbierta').attr('disabled', 'disabled');
            // $('#btnModificar').addClass('hide');
            $('#btnCancelar').addClass('hide');
            $('#btnEditar').removeClass('hide');
            $('.btnEliminarCargoAfectado').addClass('disabled');
        }
    });


    $(document).on('click', '.btnEliminarCargoAfectado', function () {
        const id = $(this).data('puesto');
        $('#IdArticulo_' + id).remove();
        $('#IdPuesto_' + id).remove();
        $('tr.fila_puesto_' + id).remove();
    });

    $('input[type=file]').prop('disabled', true);

    $(document).on('click', '#btnNull', function () {
        return null;
    });

    $(document).on('click', '#btnModificarReabierto', function () {
        ModificarReabierto();
    });


});

function cambiarEstado(idWorkflow, Nombre, id) {

    const tipo = 4;
    let text;
    let confirmButtonText;
    let infoText = '\nRecuerde guardar en el caso que haya hecho modificaciones';
    switch (id) {
        case 5:
            text = '\u00bfEst\u00E1 seguro que desea aprobar la licencia?' + infoText;
            confirmButtonText = 'S\u00ED, aprobar';
            break;
        case 6:
            text = '\u00bfEst\u00E1 seguro que desea enviar a junta m\u00E9dica?' + infoText;
            confirmButtonText = 'S\u00ED, enviar';
            break;
        case 7:
            text = '\u00bfEst\u00E1 seguro que desea denegar la licencia?' + infoText;
            confirmButtonText = 'S\u00ED, denegar';
            break;
        case 8:
            text = '\u00bfEst\u00E1 seguro que desea anular la licencia?' + infoText;
            confirmButtonText = 'S\u00ED, anular';
            break;
        case 9:
            text = '\u00bfEst\u00E1 seguro que desea enviar a rectificar la licencia?' + infoText;
            confirmButtonText = 'S\u00ED, enviar';
            break;
        default:
            text = '\u00bfEst\u00E1 seguro que desea enviar?';
            confirmButtonText = 'S\u00ED, enviar';
            break;
    }

    swal({
        title: Nombre,
        text: text,
        type: "warning",
        showCancelButton: true,
        confirmButtonText: confirmButtonText,
        confirmButtonColor: "#4ab657",
        cancelButtonText: "No, cancelar"
    }).then(result => {
        if (result.value) {
            $.blockUI({
                message: '<div style="font-size:20px; font-weight:bold"><div class="load-sistema">Procesando...</h1>',
                baseZ: 9999999999
            });
            let param = 'Id=' + $('#Id').val();
            param += '&IdWorkflow=' + idWorkflow;
            param += '&accion=' + tipo;
            param += '&IdEstadoFinal=' + id;
            enviarDatosInsertarModificar(param, tipo);
        }
    });
}

function aprobarLicencia() {
    const tipo = 4;
    let param = 'Id=' + $('#Id').val();
    param += '&accion=' + tipo;
    enviarDatosInsertarModificar(param, tipo);
}

function anularLicencia() {
    const tipo = 5;
    let param = 'Id=' + $('#Id').val();
    param += '&accion=' + tipo;
    enviarDatosInsertarModificar(param, tipo);
}

function denegarLicencia() {
    const tipo = 6;
    let param = 'Id=' + $('#Id').val();
    param += '&accion=' + tipo;
    enviarDatosInsertarModificar(param, tipo);
}

function Insertar() {
    let param;
    $.blockUI({message: '<div style="font-size:20px; font-weight:bold"><div class="load-sistema">Agregando...</h1>', baseZ: 9999999999});
    param = $("#formalta").serialize();
    param += "&" + $("#formaltaCertificados").serialize();
    param += "&accion=1&enviar=0";
    enviarDatosInsertarModificar(param, 1);
}

function InsertarEnviar() {
    swal({
        title: "Enviar",
        text: "Esta seguro que desea enviar?",
        type: "warning",
        showCancelButton: true,
        confirmButtonText: "S\u00ED, enviar!",
        confirmButtonColor: "#4ab657",
        cancelButtonText: "No, cancelar!"
    }).then(result => {
        if (result.value) {
            let param;
            $.blockUI({
                message: '<div style="font-size:20px; font-weight:bold"><div class="load-sistema">Agregando...</h1>',
                baseZ: 9999999999
            });
            param = $("#formalta").serialize();
            param += "&" + $("#formaltaCertificados").serialize();
            param += "&accion=1&enviar=1";
            enviarDatosInsertarModificar(param, 1);
        }
    });
}

function Modificar() {
    let param;
    const form = $("#formalta");
    swal({
        title: "Guardar",
        text: "Est\u00E1 seguro que desea guardar ?",
        type: "warning",
        showCancelButton: true,
        confirmButtonText: "S\u00ED, guardar!",
        confirmButtonColor: "#4ab657",
        cancelButtonText: "No, cancelar!"
    }).then(result => {
        if (result.value) {
            $.blockUI({
                message: '<div style="font-size:20px; font-weight:bold"><div class="load-sistema">Actualizando...</h1>',
                baseZ: 9999999999
            });
            param = form.serialize();
            param += "&" + form.serializeDisabledLicencias();
            param += "&" + $("#formaltaCertificados").serialize();
            param += '&' + $('#formCargosAfectados').serialize();
            param += '&seleccionaCargos=' + $('#IdMotivo').data('cargo');
            param += "&accion=2&enviar=0";
            enviarDatosInsertarModificar(param, 2);
        }
    });
}

function ModificarEnviar() {
    swal({
        title: "Enviar",
        text: "Esta seguro que desea enviar?",
        type: "warning",
        showCancelButton: true,
        confirmButtonText: "S\u00ED, enviar!",
        confirmButtonColor: "#4ab657",
        cancelButtonText: "No, cancelar!"
    }).then(result => {
        if (result.value) {
            let param;
            $.blockUI({
                message: '<div style="font-size:20px; font-weight:bold"><div class="load-sistema">Actualizando...</h1>',
                baseZ: 9999999999
            });
            param = $("#formalta").serialize();
            param += "&" + $("#formaltaCertificados").serialize();
            param += '&seleccionaCargos=' + $('#IdMotivo').data('cargo');
            param += "&accion=2&enviar=1";
            enviarDatosInsertarModificar(param, 2, 1);
        }
    });
}

function BuscarPersona() {
    let param;
    $.blockUI({message: '<div style="font-size:20px; font-weight:bold"><div class="load-sistema">Buscando...</h1>', baseZ: 9999999999});
    param = $("#formBusquedaPersona").serialize();
    param += "&accion=1";
    enviarDatosBusqueda(param, 1);
}

function BuscarAutorizante() {
    let param;
    $.blockUI({message: '<div style="font-size:20px; font-weight:bold"><div class="load-sistema">Buscando...</h1>', baseZ: 9999999999});
    param = "Matricula=" + $("#formalta #Matricula").val();
    param += "&accion=2";
    enviarDatosBusqueda(param, 2);
}

function BuscarFamiliar() {
    let param;
    $.blockUI({message: '<div style="font-size:20px; font-weight:bold"><div class="load-sistema">Buscando...</h1>', baseZ: 9999999999});
    param = "Dni=" + $("#formalta #FamiliarDni").val();
    param += "&accion=3";
    enviarDatosBusqueda(param, 3);
}

function BuscarMotivoDetalle(id) {
    let param;
    $.blockUI({message: '<div style="font-size:20px; font-weight:bold"><div class="load-sistema">Buscando...</h1>', baseZ: 9999999999});
    param = "Id=" + id;
    param += "&accion=4";
    enviarDatosBusqueda(param, 4);
}

function buscarArticulos(id, editable = "") {
    let param;
    $.blockUI({message: '<div style="font-size:20px; font-weight:bold"><div class="load-sistema">Buscando...</h1>', baseZ: 9999999999});
    param = "IdMotivo=" + id;
    param += "&Editable=" + editable;
    param += "&accion=6";
    enviarDatosBusqueda(param, 6);
}

function enviarDatosBusqueda(param, tipo) {
    $.ajax({
        type: "POST",
        url: "/lic_licencias_datos.php",
        data: param,
        dataType: "json",
        success: function (msg) {
            switch (tipo) {
                case 1:
                    if (msg.IsSucceed === true) {
                        $("#CardResultadoPersona").append(msg.Resultado);
                    }
                    break;
                case 3:
                    $("#DatosFamiliares").append(msg.Resultado);
                    $.unblockUI();
                    return;
                case 2:
                    $("#DatosAutorizante").append(msg.Resultado);
                    $.unblockUI();
                    return;
                case 4:
                    $("#Detalles").append(msg.Resultado);
                    $.unblockUI();
                    return;
                case 5:
                    $("#Observacion").val("");
                    $(".chat-rbox").remove();
                    $("#ContenedorComentarios").append(msg.Resultado);
                    $.unblockUI();
                    return;
                case 6:
                    $("#ComboArticulos").remove();
                    $("#Articulos").append(msg.Resultado);
                    $.unblockUI();
                    return;
                default:
            }

            if (msg.IsSucceed === true) {
                setTimeout(function () {
                    $("#MsgGuardar").removeClass("show");
                }, 3000);
                $.unblockUI();
            } else {
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

function enviarDatosInsertarModificar(param, tipo, enviar = 0) {
    $.ajax({
        type: "POST",
        url: "/lic_licencias_upd.php",
        data: param,
        dataType: "json",
        success: function (msg) {
            if (msg.IsSucceed) {
                $("#MsgGuardar").html(msg.Msg)
                    .addClass("show");
                setTimeout(function () {
                    $("#MsgGuardar").removeClass("show");
                }, 3000);
                $.unblockUI();
                switch (tipo) {
                    case 1:
                        swal({
                            title: "Ha generado correctamente",
                            text: "Aguarde unos segundos mientras lo redireccionamos.",
                            type: "success",
                            showCancelButton: false,
                            timer: 3000,
                            showConfirmButton: false
                        }).then(result => {
                            $.blockUI({
                                message: '<div style="font-size:20px; font-weight:bold"><div class="load-sistema">Recargando...</h1>',
                                baseZ: 9999999999
                            });
                            window.location = msg.header;
                        });
                        break;
                    case 2:
                        if (enviar === 1) {
                            window.location = msg.header;
                        } else
                            reloadCargos();
                        break;
                    case 3:
                        buscarObservaciones();
                        reloadCargos();
                        break;
                    case 4:
                    case 5:
                    case 6:
                        swal({
                            title: msg.Msg,
                            text: "Aguarde unos segundos mientras lo redireccionamos.",
                            type: "success",
                            showCancelButton: false,
                            timer: 3000,
                            showConfirmButton: false
                        }).then(result => {
                            $.blockUI({
                                message: '<div style="font-size:20px; font-weight:bold"><div class="load-sistema">Recargando...</h1>',
                                baseZ: 9999999999
                            });
                            window.location.reload();
                        });
                        break;
                    case 7:
                    case 8:
                    case 9:
                    case 10:
                        swal({
                            title: "Operaci\u00f3n completada correctamente.",
                            text: msg.Msg,
                            type: "success",
                            showCancelButton: false
                        });
                        gridReloadJuntas();
                        reloadCargos();
                }

                $(".msgaccionupd").html(msg.Msg);
                $.unblockUI();
            } else {
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

function listarJuntas() {
    let datos = {IdLicencia: $('#Id').val()};
    $('[data-toggle=tooltip]').tooltip('hide');
    jQuery("#listarDatos").jqGrid(
        {
            url: '/lic_licencias_administracion_juntas_lst_ajax.php?rand=' + Math.random(),
            postData: datos,
            datatype: "json",
            colNames: ['Id', 'Fecha', 'Hora', 'Regi\u00F3n/Nodo', 'Estado', 'Acciones'],
            colModel: [
                {name: 'Id', index: 'Id', width: 10, align: 'center'},
                {name: 'Fecha', index: 'Fecha', width: 35, align: 'center'},
                {name: 'Fecha', index: 'Fecha', width: 20, align: 'center'},
                {name: 'IdRegion', index: 'IdRegion', width: 20, align: 'center'},
                {name: 'IdEstado', index: 'Estado.Id', width: 20, align: 'center'},
                {name: 'Editar', index: 'Editar', width: 20, align: 'center', sortable: false}
            ],
            rowNum: 20,
            ajaxGridOptions: {cache: false},
            mtype: "POST",
            sortname: 'Id',
            viewrecords: true,
            sortorder: "DESC",
            styleUI: 'Bootstrap4',
            iconSet: 'fontAwesome',
            pager: '#pager2',
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
                $('[data-toggle="tooltip"]').tooltip();
                $('[data-toggle="popover"]').popover();
                rows.each(function (rowId, row) {
                    $(row).addClass(rowsData[rowId].class);
                });
            }
        });

    $(window).bind('resize', function () {
        $("#listarDatos").setGridWidth($("#LstDatos").width());
    }).trigger('resize');

}

function gridReloadJuntas() {
    $('[data-toggle=popover]').popover('hide');
    $('[data-toggle=tooltip]').tooltip('hide');
    let datos = {IdLicencia: $('#Id').val()};
    jQuery("#listarDatos").jqGrid('setGridParam', {
        url: '/lic_licencias_administracion_juntas_lst_ajax.php?rand=' + Math.random(),
        postData: datos,
        page: 1
    }).trigger("reloadGrid");
}

async function confirmar(titulo) {
    return Swal.fire({
        title: titulo,
        input: 'textarea',
        inputPlaceholder: titulo,
        inputAttributes: {
            'aria-label': titulo
        },
        showCancelButton: true
    });


}


function convertirFecha(fecha, formatoInput, formatoOutput) {
    if (!fecha)
        return fecha;
    let anio, mes, dia;
    switch (formatoInput) {
        case 'dd/mm/aaaa':
            [dia, mes, anio] = fecha.split('/');
            break;
        case 'aaaa-mm-dd':
            [anio, mes, dia] = fecha.split('-');
            break;
        default:
            return fecha;
    }
    switch ((formatoOutput)) {
        case 'dd/mm/aaaa':
            return dia + '/' + mes + '/' + anio;
        case 'aaaa-mm-dd':
            return anio + '-' + mes + '-' + dia;
        default:
            return fecha;
    }


}

function buscarObservaciones() {
    let param;
    $.blockUI({
        message: '<div style="font-size:20px; font-weight:bold"><div class="load-sistema">Buscando...</h1>',
        baseZ: 9999999999
    });
    param = $("#formaltaobservacion").serialize();
    param += "&accion=5";
    enviarDatosBusqueda(param, 5);
}

function insertarObservacion() {
    let param;
    $.blockUI({
        message: '<div style="font-size:20px; font-weight:bold"><div class="load-sistema">Agregando...</h1>',
        baseZ: 9999999999
    });
    param = $("#formaltaobservacion").serialize();
    param += "&accion=3";
    enviarDatosInsertarModificar(param, 3);
}


function buscarCargosAsociados() {
    $('#puestos').removeClass('hide');
    $.blockUI({message: '<div style="font-size:20px; font-weight:bold"><div class="load-sistema">Recalculando cargos afectados...</h1>', baseZ: 9999999999});
    let param = $('#formalta').serialize();
    param += '&' + $('#formCargosAfectados').serialize();
    for (let puesto of puestos)
        param += '&puestos[]=' + puesto;
    param += '&seleccionaCargos=' + $('#IdMotivo').data('cargo');
    param += '&revision=1&tipo=25';
    $.ajax({
        type: "POST",
        url: "/combo_ajax.php",
        data: param,
        dataType: "html",
        success: function (msg) {
            $('.tablePuestos').remove();
            $('#comboPuestos').html(msg);
            $.unblockUI();
        }
    });
}

function reloadCargos() {
    const id = $('#Id').val();
    if ('' === id)
        return;

    $('#lstCargosAfectados').html('<tr class="text-center table-info"><td colspan="10" ><i class="fas fa-spinner fa-spin" aria-hidden="true"></i>&nbsp;Recargando...</td></tr>');
    const param = {
        id: id,
        loadAjax: true
    };

    $.ajax({
        type: "POST",
        url: "/lic_licencias_administracion_am_cargos_lst_ajax.php",
        data: param,
        dataType: "html",
        success: r => {
            $('#lstCargosAfectados').html(r);
            $('.articulos.chzn-select').chosen({search_contains: true});
        },
        error: err => {
            console.error(err);
        }
    });
}

function ModificarReabierto() {
    let param;
    const form = $("#formalta");
    swal({
        title: "Guardar",
        text: "Est\u00E1 seguro ?",
        type: "warning",
        showCancelButton: true,
        confirmButtonText: "S\u00ED, guardar",
        confirmButtonColor: "#4ab657",
        cancelButtonText: "No, cancelar!"
    }).then(result => {
        if (result.value) {
            $.blockUI({
                message: '<div style="font-size:20px; font-weight:bold"><div class="load-sistema">Actualizando...</h1>',
                baseZ: 9999999999
            });

            param = form.serialize();
            param += "&" + form.serializeDisabledLicencias();
            param += "&" + $("#formaltaCertificados").serialize();
            param += '&' + $('#formCargosAfectados').serialize();
            param += '&seleccionaCargos=' + $('#IdMotivo').data('cargo');
            param += "&accion=11&enviar=0";
            enviarDatosInsertarModificar(param, 2);
        }
    });
}



function checkFechaFinAbierta(inicio = false){
    const input = document.querySelector('input[name="FechaFinAbierta"][type="checkbox"]');
    if (!input || !input.checked) {
        $(".con-fecha-fin").show().find("input, select").prop("disabled", false);
        $(".sin-fecha-fin").hide().find("input, select").prop("disabled", true);
    } else {
        if(!inicio) {
            swal({
                title: "",
                text: "Las licencias abiertas tienen fecha de fin 31/12/"+new Date().getFullYear()+" y se renuevan automáticamente.",
                type: "warning",
                showConfirmButton: true,
                confirmButtonColor: "#8bc71b",
                confirmButtonText: "Ok"
            }).then(result => {
                if (result.value) {
                    $(".con-fecha-fin").hide().find("input, select").prop("disabled", true);
                    $(".sin-fecha-fin").show().find("input, select").prop("disabled", false);
                }
                else {
                    input.checked = false;
                }
            });
        }
    }
}
