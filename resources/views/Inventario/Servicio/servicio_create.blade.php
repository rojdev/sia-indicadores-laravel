@extends('adminlte::layouts.app')

@section('css_database')
    @include('adminlte::layouts.partials.link')
@endsection

@section('htmlheader_title')
    {{ trans('adminlte_lang::message.home') }}
@endsection

@section('contentheader_title')
<div>
    <h2 class="mb-4">{{ $titulo_modulo}}</h2>
    @component('components.boton_back',['ruta' => route('servicio'),'color' => $array_color['back_button_color']])
        Botón de retorno
    @endcomponent   
</div>
    
@endsection

    
@section('main-content')

<div class="contenedor">
    <div class="card">
        <div class="card-body">
            <div class="col-md-6">
                @if ($errors->any())
                    <div class="alert alert-danger">
                    <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
                    </ul>
                    </div>
                @endif
                {!! Form::open(array('route' => array('servicio.store'),
                'method'=>'POST','id' => 'form_rols_id','enctype' =>'multipart/form-data')) !!}
                <div class="form-group">                
                    <div class="estilo-almacen">                    
                        <div style="text-align:left;">
                            {!! Form::label('nombre','Nombre Servicio', ['class' => 'control-label']) !!}<span class="required" style="color:red;">*</span>
                            {!! Form::text('nombre',old('nombre'),['placeholder' => 'Nombre Servicio','class' => 'form-control','id' => 'nombre_almacen']) !!}
                        </div>
                        <div style="text-align:left;">
                            {!! Form::label('descripcion','Descripcion', ['class' => 'control-label']) !!}<span class="required" style="color:red;">*</span>
                            {!! Form::text('descripcion',old('descripcion'),['placeholder' => 'Descripcion','class' => 'form-control','id' => 'descripcion_almacen']) !!}
                        </div>                            
                    </div>     
                    <br>
                    <span class="boton">
                        {!! Form::submit('Guardar Serv.',['class'=> 'form-control btn btn-primary','title' => 'Guardar Servicio','data-toggle' => 'tooltip','style' => 'background-color:'.$array_color['group_button_color'].';']) !!}                     
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