let licenciasListadas = false;
let template, empty, bestPictures, templateJunta;

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

const defaultTemplateLocal = `<div class="ProfileCard u-cf">
    <div class="ProfileCard-details">
        <div class="ProfileCard-realName"><span style="font-size: 12px;"><strong>Matricula {{Matricula}}</strong></span></div>
        <div class="ProfileCard-screenName"><span style="font-size: 13px;">{{nombre_completo}}</span></div>
    </div>
</div>`;

jQuery(document).ready(function () {
    template = Handlebars.compile($("#result-template").html() || defaultTemplateLocal);
    templateJunta = Handlebars.compile($("#result-template-junta").html() || defaultTemplateLocal);

    listarJuntas();
    listarMovimientos();
    const lnkSC = $('#formSolicitud').attr('action');
    $('.carousel').carousel({interval: false});

    if (($('#IdMotivo').val() !== "") && ($('#IdArticulo').val() === "")) {
        let id = $("#IdMotivo").val();
        buscarArticulos(id);
    }

    $(document).on('change', '.articulos', function () {

        let idPuesto = $(this).data('id');
        let optionSelected = $('#' + $(this).attr('id')).val();

        $('#IdArticuloPuesto_' + idPuesto).remove();

        if (optionSelected !== "") {
            $('#ArticulosPuestos_' + idPuesto).append('<input type="hidden" id="IdArticuloPuesto_' + idPuesto + '" value="' + optionSelected + '" name="IdArticuloPuesto[' + idPuesto + ']">');
        }
    });

    buscarObservaciones();

    $(document).on('click', '.btnSolicitud', function () {
        const id = $('#Id').val();
        const escuela = $(this).data('escuela');
        const puesto = $(this).data('puesto');
        const form = $('#formSolicitud');
        let idSC = $(this).data('id');
        let href = lnkSC;
        form.find('#IdEscuela').val(escuela);
        form.find('#IdPuesto').val(puesto);
        form.find('#IdLicencia').val(id);
        form.attr('action', href + idSC);
        form.submit();
    });

    $("#Inicio").datepicker({format: "dd/mm/yyyy", language: 'es', todayHighlight: true});
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

    $(document).on('click', '#btnNull', function () {
        return null;
    });

    $(document).on('click', '#btnModificarReabierto', function () {
        ModificarReabierto();
    });

    $(document).on('click', '#BtnModificarEnviar', function () {
        ModificarEnviar();
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

    $(document).on('click', '#BtnSiguiente', function () {
        $('#formResultadoPersona').submit();
    });

    $(document).on('click', '#Familiar', function () {
        if ($(this).prop("checked") === true) {
            $("#dataLicenciaFamiliar").removeClass("hide");
            $('input[name="Familiar"]').val(1);
        } else {
            $("#dataLicenciaFamiliar").addClass("hide");
            $('input[name="Familiar"]').val(0);
        }
    });

    $(document).on('change', '#IdDiagnostico', function () {
        $("#DetalleMotivo").remove();
        let id = $("#IdDiagnostico option:selected").val();
        if(!id) {
            $("#IdDiagnosticoDetalle").html('<option value="">Seleccione</option>').trigger("reloadGrid");;
            $.unblockUI();
        } else {
            BuscarMotivoDetalle(id);
        }
    });

    $(document).on('change', '#Inicio', function () {

        setTimeout(
            $.blockUI({
                message: '<div style="font-size:20px; font-weight:bold"><div class="load-sistema">Recalculando cargos afectados...</h1>',
                baseZ: 9999999999
            }),
            100);
        $.unblockUI();
        $("#TableCargosAfectados").remove();
        $("#IdCargosAfectados").html('');
        buscarCargosAsociados();
        calcularFechaFin();
    });

    $(document).on('change', '#Horas', function () {

        setTimeout(
            $.blockUI({
                message: '<div style="font-size:20px; font-weight:bold"><div class="load-sistema">Recalculando cargos afectados...</h1>',
                baseZ: 9999999999
            }),
            100);
        $.unblockUI();

        if ($("#Horas option:selected").val() === '0') {
            $(".hs").removeClass("col-md-6")
                .addClass("col-md-2");
            $(".duracion").removeClass("hide");
            $(".unidad").removeClass("hide").selectedIndex = 0;
            $('#Duracion').val(0);
            $("#Fin").val("");
        } else {
            $(".hs").removeClass("col-md-2")
                .addClass("col-md-6");
            $(".duracion").addClass("hide");
            $(".unidad").addClass("hide");
            $('#Duracion').val(0);
            $("#TableCargosAfectados").remove();
            $("#IdCargosAfectados").html('');
            buscarCargosAsociados();
            calcularFechaFin();
        }
    });

    $(document).on('change', '#Duracion', function () {

        setTimeout(
            $.blockUI({
                message: '<div style="font-size:20px; font-weight:bold"><div class="load-sistema">Recalculando cargos afectados...</h1>',
                baseZ: 9999999999
            }),
            100);
        $.unblockUI();

        $("#TableCargosAfectados").remove();
        $("#IdCargosAfectados").html('');
        buscarCargosAsociados();
        calcularFechaFin();
    });

    if ($("#Familiar").prop("checked") === true)
        $("#dataLicenciaFamiliar").css('display', 'block');


    $(document).on('click', '.btnMover', function () {
        cambiarEstado($(this).data('target'), $(this).data('nombre'), $(this).data('id'));
    });
    $(document).on('click', '.btnLiquidar', function () {
        cambiarEstadoLiquidarMov($(this).data('target'), $(this).data('nombre'), $(this).data('id'));
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
        $('#formNuevaJunta').trigger("reset");

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
        param += '&Fecha=' + fecha;
        param += '&Hora=' + $('#Hora').val();
        param += '&IdRegion=' + $('#IdRegion').val();
        param += '&IdTipoJunta=' + $('#IdTipoJunta').val();
        param += '&IdMotivoJunta=' + $('#IdMotivoJunta').val();
        param += '&Direccion=' + $('#Direccion').val();
        param += '&accion=' + tipo;
        $.blockUI({
            message: '<div style="font-size:20px; font-weight:bold"><div class="load-sistema">Agregando...</h1>',
            baseZ: 9999999999
        });

        enviarDatosInsertarModificar(param, tipo);
    });

    $(document).on('click', '.btnGuardarJunta', function () {

        swal({
            title: "Guardar",
            text: "\u00BFDesea continuar?",
            type: "warning",
            showCancelButton: true,
            confirmButtonText: "S\u00ED, guardar!",
            confirmButtonColor: "#4ab657",
            cancelButtonText: "No, cancelar"
        }).then(result => {
            if (result.value) {
                let param;
                param = 'IdAutorizante=' + $("#form_junta #IdAutorizanteJunta").val();
                param += '&FechaJunta=' + $("#form_junta #FechaJunta").val();
                param += '&HoraJunta=' + $("#form_junta #HoraJunta").val();
                param += '&Integracion=' + $("#form_junta #Integracion").val();
                param += '&Id=' + $(this).data('id');
                param += '&IdLicencia=' + $('#Id').val();
                param += '&accion=' + 13;
                $.blockUI({
                    message: '<div style="font-size:20px; font-weight:bold"><div class="load-sistema">Actualizando...</h1>',
                    baseZ: 9999999999
                });
                enviarDatosInsertarModificar(param, 13);
            }
        });
    });

    $(document).on('click', '.btnConvalidar', function () {
        const tipo = 8;
        let param = 'Id=' + $(this).data('id');
        param += '&IdLicencia=' + $('#Id').val();
        param += '&IdAutorizante=' + $("#form_junta #IdAutorizanteJunta").val();
        param += '&FechaJunta=' + $("#form_junta #FechaJunta").val();
        param += '&HoraJunta=' + $("#form_junta #HoraJunta").val();
        param += '&Integracion=' + $("#form_junta #Integracion").val();
        param += '&accion=' + tipo;

        confirmar('Ingrese una raz\u00f3n para la finalizaci\u00f3n')
            .then((resultado) => {
                if (resultado.hasOwnProperty('value')) {
                    param += '&Comentarios=' + escape(resultado.value);
                    $.blockUI({
                        message: '<div style="font-size:20px; font-weight:bold"><div class="load-sistema">Actualizando...</h1>',
                        baseZ: 9999999999
                    });
                    enviarDatosInsertarModificar(param, tipo);
                }
            });
    });

    $(document).on('click', '.btnDenegar', function () {
        const tipo = 14;
        let param = 'Id=' + $(this).data('id');
        param += '&IdLicencia=' + $('#Id').val();
        param += '&IdAutorizante=' + $("#form_junta #IdAutorizanteJunta").val();
        param += '&FechaJunta=' + $("#form_junta #FechaJunta").val();
        param += '&HoraJunta=' + $("#form_junta #HoraJunta").val();
        param += '&Integracion=' + $("#form_junta #Integracion").val();
        param += '&accion=' + tipo;

        confirmar('Ingrese una raz\u00f3n para la finalizaci\u00f3n')
            .then((resultado) => {
                if (resultado.hasOwnProperty('value')) {
                    param += '&Comentarios=' + escape(resultado.value);
                    $.blockUI({
                        message: '<div style="font-size:20px; font-weight:bold"><div class="load-sistema">Actualizando...</h1>',
                        baseZ: 9999999999
                    });
                    enviarDatosInsertarModificar(param, tipo);
                }
            });
    });

    $(document).on('click', '.btnAusente', function () {
        const tipo = 9;
        let param = 'Id=' + $(this).data('id');
        param += '&IdLicencia=' + $('#Id').val();
        param += '&IdAutorizante=' + $("#form_junta #IdAutorizanteJunta").val();
        param += '&FechaJunta=' + $("#form_junta #FechaJunta").val();
        param += '&HoraJunta=' + $("#form_junta #HoraJunta").val();
        param += '&Integracion=' + $("#form_junta #Integracion").val();
        param += '&accion=' + tipo;

        confirmar('Ingrese un comentario')
            .then((resultado) => {
                if (resultado.hasOwnProperty('value')) {
                    param += '&Comentarios=' + escape(resultado.value);
                    $.blockUI({
                        message: '<div style="font-size:20px; font-weight:bold"><div class="load-sistema">Actualizando...</h1>',
                        baseZ: 9999999999
                    });
                    enviarDatosInsertarModificar(param, tipo);
                }
            });
    });

    $(document).on('click', '.btnCancelar', function () {
        const tipo = 10;
        let param = 'Id=' + $(this).data('id');
        param += '&IdLicencia=' + $('#Id').val();
        param += '&IdAutorizante=' + $("#form_junta #IdAutorizanteJunta").val();
        param += '&FechaJunta=' + $("#form_junta #FechaJunta").val();
        param += '&HoraJunta=' + $("#form_junta #HoraJunta").val();
        param += '&Integracion=' + $("#form_junta #Integracion").val();
        param += '&accion=' + tipo;
        confirmar('Ingrese una raz\u00f3n para la cancelaci\u00f3n')
            .then((resultado) => {
                if (resultado.hasOwnProperty('value')) {
                    param += '&Comentarios=' + escape(resultado.value);
                    $.blockUI({
                        message: '<div style="font-size:20px; font-weight:bold"><div class="load-sistema">Actualizando...</h1>',
                        baseZ: 9999999999
                    });
                    enviarDatosInsertarModificar(param, tipo);
                }
            });
    });


    $(document).on('click', '.btnEditarJunta', function () {

        let param = "Id=" + $(this).data('id');
        let estado = $(this).data('estado');

        $.ajax({
            type: "POST",
            url: "/licencias/junta",
            data: param,
            success: function (msg) {
                $(".content-junta").html(msg.modal)
                $('#ModalEditarJunta').modal('show');
                $("#FechaJunta").datepicker({format: "dd/mm/yyyy", language: 'es', todayHighlight: true});
                if (estado === 1 && $("#MatriculaJunta").val() !== "")
                    BuscarAutorizantexJunta();

                $('#MatriculaJunta').typeahead(null, {
                    name: 'best-pictures',
                    display: 'id',
                    source: bestPictures,
                    displayKey: 'id',
                    templates: {
                        empty: [
                            '<div class="empty-message">',
                            'No se encuentran resultados',
                            '</div>'
                        ].join('\n'),
                        suggestion: template
                    },
                    limit: 5
                }).on('typeahead:selected', onAutocompletedJunta);
                $.unblockUI();
            }
        });
    });

    $(document).on('click', '#BtnEnviarComentario', function () {
        insertarObservacion();
    });

    $(document).on('click', '#btnEditar', function () {
        const btn = $(this);
        if (!btn.hasClass('hide')) {
            $('#Inicio').removeAttr('disabled');
            $('#FechaFinAbierta').removeAttr('disabled');
            $('#Horas').removeAttr('disabled');
            $('#Duracion').removeAttr('disabled');
            $('#Unidad').removeAttr('disabled');
            $('#Descripcion').removeAttr('disabled');
            $('#Matricula').removeAttr('disabled');
            $('#IdEspecialidad').removeAttr('disabled');
            $('#IdMotivo').prop('disabled', false).trigger("chosen:updated");
            /*$('#Familiar').removeAttr('disabled');
            $('#IdParentesco').removeAttr('disabled');
            $('#FamiliarDni').removeAttr('disabled');
            $('#FamiliarNombre').removeAttr('disabled');
            $('#FamiliarApellido').removeAttr('disabled');*/

            btn.addClass('hide');
            $('#btnCancelar').removeClass('hide');

            if (buscarCargos)
                buscarCargosAsociados();
        }
    });

    $(document).on('click', '#btnCancelar', function () {
        const btn = $(this);
        if (!btn.hasClass('hide')) {
            $('#IdPuesto').prop('disabled', true).trigger("chosen:updated");
            $('#Inicio').attr('disabled', true);
            $('#FechaFinAbierta').attr('disabled', true);
            $('#Horas').attr('disabled', true);
            $('#Duracion').attr('disabled', true);
            $('#Unidad').attr('disabled', true);
            $('#Descripcion').attr('disabled', true);
            $('#IdEspecialidad').attr('disabled', true);
            $('#Matricula').attr('disabled', true);
            $('#IdMotivo').prop('disabled', true).trigger("chosen:updated");
            /*$('#Familiar').attr('disabled', true);
            $('#IdParentesco').attr('disabled', true);
            $('#FamiliarDni').attr('disabled', true);
            $('#FamiliarNombre').attr('disabled', true);
            $('#FamiliarApellido').attr('disabled', true);*/

            btn.addClass('hide');
            $('#btnEditar').removeClass('hide');
        }
    });


    $(document).on('click', '.btnEliminarCargoAfectado', function () {
        const id = $(this).data('puesto');
        $('#IdArticulo_' + id).remove();
        $('#IdPuesto_' + id).remove();
        $('tr.fila_puesto_' + id).remove();
    });



    bestPictures = new Bloodhound({
        datumTokenizer: Bloodhound.tokenizers.obj.whitespace('nombre_completo'),
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        remote: {
            url: "/autocompletar",
            prepare: function (q, rq) {
                rq.type = "POST";
                rq.data = {
                    Nombre: $("#Matricula").val(),
                    Tipo: 4
                };
                return rq;
            },
            transport: function (obj, onS, onE) {

                $.ajax(obj).done(done).fail(fail).always(always);

                function done(data, textStatus, request) {
                    // Don't forget to fire the callback for Bloodhound
                    onS(data);
                }

                function fail(request, textStatus, errorThrown) {
                    // Don't forget the error callback for Bloodhound
                    onE(errorThrown);
                }

                function always() {
                    //$(".typeahead-loader").hide();
                }
            }
        }
    });



    $('#Matricula').typeahead(null, {
        name: 'best-pictures',
        display: 'id',
        source: bestPictures,
        displayKey: 'id',
        templates: {
            empty: [
                '<div class="empty-message">',
                'No se encuentran resultados',
                '</div>'
            ].join('\n'),
            suggestion: templateJunta
        },
        limit: 5
    }).on('typeahead:selected', onAutocompleted);
    bestPictures = new Bloodhound({
        datumTokenizer: Bloodhound.tokenizers.obj.whitespace('nombre_completo'),
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        remote: {
            url: "/autocompletar",
            prepare: function (q, rq) {
                rq.type = "POST";
                rq.data = {
                    Nombre: $("#MatriculaJunta").val(),
                    Tipo: 4
                };
                return rq;
            },
            transport: function (obj, onS, onE) {

                $.ajax(obj).done(done).fail(fail).always(always);

                function done(data, textStatus, request) {
                    onS(data);
                }

                function fail(request, textStatus, errorThrown) {
                    onE(errorThrown);
                }

                function always() {
                }
            }
        }
    });

    checkFechaFinAbierta(true);
    $(document).on('change', '#FechaFinAbierta', (e) => {
        checkFechaFinAbierta(false);
    })

});


function onAutocompleted($e, datum) {
    $('#Matricula').val(datum.Matricula);
    $('#IdAutorizante').val(datum.Id);
    BuscarAutorizante();
}

function onAutocompletedJunta($e, datum) {
    $('#MatriculaJunta').val(datum.Matricula);
    $('#IdAutorizanteJunta').val(datum.Id);
    BuscarAutorizantexJunta();
}


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

function cambiarEstadoLiquidarMov(idWorkflow, Nombre, id) {

    const tipo = 15;
    swal({
        title: '\u00bfEst\u00E1 seguro que desea enviar la licencia a Cargada en SIGA?',
        text: "\nRecuerde guardar en el caso que haya hecho modificaciones",
        type: "warning",
        showCancelButton: true,
        confirmButtonText: "S\u00ED, enviar!",
        confirmButtonColor: "#4ab657",
        cancelButtonText: "No, cancelar!"
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
    param = "Id=" + $("#formalta #IdAutorizante").val();
    param += "&Revision=1";
    param += "&accion=2";
    enviarDatosBusqueda(param, 2);
}

function BuscarAutorizantexJunta() {
    let param;
    $.blockUI({message: '<div style="font-size:20px; font-weight:bold"><div class="load-sistema">Buscando...</h1>', baseZ: 9999999999});
    param = "Id=" + $("#form_junta #IdAutorizanteJunta").val();
    param += "&accion=10";
    enviarDatosBusqueda(param, 10);
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

function buscarArticulos(id) {
    let param;
    $.blockUI({message: '<div style="font-size:20px; font-weight:bold"><div class="load-sistema">Buscando...</h1>', baseZ: 9999999999});
    param = "IdMotivo=" + id;
    param += '&Editable=' + true;
    param += "&accion=6";
    enviarDatosBusqueda(param, 6);
}

function calcularFechaFin() {
    const input = document.querySelector('input[name="FechaFinAbierta"][type="checkbox"]');
    if (!input || !input.checked) {
        let param;
        // $.blockUI({message: '<div style="font-size:20px; font-weight:bold"><div class="load-sistema">Buscando...</h1>', baseZ: 9999999999});
        param = $("#formalta").serialize();
        param += "&accion=9";
        enviarDatosBusqueda(param, 9);
    } else {
        $("#Fin").val(ultimoDiaAnio());
    }
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
                    $("#DatosMatricula").remove();
                    $("#DatosAutorizante").append(msg.Resultado);
                    $.unblockUI();

                    $('#Matricula').typeahead(null, {
                        name: 'best-pictures',
                        display: 'id',
                        source: bestPictures,
                        displayKey: 'id',
                        templates: {
                            empty: [
                                '<div class="empty-message">',
                                'No se encuentran resultados',
                                '</div>'
                            ].join('\n'),
                            suggestion: template
                        },
                        limit: 5
                    }).on('typeahead:selected', onAutocompleted);
                    return;
                case 4:
                    $("#DetalleDiagnostico").remove();
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
                case 9:
                    $("#Fin").val(msg.Resultado);
                    return;
                case 10:
                    $('#DatosMatriculaJunta').remove();
                    $('#DatosAutorizanteJunta').append(msg.Resultado);
                    $.unblockUI();
                    $('#MatriculaJunta').typeahead(null, {
                        name: 'best-pictures',
                        display: 'id',
                        source: bestPictures,
                        displayKey: 'id',
                        templates: {
                            empty: [
                                '<div class="empty-message">',
                                'No se encuentran resultados',
                                '</div>'
                            ].join('\n'),
                            suggestion: template
                        },
                        limit: 5
                    }).on('typeahead:selected', onAutocompletedJunta);
                    return;
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
                    case 14:
                        swal({
                            title: "Operaci\u00f3n completada correctamente.",
                            text: msg.Msg,
                            type: "success",
                            showCancelButton: false
                        });
                        $("#ModalJunta").modal('hide');
                        $("#ModalEditarJunta").modal('hide');
                        gridReloadJuntas();
                        reloadCargos();
                        break;
                    case 13:
                        swal({
                            title: "Operaci\u00f3n completada correctamente.",
                            text: msg.Msg,
                            type: "success",
                            showCancelButton: false
                        });
                        break;
                    case 15:
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
            url: '/licencias/medicas/revision/juntas/lst?rand=' + Math.random(),
            postData: datos,
            datatype: "json",
            colNames: ['Id', 'Fecha', 'Hora', 'Regi\u00F3n/Nodo', 'Tipo', 'Motivo', 'Estado', 'Acciones'],
            colModel: [
                {name: 'Id', index: 'Id', width: 10, align: 'center'},
                {name: 'Fecha', index: 'Fecha', width: 20, align: 'center'},
                {name: 'Fecha', index: 'Fecha', width: 20, align: 'center'},
                {name: 'IdRegion', index: 'IdRegion', width: 20, align: 'center'},
                {name: 'IdTipoJunta', index: 'IdTipoJunta', width: 20, align: 'center'},
                {name: 'IdMotivoJUnta', index: 'IdMotivoJUnta', width: 20, align: 'center'},
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
            height: 150,
            caption: "",
            responsive:true,
            autowidth: true,  // set 'true' here
            emptyrecords: "Sin datos para mostrar.",
            loadError: function (xhr, st, err) {
            },
            loadComplete: function (data) {
                let rows = $(this).find('tbody').children('tr[class!=jqgfirstrow]');
                let rowsData = data.rows;
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

function listarMovimientos() {
    let datos = {IdLicencia: $('#Id').val()};
    jQuery("#listarMovimientos").jqGrid(
        {
            url: '/licencias/medicas/revision/movimientos/lst?rand=' + Math.random(),
            postData: datos,
            datatype: "json",
            colNames: ['Id', 'IdPuesto', 'Movimiento', 'Fecha de Movimiento', 'Estado'],
            colModel: [
                {name: 'IdMovimiento', index: 'IdMovimiento', width: 10, align: 'center'},
                {name: 'IdPuesto', index: 'IdPuesto', width: 20, align: 'center'},
                {name: 'NombreMov', index: 'NombreMov', width: 20, align: 'center'},
                {name: 'FechaMovimiento', index: 'FechaMovimiento', width: 20, align: 'center'},
                {name: 'NombreEstado', index: 'NombreEstado', width: 20, align: 'center'}
            ],
            rowNum: 20,
            ajaxGridOptions: {cache: false},
            mtype: "POST",
            sortname: 'IdMovimiento',
            viewrecords: true,
            sortorder: "DESC",
            styleUI: 'Bootstrap4',
            iconSet: 'fontAwesome',
            pager: '#pager3',
            height: 150,
            caption: "",
            responsive:true,
            autowidth: true,  // set 'true' here
            emptyrecords: "Sin datos para mostrar.",
            loadError: function (xhr, st, err) {
            },
        });

    $(window).bind('resize', function () {
        $("#listarMovimientos").setGridWidth($("#LstMovimientos").width());
    }).trigger('resize');

}

function gridReloadJuntas() {
    $('[data-toggle=popover]').popover('hide');
    $('[data-toggle=tooltip]').tooltip('hide');
    let datos = {IdLicencia: $('#Id').val()};
    jQuery("#listarDatos").jqGrid('setGridParam', {
        url: '/licencias/medicas/revision/juntas/lst?rand=' + Math.random(),
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
        url: "/cargar-combo",
        data: param,
        dataType: "html",
        success: function (msg) {
            $('.tablePuestos').remove();
            $('#comboPuestos').html(msg);
            // $('#IdPuesto').chosen({search_contains: true});
            $.unblockUI();
        }
    });
}

function ocultarCamposFinLicencia() {
    $("#Horas").val('').trigger("change");
    $(".hs").addClass("hide");
    $("#Fin").prop("disabled", false);
    $(".fin").addClass("hide");
}

function mostrarCamposFinLicencia() {
    $("#Horas").val('').trigger("change");
    $(".hs").removeClass("hide");
    $("#Fin").val('').prop("disabled", true);
    $(".fin").removeClass("hide");
}

function ultimoDiaAnio() {
    return "31/12/" + new Date().getFullYear();
}

function checkFechaFinAbierta(esInicio = false) {
    const input = document.querySelector('input[name="FechaFinAbierta"][type="checkbox"]');
    if (!input || !input.checked) {
        if (!esInicio) {
            mostrarCamposFinLicencia();
        }
        return true;
    } else {
        if (!esInicio) {
            swal({
                title: "",
                text: "Seleccione esta opción si la licencia no tiene una fecha de finalización definida.",
                type: "warning",
                showConfirmButton: true,
                confirmButtonColor: "#8bc71b",
                confirmButtonText: "Ok"
            }).then(result => {
                if (result.value) {
                    ocultarCamposFinLicencia();
                    calcularFechaFin();
                    buscarCargosAsociados();
                } else {
                    input.checked = false;
                }
            });
        }
    }
}
