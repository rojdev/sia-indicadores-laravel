@extends('adminlte::layouts.app')

@section('css_database')
@include('adminlte::layouts.partials.link')
@endsection

@section('htmlheader_title')
{{ trans('adminlte_lang::message.home') }}
@endsection

@section('contentheader_title')
<div>
    <h2 class="mb-4">EDITAR ACTIVIDAD</h2>
    <div>
    @component('components.boton_back', [
        'ruta' => url()->previous(),
        'color' => $array_color['back_button_color']
    ])
        Botón de retorno
    @endcomponent
</div>
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
                    $phpValue = $rols_id;
                    $idmunicipio = $solicitud_edit->municipio_id;
                    $idjefecomunidad = $solicitud_edit->jefecomunidad_id;
                    echo "<script> var rolsJS = '" . $phpValue . "'; </script>";
                    echo "<script> var idmunicipioJS = '" . $idmunicipio . "'; </script>";
                    echo "<script> var jefecomunidadID = '" . $idjefecomunidad. "'; </script>";
                ?>
                {!! Form::open(array(
                    'route' => array('actividades.update', $solicitud_edit->id),
                    'method' => 'POST',
                    'id' => 'form_users_id',
                    'enctype' => 'multipart/form-data'
                    )) !!}


                <div class="form-group">
                <div style="text-align:left;">
                        {!! Form::label('direccion_id', 'Direccion', ['class' => 'control-label']) !!}<span
                            class="required" style="color:red;">*</span>
                        {!! Form::select('direccion_id', $direcciones, $solicitud_edit->direccion_id, ['placeholder' =>'DIRECCION', 'class' => 'form-control', 'id' => 'direccion_id']) !!}
                    </div>
                    <div style="text-align:left;">
                        {!! Form::label('coordinacion_id', 'Coordinacion', ['class' => 'control-label']) !!}<span
                            class="required" style="color:red;">*</span>
                        {!! Form::select('coordinacion_id', $coordinaciones, $solicitud_edit->coordinacion_id, ['placeholder' =>'COORDINACION', 'class' => 'form-control', 'id' => 'coordinacion_id']) !!}
                    </div>
                <div style="text-align:left;">
                        {!! Form::label('solicitud_salud_id_label', 'Correlativo de Actividades', ['class' => 'control-label']) !!}<span
                            class="required" style="color:red;">*</span>
                        {!! Form::text('accion_id2', $solicitud_edit->actividad_id, ['placeholder' => 'Correlativo de Acciones', 'class' => 'form-control', 'id' => 'accion_id', 'DISABLED' => TRUE]) !!}
                        {!! Form::hidden('accion_id', $solicitud_edit->actividad_id, ['placeholder' => 'Correlativo de Acciones', 'class' => 'form-control', 'id' => 'accion_id']) !!}
                    </div>

                    <div style="text-align:left;">
                        {!! Form::label('nombre', 'Descripcion de la Actividades de Gobierno', ['class' => 'control-label']) !!}<span
                            class="required" style="color:red;">*</span>
                        {!! Form::textarea('nombre', $solicitud_edit->descripcion, ['placeholder' => 'Descripcion de la Accion de Gobierno', 'class' => 'form-control', 'id' => 'nombre_user']) !!}
                    </div>
                    <div style="text-align:left;">
                        {!! Form::label('fechainicial','Fecha Inicial', ['class' => 'control-label']) !!}<span class="required" style="color:red;">*</span>
                        {!! Form::date('fechainicial', $solicitud_edit->fecha, ['placeholder' => trans('message.users_action.nombre'), 'class' => 'form-control', 'id' => 'nombre_user', 'required' => true]) !!}
                    </div>

                    <div style="text-align:left;">
                        {!! Form::label('cantidad', 'Cantidad Semanales', ['class' => 'control-label']) !!}
                        {!! Form::text('cantidad', $solicitud_edit->cantidad_semanales, ['placeholder' => 'Cantidad Semanales', 'class' => 'form-control', 'id' => 'cantidad']) !!}
                    </div>
                    @if(Auth::user()->rols_id === 14 || Auth::user()->rols_id === 1)
                    <br>
                    <div style="text-align:left;">
                        {!! Form::label('telefono', 'Observacion de la revisión', ['class' => 'control-label']) !!}
                        {!! Form::textarea('observacion', $solicitud_edit->observacion, ['placeholder' => 'Revision', 'class' => 'form-control', 'id' => 'telefono']) !!}
                    </div>
                    <br>
                    <<div style="text-align:left;">
                    {{ Form::label('state', trans('Estado'), ['class' => 'control-label', 'id' => 'state_id_label']) }}
                        <span class="required" style="color:red;" id="state_required">*</span>
                             <select name="state" id="state_select" class="form-control">
                             @foreach($state as $key => $value)
                             <option value="{{ $key }}" @if(old('state', $solicitud_edit->state) == $key) selected @endif>{{ $value }}</option>
                             @endforeach
                          </select>
                        </div>
                    @endif














                    <?php

                    $variable = $solicitud_edit->tipo_solicitud_id;
                    $variable2 = $solicitud_edit->asignacion;
                    if ($variable == 1) {
                        echo '<script>document.getElementById("sugerencia").style.display = "none";</script>';
                        echo '<script>document.getElementById("beneficiario").style.display = "none";</script>';
                    }
                    if ($variable == 2) {
                        echo '<script>document.getElementById("sugerencia").style.display = "none";</script>';
                        echo '<script>document.getElementById("beneficiario").style.display = "none";</script>';
                    }
                    if ($variable == 3) {
                        echo '<script>document.getElementById("sugerencia").style.display = "none";</script>';
                        echo '<script>document.getElementById("beneficiario").style.display = "none";</script>';
                    }
                    if ($variable == 4) {
                        echo '<script>document.getElementById("denunciado").style.display = "none";</script>';
                        echo '<script>document.getElementById("beneficiario").style.display = "none";</script>';
                    }
                    if ($variable == 5) {
                        echo '<script>document.getElementById("denunciado").style.display = "none";</script>';
                        echo '<script>document.getElementById("beneficiario").style.display = "none";</script>';
                    }
                    if ($variable == 6) {
                        echo '<script>document.getElementById("sugerencia").style.display = "none";</script>';
                        echo '<script>document.getElementById("denunciado").style.display = "none";</script>';
                    }
                    if ($variable2 == 'DIRECCION') {
                        echo '<script>document.getElementById("enter_descentralizados_id").style.display = "none";</script>';
                    }
                    if ($variable2 == 'ENTER') {
                        echo '<script>document.getElementById("direccion").style.display = "none";</script>';
                    }

                    ?>
                    <hr>

                    {!! Form::submit('ACT. SOLIC', ['class' => 'form-control btn btn-primary', 'title' => 'ACT. SOLIC', 'data-toggle' => 'tooltip', 'style' => 'background-color:' . $array_color['group_button_color'] . ';']) !!}
                    <input type="hidden" name="id_solicitud" value="{{$solicitud_edit->id}}">
                    {!!  Form::close() !!}
                </div>
            </div>
        </div>
    </div>
</div>
</div>
@endsection
@section('script_datatable')
<script src="{{ url('/js_users/js_users.min.js') }}" type="text/javascript"></script>
<script type="text/javascript">
    $(document).ready(function () {

        if(jefecomunidadID == ''){
            $("#jefecomunidad_id").html('<option value="">Seleccione Jefe de Comunidad</option>'); // Opción inicial
        }

        if(idmunicipioJS == 2){
            $("#parroquia_id").hide();
                $("#parroquia_id_label").hide();
                $("#parroquia_id_span").hide();
                $("#comuna_id").hide();
                $("#comuna_id_label").hide();
                $("#comuna_id_span").hide();
                $("#comunidad_id").hide();
                $("#comunidad_id_label").hide();
                $("#comunidad_id_span").hide();
                $("#jefecomunidad_label").hide();
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
        }
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
                            $("#coordinacion_id").append('<option value="' + key + '">' + value+ '</option>');
                        });
                    },
                    error: function () {
                        alert("Error al cargar las coordinaciones."); // Manejo de errores
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
                $("#jefecomunidad_label").hide();
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
                $("#jefecomunidad_label").show();
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
        $('#municipio_id').change(function () {
            $("#parroquia_id").prop('disabled', false);
        });
        $('#estado_id').change(function () {
            $("#municipio_id").prop('disabled', false);

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

        $('#jefecomunidad_id').change(function () {
            var jefecomunidadID = $(this).val();
            $.ajax({
                url: "{{ route('getJefeComunidad2') }}",
                type: "GET",
                data: { jefecomunidadID: jefecomunidadID },
                success: function (data) {
                    // Verifica si se recibieron datos
                    if (data.length > 0) {
                        var value = data[0]; // Accede al primer elemento del array
                        // Actualiza los campos usando .text() para elementos <p>
                        $("#telefonoJEFE").empty();
                        $("#telefonoJEFE").append('<option value="' + value.Telefono_Jefe_Comunidad + '">' + value.Telefono_Jefe_Comunidad + '</option>');
                        $("#nombreUBCH").empty();
                        $("#nombreUBCH").append('<option value="' + value.Telefono_Jefe_Comunidad + '">' + value.Telefono_Jefe_Comunidad + '</option>');
                        $("#nomjefeUBCH").empty();
                        $("#nomjefeUBCH").append('<option value="' + value.Nombre_Jefe_Ubch + '">' + value.Nombre_Jefe_Ubch + '</option>');
                        $("#teljefeUBCH").empty();
                        $("#teljefeUBCH").append('<option value="' + value.Telefono_Jefe_Ubch + '">' + value.Telefono_Jefe_Ubch + '</option>');
                        //  $("#telefonoJEFE").val(value.Telefono_Jefe_Comunidad);
                        //  $("#nombreUBCH").val(value.Nombre_Ubch);
                        //   $("#nomjefeUBCH").val(value.Nombre_Jefe_Ubch);
                        //  $("#teljefeUBCH").val(value.Telefono_Jefe_Ubch);
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
        });


        $('#direcciones_id').change(function () {
            var direccion = $('#direcciones_id').val();

            $.ajax({

                url: "{{ route('getCoodinacion') }}",
                type: "GET",
                data: { direccion: direccion }

            }).done(function (data) {

                $("#coordinacion_id").empty();
                $("#coordinacion_id").html('<option value="">COORDINACION<option/>');
                for (let c in data) {

                    $("#coordinacion_id").append(`<option value="${c}">${data[c]}<option/>`);

                }

            })

        });


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
@endsection
