<section id="intro" class="clearfix">
  <div class="container">    
     
    <div style="text-align:center;" class="fondo">
      <h3>Proyecto contemplado en el Plan de Gobierno del Alcalde Rafael Torrealba. <br> En Periodo 2021-2025
        La Transformación Digital del Municipio
      </h3>
    </div>        
    
  </div>
  
</section>
<!-- no borrar la línea </div> de abajo, va con otra div  -->
</div>
    @include('sweetalert::alert')
    <script src="{{ url ('/js_datatable/jquery-3.5.1.js') }}" type="text/javascript"></script>
    <script src="{{ url ('/js_bootstrap/bootstrap.min.js') }}" type="text/javascript"></script>       
</body>
</html>
<style>
  h3{
    margin-top: 540px;
  }
  body{
    background-image: url("{{ url('/images/banner.jpg') }}");
    background-repeat: no-repeat;
    background-size: 100% 100%;
    min-height: 100vh;
  }
  @media screen and (max-width: 600px){
    h3{
    margin-top: 250px;
    }
    section{
        background-image: url("{{ url('/images/bannermobile.jpg') }}");
        background-repeat: no-repeat;
        background-size: 100% 100%;
        min-height: 100vh;
    }
    
  }
</style>