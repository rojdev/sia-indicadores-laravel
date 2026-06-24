@extends('adminlte::layouts.app')

@section('css_database')
    @include('adminlte::layouts.partials.link')
@endsection

@section('htmlheader_title')
    {{ trans('adminlte_lang::message.home') }}
@endsection

@section('contentheader_title')
<div>
    <h2 class="mb-4">Editar Inventario</h2>
    @component('components.boton_back',['ruta' => route('inventario'),'color' => $array_color['back_button_color']])
        Botón de retorno
    @endcomponent   
</div>
    
@endsection

    
@section('main-content')

<div class="container-fluid">
    <div class="card">
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
                {!! Form::open(array('route' => array('inventario.update',$inventario->id),
                'method'=>'POST','id' => 'form_rols_id','enctype' =>'multipart/form-data')) !!}
                <div class="form-group">                
                <div class="col-lg-6 col-xs-6">     
                <div style="text-align:left;">
                        {!! Form::label('producto_id_label', 'Producto', ['class' => 'control-label', 'id' => 'Producto_id_label']) !!}<span
                            class="required" style="color:red;" id="producto_id_span">*</span>
                        <select  name="producto_id" disabled id="producto_id" class="form-control">
                            @foreach($producto as $key => $value)
                                <option  value="{{ $value->id }}" @if(old('producto_id', $inventario->producto_id) == $value->id) selected @endif>{{ $value->nombre }}</option>
                            @endforeach
                        </select>
                    </div>        
                    <div style="text-align:left;">
                        {!! Form::label('almacen_id_label', 'Almacen', ['class' => 'control-label', 'id' => 'Almacen_id_label']) !!}<span
                            class="required" style="color:red;" id="almacen_id_span">*</span>
                        <select disable name="almacen_id" disabled id="almacen_id" class="form-control">
                            @foreach($almacen as $key => $value)
                                <option  value="{{ $value->id }}" @if(old('almacen_id', $inventario->almacen_id) == $value->id) selected @endif>{{ $value->nombre }}</option>
                            @endforeach
                        </select>
                    </div>  
                    <div style="text-align:left;">
                        {!! Form::label('tipoentrada_id', 'Tipo Entrada', ['class' => 'control-label']) !!}<span
                            class="required" style="color:red;">*</span>
                        {!! Form::select('tipoentrada', $tipoentrada, $inventario->tipoentrada, ['placeholder' => 'Tipo de Entrada', 'class' => 'form-control', 'id' => 'tipoentrada', 'disabled' => true]) !!}    
                    </div>   
                    @if($inventario->tipoentrada == 'COMPRA')              
                    <div style="text-align:left;">
                        {!! Form::label('numerofactura','Nro Factura', ['class' => 'control-label', 'id' => 'label_numerofactura']) !!}
                        {!! Form::text('numerofactura',$inventario->numerofactura,['class' => 'form-control','id' => 'numerofactura', 'disabled' => true]) !!}
                    </div>
                    @endif
                    @if($inventario->tipoentrada == 'DONACIONES') 
                    <div style="text-align:left;">
                        {!! Form::label('numerodonacion','Nro Donacion', ['class' => 'control-label',  'id' => 'label_numerodonacion']) !!}
                        {!! Form::text('numerodonacion',$inventario->numerodonacion,['class' => 'form-control','id' => 'name_description', 'disabled' => true]) !!}
                    </div> 
                    @endif
                    <div style="text-align:left;">
                        {!! Form::label('cantidad','Existencia', ['class' => 'control-label']) !!}<span class="required" style="color:red;">*</span>
                        {!! Form::text('cantidad',$inventario->cantidad,['class' => 'form-control','id' => 'name_description']) !!}
                    </div>    
                    <div style="text-align:left;">
                    {!! Form::label('fechavencimiento','Fecha Vencimiento', ['class' => 'control-label']) !!}<span class="required" style="color:red;">*</span>
                    {!! Form::date('fechavencimiento', \Carbon\Carbon::parse($inventario->fechavencimiento)->format('Y-m-d'), ['class' => 'form-control','id' => 'name_description']) !!}
                    </div>        
                    
                         
                </div>        
                <hr>
                        {!! Form::submit('Actualizar',['class'=> 'form-control btn btn-primary','title' => 'Actualizar','data-toggle' => 'tooltip','style' => 'background-color:'.$array_color['group_button_color'].';']) !!}                     
                </div>      
                {!!  Form::close() !!}
            </div>             
        </div>
    </div>
</div>
@endsection
@section('script_datatable')

<script type="text/javascript">

$(document).ready(function () {

    $('#tipoentrada').change(function () {
        var a = $(this).val();
        alert('holaaaaaaa');
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