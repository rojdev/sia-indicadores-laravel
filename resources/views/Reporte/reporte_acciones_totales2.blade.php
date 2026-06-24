@extends('adminlte::layouts.app')

@section('css_database')
    @include('adminlte::layouts.partials.link')
@endsection

@section('htmlheader_title')
    {{ trans('adminlte_lang::message.home') }}
@endsection

@section('contentheader_title')
@endsection

@section('link_css_datatable')
    <link href="{{ url ('/css_datatable/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ url ('/css_datatable/dataTables.bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ url ('/css_datatable/responsive.bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ url ('/css_datatable/buttons.dataTables.min.css') }}" rel="stylesheet">
@endsection

@section('main-content')
@component('components.alert_msg',['tipo_alert'=>$tipo_alert])
Componentes para los mensajes de Alert, No Eliminar
@endcomponent
    <h2 style="margin: -1.25rem auto 1.25rem auto; text-align: center">
        <img src="{{ url('/images/icons/logoSIA.png') }}" alt="logo" height="100px" >
        Reporte de Totales Acciones por Comuna
    </h2>
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-2">
                        <label for="fecha_desde">Fecha Desde:</label>
                        <input type="date" class="form-control" id="fecha_desde">
                    </div>
                    <div class="col-md-2">
                        <label for="fecha_hasta">Fecha Hasta:</label>
                        <input type="date" class="form-control" id="fecha_hasta">
                    </div>

                    <div class="col-md-2">
                        <label for="comuna">Comuna:</label>
                        {!! Form::select('comuna', [NULL => 'Seleccionar',] + $comuna, NULL, ['class' => 'form-control', 'id' => 'comuna_id']) !!}
                    </div>

                    <div class="col-md-2">
                        <button class="btn btn-primary" id="btn_filtrar" style="margin-top: 25px;">Filtrar</button>
                    </div>
                </div>
                <br>
            </div>
        </div>


        <div class ="card">
            <div class="container-fluid">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-header">
                                        Totales por Comunidad y Comuna
                                    </div>
                                    <div class="card-body">
                                        <table class="table table-bordered solicitud_all">
                                            <thead>
                                                <tr>
                                                    <th>Comunidad</th>
                                                    <th>Fundación</th>
                                                    <th>Impaez</th>
                                                    <th>Imdep</th>
                                                    <th>Inmujer</th>
                                                    <th>Total</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <a href="#" id="btn_listado"> <button class="btn btn-primary" style="padding:5px;" id="btn_imprimir"><i class="fa fa-file-pdf-o" aria-hidden="true" style="color: red; background-color: white;"></i> Imprimir</button> </a>
        </div>
    </div>
@endsection

@section('script_datatable')
<script src="{{ url ('/js_datatable/jquery.dataTables.min.js') }}" type="text/javascript"></script>
<script src="{{ url ('/js_datatable/dataTables.bootstrap.min.js') }}" type="text/javascript"></script>
<script src="{{ url ('/js_datatable/dataTables.responsive.min.js') }}" type="text/javascript"></script>
<script src="{{ url ('/js_datatable/responsive.bootstrap.min.js') }}" type="text/javascript"></script>
<script src="{{ url ('/js_datatable/dataTables.buttons.min.js') }}" type="text/javascript"></script>
<script src="{{ url ('/js_delete/sweetalert.min.js') }}" type="text/javascript"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        // Inicializa los botones como deshabilitados
        $('#btn_filtrar').prop('disabled', true);
        $('#btn_imprimir').prop('disabled', true);

        $('#comuna_id').change(function() {
            if ($(this).val() === null || $(this).val() === '') {
                $('#btn_filtrar').prop('disabled', true);
                $('#btn_imprimir').prop('disabled', true);
            } else {
                $('#btn_filtrar').prop('disabled', false);
                $('#btn_imprimir').prop('disabled', false);
            }
        });
    });
    $(function () {
        $('#btn_listado').click(function() {
            // Obtén los valores de los campos de fecha
            var fechaDesde = $('#fecha_desde').val();
            var fechaHasta = $('#fecha_hasta').val();
            var comuna = $('#comuna_id').val(); // Usar comuna_id
            var comunidad = $('#comunidad_id').val(); // Usar comunidad_id
            var direccion = $('#direcciones').val();

            // Construye la URL con los parámetros
            var url = "{{ route('imprimiraccionesgobiernototalesInstitutos') }}" + "?fecha_desde=" + fechaDesde + "&fecha_hasta=" + fechaHasta + "&comuna=" + comuna + "&comunidad=" + comunidad + "&direcciones=" + direccion;

            // Redirige a la URL construida
            window.location.href = url;
        });

        $('#btn_totales').click(function() {
            var url = "{{ route('solicitud.solicitudTotalFinalizadas') }}";
            window.location.href = url;
        });
        $('#btn_excell').click(function() {
        // Obtén los valores de los campos de fecha y otros filtros
        var fechaDesde = $('#fecha_desde').val();
        var fechaHasta = $('#fecha_hasta').val();
        var comuna = $('#comuna_id').val();

        // Construye la URL con los parámetros para el archivo Excel
        var url = "{{ route('imprimirnewExcel') }}" + "?fecha_desde=" + fechaDesde + "&fecha_hasta=" + fechaHasta + "&comuna=" + comuna + "&comunidad=" + comunidad + "&direcciones=" + direccion;

        // Abre la URL en una nueva ventana o pestaña para descargar el archivo
        window.open(url, '_blank');  // '_blank' abre en una nueva pestaña
    });
        $('#comuna_id').change(function () {
            var comuna = $('#comuna_id').val();
            var comunaId = $(this).val();
            $("#comunidad_id").prop('disabled', false);

            $.ajax({
                url: "{{ route('getComunidad2') }}", // Ruta a tu controlador
                type: "GET",
                data: { comuna: comuna },
                success: function (data) {
                    $("#comunidad_id").empty(); // Limpia opciones anteriores
                    $("#comunidad_id").append('<option value="">Seleccione Comunidad</option>'); // Opción inicial

                    $.each(data, function (key, value) {
                        $("#comunidad_id").append('<option value="' + value.id + '">' + value.nombre + '</option>');
                    });
                },
                error: function () {
                    // Manejo de errores (opcional)
                    alert("Error al cargar la comunidad.");
                }
            });
        });
        var table = $('.solicitud_all').DataTable({
            processing: true,
        serverSide: true,
        responsive: true,
        autoWidth: false,
        paging: false,
            ajax: {
                url: "{{ route('total.totalescomunidadxcomuna2') }}",
                data: function (d) {
                    d.fecha_desde = $('#fecha_desde').val();
                    d.fecha_hasta = $('#fecha_hasta').val();
                    d.comuna = $('#comuna_id').val();
                }
            },
            columns: [
                {
    data: 'comunidad',
    name: 'comunidad',
    render: function (data, type, row) {
        return data === 'ZTotales Generales' ? 'Totales Generales' : data;
    }
},


                {data: 'fundacion', name: 'fundacion'},
                {data: 'impaez', name: 'impaez'},
                {data: 'imdep', name: 'imdep'},
                {data: 'inmujer', name: 'inmujer'},
                {data: 'total_comuna', name: 'total_comuna'},

            ],
            "language": {
                "lengthMenu": "Mostrar _MENU_ registros por página",
                "zeroRecords": "Nada encontrado !!! - disculpe",
                "info": "Mostrando la página _PAGE_ de _PAGES_",
                "infoEmpty": "Registros no disponible",
                "infoFiltered": "(filtrado de _MAX_ registros totales)",
                "search": "Buscar:",
                "paginate": {
                    "next": "Siguiente",
                    "previous": "Anterior",
                }
            }
        });

        $('#btn_filtrar').click(function() {
            table.ajax.reload();
        });
    });
</script>
    <script src="{{ url ('/js_delete/delete_confirm.min.js') }}"></script>
    <style>
        th{
            text-align: center;
        }
        td {
            text-align: center;
        }
        .oculto {
            display: none;
        }
        .tabla-acciones {
            width: auto;
            margin: 0 auto;
        }
    </style>
@endsection
