<!-- Left side column. contains the logo and sidebar -->
<aside class="main-sidebar" style="background-color: {{$array_color['menu_color']}};">

    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">

        <!-- Sidebar user panel (optional) -->
        @if (! Auth::guest())
            <div class="user-panel">
                <div class="pull-left image">
                  <!--  <img src="{{-- Gravatar::get($user->email) --}}" class="img-circle" alt="User Image" /> -->
                    @if (Auth::user()->avatar == 'default.jpg' || is_null(Auth::user()->avatar))
                        <img src="{{ url('/storage/avatars/default.jpg') }}" class="img-circle" alt="User Image"/>
                    @else
                        <img src="{{ url('/storage/avatars/'.Auth::user()->avatar) }}" class="img-circle" alt="User Image">
                    @endif
                </div>
                <div class="pull-left info">
                    <p style="overflow: hidden;text-overflow: ellipsis;max-width: 160px;" data-toggle="tooltip" title="{{ Auth::user()->name }}">{{ Auth::user()->name }}</p>
                    <!-- Status -->
                    <a href="#"><i class="fa fa-circle text-success"></i> {{ trans('message.online') }}</a>
                </div>
            </div>
        @endif

        <!-- search form (Optional) -->
        <form action="#" method="get" class="sidebar-form">
            <div class="input-group">
                <input type="text" name="q" class="form-control" placeholder="{{ trans('message.search') }}..."/>
              <span class="input-group-btn">
                <button type='submit' name='search' id='search-btn' class="btn btn-flat"><i class="fa fa-search"></i></button>
              </span>
            </div>
        </form>
        <!-- /.search form -->

        <!-- Sidebar Menu -->
        <ul class="sidebar-menu" data-widget="tree">
            <li class="header">{{ trans('message.header') }}</li>
            <!-- Optionally, you can add icons to the links -->
            <li><a href="{{ url('/dashboard') }}"><i class="fa fa-home" aria-hidden="true"></i><span>{{ trans('message.dashboard') }}</span></a></li>
            <!-- <li><a href="{{ url('/bandeja') }}"><i class='fa fa-link'></i> <span>{{'Bandeja'}}</span></a></li>             -->
            <!-- <li><a href="{{ url('/solicitud/buscarsolicitud') }}"><i class='fa fa-link'></i> <span>{{'Buscar Solicitudes'}}</span></a></li> -->
            <!-- Optionally, you can add icons to the links -->
            @if(Auth::user()->rols_id === 3 || Auth::user()->rols_id == 1 | Auth::user()->rols_id == 14)
            <li><a href="{{ url('/acciones/create') }}"><i class="fa fa-pencil-square-o" aria-hidden="true"></i><span>Crear Acciones</span></a></li>
            <li><a href="{{ url('/acciones/list') }}"><i class="fa fa-list-alt" aria-hidden="true"></i></i><span>Listado Acciones</span></a></li>
            <li><a href="{{ url('/actividades/create') }}"><i class="fa fa-pencil-square-o" aria-hidden="true"></i><span>Crear Actividades</span></a></li>
            <li><a href="{{ url('/actividades/list') }}"><i class="fa fa-list-alt" aria-hidden="true"></i></i><span>Listado Actividades</span></a></li>
            @endif
            @if(Auth::user()->rols_id === 13)
                 <li><a href="{{ url('/acciones/list') }}"><i class="fa fa-list-alt" aria-hidden="true"></i></i><span>Listado Acciones</span></a></li>
                  <li><a href="{{ url('/actividades/list') }}"><i class="fa fa-list-alt" aria-hidden="true"></i></i><span>Listado Actividades</span></a></li>

            @endif
            <li class="treeview">
                <a href="#"><i class="fa fa-print" aria-hidden="true"></i> <span>Reportes</span><i class="fa fa-angle-left pull-right"></i></a>
                <ul class="treeview-menu">

                <li><a href="{{ url('/reporte/accionesgobierno2024') }}"><i class='fa fa-link'></i> <span>Acciones 2024-2025</span></a></li>
                <li><a href="{{ url('/reporte/actividadesgobierno2024') }}"><i class='fa fa-link'></i> <span>Actividades 2024-2025</span></a></li>
                <li><a href="{{ url('/reporte/accionesgobiernototales') }}"><i class='fa fa-link'></i> <span>Reporte Cuantificado Direcciones</span></a></li>
                <li><a href="{{ url('/reporte/accionesgobiernototales2') }}"><i class='fa fa-link'></i> <span>Reporte Cuantificado Institutos</span></a></li>
                <li><a href="{{ url('/reporte/tomosview') }}"><i class='fa fa-link'></i> <span>Reporte Tomos</span></a></li>

                @if(Auth::user()->rols_id === 13 || Auth::user()->rols_id === 1)
                    <li><a href="{{ url('/importar/importar') }}"><i class='fa fa-link'></i> <span>Importar</span></a></li>
                @endif
            </li>



        </ul>

        @if(Auth::user()->rols_id === 13 || Auth::user()->rols_id === 1)
                    <li><a href="{{ url('/fecha/setfecha') }}"><i class='fa fa-link'></i> <span>Fecha</span></a></li>
                @endif
            </li>
            @if(Auth::user()->rols_id === 1 )
            <li><a href="{{ url('/users') }}"><i class='fa fa-link'></i> <span>{{ trans('message.users') }}</span></a></li>
            <li><a href="{{ url('/notificaciones') }}"><i class='fa fa-link'></i> <span>{{ trans('message.menu_notificaciones') }}</span></a></li>
            @endif

            @if(Auth::user()->rols_id === 1 )
            <li><a href="{{ url('/notificaciones') }}"><i class='fa fa-link'></i> <span>{{ trans('message.menu_notificaciones') }}</span></a></li>
            <li class="treeview">
                <a href="#"><i class='fa fa-link'></i> <span>{{ trans('message.menu_seguridad') }}</span> <i class="fa fa-angle-left pull-right"></i></a>
                <ul class="treeview-menu">
                    <li><a href="{{ url('/rols') }}">{{ trans('message.menu_rol') }}</a></li>
                    <li><a href="{{ url('/modulos') }}">{{ trans('message.menu_modulo') }}</a></li>
                    <li><a href="{{ url('/permisos') }}">{{ trans('message.menu_permiso') }}</a></li>
                </ul>
            </li>
            @endif
            <!-- <li><a href="{{ url('/users/color_view') }}"><i class='fa fa-link'></i> <span>{{ trans('message.menu_color') }}</span></a></li> -->
         </ul><!-- /.sidebar-menu -->
    </section>
    <!-- /.sidebar -->
</aside>
