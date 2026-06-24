@extends('adminlte::layouts.app')

@section('css_database')
    @include('adminlte::layouts.partials.link')
@endsection

@section('htmlheader_title')
    {{ trans('adminlte_lang::message.home') }}
@endsection

@section('contentheader_title')
<div>
    <h2 class="mb-4">Editar Producto</h2>
    @component('components.boton_back',['ruta' => route('producto'),'color' => $array_color['back_button_color']])
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
                {!! Form::open(array('route' => array('producto.update',$producto->id),
                'method'=>'POST','id' => 'form_rols_id','enctype' =>'multipart/form-data')) !!}
                <div class="form-group">                
                <div class="col-lg-6 col-xs-6">                    
                    <div style="text-align:left;">
                        {!! Form::label('nombre','Nombre', ['class' => 'control-label']) !!}<span class="required" style="color:red;">*</span>
                        {!! Form::text('nombre',$producto->nombre,['class' => 'form-control','id' => 'name_rol', 'disabled' => false]) !!}
                    </div>
                    <div style="text-align:left;">
                        {!! Form::label('descripcion','Descripcion del Producto', ['class' => 'control-label']) !!}<span class="required" style="color:red;">*</span>
                        {!! Form::text('descripcion',$producto->descripcion,['class' => 'form-control','id' => 'name_description']) !!}
                    </div> 
                    <div style="text-align:left;">
                        {!! Form::label('cantidad','Cantidad del Producto', ['class' => 'control-label']) !!}<span class="required" style="color:red;">*</span>
                        {!! Form::text('cantidad',$producto->cantidad,['class' => 'form-control','id' => 'name_description']) !!}
                    </div>    
                    <div style="text-align:left;">
                        {!! Form::label('precio','Precio del Producto', ['class' => 'control-label']) !!}<span class="required" style="color:red;">*</span>
                        {!! Form::text('precio',$producto->precio,['class' => 'form-control','id' => 'name_description']) !!}
                    </div>         
                    <div style="text-align:left;">
                        {!! Form::label('categoria_id_label', 'Categoria', ['class' => 'control-label', 'id' => 'categoria_id_label']) !!}<span
                            class="required" style="color:red;" id="categoria_id_span">*</span>
                        <select name="categoria_id" id="categoria_id" class="form-control">
                            @foreach($categoria as $key => $value)
                                <option value="{{ $value->id }}" @if(old('categoria_id', $producto->categoria_id) == $value->id) selected @endif>{{ $value->nombre }}</option>
                            @endforeach
                        </select>
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