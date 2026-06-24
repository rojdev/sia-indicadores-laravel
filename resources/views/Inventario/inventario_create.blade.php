@extends('adminlte::layouts.app')

@section('css_database')
    @include('adminlte::layouts.partials.link')
@endsection

@section('htmlheader_title')
    {{ trans('adminlte_lang::message.home') }}
@endsection

@section('contentheader_title')
<div>
    <h2 class="mb-4">Crear Inventario</h2>
    @component('components.boton_back',['ruta' => route('inventario'),'color' => $array_color['back_button_color']])
        Botón de retorno
    @endcomponent   
</div>
    
@endsection

    
@section('main-content')

<div class="contenedor">
    <div class="card col-lg-12 col-xs-12">
        <div class="card-body">
            <div class="col-lg-12 col-xs-12">
                @if ($errors->any())
                    <div class="alert alert-danger">
                    <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
                    </ul>
                    </div>
                @endif
                {!! Form::open(array('route' => array('inventario.store'),
                'method'=>'POST','id' => 'form_rols_id','enctype' =>'multipart/form-data')) !!}
                <div class="form-group col-lg-12 col-xs-12">                
                    <div class="estilo-almacen">    
                    <div style="text-align:left;">
                            {!! Form::label('almacen_id', 'Almacen', ['class' => 'control-label', 'id' => 'almacen_Label']) !!}
                            <select name="almacen_id" id="almacen_id" class="form-control">
                            @foreach($almacen as $key => $value)
                                <option value="{{ $value->id }}" @if(old('almacen_id',$value->id ) ) selected @endif>
                                    {{ $value->nombre }}
                                </option>
                            @endforeach
                            </select>
                        </div>       
                        <div style="text-align:left;">
                            {!! Form::label('compra_id', 'Compra', ['class' => 'control-label', 'id' => 'producto_Label']) !!}
                            <select name="producto_id" id="producto_id" class="form-control">
                            @foreach($producto as $key => $value)
                                <option value="{{ $value->id }}" @if(old('producto_id',$value->id ) ) selected @endif>
                                    {{ $value->nombre }}
                                </option>
                            @endforeach
                            </select>
                        </div> 
                        <div style="text-align:left;">
                        <label>TIPO ENTRADA*</label>
                        <select required name="tipoentrada" id="tipoentrada" class="selectpicker form-control"
                            data-live-search="true" data-live-search-style="begins">
                            <option value="SELECCIONE UNA OPCION">SELECCIONE UNA OPCION</option>
                            <option value="COMPRA">COMPRA</option>
                            <option value="DONACIONES">DONACIONES</option>
                        </select>
                    </div>
                            
                        <div style="text-align:left;">
                            {!! Form::label('numerofactura','Nro Factura', ['class' => 'control-label', 'id' => 'label_numerofactura']) !!}
                            {!! Form::text('numerofactura',old('numerofactura'),['placeholder' => 'Nro factura','class' => 'form-control','id' => 'numerofactura']) !!}
                        </div>
                        <div style="text-align:left;">
                            {!! Form::label('numerodonacion','Nro Donacion', ['class' => 'control-label', 'id' => 'label_numerodonacion']) !!}
                            {!! Form::text('numerodonacion',old('numerodonacion'),['placeholder' => 'Nro Donacion','class' => 'form-control','id' => 'numerodonacion']) !!}
                        </div>
                        <div style="text-align:left;">
                            {!! Form::label('cantidad','Existencia', ['class' => 'control-label']) !!}<span class="required" style="color:red;">*</span>
                            {!! Form::text('cantidad',old('nombre'),['placeholder' => 'Nombre Producto','class' => 'form-control','id' => 'cantidad']) !!}
                        </div>
                        <div style="text-align:left;">
                            {!! Form::label('fechavencimiento','Fecha Vencimiento', ['class' => 'control-label']) !!}<span class="required" style="color:red;">*</span>
                            {!! Form::date('fechavencimiento',old('fechavencimiento'),['placeholder' => 'Fecha Vencimiento','class' => 'form-control','id' => 'fechavencimiento']) !!}
                        </div>     
                        
                                                             
                    </div>    
                    <br>
                    <br>
                    <span class="boton">
                        {!! Form::submit('Guardar Prod.',['class'=> 'form-control btn btn-primary','title' => 'Guardar Almacen','data-toggle' => 'tooltip','style' => 'background-color:'.$array_color['group_button_color'].';']) !!}                     
                    </span>   
                </div> 
                {!!  Form::close() !!}
            </div>             
        </div>
    </div>
</div>
<style>
    .estilo-almacen{
        display: flex;
        flex-direction: column;
        gap: 20px;
    }
    .contenedor{
        max-width: 120px;
    }
</style>
@endsection
@section('script_datatable')

<script type="text/javascript">

$(document).ready(function () {
     $('#label_numerodonacion').hide();
     $('#label_numerofactura').hide();
    $('#numerofactura').hide();
    $('#numerodonacion').hide();
    $('#tipoentrada').change(function () {
        var a = $(this).val();
        if (a == 'DONACIONES') {
            $('#numerofactura').hide();
            $('#numerodonacion').show();
            $('#label_numerodonacion').show();
            $('#label_numerofactura').hide();
        }
        if (a == 'COMPRA') {
            $('#numerofactura').show();
            $('#numerodonacion').hide();
            $('#label_numerodonacion').hide();
            $('#label_numerofactura').show();
        } 
        if (a == 'SELECCIONE UNA OPCION') {
            $('#numerofactura').hide();
            $('#numerodonacion').hide();
            $('#label_numerodonacion').hide();
            $('#label_numerofactura').hide();
        }

    });

});
</script>
@endsection
