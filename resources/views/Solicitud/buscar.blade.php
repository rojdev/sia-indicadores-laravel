@extends('adminlte::layouts.app')

@section('css_database')
    @include('adminlte::layouts.partials.link')
@endsection

@section('htmlheader_title')
    {{ trans('adminlte_lang::message.home') }}
@endsection

@section('contentheader_title')  


@endsection

@section('link_css_datatable')
    <link href="{{ url ('/css_datatable/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ url ('/css_datatable/dataTables.bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ url ('/css_datatable/responsive.bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ url ('/css_datatable/buttons.dataTables.min.css') }}" rel="stylesheet">
@endsection    
@section('main-content')
@component('components.alert_msg',['tipo_alert'=>$tipo_alert])
    Componentes para los mensajes de Alert, No Eliminar
@endcomponent
<h2 style="margin: -25px 0px -25px 0px"><img src="{{ url('/images/icons/logoSIA.png') }}" alt="logo" height="100px" >Busqueda de Solicitudes</h2>

<main>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-6 offset-md-3">
            <div class="input-group"> 
                <input type="text" class="form-control"  
 id="searchInput" placeholder="Cédula o Número de Solicitud">
                <div class="input-group-append">
                    <button class="btn btn-primary" type="button" id="searchButton">Buscar</button>
                </div>
            </div>
        </div>
    </div>
    <div id="cardsContainer" class="row"></div>
    <div id="seguimientoCardContainer" class="row mt-4" style="display:none;"> 
    </div>

    <div id="noResultsMessage" class="text-center mt-3" style="display: none;">
        No se encontraron resultados.
    </div>

    <div id="initialMessage" class="text-center mt-3">
        Ingrese un número de cédula o solicitud para buscar.
    </div>
</div>

<div class="modal fade" id="seguimientoModal" tabindex="-1" role="dialog" aria-labelledby="seguimientoModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="seguimientoModalLabel">Seguimiento de la Solicitud</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="seguimientoModalBody">

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>


</main>  


@endsection
@section('script_datatable')
<script src="{{ url ('/js_datatable/jquery.dataTables.min.js') }}" type="text/javascript"></script>
<script src="{{ url ('/js_datatable/dataTables.responsive.min.js') }}" type="text/javascript"></script>
<script src="{{ url ('/js_datatable/responsive.bootstrap.min.js') }}" type="text/javascript"></script>
<script src="{{ url ('/js_datatable/dataTables.buttons.min.js') }}" type="text/javascript"></script>
<script src="{{ url ('/js_delete/sweetalert.min.js') }}" type="text/javascript"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>   

<script>
const  
 baseUrl = 'http://156.235.91.67:4000'; 

 $(document).ready(function() {
    // Attach click event to the search button
    $('#searchButton').on('click', function() {
        var searchTerm = $('#searchInput').val(); 
        $('#seguimientoCardContainer').empty().hide();
        fetchData(searchTerm); 
    });

    function fetchData(searchTerm) {
        $.ajax({
            url: '/solicitud/buscargeneral', 
            method: 'GET',
            data: { params: searchTerm },
            success: function(response) {
                solicitudes = response; 
                updateCards(solicitudes);
            },
            error: function(error) {
                console.error('Error en la solicitud AJAX:', error);
                $('#cardsContainer').empty();
                $('#noResultsMessage').show();
                $('#initialMessage').hide();
            }
        });
    }

    function updateCards(solicitudes) {
        $('#cardsContainer').empty();
        $('#noResultsMessage').hide();
        $('#initialMessage').hide();
        var cardHTML = '';

        if (typeof solicitudes !== 'undefined' && solicitudes.length === 0) {
            if ($('#searchInput').val() !== '') { // Check if search term is not empty
                $('#noResultsMessage').show();
            } else {
                $('#initialMessage').show();
            }
            return;
        }

        for (var i = 0; i < solicitudes.length; i++) {
            var solicitud = solicitudes[i];

            var beneficiarios = null; 
            if (solicitud.beneficiario && solicitud.beneficiario.trim() !== "") {
            try {
                beneficiarios = JSON.parse(solicitud.beneficiario);
            } catch (error) {
                    console.error('Error parsing beneficiarios:', error);
                }
            }

             cardHTML = `
                <div class="col-md-4 mb-3" style="margin-top: 20px"> 
                    <div class="card">
                        <div class="card-header"><span style="font-weight: bold;">Dirección: ${solicitud.direccionnombre}</span></div>
                        <div class="card-header"><span style="font-weight: bold;">Solicitud: #${solicitud.id}</span></div>
                        <div class="card-body">
                            <h5 class="card-title"><span style="font-weight: bold;">Solicitante: ${solicitud.solicitante}</span></h5>
                            <p class="card-text"><span style="font-weight: bold;">Estatus: ${solicitud.nombrestatus} ${getIconForStatus(solicitud.nombrestatus)}</span></p>
                            <p class="card-text">Fecha: ${formatDate(solicitud.fecha)}</p>
                            <p class="card-text">Tipo Solicitud: ${solicitud.nombretipo} ${getIconForTipoSolicitud(solicitud.nombretipo)}</p>`;

            if (beneficiarios && beneficiarios.length > 0) {
                cardHTML += `<hr>
                             <h6 class="card-subtitle mb-2 text-muted">Beneficiario(s)</h6>`;
                for (var j = 0; j < beneficiarios.length; j++) {
                    var beneficiario = beneficiarios[j];
                    cardHTML += `
                        <p class="card-text">Beneficiario: ${beneficiario.nombre}</p>
                        <p class="card-text">Cédula: ${beneficiario.cedula}</p> 
                        <p class="card-text">Solicita: <span class="${getTextColorForStatus(solicitud.nombrestatus)}">${beneficiario.solicita}</span></p>
                    `;
                }
            }

            cardHTML += `       <p class="card-text">Comuna: ${solicitud.comuna}</p>
                            <p class="card-text">Comunidad: ${solicitud.comunidad}</p> 
                            <button class="btn btn-primary ver-solicitud-btn" data-solicitud-id="${solicitud.id}">Ver</button> 
                        </div>
                    </div>
                </div>
            `;

            $('#cardsContainer').append(cardHTML);
        }

        $('.ver-solicitud-btn').click(function() {
            var solicitudId = $(this).data('solicitud-id');
            verSolicitud(solicitudId);
        });
    }

    function formatDate(fechaISO) {
    const fecha = new Date(fechaISO);
    const dia = fecha.getDate().toString().padStart(2, '0');
    const mes = (fecha.getMonth() + 1).toString().padStart(2, '0');
    const año = fecha.getFullYear();
    const hora = fecha.getHours().toString().padStart(2, '0');
    const minutos = fecha.getMinutes().toString().padStart(2, '0');  

    return `Fecha: ${dia}-${mes}-${año} ${hora}:${minutos}`; 
}

    function getIconForStatus(status) {
        switch (status) {
            case 'FINALIZADA': return '<i class="fas fa-check text-green"></i>';
            case 'EN ANALISIS': return '<i class="fas fa-magnifying-glass text-blue"></i>';
            case 'REGISTRADA': return '<i class="fas fa-paperclip text-yellow"></i>';
            case 'RECHAZADA': 
            case 'ANULADA': return '<i class="fas fa-xmark text-red"></i>';
            default: return ''; 
        }
    }
    function getIconForTipoSolicitud(tipo) {
        switch (tipo) {
            case 'MEDICINA': return '<i class="fas fa-pills"></i>';
            case 'LABORATORIO': return '<i class="fas fa-flask"></i>';
            case 'ESTUDIO': return '<i class="fas fa-clipboard"></i>';
            case 'INSUMOS': return '<i class="fas fa-stethoscope"></i>';
            case 'CONSULTAS': return '<i class="fas fa-hand-holding-heart"></i>';
            case 'DONACIONES Y AYUDA ECONOMICA': return '<i class="fas fa-money-bill"></i>';
            case 'AYUDAS TECNICAS': return '<i class="fas fa-hand"></i>';
            case 'CIRUGIAS': return '<i class="fas fa-pen-fancy"></i>';
            case 'OFTAMOLOGIA': return '<i class="fas fa-glasses"></i>';
            case 'VISITA SOCIAL': return '<i class="fas fa-hands-holding-child"></i>';
            case 'MATERIALES':
            case 'DOTACION': return '<i class="fas fa-boxes-stacked"></i>';
            case 'JORNADAS': return '<i class="fas fa-person-shelter"></i>';
            case 'ALTO COSTO': return '<i class="fas fa-money-check-dollar"></i>';
            case 'HURNAS': 
            case 'FOSAS': return '<i class="fas fa-rainbow"></i>'; 
            case 'APOYO LOGISTICO': return '<i class="fas fa-truck"></i>';
            case 'OTROS': return '<i class="fas fa-question"></i>';
            default: return ''; 
        }
    }

    function getTextColorForStatus(status) {
        switch (status) {
            case 'FINALIZADA': return 'text-green';
            case 'EN ANALISIS': return 'text-blue';
            case 'REGISTRADA': return 'text-yellow';
            case 'RECHAZADA': return 'text-red';
            case 'ANULADA': return 'text-blue'; 
            default: return ''; 
        }
    }

    function verSolicitud(id) {
    $.ajax({
        url: '/seguimiento/list2', 
        method: 'GET',
        data: { params: id },
        success: function(response) {
            // Accede al primer elemento del array de respuesta
            // if (response.length > 0) {
            //     mostrarSeguimiento(response[0]); 
            // } else {
            //     // Manejar el caso donde no se encontraron resultados
            //     console.error('No se encontraron datos de seguimiento para la solicitud:', id);
            //     // Puedes mostrar un mensaje de error al usuario aquí si lo deseas
            // }
            mostrarSeguimiento(response[0]); 
        },
        error: function(error) {
            console.error('Error al obtener el seguimiento:', error);
            // Manejar el error de alguna manera (mostrar un mensaje, etc.)
        }
    });
}

    function mostrarSeguimiento(data) {
    var seguimientoHTML = '';
    if (data && data.Seguimiento) {
        var seguimientoItems = JSON.parse(data.Seguimiento).sort(function(a, b) {
            return new Date(b.fecha) - new Date(a.fecha); 
        });
        
        seguimientoHTML += `
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Seguimiento de la Solicitud ${data.NumeroSolicitud}</h5> 
                    </div>
                    <div class="card-body">
            `;

        for (var i = 0; i < seguimientoItems.length; i++) {
            var item = seguimientoItems[i];

            seguimientoHTML += `
                        <div class="seguimiento-card">
                            <div class="section">
                                <div class="seccion-1" style="background-color: black; color: white; width: 100%; text-align: center; height: 20px;">
                                    <p class="section-title"><span class="negrita">#</span>${item.item}</p>
                                </div>
                            </div>
                            <div class="section">
                                <div class="seccion-1">
                                    <p class="section-title"><span class="negrita">Fecha y Hora: </span>${formatDate(item.fecha)}</p>
                                    <p class="section-title"><span class="negrita">Descripcion: </span>${item.asunto || 'N/A'}</p>
                                </div>
                                <div class="seccion-1">
                                    <p class="section-title"><span class="negrita">Imagen: </span></p>
                                    ${item.imagen ? `<a href="${baseUrl}/${item.imagen}" target="_blank">
                                        <img src="${baseUrl}/${item.imagen}" style="height: 100px; max-width: 150px" />
                                    </a>` : 'No hay imagen disponible'} 
                                </div>
                            </div>
                        </div>
            `;
        }

        seguimientoHTML += `
                    </div>
                </div>
            </div>
        `;
    } else {
        seguimientoHTML = `
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Seguimiento de la Solicitud</h5>
                    </div>
                    <div class="card-body">
                        Aún no se ha realizado seguimiento a esta solicitud.
                    </div>
                </div>
            </div>
        `;
    }

    $('#seguimientoCardContainer').html(seguimientoHTML);
    $('#seguimientoCardContainer').show(); // Show the card container
}
});
</script>
<style>
section.content{
        background-image: url("{{ url('/images/siabuscar.png') }}");
        background-size: 100%;
}
</style>
@endsection  
