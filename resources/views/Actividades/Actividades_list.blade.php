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
    <h2 style="margin: -1.25rem auto 1.25rem auto; text-align: center"><img src="{{ url('/images/icons/logoSIA.png') }}" alt="logo" height="100px" >Actividades del 2025</h2>
    <a href="/actividades/create" ><button class="btn btn-primary" style="margin-bottom: 1rem;"><i class="fa fa-plus" aria-hidden="true"></i>Actividades</button></a>
    <div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <br>
            <table class="table table-bordered solicitud_all">
                <thead>
                    <tr>
                    <th>Nº Registro</th>
                    <th>Correlativo</th>
                    <th>Fecha</th>
                    <th>Direccion Encargada</th>
                    <th>Coordinacion</th>
                    <th>Actividad</th>
                    <th>Estado</th>
                    <th>Editar</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
    <!-- <a href="#" id="btn_listado"> <button class="btn btn-primary" style="padding:5px;"><i class="fa fa-file-pdf-o" aria-hidden="true" style="color: red; background-color: white;"></i> Imprimir</button> </a> -->
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
    $(function () {
        $('#btn_listado').click(function() {
            // Obtén los valores de los campos de fecha
            var fechaDesde = $('#fecha_desde').val();
            var fechaHasta = $('#fecha_hasta').val();
            var comuna = $('#comuna_id').val(); // Usar comuna_id
            var comunidad = $('#comunidad_id').val(); // Usar comunidad_id
            var direccion = $('#direcciones').val();

            // Construye la URL con los parámetros
            var url = "{{ route('imprimiractividades') }}" + "?fecha_desde=" + fechaDesde + "&fecha_hasta=" + fechaHasta + "&comuna=" + comuna + "&comunidad=" + comunidad + "&direcciones=" + direccion;

            // Redirige a la URL construida
            window.location.href = url;
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
        ajax: {
            url: "{{ route('actividades.getdataactividades') }}",
            data: function (d) {
                d.fecha_desde = $('#fecha_desde').val();
                d.fecha_hasta = $('#fecha_hasta').val();
                d.comuna = $('#comuna').val();
                d.comunidad = $('#comunidad').val();
                d.direcciones = $('#direcciones').val();
            }
        },
        columns: [
            { data: 'id', name: 'id' },
            {
                data: 'actividad_id', name: 'actividad_id',
                "render": function (data, type, row) {
                    return '<div style="text-align:center;"><b>' + data + '</b></div>';
                }
            },
            {
                data: 'write_date', name: 'write_date',
                "render": function (data, type, row) {
                    var fechaMoment = moment(data);
                    var fechaFormateada = fechaMoment.format('DD-MM-YYYY, HH:mm');
                    return fechaFormateada;
                }
            },
            { data: 'Direccion', name: 'Direccion' },
            { data: 'Coordinacion_id', name: 'Coordinacion_id' },
            { data: 'accion', name: 'accion' },
            { data: 'state', name: 'Estado' },
            {
                data: 'edit', name: 'edit', orderable: false, searchable: false,
                "render": function (data, type, row) {
                    return '<div style="text-align:center;">' + data + '</div>';
                }
            },
        ],
        order: [[0, 'desc']],
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
</style>
@endsection
