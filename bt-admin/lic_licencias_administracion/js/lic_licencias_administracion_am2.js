/**
 * JavaScript para el módulo de Revisión de Licencias - SUNA
 * Bootstrap 4 + jQuery
 */

// Variables globales
let licenciasListadas = false;
let template, templateJunta, templateCargo, templateCertificado, templateComentario, templateMovimiento;
let bestPictures;
let certificadosData = [];
let certificadoActualIndex = 0;
let buscarCargos = true;

// Importaciones necesarias
const $ = window.$;
const jQuery = window.jQuery;
const Handlebars = window.Handlebars;
const Bloodhound = window.Bloodhound;
const Swal = window.Swal;

// Serializar formularios con campos disabled
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
};

// Serializar objeto de formulario
$.fn.serializeObject = function() {
    let o = {};
    let a = this.serializeArray();
    $.each(a, function() {
        if (o[this.name]) {
            if (!o[this.name].push) {
                o[this.name] = [o[this.name]];
            }
            o[this.name].push(this.value || '');
        } else {
            o[this.name] = this.value || '';
        }
    });
    return o;
};

// ========================================
// INICIALIZACIÓN
// ========================================
jQuery(document).ready(function () {
    // Compilar templates Handlebars
    inicializarTemplates();

    // Inicializar plugins
    inicializarPlugins();

    // Cargar datos iniciales
    cargarDatosLicencia();

    // Configurar eventos
    configurarEventos();

    // Configurar drag & drop para certificados
    configurarDragDrop();

    // Configurar autocompletado
    configurarTypeahead();
});

// ========================================
// TEMPLATES HANDLEBARS
// ========================================
function inicializarTemplates() {
    // Template por defecto para autorizante
    const defaultTemplate = `<div class="ProfileCard u-cf">
        <div class="ProfileCard-details">
            <div class="ProfileCard-realName"><span style="font-size: 12px;"><strong>Matrícula {{Matricula}}</strong></span></div>
            <div class="ProfileCard-screenName"><span style="font-size: 13px;">{{nombre_completo}}</span></div>
        </div>
    </div>`;

    template = Handlebars.compile($("#result-template").html() || defaultTemplate);
    templateJunta = Handlebars.compile($("#result-template-junta").html() || defaultTemplate);

    // Compilar otros templates si existen
    if ($("#template-cargo").length) {
        templateCargo = Handlebars.compile($("#template-cargo").html());
    }
    if ($("#template-certificado").length) {
        templateCertificado = Handlebars.compile($("#template-certificado").html());
    }
    if ($("#template-comentario").length) {
        templateComentario = Handlebars.compile($("#template-comentario").html());
    }
    if ($("#template-movimiento").length) {
        templateMovimiento = Handlebars.compile($("#template-movimiento").html());
    }
    if ($("#template-junta").length) {
        Handlebars.compile($("#template-junta").html());
    }
}

// ========================================
// PLUGINS
// ========================================
function inicializarPlugins() {
    // Chosen selects
    $('.chzn-select').chosen({
        search_contains: true,
        width: '100%',
        no_results_text: 'No se encontraron resultados'
    });

    // Datepickers
    $(".datepicker, #Inicio, #Fecha").datepicker({
        format: "dd/mm/yyyy",
        language: 'es',
        todayHighlight: true,
        autoclose: true
    });

    // Carousel
    $('.carousel').carousel({ interval: false });
}

// ========================================
// EVENTOS
// ========================================
function configurarEventos() {
    // === SECCIONES COLAPSABLES ===
    $(document).on('click', '.card-header[data-toggle="collapse"]', function() {
        $(this).find('.fa-chevron-down').toggleClass('collapsed');
    });

    // === NAVEGACIÓN POR SECCIONES ===
    $(document).on('click', '.nav-pills .nav-link', function(e) {
        e.preventDefault();
        const target = $(this).attr('href');
        if (target && target !== '#') {
            $('html, body').animate({
                scrollTop: $(target).offset().top - 100
            }, 500);
        }
    });

    // === EDICIÓN DE LICENCIA ===
    $(document).on('click', '#btnEditar', function() {
        habilitarEdicion();
    });

    $(document).on('click', '#btnCancelar', function() {
        deshabilitarEdicion();
    });

    $(document).on('click', '#btnModificar', function() {
        guardarLicencia();
    });

    // === ACCIONES PRINCIPALES ===
    $(document).on('click', '#btnAprobar', function() {
        aprobarLicencia();
    });

    $(document).on('click', '#btnDenegar', function() {
        denegarLicencia();
    });

    $(document).on('click', '#btnAnular', function() {
        anularLicencia();
    });

    $(document).on('click', '#btnSolicitarJunta, #btnFechaJunta, #btnAgregarPrimeraJunta', function() {
        $('#formNuevaJunta').trigger("reset");
        $('#ModalJunta').modal('show');
    });

    // === JUNTAS MÉDICAS ===
    $(document).on('click', '#btnAgregarJunta', function() {
        agregarJunta();
    });

    $(document).on('click', '.btnEditarJunta', function() {
        editarJunta($(this).data('id'), $(this).data('estado'));
    });

    $(document).on('click', '.btnConvalidar', function() {
        convalidarJunta($(this).data('id'));
    });

    $(document).on('click', '.btnAusente', function() {
        marcarAusenteJunta($(this).data('id'));
    });

    $(document).on('click', '.btnCancelar', function() {
        cancelarJunta($(this).data('id'));
    });

    $(document).on('click', '.btnGuardarJunta', function() {
        guardarJunta($(this).data('id'));
    });

    // === CARGOS ===
    $(document).on('change', '.articulos', function() {
        let idPuesto = $(this).data('id');
        let optionSelected = $(this).val();
        $('#IdArticuloPuesto_' + idPuesto).remove();
        if (optionSelected !== "") {
            $('#ArticulosPuestos').append(
                '<input type="hidden" id="IdArticuloPuesto_' + idPuesto + '" value="' + optionSelected + '" name="IdArticuloPuesto[' + idPuesto + ']">'
            );
        }
    });

    $(document).on('click', '.btnEliminarCargoAfectado', function() {
        const id = $(this).data('puesto');
        $('#IdArticulo_' + id).remove();
        $('#IdPuesto_' + id).remove();
        $('tr.fila_puesto_' + id).remove();
        $('.cargo-item[data-puesto="' + id + '"]').fadeOut(300, function() { $(this).remove(); });
        actualizarContadorCargos();
    });

    // === FECHAS Y DURACIÓN ===
    $(document).on('change', '#Inicio', function() {
        recalcularCargos();
    });

    $(document).on('change', '#Duracion', function() {
        recalcularCargos();
    });

    $(document).on('change', '#Horas', function() {
        manejarCambioHoras();
    });

    // === DIAGNÓSTICO ===
    $(document).on('change', '#IdDiagnostico', function() {
        let id = $("#IdDiagnostico option:selected").val();
        if (!id) {
            $("#IdDiagnosticoDetalle").html('<option value="">Seleccione</option>').trigger("chosen:updated");
        } else {
            buscarMotivoDetalle(id);
        }
    });

    $(document).on('change', '#IdMotivo', function() {
        let id = $(this).val();
        if (id) {
            buscarArticulos(id);
        }
    });

    // === FAMILIAR ===
    $(document).on('click', '#Familiar', function() {
        if ($(this).prop("checked")) {
            $("#dataLicenciaFamiliar").removeClass("hide");
            $('input[name="Familiar"]').val(1);
        } else {
            $("#dataLicenciaFamiliar").addClass("hide");
            $('input[name="Familiar"]').val(0);
        }
    });

    $(document).on('blur', '#FamiliarDni', function() {
        buscarFamiliar();
    });

    // === CERTIFICADOS ===
    $(document).on('change', '#inputCertificados', function() {
        subirCertificados(this.files);
    });

    $(document).on('click', '.btnVerCertificado, .certificado-preview', function() {
        const id = $(this).data('id');
        const url = $(this).data('url');
        verCertificado(id, url);
    });

    $(document).on('click', '.btnEliminarCertificado', function() {
        eliminarCertificado($(this).data('id'));
    });

    $(document).on('click', '#btnCertificadoAnterior', function() {
        navegarCertificado(-1);
    });

    $(document).on('click', '#btnCertificadoSiguiente', function() {
        navegarCertificado(1);
    });

    // === COMENTARIOS ===
    $(document).on('click', '#BtnEnviarComentario', function() {
        enviarComentario();
    });

    $(document).on('keypress', '#textoComentario', function(e) {
        if (e.which === 13 && !e.shiftKey) {
            e.preventDefault();
            enviarComentario();
        }
    });
}

// ========================================
// DRAG & DROP CERTIFICADOS
// ========================================
function configurarDragDrop() {
    const dropZone = document.getElementById('dropZoneCertificados');
    if (!dropZone) return;

    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, preventDefaults, false);
    });

    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    ['dragenter', 'dragover'].forEach(eventName => {
        dropZone.addEventListener(eventName, () => {
            dropZone.classList.add('dragover');
        }, false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, () => {
            dropZone.classList.remove('dragover');
        }, false);
    });

    dropZone.addEventListener('drop', (e) => {
        const files = e.dataTransfer.files;
        subirCertificados(files);
    }, false);
}

// ========================================
// TYPEAHEAD / AUTOCOMPLETADO
// ========================================
function configurarTypeahead() {
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
                $.ajax(obj).done(function(data) {
                    onS(data);
                }).fail(function(request, textStatus, errorThrown) {
                    onE(errorThrown);
                });
            }
        }
    });

    $('#Matricula').typeahead(null, {
        name: 'best-pictures',
        display: 'id',
        source: bestPictures,
        displayKey: 'id',
        templates: {
            empty: '<div class="empty-message p-2 text-muted">No se encuentran resultados</div>',
            suggestion: template
        },
        limit: 5
    }).on('typeahead:selected', onAutocompleted);
}

function onAutocompleted(e, datum) {
    $('#IdAutorizante').val(datum.id);
    $('#NombreAutorizante').text(datum.nombre_completo);
    $('#MatriculaAutorizante').text(datum.Matricula);
    $('#EspecialidadAutorizante').text(datum.especialidad || '--');
    $('#DatosAutorizante').slideDown();
}

// ========================================
// CARGA DE DATOS
// ========================================
function cargarDatosLicencia() {
    const idLicencia = $('#Id').val();
    if (!idLicencia) return;

    $.blockUI({
        message: '<div class="text-center"><div class="load-sistema"></div><p class="mt-2">Cargando datos...</p></div>',
        baseZ: 9999999999
    });

    // Cargar datos de la licencia
    $.ajax({
        type: "POST",
        url: "/licencias/medicas/revision/datos",
        data: { Id: idLicencia },
        dataType: "json",
        success: function(response) {
            if (response.success) {
                llenarDatosLicencia(response.data);
            } else {
                mostrarError(response.error_description || 'Error al cargar datos');
            }
            $.unblockUI();
        },
        error: function() {
            mostrarError('Error de conexión');
            $.unblockUI();
        }
    });

    // Cargar juntas
    listarJuntas();

    // Cargar movimientos
    listarMovimientos();

    // Cargar observaciones
    buscarObservaciones();

    // Cargar certificados
    listarCertificados();
}

function llenarDatosLicencia(data) {
    // Header
    $('#HeaderNombreAgente').text(data.Persona?.NombreCompleto || '--');
    $('#HeaderDuracion').text((data.Duracion || '--') + ' días');
    $('#HeaderFechaEnvio').text(data.FechaEnvio || '--/--/----');

    // Estado
    actualizarEstado(data.Estado);

    // Datos del agente
    $('#NombreCompletoAgente').text(data.Persona?.NombreCompleto || '--');
    $('#DniAgente').text(data.Persona?.Dni || '--');
    $('#CuilAgente').text(data.Persona?.Cuil || '--');
    $('#LegajoAgente').text(data.Persona?.Legajo || '--');
    $('#EmailAgente').val(data.Persona?.Email || '');
    $('#TelefonoAgente').val(data.Persona?.Telefono || '');
    $('#CelularAgente').val(data.Persona?.Celular || '');
    $('#FechaNacAgente').val(data.Persona?.FechaNacimiento || '');
    $('#DomicilioAgente').val(data.Persona?.Domicilio || '');
    $('#IdPersona').val(data.Persona?.Id || '');

    // Tipo de licencia
    $('#Inicio').val(data.Inicio || '');
    $('#Fin').val(data.Fin || '');
    $('#Duracion').val(data.Duracion || '');
    $('#Horas').val(data.Horas || '0');
    $('#FechaFinAbierta').prop('checked', data.FechaFinAbierta == 1);
    $('#Descripcion').val(data.Descripcion || '');

    // Motivo y artículo
    if (data.IdMotivo) {
        $('#IdMotivo').val(data.IdMotivo).trigger("chosen:updated");
        if (data.IdArticulo) {
            buscarArticulos(data.IdMotivo, data.IdArticulo);
        }
    }

    // Diagnóstico
    if (data.IdDiagnostico) {
        $('#IdDiagnostico').val(data.IdDiagnostico).trigger("chosen:updated");
        if (data.IdDiagnosticoDetalle) {
            buscarMotivoDetalle(data.IdDiagnostico, data.IdDiagnosticoDetalle);
        }
    }

    // Autorizante
    if (data.Autorizante) {
        $('#Matricula').val(data.Autorizante.Matricula || '');
        $('#IdAutorizante').val(data.Autorizante.Id || '');
        $('#NombreAutorizante').text(data.Autorizante.NombreCompleto || '--');
        $('#MatriculaAutorizante').text(data.Autorizante.Matricula || '--');
        $('#EspecialidadAutorizante').text(data.Autorizante.Especialidad || '--');
        $('#IdEspecialidad').val(data.Autorizante.IdEspecialidad || '').trigger("chosen:updated");
        $('#DatosAutorizante').show();
    }

    // Familiar
    if (data.Familiar == 1) {
        $('#Familiar').prop('checked', true);
        $('#dataLicenciaFamiliar').removeClass('hide');
        $('#FamiliarDni').val(data.FamiliarDni || '');
        $('#FamiliarNombre').val(data.FamiliarNombre || '');
        $('#FamiliarApellido').val(data.FamiliarApellido || '');
        $('#IdParentesco').val(data.IdParentesco || '');
    }

    // Fechas info
    $('#FechaCreacion').text(data.FechaCreacion || '--/--/----');
    $('#FechaEnvio').text(data.FechaEnvio || '--/--/----');
    $('#FechaModificacion').text(data.FechaModificacion || '--/--/----');

    // Cargar cargos
    if (data.Cargos && data.Cargos.length > 0) {
        renderizarCargos(data.Cargos);
    }
}

function actualizarEstado(estado) {
    if (!estado) return;

    const clases = {
        'danger': 'badge-danger',
        'warning': 'badge-warning',
        'success': 'badge-success',
        'info': 'badge-info',
        'default': 'badge-secondary',
        'light': 'badge-light',
        'secondary': 'badge-secondary'
    };

    const clase = clases[estado.Clase] || 'badge-secondary';

    $('#BadgeEstado').removeClass().addClass('badge badge-pill px-3 py-2 mr-3 ' + clase);
    $('#TextoEstado').text(estado.NombrePublico || estado.Nombre || 'Desconocido');

    $('#EstadoActualBadge').removeClass().addClass('badge badge-pill px-4 py-2 ' + clase);
    $('#EstadoActualTexto').text(estado.NombrePublico || estado.Nombre || 'Desconocido');
}

// ========================================
// JUNTAS MÉDICAS
// ========================================
function listarJuntas() {
    const idLicencia = $('#Id').val();
    if (!idLicencia) return;

    $.ajax({
        type: "POST",
        url: "/licencias/medicas/revision/juntas",
        data: { IdLicencia: idLicencia },
        dataType: "json",
        success: function(response) {
            if (response.success && response.data) {
                renderizarJuntas(response.data);
            }
        }
    });
}

function renderizarJuntas(juntas) {
    const container = $('#listaJuntas');
    container.find('.junta-item').remove();

    if (!juntas || juntas.length === 0) {
        $('#sinJuntas').show();
        $('#contadorJuntas, #badgeJuntas').text('0');
        return;
    }

    $('#sinJuntas').hide();
    $('#contadorJuntas, #badgeJuntas').text(juntas.length);

    juntas.forEach(function(junta) {
        // Usar template si existe, sino crear HTML manual
        const html = `
            <div class="junta-item border-bottom" data-id="${junta.Id}">
                <div class="p-3">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="d-flex align-items-start">
                                <div class="mr-3">
                                    <div class="avatar-circle" style="width: 45px; height: 45px; background: ${junta.Convalidada ? '#28a745' : '#fd7e14'}; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                        <i class="fas ${junta.Convalidada ? 'fa-check' : 'fa-calendar-check'} text-white"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1 font-weight-bold">
                                        Junta #${junta.Id} - ${junta.TipoJunta || 'Junta Médica'}
                                        <span class="badge badge-${junta.Convalidada ? 'success' : (junta.Cancelada ? 'danger' : (junta.Ausente ? 'warning' : 'info'))} ml-2">
                                            ${junta.Convalidada ? 'Convalidada' : (junta.Cancelada ? 'Cancelada' : (junta.Ausente ? 'Ausente' : 'Pendiente'))}
                                        </span>
                                    </h6>
                                    <p class="mb-1 small">
                                        <i class="fas fa-calendar mr-1 text-muted"></i>
                                        <strong>${junta.Fecha || '--'}</strong> a las <strong>${junta.Hora || '--'}</strong>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 border-left">
                            <h6 class="small text-muted mb-2"><i class="fas fa-hospital mr-1"></i>Centro Médico</h6>
                            <p class="mb-1 small font-weight-bold">${junta.CentroMedico?.Nombre || junta.Region || '--'}</p>
                            <p class="mb-0 small text-muted"><i class="fas fa-map-marker-alt mr-1"></i>${junta.Direccion || '--'}</p>
                        </div>
                    </div>
                    ${!junta.Convalidada && !junta.Cancelada ? `
                    <div class="mt-3 pt-3 border-top">
                        <div class="btn-group btn-group-sm">
                            <button type="button" class="btn btn-outline-primary btnEditarJunta" data-id="${junta.Id}" data-estado="${junta.IdEstado}">
                                <i class="fas fa-edit mr-1"></i>Editar
                            </button>
                            <button type="button" class="btn btn-outline-success btnConvalidar" data-id="${junta.Id}">
                                <i class="fas fa-check mr-1"></i>Convalidar
                            </button>
                            <button type="button" class="btn btn-outline-warning btnAusente" data-id="${junta.Id}">
                                <i class="fas fa-user-slash mr-1"></i>Ausente
                            </button>
                            <button type="button" class="btn btn-outline-danger btnCancelar" data-id="${junta.Id}">
                                <i class="fas fa-times mr-1"></i>Cancelar
                            </button>
                        </div>
                    </div>
                    ` : ''}
                </div>
            </div>
        `;
        container.append(html);
    });
}

function agregarJunta() {
    const fecha = $('#Fecha').val();
    const hora = $('#Hora').val();

    if (!fecha || !hora) {
        mostrarError('Complete fecha y hora de la junta');
        return;
    }

    let param = {
        IdLicencia: $('#Id').val(),
        Fecha: fecha,
        Hora: hora,
        IdRegion: $('#IdRegion').val(),
        IdTipoJunta: $('#IdTipoJunta').val(),
        IdMotivoJunta: $('#IdMotivoJunta').val(),
        Direccion: $('#Direccion').val(),
        accion: 7
    };

    $.blockUI({
        message: '<div class="text-center"><div class="load-sistema"></div><p class="mt-2">Agregando junta...</p></div>',
        baseZ: 9999999999
    });

    enviarDatosJunta(param, function() {
        $('#ModalJunta').modal('hide');
        listarJuntas();
    });
}

function convalidarJunta(id) {
    confirmarAccion('Ingrese una razón para la convalidación')
        .then((resultado) => {
            if (resultado.value) {
                let param = {
                    Id: id,
                    IdLicencia: $('#Id').val(),
                    Comentarios: resultado.value,
                    accion: 8
                };

                $.blockUI({
                    message: '<div class="text-center"><div class="load-sistema"></div><p class="mt-2">Convalidando...</p></div>',
                    baseZ: 9999999999
                });

                enviarDatosJunta(param, function() {
                    listarJuntas();
                });
            }
        });
}

function marcarAusenteJunta(id) {
    confirmarAccion('Ingrese un comentario')
        .then((resultado) => {
            if (resultado.value) {
                let param = {
                    Id: id,
                    IdLicencia: $('#Id').val(),
                    Comentarios: resultado.value,
                    accion: 9
                };

                $.blockUI({
                    message: '<div class="text-center"><div class="load-sistema"></div><p class="mt-2">Actualizando...</p></div>',
                    baseZ: 9999999999
                });

                enviarDatosJunta(param, function() {
                    listarJuntas();
                });
            }
        });
}

function cancelarJunta(id) {
    confirmarAccion('Ingrese una razón para la cancelación')
        .then((resultado) => {
            if (resultado.value) {
                let param = {
                    Id: id,
                    IdLicencia: $('#Id').val(),
                    Comentarios: resultado.value,
                    accion: 10
                };

                $.blockUI({
                    message: '<div class="text-center"><div class="load-sistema"></div><p class="mt-2">Cancelando...</p></div>',
                    baseZ: 9999999999
                });

                enviarDatosJunta(param, function() {
                    listarJuntas();
                });
            }
        });
}

function editarJunta(id, estado) {
    $.ajax({
        type: "POST",
        url: "/licencias/junta",
        data: { Id: id },
        success: function(msg) {
            $(".content-junta").html(msg.modal);
            $('#ModalEditarJunta').modal('show');
            $("#FechaJunta").datepicker({format: "dd/mm/yyyy", language: 'es', todayHighlight: true});
        }
    });
}

function enviarDatosJunta(param, callback) {
    $.ajax({
        type: "POST",
        url: "/licencias/medicas/revision/upd",
        data: param,
        dataType: "json",
        success: function(response) {
            $.unblockUI();
            if (response.success) {
                mostrarExito(response.mensaje || 'Operación exitosa');
                if (callback) callback();
            } else {
                mostrarError(response.error_description || 'Error en la operación');
            }
        },
        error: function() {
            $.unblockUI();
            mostrarError('Error de conexión');
        }
    });
}

// ========================================
// CARGOS
// ========================================
function renderizarCargos(cargos) {
    const container = $('#IdCargosAfectados');
    container.empty();

    if (!cargos || cargos.length === 0) {
        $('#sinCargos').show();
        $('#contadorCargos, #badgeCargos').text('0');
        return;
    }

    $('#sinCargos').hide();
    $('#contadorCargos, #badgeCargos').text(cargos.length);

    cargos.forEach(function(cargo) {
        const html = `
            <div class="cargo-item border-bottom" data-puesto="${cargo.IdPuesto}">
                <div class="p-3">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input cargo-checkbox"
                                       id="checkCargo_${cargo.IdPuesto}" checked>
                                <label class="custom-control-label" for="checkCargo_${cargo.IdPuesto}"></label>
                            </div>
                        </div>
                        <div class="col-auto">
                            <div class="avatar-circle" style="width: 45px; height: 45px; background: #17a2b8; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-id-badge text-white"></i>
                            </div>
                        </div>
                        <div class="col">
                            <h6 class="mb-1 font-weight-bold">${cargo.Cargo || cargo.Nombre || '--'}</h6>
                            <p class="mb-0 small text-muted">
                                <i class="fas fa-school mr-1"></i>${cargo.Escuela || '--'}
                                <span class="mx-2">|</span>
                                <i class="fas fa-map-marker-alt mr-1"></i>${cargo.Localidad || '--'}
                            </p>
                        </div>
                        <div class="col-md-3 text-right">
                            <small class="text-muted d-block">
                                <i class="fas fa-hashtag mr-1"></i>Puesto: <strong>${cargo.IdPuesto}</strong>
                            </small>
                        </div>
                        <div class="col-auto">
                            <button type="button" class="btn btn-sm btn-outline-danger btnEliminarCargoAfectado"
                                    data-puesto="${cargo.IdPuesto}" title="Quitar cargo">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <input type="hidden" id="IdPuesto_${cargo.IdPuesto}" name="IdPuesto[]" value="${cargo.IdPuesto}">
            </div>
        `;
        container.append(html);
    });
}

function actualizarContadorCargos() {
    const count = $('.cargo-item').length;
    $('#contadorCargos, #badgeCargos').text(count);
}

function recalcularCargos() {
    $.blockUI({
        message: '<div class="text-center"><div class="load-sistema"></div><p class="mt-2">Recalculando cargos...</p></div>',
        baseZ: 9999999999
    });

    buscarCargosAsociados();
    calcularFechaFin();

    setTimeout(() => $.unblockUI(), 500);
}

function buscarCargosAsociados() {
    const idPersona = $('#IdPersona').val();
    const inicio = $('#Inicio').val();

    if (!idPersona || !inicio) return;

    $.ajax({
        type: "POST",
        url: "/licencias/medicas/cargos",
        data: {
            IdPersona: idPersona,
            Inicio: inicio
        },
        dataType: "json",
        success: function(response) {
            if (response.success && response.data) {
                renderizarCargos(response.data);
            }
        }
    });
}

function calcularFechaFin() {
    const inicio = $('#Inicio').val();
    const duracion = $('#Duracion').val();
    const unidad = $('#Unidad').val();

    if (!inicio || !duracion) return;

    // Calcular fecha fin (simplificado, ajustar según lógica real)
    const partes = inicio.split('/');
    const fechaInicio = new Date(partes[2], partes[1] - 1, partes[0]);

    if (unidad == 1) { // Días
        fechaInicio.setDate(fechaInicio.getDate() + parseInt(duracion) - 1);
    } else { // Meses
        fechaInicio.setMonth(fechaInicio.getMonth() + parseInt(duracion));
        fechaInicio.setDate(fechaInicio.getDate() - 1);
    }

    const fin = ('0' + fechaInicio.getDate()).slice(-2) + '/' +
        ('0' + (fechaInicio.getMonth() + 1)).slice(-2) + '/' +
        fechaInicio.getFullYear();

    $('#Fin').val(fin);
}

function manejarCambioHoras() {
    if ($("#Horas option:selected").val() === '0') {
        $(".duracion").removeClass("hide");
        $(".unidad").removeClass("hide");
    } else {
        $(".duracion").addClass("hide");
        $(".unidad").addClass("hide");
        $('#Duracion').val(1);
        calcularFechaFin();
    }
    buscarCargosAsociados();
}

// ========================================
// CERTIFICADOS
// ========================================
function listarCertificados() {
    const idLicencia = $('#Id').val();
    if (!idLicencia) return;

    $.ajax({
        type: "POST",
        url: "/licencias/medicas/revision/certificados",
        data: { IdLicencia: idLicencia },
        dataType: "json",
        success: function(response) {
            if (response.success && response.data) {
                certificadosData = response.data;
                renderizarCertificados(response.data);
            }
        }
    });
}

function renderizarCertificados(certificados) {
    const container = $('#listaCertificados');
    container.empty();

    if (!certificados || certificados.length === 0) {
        $('#sinCertificados').show();
        $('#contadorCertificados, #badgeCertificados').text('0');
        return;
    }

    $('#sinCertificados').hide();
    $('#contadorCertificados, #badgeCertificados').text(certificados.length);

    certificados.forEach(function(cert, index) {
        const esPDF = cert.Nombre?.toLowerCase().endsWith('.pdf');
        const html = `
            <div class="certificado-item mb-2" data-id="${cert.Id}" data-index="${index}">
                <div class="card">
                    <div class="card-body p-2">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <div class="certificado-preview position-relative"
                                     style="width: 60px; height: 60px; cursor: pointer;"
                                     data-id="${cert.Id}" data-url="${cert.Url}">
                                    ${esPDF ?
            `<div class="d-flex align-items-center justify-content-center h-100 bg-danger text-white rounded">
                                            <i class="fas fa-file-pdf fa-2x"></i>
                                        </div>` :
            `<img src="${cert.ThumbnailUrl || cert.Url}" alt="Preview"
                                             class="img-fluid rounded"
                                             style="width: 60px; height: 60px; object-fit: cover;">`
        }
                                    <div class="certificado-overlay position-absolute rounded d-flex align-items-center justify-content-center"
                                         style="top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.5);opacity:0;transition:opacity 0.2s;">
                                        <i class="fas fa-search-plus text-white"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col">
                                <h6 class="mb-0 small font-weight-bold text-truncate" title="${cert.Nombre}">
                                    ${cert.Nombre || 'Certificado'}
                                </h6>
                                <small class="text-muted">
                                    ${cert.Tamano || '--'} | ${cert.FechaSubida || '--'}
                                </small>
                            </div>
                            <div class="col-auto">
                                <div class="btn-group btn-group-sm">
                                    <button type="button" class="btn btn-outline-primary btnVerCertificado"
                                            data-id="${cert.Id}" data-url="${cert.Url}" title="Ver">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <a href="${cert.Url}" download class="btn btn-outline-secondary" title="Descargar">
                                        <i class="fas fa-download"></i>
                                    </a>
                                    <button type="button" class="btn btn-outline-danger btnEliminarCertificado"
                                            data-id="${cert.Id}" title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
        container.append(html);
    });
}

function subirCertificados(files) {
    if (!files || files.length === 0) return;

    const formData = new FormData();
    formData.append('IdLicencia', $('#Id').val());

    for (let i = 0; i < files.length; i++) {
        formData.append('certificados[]', files[i]);
    }

    $.blockUI({
        message: '<div class="text-center"><div class="load-sistema"></div><p class="mt-2">Subiendo certificados...</p></div>',
        baseZ: 9999999999
    });

    $.ajax({
        type: "POST",
        url: "/licencias/medicas/revision/certificado/subir",
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            $.unblockUI();
            if (response.success) {
                mostrarExito('Certificados subidos correctamente');
                listarCertificados();
            } else {
                mostrarError(response.error_description || 'Error al subir certificados');
            }
        },
        error: function() {
            $.unblockUI();
            mostrarError('Error de conexión');
        }
    });
}

function verCertificado(id, url) {
    certificadoActualIndex = certificadosData.findIndex(c => c.Id == id);

    const cert = certificadosData[certificadoActualIndex];
    const esPDF = cert?.Nombre?.toLowerCase().endsWith('.pdf');

    $('#nombreCertificadoModal').text(cert?.Nombre || 'Certificado');
    $('#btnDescargarCertificado').attr('href', url);

    $('#loadingCertificado').show();
    $('#iframeCertificado, #imagenCertificado').hide();

    if (esPDF) {
        $('#iframeCertificado').attr('src', url).show();
    } else {
        $('#imagenCertificado').attr('src', url).show();
    }

    $('#loadingCertificado').hide();

    actualizarNavegacionCertificados();
    $('#ModalCertificado').modal('show');
}

function navegarCertificado(direccion) {
    certificadoActualIndex += direccion;

    if (certificadoActualIndex < 0) certificadoActualIndex = certificadosData.length - 1;
    if (certificadoActualIndex >= certificadosData.length) certificadoActualIndex = 0;

    const cert = certificadosData[certificadoActualIndex];
    verCertificado(cert.Id, cert.Url);
}

function actualizarNavegacionCertificados() {
    $('#indiceCertificado').text(certificadoActualIndex + 1);
    $('#totalCertificados').text(certificadosData.length);
}

function eliminarCertificado(id) {
    Swal.fire({
        title: 'Eliminar certificado',
        text: '¿Está seguro de eliminar este certificado?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                type: "POST",
                url: "/licencias/medicas/revision/certificado/eliminar",
                data: { Id: id },
                dataType: "json",
                success: function(response) {
                    if (response.success) {
                        mostrarExito('Certificado eliminado');
                        listarCertificados();
                    } else {
                        mostrarError(response.error_description || 'Error al eliminar');
                    }
                }
            });
        }
    });
}

// ========================================
// COMENTARIOS / OBSERVACIONES
// ========================================
function buscarObservaciones() {
    const idLicencia = $('#Id').val();
    if (!idLicencia) return;

    $.ajax({
        type: "POST",
        url: "/licencias/medicas/revision/observaciones",
        data: { IdLicencia: idLicencia },
        dataType: "json",
        success: function(response) {
            if (response.success && response.data) {
                renderizarComentarios(response.data);
            }
        }
    });
}

function renderizarComentarios(comentarios) {
    const container = $('#listaComentarios');
    container.find('.comentario-item').remove();

    if (!comentarios || comentarios.length === 0) {
        $('#sinComentarios').show();
        $('#contadorComentarios').text('0');
        return;
    }

    $('#sinComentarios').hide();
    $('#contadorComentarios').text(comentarios.length);

    comentarios.forEach(function(com) {
        const html = `
            <div class="comentario-item mb-3 ${com.EsPropio ? 'text-right' : ''}">
                <div class="d-inline-block ${com.EsPropio ? 'bg-primary text-white' : 'bg-light'} rounded px-3 py-2"
                     style="max-width: 85%;">
                    <div class="small ${com.EsPropio ? 'text-white-50' : 'text-muted'} mb-1">
                        <strong>${com.Usuario || 'Usuario'}</strong>
                        <span class="mx-1">-</span>
                        ${com.Fecha || '--'}
                    </div>
                    <p class="mb-0 small">${com.Texto || com.Comentario || ''}</p>
                </div>
            </div>
        `;
        container.append(html);
    });

    // Scroll al final
    container.scrollTop(container[0].scrollHeight);
}

function enviarComentario() {
    const texto = $('#textoComentario').val().trim();
    if (!texto) return;

    $.ajax({
        type: "POST",
        url: "/licencias/medicas/revision/observacion",
        data: {
            IdLicencia: $('#Id').val(),
            Comentario: texto
        },
        dataType: "json",
        success: function(response) {
            if (response.success) {
                $('#textoComentario').val('');
                buscarObservaciones();
            } else {
                mostrarError(response.error_description || 'Error al enviar comentario');
            }
        }
    });
}

// ========================================
// MOVIMIENTOS / HISTORIAL
// ========================================
function listarMovimientos() {
    const idLicencia = $('#Id').val();
    if (!idLicencia) return;

    $.ajax({
        type: "POST",
        url: "/licencias/medicas/revision/movimientos",
        data: { IdLicencia: idLicencia },
        dataType: "json",
        success: function(response) {
            if (response.success && response.data) {
                renderizarMovimientos(response.data);
            }
        }
    });
}

function renderizarMovimientos(movimientos) {
    const container = $('#listaMovimientos');
    container.empty();

    if (!movimientos || movimientos.length === 0) return;

    const colores = {
        'danger': '#dc3545',
        'warning': '#fd7e14',
        'success': '#28a745',
        'info': '#17a2b8',
        'default': '#6c757d',
        'secondary': '#6c757d'
    };

    movimientos.forEach(function(mov) {
        const color = colores[mov.Clase] || '#6c757d';
        const html = `
            <div class="movimiento-item d-flex mb-3">
                <div class="timeline-indicator mr-3">
                    <div class="timeline-dot" style="width: 12px; height: 12px; border-radius: 50%; background: ${color};"></div>
                    <div class="timeline-line" style="width: 2px; height: calc(100% + 10px); background: #dee2e6; margin-left: 5px;"></div>
                </div>
                <div class="timeline-content flex-grow-1">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <span class="badge badge-${mov.Clase || 'secondary'} mb-1">${mov.EstadoNuevo || mov.Estado || '--'}</span>
                            <p class="small mb-0 text-muted">${mov.Descripcion || ''}</p>
                        </div>
                        <small class="text-muted">${mov.Fecha || '--'}</small>
                    </div>
                    <small class="text-muted">
                        <i class="fas fa-user mr-1"></i>${mov.Usuario || '--'}
                    </small>
                </div>
            </div>
        `;
        container.append(html);
    });
}

// ========================================
// ACCIONES PRINCIPALES
// ========================================
function habilitarEdicion() {
    $('#Inicio').removeAttr('disabled');
    $('#FechaFinAbierta').removeAttr('disabled');
    $('#Horas').removeAttr('disabled');
    $('#Duracion').removeAttr('disabled');
    $('#Unidad').removeAttr('disabled');
    $('#Descripcion').removeAttr('disabled');
    $('#Matricula').removeAttr('disabled');
    $('#IdEspecialidad').removeAttr('disabled');
    $('#IdMotivo').prop('disabled', false).trigger("chosen:updated");

    $('#btnEditar').addClass('hide');
    $('#btnCancelar').removeClass('hide');

    if (buscarCargos) {
        buscarCargosAsociados();
    }
}

function deshabilitarEdicion() {
    $('#Inicio').attr('disabled', true);
    $('#FechaFinAbierta').attr('disabled', true);
    $('#Horas').attr('disabled', true);
    $('#Duracion').attr('disabled', true);
    $('#Unidad').attr('disabled', true);
    $('#Descripcion').attr('disabled', true);
    $('#Matricula').attr('disabled', true);
    $('#IdEspecialidad').attr('disabled', true);
    $('#IdMotivo').prop('disabled', true).trigger("chosen:updated");

    $('#btnCancelar').addClass('hide');
    $('#btnEditar').removeClass('hide');
}

function guardarLicencia() {
    let formData = $('#formLicencia').serialize();
    formData += '&' + $('#formLicencia').serializeDisabledLicencias();
    formData += '&accion=2';

    $.blockUI({
        message: '<div class="text-center"><div class="load-sistema"></div><p class="mt-2">Guardando...</p></div>',
        baseZ: 9999999999
    });

    $.ajax({
        type: "POST",
        url: "/licencias/medicas/revision/upd",
        data: formData,
        dataType: "json",
        success: function(response) {
            $.unblockUI();
            if (response.success) {
                mostrarExito('Licencia guardada correctamente');
                deshabilitarEdicion();
            } else {
                mostrarError(response.error_description || 'Error al guardar');
            }
        },
        error: function() {
            $.unblockUI();
            mostrarError('Error de conexión');
        }
    });
}

function aprobarLicencia() {
    Swal.fire({
        title: 'Aprobar licencia',
        text: '¿Está seguro de aprobar esta licencia?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        confirmButtonText: 'Sí, aprobar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.blockUI({
                message: '<div class="text-center"><div class="load-sistema"></div><p class="mt-2">Aprobando...</p></div>',
                baseZ: 9999999999
            });

            $.ajax({
                type: "POST",
                url: "/licencias/medicas/revision/upd",
                data: {
                    Id: $('#Id').val(),
                    accion: 3
                },
                dataType: "json",
                success: function(response) {
                    $.unblockUI();
                    if (response.success) {
                        mostrarExito('Licencia aprobada correctamente');
                        cargarDatosLicencia();
                    } else {
                        mostrarError(response.error_description || 'Error al aprobar');
                    }
                },
                error: function() {
                    $.unblockUI();
                    mostrarError('Error de conexión');
                }
            });
        }
    });
}

function denegarLicencia() {
    confirmarAccion('Ingrese el motivo del rechazo')
        .then((resultado) => {
            if (resultado.value) {
                $.blockUI({
                    message: '<div class="text-center"><div class="load-sistema"></div><p class="mt-2">Denegando...</p></div>',
                    baseZ: 9999999999
                });

                $.ajax({
                    type: "POST",
                    url: "/licencias/medicas/revision/upd",
                    data: {
                        Id: $('#Id').val(),
                        Comentarios: resultado.value,
                        accion: 4
                    },
                    dataType: "json",
                    success: function(response) {
                        $.unblockUI();
                        if (response.success) {
                            mostrarExito('Licencia denegada');
                            cargarDatosLicencia();
                        } else {
                            mostrarError(response.error_description || 'Error al denegar');
                        }
                    },
                    error: function() {
                        $.unblockUI();
                        mostrarError('Error de conexión');
                    }
                });
            }
        });
}

function anularLicencia() {
    confirmarAccion('Ingrese el motivo de la anulación')
        .then((resultado) => {
            if (resultado.value) {
                $.blockUI({
                    message: '<div class="text-center"><div class="load-sistema"></div><p class="mt-2">Anulando...</p></div>',
                    baseZ: 9999999999
                });

                $.ajax({
                    type: "POST",
                    url: "/licencias/medicas/revision/upd",
                    data: {
                        Id: $('#Id').val(),
                        Comentarios: resultado.value,
                        accion: 5
                    },
                    dataType: "json",
                    success: function(response) {
                        $.unblockUI();
                        if (response.success) {
                            mostrarExito('Licencia anulada');
                            cargarDatosLicencia();
                        } else {
                            mostrarError(response.error_description || 'Error al anular');
                        }
                    },
                    error: function() {
                        $.unblockUI();
                        mostrarError('Error de conexión');
                    }
                });
            }
        });
}

// ========================================
// UTILIDADES
// ========================================
function buscarArticulos(idMotivo, idArticuloSeleccionado) {
    $.ajax({
        type: "POST",
        url: "/combo_ajax.php",
        data: {
            IdMotivo: idMotivo,
            tipo: 'articulos'
        },
        dataType: "json",
        success: function(response) {
            let options = '<option value="">Seleccione...</option>';
            if (response.data) {
                response.data.forEach(function(item) {
                    const selected = (item.Id == idArticuloSeleccionado) ? 'selected' : '';
                    options += `<option value="${item.Id}" ${selected}>${item.Nombre}</option>`;
                });
            }
            $('#IdArticulo').html(options).prop('disabled', false).trigger("chosen:updated");
        }
    });
}

function buscarMotivoDetalle(idDiagnostico, idDetalleSeleccionado) {
    $.ajax({
        type: "POST",
        url: "/combo_ajax.php",
        data: {
            IdDiagnostico: idDiagnostico,
            tipo: 'diagnostico_detalle'
        },
        dataType: "json",
        success: function(response) {
            let options = '<option value="">Seleccione...</option>';
            if (response.data) {
                response.data.forEach(function(item) {
                    const selected = (item.Id == idDetalleSeleccionado) ? 'selected' : '';
                    options += `<option value="${item.Id}" ${selected}>${item.Nombre}</option>`;
                });
            }
            $('#IdDiagnosticoDetalle').html(options).trigger("chosen:updated");
        }
    });
}

function buscarFamiliar() {
    const dni = $('#FamiliarDni').val();
    if (!dni) return;

    $.ajax({
        type: "POST",
        url: "/autocompletar",
        data: {
            Dni: dni,
            Tipo: 5
        },
        dataType: "json",
        success: function(response) {
            if (response && response.length > 0) {
                const familiar = response[0];
                $('#FamiliarNombre').val(familiar.Nombre || '');
                $('#FamiliarApellido').val(familiar.Apellido || '');
            }
        }
    });
}

function mostrarExito(mensaje) {
    Swal.fire({
        icon: 'success',
        title: 'Éxito',
        text: mensaje,
        timer: 2000,
        showConfirmButton: false
    });
}

function mostrarError(mensaje) {
    Swal.fire({
        icon: 'error',
        title: 'Error',
        text: mensaje
    });
}

function confirmarAccion(mensaje) {
    return Swal.fire({
        title: mensaje,
        input: 'textarea',
        inputPlaceholder: 'Escriba aquí...',
        showCancelButton: true,
        confirmButtonText: 'Confirmar',
        cancelButtonText: 'Cancelar',
        inputValidator: (value) => {
            if (!value) {
                return 'Debe ingresar un texto';
            }
        }
    });
}

function convertirFecha(fecha, formatoOrigen, formatoDestino) {
    if (!fecha) return '';

    const partes = fecha.split('/');
    if (formatoOrigen === 'dd/mm/aaaa' && formatoDestino === 'aaaa-mm-dd') {
        return partes[2] + '-' + partes[1] + '-' + partes[0];
    }
    return fecha;
}

function guardarJunta(id) {
    // Implementación de guardarJunta aquí
    console.log('Guardar junta con ID:', id);
}
