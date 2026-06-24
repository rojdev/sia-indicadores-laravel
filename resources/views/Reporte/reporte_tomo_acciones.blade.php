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
        Tomos Acciones de Gobierno por Direccion
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
                        <label for="direccion">Direccion:</label>
                        {!! Form::select('direccion', [NULL => 'Seleccionar',] + $direcciones->toArray(), NULL, ['class' => 'form-control', 'id' => 'direccion_id']) !!}
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
                                    <div class="card-body">
                                        <table class="table table-bordered solicitud_all">
                                            <thead>
                                                <tr>
                                                    <th>Correlativo</th>
                                                    <th>Descripcion</th>
                                                    <th>Parroquia</th>
                                                    <th>Comuna</th>
                                                    <th>Comunidad</th>
                                                    <th>Direccion</th>
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

        // Agrega un evento 'change' al select 'direccion_id'
        $('#direccion_id').change(function() {
            // Verifica si el valor seleccionado es nulo o vacío
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
            var direccion = $('#direccion_id').val();

            // Construye la URL con los parámetros
            var url = "{{ route('imprimirtomo') }}" + "?direcciones=" + direccion + "&fecha_desde=" + fechaDesde + "&fecha_hasta=" + fechaHasta;

            // Redirige a la URL construida
            window.location.href = url;
        });

        var table; // Declarar la variable table fuera del evento click

        $('#btn_filtrar').click(function() {
            if ($.fn.DataTable.isDataTable('.solicitud_all')) {
                // Si la tabla ya está inicializada, simplemente recargarla
                table.ajax.reload();
            } else {
                // Inicializar DataTable solo si no ha sido inicializado antes
                table = $('.solicitud_all').DataTable({
                    processing: true,
                    serverSide: true,
                    responsive: true,
                    autoWidth: false,
                    ajax: {
                        url: "{{ route('total.totalestomos') }}",
                        data: function (d) {
                            d.fecha_desde = $('#fecha_desde').val();
                            d.fecha_hasta = $('#fecha_hasta').val();
                            d.direccion = $('#direccion_id').val();
                        }
                    },
                    columns: [
                        {data: 'accion_id', name: 'accion_id'},
                        {data: 'nombre', name: 'nombre'},
                        {data: 'Parroquia', name: 'parroquia'},
                        {data: 'Comuna', name: 'comuna'},
                        {data: 'Comunidad', name: 'comunidad'},
                        {data: 'direccionhab', name: 'direccionhab'},
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
