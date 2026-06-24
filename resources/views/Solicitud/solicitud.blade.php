@extends('adminlte::layouts.app')

@section('css_database')
    @include('adminlte::layouts.partials.link')
@endsection

@section('htmlheader_title')
    {{ trans('adminlte_lang::message.home') }}
@endsection

@section('contentheader_title')
<div>
    @if($tipo == 1)
    <h2 style="margin: -25px 0px -25px 0px"><img src="{{ url('/images/icons/logoSIA.png') }}" alt="logo" height="100px" >Solicitudes de Denuncias, Quejas, Reclamos</h2>
    @elseif($tipo == 2)
    <h2 style="margin: -25px 0px -25px 0px"><img src="{{ url('/images/icons/logoSIA.png') }}" alt="logo" height="100px" >Solicitudes de Asesorias, Sugerencias</h2>
    @endif
</div>
<a href="/solicitud/create?tipo={{$tipo}}"><button style="color: white; background-color: black; width: 200px;border-radius: 7px;height: 30px; font-size: 16px;">Nueva Solicitud</button></a>

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
<main>
<div class="container-fluid">
<?php
    $rols_id = auth()->user()->rols_id;
    $phpValue = $rols_id;
    echo "<script> var rolsJS = '" . $phpValue . "'; </script>";
    echo "<script> var tipoJS = '" . $tipo . "'; </script>";
?>
    <input type="text" name="tipo" id="tipo" value="{{ $tipo }}" hidden>
    <div class="card">
        <div class="card-body">
                <table class="table table-bordered solicitud_all">
                        <thead>
                            <tr>
                                @if($tipo == 1)
                                    <th>Nro Solicitud</th>
                                    <th>Nombre de Solicitante</th>
                                    <th>Fecha</th>
                                    <th>Cèdula de Solicitante</th>
                                    <th>Tipo de Solicitud</th>
                                    <th>Nombre del Denunciado</th>
                                    <th>Cedula Denunciado</th>
                                    <th>Testigo</th>
                                    <th>Status</th>
                                    <th>{{ trans('message.botones.edit') }}</th>
                                    <th>{{ trans('message.botones.view') }}</th>
                                @else
                                    <th>Nro Solicitud</th>
                                    <th>Nombre de Solicitante</th>
                                    <th>Fecha</th>
                                    <th>Cèdula de Solicitante</th>
                                    <th>Tipo de Solicitud</th>
                                    <th>Observacion</th>
                                    <th>Status</th>
                                    <th>{{ trans('message.botones.edit') }}</th>
                                    <th>{{ trans('message.botones.view') }}</th>
                                @endif
                            </tr>
                        </thead>
                    <tbody>
                    </tbody>
                </table>
        </div>
    </div>
</div>
</main>

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
    var tipoID = tipoJS;

    if(tipoID == 1){
        var table = $('.solicitud_all').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        autoWidth : false,
        ajax: {
            url: "{{ route('solicitud.list') }}",
            data: function (d) {
                d.tipo = tipoID;
            }
        },

        columns: [
            {
                data: 'atcID', name: 'id',
                "render": function ( data, type, row ) {
                    return '<div style="text-align:center;"><b>'+data+'</b></div>';
                }
            },
            {data: 'solicitante', name: 'solicitante'},
            {
                    data: 'fecha', name: 'fecha',
                    "render": function ( data, type, row ) {
                        var fechaMoment = moment(data);
                        var fechaFormateada = fechaMoment.format('DD-MM-YYYY, HH:mm');
                        return fechaFormateada;
                    }
                },
            {data: 'cedula', name: 'cedula'},
            {data: 'nombretipo', name: 'nombretipo'},
            {data: 'denunciadonombre', name: 'nombrebeneficiario'},
            {data: 'cedula2', name: 'cedula2'},
            {data: 'testigo', name: 'solicita'},
            {data: 'nombrestatus', name: 'nombrestatus'},
            {
            data: 'edit', name: 'edit', orderable: false, searchable: false,
            "render": function ( data, type, row ) {
                return '<div style="text-align:center;">'+data+'</div>';
            }
            },
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
    }else{
    var table = $('.solicitud_all').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        autoWidth : false,
        ajax: {
            url: "{{ route('solicitud.list') }}",
            data: function (d) {
                d.tipo = tipoID;
            }
        },

        columns: [
            {
                data: 'atcID', name: 'id',
                "render": function ( data, type, row ) {
                    return '<div style="text-align:center;"><b>'+data+'</b></div>';
                }
            },
            {data: 'solicitante', name: 'solicitante'},
            {
                    data: 'fecha', name: 'fecha',
                    "render": function ( data, type, row ) {
                        var fechaMoment = moment(data);
                        var fechaFormateada = fechaMoment.format('DD-MM-YYYY, HH:mm');
                        return fechaFormateada;
                    }
                },
            {data: 'cedula', name: 'cedula'},
            {data: 'nombretipo', name: 'nombretipo'},
            {data: 'observacion', name: 'observacion'},
            {data: 'nombrestatus', name: 'nombrestatus'},
            {
                data: 'edit', name: 'edit', orderable: false, searchable: false,
                "render": function ( data, type, row ) {
                    return '<div style="text-align:center;">'+data+'</div>';
                }
            },
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
}
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
