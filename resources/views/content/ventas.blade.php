@extends('layout.layout')
@section('contenido')
@section('titulo', 'Ventas')

  <link rel="stylesheet" href="{{ asset('css/productos.css') }}">
<style>

#resultadosBusqueda {
    max-width: 100%;
    overflow: hidden;
}

.dropdown-item {
    white-space: normal;
    word-wrap: break-word;
}

</style>
  <div class="container-fluid p-0 bg-white rounded shadow border border-secondary-subtle">
<div id="alertContainer" class="position-fixed top-0 end-0 p-3" style="z-index: 1100"></div>
   <div class="p-4">
    <div class="d-flex flex-column flex-sm-row align-items-sm-center gap-3 mb-4">
<div class="position-relative form-control-sm w-100 w-sm-auto">
    <input type="text" id="buscarProducto" class="form-control form-control-sm w-100 w-sm-auto" placeholder="Buscar Producto..." aria-label="Buscar Producto"/>
    <div id="resultadosBusqueda" class="dropdown-menu w-100" style="display: none;"></div>
</div>
<select class="form-select form-select-sm w-auto" id="itemsPorPagina" aria-label="Rows per page">
    <option value="7" selected>7</option>
    <option value="10">10</option>
    <option value="15">15</option>
    <option value="25">25</option>
    <option value="50">50</option>
</select>
@if(auth()->user()->rol !== 'Empleado')
<button type="button" class="btn btn-outline-success btn-sm d-flex align-items-center gap-1" id="btnAbrirModalReporte">
    <i class="fas fa-file-pdf"></i>
    Generar Reporte
</button>
@endif
     <button type="button" class="btn btn-success btn-sm ms-auto ms-sm-0">Cobrar</button>
    </div>
    <div class="table-responsive">
            <div class="d-flex justify-content-end mb-2 p-2 bg-light rounded">
        <div class="d-flex align-items-center gap-3">
            <span class="fw-bold">Total General:</span>
            <span id="totalGeneral" class="fs-5 fw-bold text-primary">$0.00</span>
        </div>
    </div>
     <table class="table table-borderless align-middle text-secondary-subtle">
    <thead class="bg-light border border-secondary-subtle rounded-2">
        <tr>
            <th class="text-uppercase fw-semibold text-center" style="font-size: 0.625rem; width: 25%;">
                Producto 
            </th>
            <th class="text-uppercase fw-semibold text-center" style="font-size: 0.625rem; width: 15%;">
                Unidad de Medida 
            </th>
            <th class="text-uppercase fw-semibold text-center" style="font-size: 0.625rem; width: 10%;">
                Categoría 
            </th>            
            <th class="text-uppercase fw-semibold text-center" style="font-size: 0.625rem; width: 10%;">
                Cantidad
            </th>
            <th class="text-uppercase fw-semibold text-center" style="font-size: 0.625rem; width: 10%;">
                Subtotal
            </th>        
            <th class="text-uppercase fw-semibold text-center" style="font-size: 0.625rem; width: 15%;">
                Acciones 
            </th>
        </tr>
    </thead>
<tbody class="border border-secondary-subtle rounded-2 bg-white" id="tablaProductos">
    <!-- Las filas de productos se agregarán aquí dinámicamente -->
</tbody>
</table>
<div class="d-flex justify-content-between align-items-center mt-3">
    <div class="text-muted small" id="infoPaginacion">
        Mostrando <span id="desde">0</span> a <span id="hasta">0</span> de <span id="total">0</span> productos
    </div>
    <nav aria-label="Page navigation">
        <ul class="pagination pagination-sm mb-0" id="paginacion">
            <!-- Los botones de paginación se generarán aquí -->
        </ul>
    </nav>
</div>
    </div>
   </div>

<!-- Modal de Cobro -->
<div class="modal fade" id="modalCobro" tabindex="-1" aria-labelledby="modalCobroLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="modalCobroLabel">Proceso de Cobro</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
<div class="modal-body">
    <div class="row">
        <div class="col-md-6">
            <h5 class="mb-3">Detalles de la Venta</h5>
            <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                <table class="table table-sm table-sticky">
                    <thead>
                        <tr>
                            <th style="position: sticky; top: 0; background: white; z-index: 10;">Producto</th>
                            <th class="text-end" style="position: sticky; top: 0; background: white; z-index: 10;">Cantidad</th>
                            <th class="text-end" style="position: sticky; top: 0; background: white; z-index: 10;">Precio</th>
                            <th class="text-end" style="position: sticky; top: 0; background: white; z-index: 10;">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody id="detallesCobro">
                        <!-- Los productos se agregarán aquí -->
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="3" class="text-end" style="position: sticky; bottom: 0; background: white; z-index: 10;">Total:</th>
                            <th class="text-end" id="totalModal" style="position: sticky; bottom: 0; background: white; z-index: 10;">$0.00</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
                    <div class="col-md-6">
                        <h5 class="mb-3">Datos de Pago</h5>
                        <div class="mb-3">
                            <label for="metodoPago" class="form-label">Método de Pago</label>
                            <select class="form-select" id="metodoPago">
                                <option value="efectivo">Efectivo</option>
                            </select>
                        </div>
                        <div class="mb-3" id="efectivoContainer">
                            <label for="montoRecibido" class="form-label">Monto Recibido</label>
                            <input type="number" class="form-control" id="montoRecibido" placeholder="0.00" step="0.01" min="0">
                        </div>
                        <div class="alert alert-info">
                            <strong>Cambio:</strong> <span id="cambioCalculado">$0.00</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-success" id="confirmarCobro">Confirmar Cobro</button>
            </div>
        </div>
    </div>
</div>


<!-- Modal para Generar Reporte -->
<div class="modal fade" id="modalGenerarReporte" tabindex="-1" aria-labelledby="modalGenerarReporteLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalGenerarReporteLabel">Generar Reporte de Ventas</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formGenerarReporte">
                    <div class="mb-3">
                        <label for="fechaInicio" class="form-label">Fecha de Inicio</label>
                        <input type="date" class="form-control" id="fechaInicio" required>
                    </div>
                    <div class="mb-3">
                        <label for="fechaFin" class="form-label">Fecha de Fin</label>
                        <input type="date" class="form-control" id="fechaFin" required>
                    </div>
                    <div class="mb-3">
                        <label for="tipoReporte" class="form-label">Tipo de Reporte</label>
                        <select class="form-select" id="tipoReporte" required>
                            <option value="pdf" selected>PDF</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnGenerarReporte">Generar Reporte</button>
            </div>
        </div>
    </div>
</div>

  </div>


<script>
    
$(document).ready(function() {
    // Abrir modal al hacer clic en el botón Generar Reporte
    $('#btnAbrirModalReporte').click(function() {
        $('#modalGenerarReporte').modal('show');
    });

    // Validar que fecha fin no sea menor que fecha inicio
    $('#fechaInicio, #fechaFin').change(function() {
        const fechaInicio = new Date($('#fechaInicio').val());
        const fechaFin = new Date($('#fechaFin').val());
        
        if (fechaFin < fechaInicio) {
            alert('La fecha de fin no puede ser anterior a la fecha de inicio');
            $('#fechaFin').val($('#fechaInicio').val());
        }
    });

    // Generar reporte al confirmar
    $('#btnGenerarReporte').click(function() {
        const form = $('#formGenerarReporte');
        if (form[0].checkValidity()) {
            const fechaInicio = $('#fechaInicio').val();
            const fechaFin = $('#fechaFin').val();
            const tipoReporte = $('#tipoReporte').val();
            

            generarReporte(fechaInicio, fechaFin, tipoReporte);
            
            $('#modalGenerarReporte').modal('hide');
        } else {
            form[0].reportValidity();
        }
    });

function generarReporte(fechaInicio, fechaFin, tipo) {
    if (!fechaInicio || !fechaFin) {
        mostrarAlerta('warning', 'Por favor seleccione ambas fechas');
        return;
    }
    
    mostrarAlerta('info', 'Generando reporte, por favor espere...');
    $('#btnGenerarReporte').prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Generando...');
    
    $.ajax({
        url: '{{ route("generar.reporte.venta") }}',
        type: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            fecha_inicio: fechaInicio,
            fecha_fin: fechaFin,
            tipo: tipo
        },
        success: function(response) {
            $('#btnGenerarReporte').prop('disabled', false).text('Generar Reporte');
            
            if (response.success) {
                mostrarAlerta('success', 'Reporte generado correctamente');
                
                // Abrir PDF en nueva pestaña
                if (response.url) {
                    window.open(response.url, '_blank');
                }
            const fechaInicio = $('#fechaInicio').val('');
            const fechaFin = $('#fechaFin').val('');
            const tipoReporte = $('#tipoReporte').val();
            } else {
                const errorMsg = response.message || 'Error desconocido al generar el reporte';
                mostrarAlerta('danger', errorMsg);
                console.error('Error en la respuesta:', response);
            }
        },
        error: function(xhr) {
            $('#btnGenerarReporte').prop('disabled', false).text('Generar Reporte');
            
            let errorMessage = 'Error al conectar con el servidor';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
            }
            
            mostrarAlerta('danger', errorMessage);
            console.error('Error en la petición:', xhr.statusText);
        }
    });
}

    function mostrarAlerta(tipo, mensaje) {
        $('#alertContainer').empty();
        const alerta = `
            <div class="alert alert-${tipo} alert-dismissible fade show" role="alert">
                ${mensaje}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;
        $('#alertContainer').append(alerta);
        setTimeout(() => {
            $('.alert').alert('close');
        }, 5000);
    }
});

</script>
<script src="js/ventas.js"></script>

@endsection