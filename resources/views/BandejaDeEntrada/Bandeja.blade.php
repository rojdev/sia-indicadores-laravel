@extends('adminlte::layouts.app')

@section('css_database')
    @include('adminlte::layouts.partials.link')
@endsection

@section('htmlheader_title')
    {{ trans('adminlte_lang::message.home') }}
@endsection

@section('contentheader_title')
<!-- Componente Button Para todas las Ventanas de los Módulos, no Borrar.--> 

<h2 style="margin: -25px 0px -25px 0px"><img src="{{ url('/images/icons/logoSIA.png') }}" alt="logo" height="100px" >Denuncias Recibidas</h2>

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
<div class="container-fluid">
<?php 
    $rols_id = auth()->user()->rols_id;
    $phpValue = $rols_id;
    echo "<script> var rolsJS = '" . $phpValue . "'; </script>";
?>
    <div class="card">
        <div class="card-body">            
                <table class="table table-bordered solicitud_all">
                        <thead>
                            <tr>
                                <th>Numero de Solicitud</th>
                                <th>Tipo Solicitud</th>
                                <th>Fecha</th>
                                <th>Direccion Responsable</th>
                                <th>Asunto</th>
                                <th>Status</th>
                                <th>{{ trans('message.botones.view') }}</th>
                            </tr>
                        </thead>
                    <tbody>
                    </tbody>
                </table>
        </div>
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
$(function () {
    var rolID = rolsJS;
        var table = $('.solicitud_all').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        autoWidth : false,
        ajax: "{{ route('bandeja.list') }}",
        
        columns: [          
            {
                data: 'atcID', name: 'id',
                "render": function ( data, type, row ) {
                    return '<div style="text-align:center;"><b>'+data+'</b></div>';
                }
            },
            {data: 'nombretipo', name: 'nombretipo'}, 
            {
                data: 'fecha', name: 'fecha',
                "render": function ( data, type, row ) {
                    var fechaMoment = moment(data);
                    var fechaFormateada = fechaMoment.format('DD-MM-YYYY, HH:mm');
                    return fechaFormateada;
                }
            },
            {data: 'direccionnombre', name: 'direccionnombre'}, 
            {data: 'denunciarelato', name: 'relato'},
            {data: 'nombrestatus', name: 'nombrestatus'},
            {
                data: 'view', name: 'view', orderable: false, searchable: false,                
                "render": function ( data, type, row ) {                    
                    return '<div style="text-align:center;">'+data+'</div>';
                }
            },
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
