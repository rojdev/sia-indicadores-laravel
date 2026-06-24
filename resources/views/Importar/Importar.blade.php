@extends('adminlte::layouts.app')

@section('css_database')
    @include('adminlte::layouts.partials.link')
@endsection

@section('htmlheader_title')
    {{ trans('adminlte_lang::message.home') }}
@endsection

@section('contentheader_title')
<div>
<h2 style="margin: -1.25rem auto 1.25rem auto; text-align: center"><img src="{{ url('/images/icons/logoSIA.png') }}" alt="logo" height="100px" >Importar Acciones de Gobierno</h2>
</div>


@endsection


@section('main-content')

<div class="container-fluid w-50" style="max-width:640px">
    {!! Form::open(array('route' => array('importar.store'),'method' => 'POST','id' => 'form_acciones_id','enctype' => 'multipart/form-data'))!!}
    {{ csrf_field() }}
    <div class="form-group ">
        <div class="card">
            <div style="text-align:left; margin: 10px 10px 10px 0px;">
                {!! Form::label('data','Data', ['class' => 'control-label']) !!}
                <input type="file" name="excel" id="excel" >
            </div>
            <p>Seleccione el Año de la Data a Importar</p>
            <select name="anno" class="fo" id="anno">
                <option value="null">Seleccione un Año</option>
                <option value="2024">2024</option>
                <option value="2025">2025</option>
            </select>
            {!! Form::submit('Importar', ['class' => 'form-control btn btn-primary', 'title' => trans('message.solicitud_action.new_solicitud'), 'data-toggle' => 'tooltip', 'style' => 'background-color:' . $array_color['group_button_color'] . ';']) !!}

        </div>
    </div>
</div>
@endsection
@section('script_datatable')
    <script src="{{ url ('/js_users/js_users.min.js') }}" type="text/javascript"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            // Inicialmente, deshabilitar el botón
            $('#form_acciones_id input[type="submit"]').prop('disabled', true);

            $('#anno').change(function() {
                if ($(this).val() === 'null') {
                    $('#form_acciones_id input[type="submit"]').prop('disabled', true);
                } else {
                    $('#form_acciones_id input[type="submit"]').prop('disabled', false);
                }
            });
        });
    </script>
@endsection
