    @extends('adminlte::layouts.app')

    @section('css_database')
    @include('adminlte::layouts.partials.link')
    @endsection

    @section('htmlheader_title')
    {{ trans('adminlte_lang::message.home') }}
    @endsection

    @section('contentheader_title')

    <div style="text-align: center">
        @if($tipo == 1)
        <h2 style="margin: -25px 0px 0px 25pxpx"><img src="{{ url('/images/icons/logoSIA.png') }}" alt="logo" height="100px" >Crear Solicitudes de Denuncias, Quejas, Reclamos</h2>
        @elseif($tipo == 2)
        <h2 style="margin: -25px 0px 0px 25pxpx"><img src="{{ url('/images/icons/logoSIA.png') }}" alt="logo" height="100px" >Crear Solicitudes de Asesorias, Sugerencias</h2>
        @endif
    </div>

    @endsection


    @section('main-content')

    <div class="container-fluid w-50" style="max-width:640px">
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
                    <?php
                        $rols_id = auth()->user()->rols_id;
                        $direccion_id = auth()->user()->sala_unidad_id;
                        $phpValue = $rols_id;
                        $phpValue2 = $direccion_id;
                        echo "<script> var rolsJS = '" . $phpValue . "'; </script>";
                        echo "<script> var direccionJS = '" . $phpValue2 . "'; </script>";
                        echo "<script> var tipoJS = '" . $tipo . "'; </script>";

                    ?>
                    {!! Form::open(
                    array(
                        'route' => array('actividades.store'),
                        'method' => 'POST',
                        'id' => 'form_actividades_id',
                        'enctype' => 'multipart/form-data'
                    )
                ) !!}

                    {{ csrf_field() }}
                    <div class="form-group ">
                        <h3>Datos de la Actividades</h3>
                        <br>

                        <input type="text" name="tipo" id="tipo" value="{{ $tipo }}" hidden>

                        <div style="text-align:left;">
                            {!! Form::label('direccion_label', 'DIRECCION', ['class' => 'control-label']) !!}<span
                                class="required" style="color:red;">*</span>
                            {!! Form::text('direccion_show', old('direccion_id'), ['placeholder' => $sala_unidad ?? '', 'class' => 'form-control', 'id' => 'solicitud_salud_id', 'DISABLED' => TRUE]) !!}
                            <input type="text" name="sala_unidad_id" id="sala_unidad_id" value="{{ $sala_unidad_id ?? '' }}" hidden>
                        </div>
                        <div style="text-align:left;">
                            {!! Form::label('coordinacion', 'COORDINACION', ['class' => 'control-label']) !!}<span
                                class="required" style="color:red;">*</span>
                            {!! Form::select('coordinacion', $coordinacion, old('coordinacion'), ['placeholder' => 'Coordinacion', 'class' => 'form-control', 'id' => 'coordinacion_id', 'required' => true]) !!}
                            <input type="text" name="coordinacion_unidad_id" id="coordinacion_unidad_id" value="{{ $coordinacion_unidad_id ?? '' }}" hidden>
                        </div>

                        <div style="text-align:left;">
                            {!! Form::label('solicitud_salud_id_label', 'CORRELATIVO DE ACTIVIDAD', ['class' => 'control-label']) !!}<span
                                class="required" style="color:red;">*</span>
                            {!! Form::text('solicitud_salud_id_show', old('solicitud_salud_id'), ['placeholder' => $correlativoATC ?? '', 'class' => 'form-control', 'id' => 'solicitud_salud_id', 'DISABLED' => TRUE]) !!}
                            <input type="text" name="accion_id" id="accion_id" value="{{ $correlativoATC ?? '' }}" hidden>
                        </div>
                        <div style="text-align:left;">
                            {!! Form::label('descripcion','Descripcion de la Actividad Interna', ['class' => 'control-label']) !!}<span
                                class="required" style="color:red;">*</span>
                            {!! Form::textarea('descripcion', old('descripcion'), ['placeholder' => 'Descripcion de la Actividad Interna', 'class' => 'form-control', 'id' => 'descripcion_user', 'required' => true]) !!}
                        </div>
                        <div style="text-align:left;">
                            {!! Form::label('fecha','Fecha', ['class' => 'control-label']) !!}<span
                                class="required" style="color:red;">*</span>
                            {!! Form::date('fecha', old('fecha'), ['placeholder' => trans('message.users_action.fecha'), 'class' => 'form-control', 'id' => 'fecha_user', 'required' => true]) !!}
                        </div>
                        <div style="text-align:left;">
                            {!! Form::label('cantidad_semanales','Cantidad Semanal de Actividades', ['class' => 'control-label']) !!}<span
                                class="required" style="color:red;">*</span>
                            {!! Form::text('cantidad_semanales', old('cantidad_semanales'), ['placeholder' => 'Cantidad Semanal de Actividades', 'class' => 'form-control', 'id' => 'cantidad_user', 'required' => true]) !!}
                        </div>
                    </div>
                    <br>
                    {!! Form::submit('Actividades', ['class' => 'form-control btn btn-primary', 'title' => trans('message.solicitud_action.new_solicitud'), 'data-toggle' => 'tooltip', 'style' => 'background-color:' . $array_color['group_button_color'] . ';']) !!}
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

            var rolID = rolsJS;
            var direccionID = direccionJS;
            var tipoID = tipoJS;

            $(document).ready(function() {
                $('#cedula_user').on('input', function() {
                    this.value = this.value.replace(/[^0-9]/g, ''); // Elimina cualquier carácter que no sea un número
                });
            });

            $(document).ready(function() {
                var direccion = $('#sala_unidad_id').val();
                $.ajax({
                    url: "{{ route('getcoordxdireccion') }}",
                    type: "GET",
                    data: { direccion: direccion },
                    success: function (data) {
                        $("#coordinacion_id").empty();
                        $("#coordinacion_id").append('<option value="">Coordinacion</option>'); // Opción inicial
                        $.each(data, function (key, value) {
                            $("#coordinacion_id").append('<option value="' + key + '">' + value + '</option>');
                        });
                    },
                    error: function () {
                        alert("Error al cargar las Coordinaciones."); // Manejo de errores
                    }
                });
            })
            // $("#comuna_id").empty()
            $("#comuna_id").html('<option value="">COMUNA<option/>')

            // const comuna = $('#parroquia_id')
            $("#municipio_id").prop('disabled', true);
            $("#parroquia_id").prop('disabled', true);
            $("#comuna_id").prop('disabled', true);
            $("#comunidad_id").prop('disabled', true);
            $("#denunciado").hide();
            $("#sugerencia").hide();
            if (rolID == 10) {
                x = $("#tipo_solicitud_id").val();
                $("#direccion").show();
                $("#beneficiario").show();
                $("#Asignaciones").hide();
            } else {
                $("#sinasignar").hide();
                $("#beneficiario").hide();
                $("#direccion").show();
            }
            $("#enter").hide();
            $('#municipio_id').change(function () {
                $("#parroquia_id").prop('disabled', false)
            });

            $('#estado_id').change(function () {
                $("#municipio_id").prop('disabled', false);

            });

            $('#coordinacion_id').change(function () {
                var coordinacion = $('#coordinacion_id').val();

                $('#coordinacion_unidad_id').val(coordinacion);
            });

            $('#tiposolicitud_id').change(function () {
                var tiposolicitud_id = $('#tiposolicitud_id').val();

                $.ajax({
                    url: '/getTipoSolicitudes', // Ruta a tu controlador
                    type: 'GET',
                    data: { tiposolicitud_id: tiposolicitud_id }, // Datos a enviar al controlador
                    success: function (data) {
                        // Limpiar las opciones existentes del select
                        $('#subtiposolicitud_id').empty();

                        // Agregar un placeholder si el select no está deshabilitado
                        if ($('#subtiposolicitud_id').not(':disabled')) {
                            $('#subtiposolicitud_id').append('<option value="">{{ trans('message.solicitud_action.tipo_solicitud') }}</option>');
                        }

                        // Agregar las nuevas opciones al select
                        $.each(data, function (key, value) {
                            $('#subtiposolicitud_id').append('<option value="' + value.id + '">' + value.nombre + '</option>');
                        });
                    }
                });
            });

            $('#municipio_id').change(function () {
                var municipio = $('#municipio_id').val();
                if (municipio == 2) {
                    $("#parroquia_id").hide();
                    $("#parroquia_id_label").hide();
                    $("#parroquia_id_span").hide();
                    $("#comuna_id").hide();
                    $("#comuna_id_label").hide();
                    $("#comuna_id_span").hide();
                    $("#comunidad_id").hide();
                    $("#comunidad_id_label").hide();
                    $("#comunidad_id_span").hide();
                    $("#jefecomunidad_Label").hide();
                    $("#jefecomunidad_Span").hide();
                    $("#jefecomunidad_id").hide();
                    $("#telefonoJEFE").hide();
                    $("#telefonoJEFE_label").hide();
                    $("#telefonoJEFE_span").hide();
                    $("#nombreUBCH").hide();
                    $("#nombreUBCH_label").hide();
                    $("#nombreUBCH_span").hide();
                    $("#nomjefeUBCH").hide();
                    $("#nomjefeUBCH_label").hide();
                    $("#nomjefeUBCH_span").hide();
                    $("#teljefeUBCH").hide();
                    $("#teljefeUBCH_label").hide();
                    $("#teljefeUBCH_span").hide();
                } else {
                    $("#parroquia_id").show();
                    $("#parroquia_id_label").show();
                    $("#parroquia_id_span").show();
                    $("#comuna_id").show();
                    $("#comuna_id_label").show();
                    $("#comuna_id_span").show();
                    $("#comunidad_id").show();
                    $("#comunidad_id_label").show();
                    $("#comunidad_id_span").show();
                    $("#jefecomunidad_id").show();
                    $("#jefecomunidad_Label").show();
                    $("#jefecomunidad_Span").show();
                    $("#telefonoJEFE").show();
                    $("#telefonoJEFE_label").show();
                    $("#telefonoJEFE_span").show();
                    $("#nombreUBCH").show();
                    $("#nombreUBCH_label").show();
                    $("#nombreUBCH_span").show();
                    $("#nomjefeUBCH").show();
                    $("#nomjefeUBCH_label").show();
                    $("#nomjefeUBCH_span").show();
                    $("#teljefeUBCH").show();
                    $("#teljefeUBCH_label").show();
                    $("#teljefeUBCH_span").show();
                }
            })


            $("#jefecomunidad_id").prop('disabled', true);
            $('#direccion_id').change(function () {
                var direccion = $('#direccion_id').val();
                $("#direccion_id").prop('disabled', false);
                $.ajax({
                    url: "{{ route('getcoordxdireccion') }}",
                    type: "GET",
                    data: { direccion: direccion },
                    success: function (data) {
                        $("#coordinacion_id").empty();
                        $("#coordinacion_id").append('<option value="">Coordinacion</option>'); // Opción inicial
                        $.each(data, function (key, value) {
                            $("#coordinacion_id").append('<option value="' + value.id + '">' + value.nombre + '</option>');
                        });
                    },
                    error: function () {
                        alert("Error al cargar las coordinaciones."); // Manejo de errores
                    }
                });
            });

            $('#parroquia_id').change(function () {
                var parroquia = $('#parroquia_id').val();
                $("#comuna_id").prop('disabled', false);

                $.ajax({
                    url: "{{ route('getComunas') }}",
                    type: "GET",
                    data: { parroquia: parroquia },
                    success: function (data) {
                        $("#comuna_id").empty();
                        $("#comuna_id").append('<option value="">COMUNA</option>'); // Opción inicial
                        $.each(data, function (key, value) {
                            $("#comuna_id").append('<option value="' + value.id + '">' + value.codigo + '</option>');
                        });
                    },
                    error: function () {
                        alert("Error al cargar las comunas."); // Manejo de errores
                    }
                });
            });

            // $("#sinasignar").hide();
            // $("#enter").hide();
            $('#asignacion').change(function () {

                var asignacion = $("#asignacion").val();

                if (asignacion == "DIRECCION") {
                    $("#enter").hide();
                    $("#direccion").show();

                }
                if (asignacion == "ENTER") {
                    $("#enter").show();
                    $("#direccion").hide();

                }
            })
            $('#comuna_id').change(function () {
                var comuna = $('#comuna_id').val();
                var comunaId = $(this).val();
                $("#comunidad_id").prop('disabled', false);

                $.ajax({
                    url: "{{ route('getComunidad2') }}", // Ruta a tu controlador
                    type: "GET",
                    data: { comuna: comuna },
                    success: function (data) {
                        $("#comunidad_id").empty(); // Limpia opciones anteriores
                        $("#comunidad_id").append('<option value="">Seleccione Comunidad</option>'); // Opción inicial

                        $.each(data, function (key, value) {
                            $("#comunidad_id").append('<option value="' + value.id + '">' + value.nombre + '</option>');
                        });
                    },
                    error: function () {
                        // Manejo de errores (opcional)
                        alert("Error al cargar la comunidad.");
                    }
                });

            });

            $('#comunidad_id').change(function () {
                $("#jefecomunidad_id").prop('disabled', false);
                $("#comunidad_id").prop('disabled', false);
                var comunidad = $('#comunidad_id').val();
                var comunidadId = $(this).val();

                $.ajax({
                    url: "{{ route('getJefeComunidad') }}", // Ruta a tu controlador
                    type: "GET",
                    data: { comunidad_id: comunidadId },
                    success: function (data) {
                        $("#jefecomunidad_id").empty(); // Limpia opciones anteriores

                        $.each(data, function (key, value) {
                            $("#jefecomunidad_id").append('<option value="' + value.id + '">' + value.Nombre_Jefe_Comunidad + '</option>');
                        });
                    },
                    error: function () {
                        // Manejo de errores (opcional)
                        alert("Error al cargar los jefes de comunidad.");
                    }
                });

                var jefecomunidadID = $(this).val();
                $("#jefecomunidad_id").append('<option value="">Seleccione Jefe de Comunidad</option>'); // Opción inicial

                $.ajax({
                    url: "{{ route('getJefeComunidad2') }}",
                    type: "GET",
                    data: { jefecomunidadID: jefecomunidadID },
                    success: function (data) {
                        // Verifica si se recibieron datos
                        if (data.length > 0) {
                            var value = data[0]; // Accede al primer elemento del array

                            // Actualiza los campos usando .text() para elementos <p>
                            $("#telefonoJEFE").text(value.Telefono_Jefe_Comunidad);
                            $("#nombreUBCH").text(value.Nombre_Ubch);
                            $("#nomjefeUBCH").text(value.Nombre_Jefe_Ubch);
                            $("#teljefeUBCH").text(value.Telefono_Jefe_Ubch);
                        } else {
                            // Maneja el caso donde no se encontraron datos
                            $("#telefonoJEFE").text('');
                            $("#nombreUBCH").text('');
                            $("#nomjefeUBCH").text('');
                            $("#teljefeUBCH").text('');
                        }
                    },
                    error: function () {
                        alert("Error al cargar los datos.");
                    }
                });
            })

            $('#tipo_solicitud_id').change(function () {

                var tipo = $('#tipo_solicitud_id').val();
                if (tipo == 0) {
                    $("#denunciado").hide();
                    $("#sugerencia").hide();
                    $("#beneficiario").hide();
                }

                if (tipo == 1) {
                    $("#denunciado").show();
                    $("#sugerencia").hide();
                    $("#beneficiario").hide();
                }

                if (tipo == 2) {
                    $("#denunciado").show();
                    $("#sugerencia").hide();
                    $("#beneficiario").hide();
                }
                if (tipo == 3) {
                    $("#denunciado").show();
                    $("#sugerencia").hide();
                    $("#beneficiario").hide();
                }
                if (tipo == 4) {
                    $("#denunciado").hide();
                    $("#sugerencia").show();
                    $("#beneficiario").hide();
                }
                if (tipo == 5) {
                    $("#denunciado").hide();
                    $("#sugerencia").show();
                    $("#beneficiario").hide();
                }
                if (tipo == 6) {
                    $("#denunciado").hide();
                    $("#sugerencia").hide();
                    $("#beneficiario").show();
                }

            });
        })
    </script>
    <style>
    section.content{
            background-image: url("{{ url('/images/siacreate.jpg') }}");
    }
    </style>
    @endsection
