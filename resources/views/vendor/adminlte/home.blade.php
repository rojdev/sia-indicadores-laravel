@extends('adminlte::layouts.app')

@section('htmlheader_title')
	{{ trans('message.home_1') }}
@endsection

@section('contentheader_title')
    <div >
      <h2 class="mb-4" style="text-align: center; display: flex; justify-content: space-between; margin-bottom: -50px">
        <img src="{{ url('/images/icons/logo.png') }}" alt="logo" height="100px">
        <p style="margin-top: 40px; font-size: 36px">SIA - Sistema Integral de Atención</p>
        <img src="{{ url('/images/icons/logoSIA.png') }}" alt="logo" height="150px" style="margin-top: -15px">
      </h2>
    </div>
    <br>
    <br>
@endsection

@section('main-content')

<?php

?>

	<!--  CANVAS de las Metricas Para User, Rol y Notificaciones, para View-->
        <div class="row justify-content-center">
          <div class="col-sm-12 align-self-center">
            <div class="row">
              <div class="col-lg-6">
                <div class="panel panel-default">
                  <div class="panel-heading"><b>Total de Acciones de Gobierno y Actividades 2024-2025</b></div>
                  <div class="panel-body" id="contenedor_02">
                    <canvas style="width: 684px; height: 400px;" id="solicitudTipo"></canvas>
                  </div>
                </div>
              </div>
              <div class="col-lg-6">
                <div class="panel panel-default">
                  <div class="panel-heading"><b>Total de Acciones de Gobierno y Actividades 2024-2025</b></div>
                  <div class="panel-body" id="contenedor_01">
                    <canvas style="width: 684px; height: 400px;" id="solicitudTipo2"></canvas>
                  </div>
                </div>
              </div>

              <div class="col-lg-6">
                <div class="panel panel-default">
                  <div class="panel-heading"><b>Total de Acciones de Gobierno y Actividades 2024-2025</b></div>
                  <div class="panel-body" id="contenedor_03">
                    <canvas style="width: 684px; height: 400px;" id="solicitudTipo3"></canvas>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <hr/>
@endsection

@section('script_Chart')
<script src="{{ url('/js_Chart/Chart.min.js') }}" type="text/javascript"></script>
<script src="{{ url('/js_dashboard/graficos_dashboard.min.js') }}" type="text/javascript"></script>
@endsection
