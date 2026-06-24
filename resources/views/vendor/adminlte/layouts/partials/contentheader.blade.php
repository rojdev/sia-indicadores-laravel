<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        @yield('contentheader_title', '')        
        <small>@yield('contentheader_description')</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="{{route('dashboard.dashboard')}}"><i class="fa fa-dashboard active"></i> Inicio</a></li>
       <!-- <li class="active">Dashboard</li> -->
    </ol>
</section>