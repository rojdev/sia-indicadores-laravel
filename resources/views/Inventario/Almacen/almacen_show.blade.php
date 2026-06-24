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
    @component('components.boton_back',['ruta' => route('almacen'),'color' => $array_color['back_button_color']])
        Botón de retorno
    @endcomponent   
</div>
    
@endsection

    
@section('main-content')

<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <div class="col-lg-12 col-xs-12">                                
                <div class="form-group">                
                    <div class="col-lg-6 col-xs-6">                    
                        <div class="form-group">                                            
                                <div style="text-align:left;">
                                    {!! Form::label('nombre','Nombre', ['class' => 'control-label']) !!}<span class="required" style="color:red;">*</span>
                                    {!! Form::text('nombre',$almacen->nombre,['class' => 'form-control','id' => 'nombre','disabled' => true]) !!}
                                </div>
                                <div style="text-align:left;">
                                    {!! Form::label('ubicacion','Ubicacion', ['class' => 'control-label']) !!}<span class="required" style="color:red;">*</span>
                                    {!! Form::text('ubicacion',$almacen->ubicacion,['class' => 'form-control','id' => 'ubicacion','disabled' => true]) !!}
                                </div>                                                        
                        </div>                        
                    </div>                        
                </div>
            </div>             
        </div>
    </div>
</div>
@endsection