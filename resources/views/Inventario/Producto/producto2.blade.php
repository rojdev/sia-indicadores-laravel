@extends('adminlte::layouts.app')

@section('css_database')
    @include('adminlte::layouts.partials.link')
@endsection

@section('htmlheader_title')
    {{ trans('adminlte_lang::message.home') }}
@endsection

@section('contentheader_title')
<!-- Componente Button Para todas las Ventanas de los Módulos, no Borrar.--> 

<div class="container" >
    <div class="col-md-6 col-sm-6" >
        <div class="row">
        <iframe class="embed-responsive-item" style="width: 100%; height: 600px" src="http://0.0.0.0:7000/producto" allowfullscreen seamless></iframe>
        </div>
    </div>
    <div class="col-md-6 col-sm-6" >
        <div class="row">
            <iframe class="embed-responsive-item" style="width: 100%; height: 600px" src="http://0.0.0.0:7000/almacen" allowfullscreen></iframe>
        </div>
    </div>
    <div class="col-md-4 col-sm-4" >
        <div class="row">
            <iframe class="embed-responsive-item" style="width: 100%; height: 400px" src="https://alcaldiapaez.gob.ve" allowfullscreen></iframe>  
        </div>
    </div>
    <div class="col-md-4 col-sm-4" >
        <div class="row">
            <iframe class="embed-responsive-item" style="width: 100%; height: 400px" src="http://0.0.0.0:7000/dashboard" allowfullscreen></iframe>  
        </div>
    </div>
    <div class="col-md-4 col-sm-4" >
        <div class="row">
            <iframe class="embed-responsive-item" style="width: 100%; height: 400px" src="http://0.0.0.0:7000/servicio" allowfullscreen></iframe>  
        </div>
    </div>
</div>

    
@endsection

@section('link_css_datatable')
    <link href="{{ url ('/css_datatable/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ url ('/css_datatable/dataTables.bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ url ('/css_datatable/responsive.bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ url ('/css_datatable/buttons.dataTables.min.css') }}" rel="stylesheet">
@endsection

    
@section('main-content')


@endsection
@section('script_datatable')
<script src="{{ url ('/js_datatable/jquery.dataTables.min.js') }}" type="text/javascript"></script>
<script src="{{ url ('/js_datatable/dataTables.bootstrap.min.js') }}" type="text/javascript"></script>
<script src="{{ url ('/js_datatable/dataTables.responsive.min.js') }}" type="text/javascript"></script>
<script src="{{ url ('/js_datatable/responsive.bootstrap.min.js') }}" type="text/javascript"></script>
<script src="{{ url ('/js_datatable/dataTables.buttons.min.js') }}" type="text/javascript"></script>
<script src="{{ url ('/js_delete/sweetalert.min.js') }}" type="text/javascript"></script>
<script type="text/javascript">
 
</script>
<script src="{{ url ('/js_delete/delete_confirm.min.js') }}"></script>
<style>
iframe .main-header {
    display: none;
}

</style>
@endsection  
