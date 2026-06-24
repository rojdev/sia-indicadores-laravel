@extends('adminlte::layouts.app')

@section('css_database')
    @include('adminlte::layouts.partials.link')
@endsection

@section('htmlheader_title')
    {{ trans('adminlte_lang::message.home') }}
@endsection

@section('contentheader_title')
<!-- Componente Button Para todas las Ventanas de los Módulos, no Borrar.--> 

  
    
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
<h2 style="margin: -25px 0px -25px 0px; text-align: center"><img src="{{ url('/images/icons/logoSIA.png') }}" alt="logo" height="100px" >Reporte de Solicitudes</h2>


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
                    {!! Form::select('comuna', [NULL => 'Seleccionar'] + $comuna, NULL, ['class' => 'form-control', 'id' => 'comuna']) !!}    
                </div>
                <div class="col-md-2">
                    <label for="direcciones">Direcciones:</label>
                    {!! Form::select('direcciones', [NULL => 'Seleccionar'] + $direcciones, NULL, ['class' => 'form-control', 'id' => 'direcciones']) !!}    
                </div>
                <div class="col-md-2">
                    <label for="sexo">Sexo:</label>
                    {!! Form::select('sexo', [NULL => 'Seleccionar'] + $sexo, NULL, ['class' => 'form-control', 'id' => 'sexo']) !!}    
                </div>
                <div class="col-md-2">
                    <button class="btn btn-primary" id="btn_filtrar" style="margin-top: 25px;">Filtrar</button>
                </div>
            </div>
            <br>
            <table class="table table-bordered solicitud_all">
                <thead>
                    <tr>
                        <th>Nro Solicitud</th>
                        <th>Funcionario Receptor</th>
                        <th>Fecha</th>
                        <th>Nombre de Solicitante</th>
                        <th>Cèdula de Solicitante</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
    <a href="#" id="btn_listado"> <button class="btn btn-primary" style="padding:5px;">Imprimir Listado</button></a>
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
            var comuna = $('#comuna').val();
            var direcciones = $('#direcciones').val();
            var sexo = $('#sexo').val();

            // Construye la URL con los parámetros
            var url = "{{ route('imprimir3') }}" + "?fecha_desde=" + fechaDesde + "&fecha_hasta=" + fechaHasta + "&comuna=" + comuna + "&direcciones=" + direcciones + "&sexo=" + sexo;

            // Redirige a la URL construida
            window.location.href = url;
        });

        $('#btn_totales').click(function() {
        $.ajax({
            url: "{{ route('solicitud.solicitudTotalFinalizadas2') }}",
            method: 'GET',
            dataType: 'json', // Indicamos que esperamos una respuesta JSON
            success: function(response) {
                console.log(response); 
                // Aquí puedes mostrar los resultados en tu interfaz de usuario
                // Por ejemplo: 
                // alert("Total de solicitudes: " + response.TOTAL_SOLICITUD);
            },
            error: function() {
                console.error("Error al obtener los totales.");
            }
            });
        });
        
        var table = $('.solicitud_all').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            autoWidth : false,
            ajax: {
                url: "{{ route('seguimiento.finalizadas2') }}",
                data: function (d) {
                    d.fecha_desde = $('#fecha_desde').val();
                    d.fecha_hasta = $('#fecha_hasta').val();
                    d.comuna = $('#comuna').val();
                    d.direcciones = $('#direcciones').val();
                    d.sexo = $('#sexo').val();
                }
            },
            columns: [
                {
                    data: 'atcID', name: 'atcID',
                    "render": function ( data, type, row ) { 
                        return '<div style="text-align:center;"><b>'+data+'</b></div>';
                    }                    
                },
                {data: 'usuario', name: 'usuario'}, 
                {
                    data: 'fecha', name: 'fecha',
                    "render": function ( data, type, row ) {
                        var fechaMoment = moment(data);
                        var fechaFormateada = fechaMoment.format('DD-MM-YYYY, HH:mm');
                        return fechaFormateada;
                    }
                },
                {data: 'solicitante', name: 'solicitante'},
                {data: 'cedula', name: 'cedula'}, 
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
</style>
@endsection  
