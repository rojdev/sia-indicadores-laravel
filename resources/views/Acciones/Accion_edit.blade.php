@extends('adminlte::layouts.app')

@section('css_database')
@include('adminlte::layouts.partials.link')
@endsection

@section('htmlheader_title')
{{ trans('adminlte_lang::message.home') }}
@endsection

@section('contentheader_title')
<div>
    <h2 class="mb-4">EDITAR ACCION</h2>
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

    <div class="container-fluid">
         <div class="card">
            <div class="card-body">

                 <div class="row">
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
                        'route' => array('acciones.update', $solicitud_edit->id),
                        'method' => 'POST',
                        'id' => 'form_users_id',
                        'enctype' => 'multipart/form-data'
                        )) !!}       @if ($errors->any())
                            <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                                </div>
                            @endif
                     <div class="col-md-6">



                    <h3>Datos de la Acción</h3>
                    <div class="form-group">
                        {!! Form::label('direccion_id', 'Direccion', ['class' => 'control-label']) !!}<span class="required" style="color:red;">*</span>
                        {!! Form::select('direccion_id', $direcciones, $solicitud_edit->direccion_id, ['placeholder' =>'DIRECCION', 'class' => 'form-control', 'id' => 'direccion_id']) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('coordinacion_id', 'Coordinacion', ['class' => 'control-label']) !!}<span class="required" style="color:red;">*</span>
                        {!! Form::select('coordinacion_id', $coordinaciones, $solicitud_edit->coordinacion_sala_id, ['placeholder' =>'COORDINACION', 'class' => 'form-control', 'id' => 'coordinacion_id']) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('solicitud_salud_id_label', 'Correlativo de Accion', ['class' => 'control-label']) !!}<span class="required" style="color:red;">*</span>
                        {!! Form::text('accion_id2', $solicitud_edit->accion_id, ['placeholder' => 'Correlativo de Acciones', 'class' => 'form-control', 'id' => 'accion_id', 'readonly' => true]) !!} {{-- Cambiado a readonly --}}
                        {!! Form::hidden('accion_id', $solicitud_edit->accion_id) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('nombre', 'Descripcion de la Accion de Gobierno', ['class' => 'control-label']) !!}<span class="required" style="color:red;">*</span>
                        {!! Form::textarea('nombre', $solicitud_edit->nombre, ['placeholder' => 'Descripcion de la Accion de Gobierno', 'class' => 'form-control', 'id' => 'nombre_user']) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('fechainicial','Fecha Inicial', ['class' => 'control-label']) !!}<span class="required" style="color:red;">*</span>
                        {!! Form::date('fechainicial', $solicitud_edit->fechainicial, ['class' => 'form-control', 'id' => 'fechainicial', 'required' => true]) !!} {{--  ID corregido --}}
                    </div>
                    <div class="form-group">
                        {!! Form::label('fechafinal','Fecha Final', ['class' => 'control-label']) !!}<span class="required" style="color:red;">*</span>
                        {!! Form::date('fechafinal', $solicitud_edit->fechafinal, ['class' => 'form-control', 'id' => 'fechafinal', 'required' => true]) !!} {{-- ID corregido --}}
                    </div>
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    {!! Form::submit('ACT. Acciones', ['class' => 'btn btn-primary', 'title' => 'ACT. Acciones', 'data-toggle' => 'tooltip', 'style' => 'background-color:' . $array_color['group_button_color'] . ';']) !!}
                    <input type="hidden" name="id_solicitud" value="{{$solicitud_edit->id}}">
                </div>

                <div class="col-md-6">
                    <h3>Datos de la Ubicación</h3>
                    <div style="text-align:left;">
                    {!! Form::label('estado_id', trans('message.solicitud_action.estado'), ['class' => 'control-label']) !!}<span class="required" style="color:red;">*</span>
                    {!! Form::select('estado_id', $estado, $solicitud_edit->estado_id, ['placeholder' => trans('message.solicitud_action.estado'), 'class' => 'form-control', 'id' => 'estado_id', 'disabled' => true]) !!}
                    </div>
                    <div style="text-align:left;">
                        {!! Form::label('municipio_id', trans('message.solicitud_action.municipio'), ['class' => 'control-label']) !!}<span
                            class="required" style="color:red;">*</span>
                        {!! Form::select('municipio_id', $municipio, $solicitud_edit->municipio_id, ['placeholder' => trans('message.solicitud_action.municipio'), 'class' => 'form-control', 'id' => 'municipio_id',  'disabled' => true]) !!}
                    </div>
                    <div style="text-align:left;">
                        {!! Form::label('parroquia_id', trans('message.solicitud_action.parroquia'), ['class' => 'control-label', 'id' => 'parroquia_id_label']) !!}<span
                            class="required" style="color:red;" id="parroquia_id_span">*</span>
                        {!! Form::select('parroquia_id', $parroquia, $solicitud_edit->parroquia_id, ['placeholder' => trans('message.solicitud_action.parroquia'), 'class' => 'form-control', 'id' => 'parroquia_id']) !!}
                    </div>

                    <div style="text-align:left;">
                        {!! Form::label('comuna_id_label', trans('message.solicitud_action.comuna'), ['class' => 'control-label', 'id' => 'comuna_id_label']) !!}<span
                            class="required" style="color:red;" id="comuna_id_span">*</span>
                        <select name="comuna_id" id="comuna_id" class="form-control">
                            @foreach($comuna as $key => $value)
                                <option value="{{ $value->id }}" @if(old('comuna_id', $solicitud_edit->comuna_id) == $value->id) selected @endif>{{ $value->codigo }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div style="text-align:left;">
                        {!! Form::label('comunidad_id', trans('message.solicitud_action.comunidad'), ['class' => 'control-label', 'id' => 'comunidad_id_label']) !!}<span
                            class="required" style="color:red;" id="comunidad_id_span">*</span>
                        <select name="comunidad_id" id="comunidad_id" class="form-control">
                            @foreach($comunidad as $key => $value)
                                <option value="{{ $value->id }}" @if(old('comunidad_id', $solicitud_edit->comunidad_id) == $value->id) selected @endif>{{ $value->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div style="text-align:left;">
    {!! Form::label('jefecomunidad', 'Jefe de Comunidad', ['class' => 'control-label', 'id' => 'jefecomunidad_label']) !!}
    <span class="required" style="color:red;" id="jefecomunidad_Span">*</span>
    <select name="jefecomunidad_id" id="jefecomunidad_id" class="form-control">
        <option value="">Seleccionar jefe de Comunidad</option>
        @foreach($jefecomunidad2 as $key => $value)
            @php
                $selected = false;
                if (isset($solicitud_edit) && $solicitud_edit->jefecomunidad_id == $value->id) {
                    $selected = true;
                } elseif (old('jefecomunidad_id') == $value->id) {
                    $selected = true;
                }
            @endphp
            <option value="{{ $value->id }}" @if($selected) selected @endif>
                {{ $value->Nombre_Jefe_Comunidad }}
            </option>
        @endforeach
    </select>
</div>

                    <div style="text-align:left;">
                        {!! Form::label('telefonoJEFE', 'Telefono de Jefe de Comunidad', ['class' => 'control-label', 'id' => 'telefonoJEFE_label']) !!}<span
                            class="required" style="color:red;" id="telefonoJEFE_span">*</span>
                            {!! Form::text('telefonoJEFE', $jefecomunidad[0]->Telefono_Jefe_Comunidad , ['placeholder' => 'Vocero', 'class' => 'form-control', 'id' => 'telefonoJEFE', 'DISABLED' => TRUE]) !!}
                    </div>

                    <div style="text-align:left;">
                        {!! Form::label('nombreUBCH', 'Nombre de UBCH', ['class' => 'control-label', 'id' => 'nombreUBCH_label']) !!}<span
                            class="required" style="color:red;" id="nombreUBCH_span">*</span>
                            {!! Form::text('nombreUBCH', $jefecomunidad2[0]->Nombre_Ubch, ['placeholder' => 'Vocero', 'class' => 'form-control', 'id' => 'nombreUBCH', 'DISABLED' => TRUE]) !!}

                    </div>
                    <div style="text-align:left;">
                        {!! Form::label('nomjefeUBCH', 'Nombre de Jefe UBCH', ['class' => 'control-label', 'id' => 'nomjefeUBCH_label']) !!}<span
                            class="required" style="color:red;" id="nomjefeUBCH_span">*</span>
                            {!! Form::text('nomjefeUBCH', $jefecomunidad2[0]->Nombre_Jefe_Ubch, ['placeholder' => 'Vocero', 'class' => 'form-control', 'id' => 'nomjefeUBCH', 'DISABLED' => TRUE]) !!}

                    </div>

                    <div style="text-align:left;">
                        {!! Form::label('teljefeUBCH', 'Telefono de Jefe UBCH', ['class' => 'control-label', 'id' => 'teljefeUBCH_label']) !!}<span
                            class="required" style="color:red;" id="teljefeUBCH_span">*</span>
                            {!! Form::text('teljefeUBCH', $jefecomunidad2[0]->Telefono_Jefe_Ubch, ['placeholder' => 'Vocero', 'class' => 'form-control', 'id' => 'teljefeUBCH', 'DISABLED' => TRUE]) !!}

                    </div>
                    <div class="form-group">
                        {!! Form::label('direccion', 'Dirección', ['class' => 'control-label']) !!}
                        {!! Form::text('direccion', $solicitud_edit->direccion, ['placeholder' => 'Dirección', 'class' => 'form-control', 'id' => 'direccion']) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('vocero', 'Vocero', ['class' => 'control-label']) !!}
                        {!! Form::text('vocero', $solicitud_edit->vocero, ['placeholder' => 'Vocero', 'class' => 'form-control', 'id' => 'vocero']) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('telefono', 'Vocero Telefono', ['class' => 'control-label']) !!}
                        {!! Form::text('telefono', $solicitud_edit->telefono, ['placeholder' => 'Telefono', 'class' => 'form-control', 'id' => 'telefono_user']) !!}
                    </div>
                    @if(Auth::user()->rols_id === 14 || Auth::user()->rols_id === 1 || Auth::user()->rols_id === 13)
                    <div class="form-group">
                        {!! Form::label('observacion', 'Observacion de la revisión', ['class' => 'control-label']) !!}
                        {!! Form::textarea('observacion', $solicitud_edit->observacion, ['placeholder' => 'Revision', 'class' => 'form-control', 'id' => 'observacion']) !!} {{-- ID corregido --}}
                    </div>
                    <div class="form-group">
                         {!! Form::label('state', 'Estado', ['class' => 'control-label']) !!}
                        <span class="required" style="color:red;">*</span>
                        {!! Form::select('state', $state, old('state', $solicitud_edit->state), ['class' => 'form-control', 'id' => 'state_select']) !!}
                    </div>
                    @endif
                    @if(Auth::user()->rols_id === 3 && $solicitud_edit->state === "RECHAZADO")
                    <div class="form-group">
                        {!! Form::label('observacion', 'Observacion de la revisión', ['class' => 'control-label']) !!}
                         <p class="form-control-static">{{ $solicitud_edit->observacion }}</p>
                         {!! Form::hidden('observacion', $solicitud_edit->observacion) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('state', 'Estado', ['class' => 'control-label']) !!}
                         <p class="form-control-static">{{ $state[$solicitud_edit->state] ?? '' }}</p>
                        {!! Form::hidden('state', $solicitud_edit->state) !!}
                    </div>
                    @endif

                     <div class="form-group">
                        {!! Form::label('asunto','Evidencia', ['class' => 'control-label']) !!}
                        <br>
                        @if ($solicitud_edit->evidencia_path)
                            <img src="{{ asset(env('API_URL').$solicitud_edit->evidencia_path) }}" alt="Evidencia" width="200">
                            <br>
                            <label for="image">Reemplazar imagen:</label>
                        @endif
                        <input type="file" name="image" id="image">
                    </div>
                    <div class="form-group">
                        {!! Form::label('asunto2','Evidencia2', ['class' => 'control-label']) !!}
                        <br>
                        @if ($solicitud_edit->evidencia_path2)
                            <img src="{{ asset(env('API_URL').$solicitud_edit->evidencia_path2) }}" alt="Evidencia2" width="200">
                            <br>
                            <label for="image">Reemplazar imagen:</label>
                        @endif
                        <input type="file" name="image2" id="image2">
                    </div>

                </div> {{-- End second column --}}
                {!!  Form::close() !!}


            </div> {{-- End row --}}


        </div>
    </div>
</div>
@endsection

@section('script_datatable')
<script src="{{ url('/js_users/js_users.min.js') }}" type="text/javascript"></script>
<script type="text/javascript">
    $(document).ready(function () {
        //Restriccion de caracteres
        $("#telefono_user").on("input", function() {
            let inputValue = $(this).val();
            let onlyLetters = inputValue.replace(/[^a-zA-Z\s]/g, '');
            $(this).val(onlyLetters);
        });

        // Inicializar selects (si están vacíos)
        if(jefecomunidadID == ''){
            $("#jefecomunidad_id").html('<option value="">Seleccione Jefe de Comunidad</option>');
        }

        // Ocultar/Mostrar campos según Municipio
        function toggleCamposMunicipio() {
            var municipio = $('#municipio_id').val();  // Obtener valor actual (podría ser el del hidden)
            if (!municipio) {
                municipio = idmunicipioJS;  // Si no hay valor en el select, usar el valor inicial de JS
            }
            var mostrar = (municipio != 2); // Mostrar si no es 2

            $("#parroquia_id_label, #parroquia_id_span, #parroquia_id, #comuna_id_label, #comuna_id_span, #comuna_id, #comunidad_id_label, #comunidad_id_span, #comunidad_id, #jefecomunidad_label, #jefecomunidad_Span, #jefecomunidad_id, #telefonoJEFE_label, #telefonoJEFE_span, #telefonoJEFE, #nombreUBCH_label, #nombreUBCH_span, #nombreUBCH, #nomjefeUBCH_label, #nomjefeUBCH_span, #nomjefeUBCH, #teljefeUBCH_label, #teljefeUBCH_span, #teljefeUBCH").toggle(mostrar);
        }
		//Ejecutamos las funciones
        toggleCamposMunicipio(); // Ejecutar al inicio

        $('#direccion_id').change(function () {
            var direccion = $(this).val();
            $.ajax({
                url: "{{ route('getcoordxdireccion') }}",
                type: "GET",
                data: { direccion: direccion },
                success: function (data) {
                    $("#coordinacion_id").empty().append('<option value="">Coordinacion</option>');
                    $.each(data, function (key, value) {
                        $("#coordinacion_id").append('<option value="' + key + '">' + value+ '</option>');
                    });
                },
                error: function () {
                    alert("Error al cargar las coordinaciones.");
                }
            });
        });
        $('#municipio_id').change(toggleCamposMunicipio);  // Ejecutar al cambiar municipio

        $('#parroquia_id').change(function () {
            var parroquia = $(this).val();
            $.ajax({
                url: "{{ route('getComunas') }}",
                type: "GET",
                data: { parroquia: parroquia },
                success: function (data) {
                    $("#comuna_id").empty().append('<option value="">COMUNA</option>');
                    $.each(data, function (key, value) {
                        $("#comuna_id").append('<option value="' + value.id + '">' + value.codigo + '</option>');
                    });
                },
                error: function () {
                    alert("Error al cargar las comunas.");
                }
            });
        });


        $('#comuna_id').change(function () {
            var comuna = $(this).val();
            $.ajax({
                url: "{{ route('getComunidad2') }}",
                type: "GET",
                data: { comuna: comuna },
                success: function (data) {
                    $("#comunidad_id").empty().append('<option value="">Seleccione Comunidad</option>');
                    $.each(data, function (key, value) {
                        $("#comunidad_id").append('<option value="' + value.id + '">' + value.nombre + '</option>');
                    });
                },
                error: function () {
                    alert("Error al cargar la comunidad.");
                }
            });
        });

        $('#comunidad_id').change(function () {
            var comunidadId = $(this).val();

            $.ajax({
                url: "{{ route('getJefeComunidad') }}",
                type: "GET",
                data: { comunidad_id: comunidadId },
                success: function (data) {
                  if (data.length > 0) {
                        //Actualiza el p
                        $("#jefecomunidad_id_p").text(data[0].Nombre_Jefe_Comunidad);
                        //Actualiza el valor del hidden.
                         $("#jefecomunidad_id").val(data[0].id);
                    } else {
                        $("#jefecomunidad_id_p").text("Seleccione...");
                        $("#jefecomunidad_id").val(""); //Limpia el hidden
                    }
                },
                error: function () {
                    alert("Error al cargar los jefes de comunidad.");
                }
            });

            //Cargar los otros datos relacionados.
            $.ajax({
                url: "{{ route('getJefeComunidad2') }}",
                type: "GET",
                data: { jefecomunidadID: comunidadId }, // Usar directamente comunidadId
                success: function (data) {
                    if (data.length > 0) {
                        var value = data[0];
                        $("#telefonoJEFE_p").text(value.Telefono_Jefe_Comunidad);
                        $("#nombreUBCH_p").text(value.Nombre_Ubch); //  Ubch, no Jefe_Ubch
                        $("#nomjefeUBCH_p").text(value.Nombre_Jefe_Ubch);
                        $("#teljefeUBCH_p").text(value.Telefono_Jefe_Ubch);

                         // Actualizar los campos ocultos
                        $("#telefonoJEFE").val(value.Telefono_Jefe_Comunidad);
                        $("#nombreUBCH").val(value.Nombre_Ubch);
                        $("#nomjefeUBCH").val(value.Nombre_Jefe_Ubch);
                        $("#teljefeUBCH").val(value.Telefono_Jefe_Ubch);

                    } else {
                        $("#telefonoJEFE_p, #nombreUBCH_p, #nomjefeUBCH_p, #teljefeUBCH_p").text('');
                         $("#telefonoJEFE, #nombreUBCH, #nomjefeUBCH, #teljefeUBCH").val('');
                    }
                },
                error: function () {
                    alert("Error al cargar los datos.");
                }
            });
        });

        // No necesitas el evento $('#jefecomunidad_id').change() porque ya actualizamos todo en comunidad_id

    });
</script>
@endsection
