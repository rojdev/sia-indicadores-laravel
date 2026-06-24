<?php
/**
* Realizado por @author Tarsicio Carrizales Agosto 2021
* Correo: telecom.com.ve@gmail.com
*/
use App\Http\Controllers\NotificarController;
use Illuminate\Support\Facades\Route;
use App\Models\User\User;
use App\Http\Controllers\Auth\LoginController;
//use App\Http\Controllers\User\UserController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Auth::routes();

Route::get('idioma/{lang}', 'Lenguaje\LenguajeController@cambioLenguaje')
            ->name('idioma.cambioLenguaje');

Route::get('/deny', function () {
    return view('deny');
});

Route::get('/check_your_mail', function () {
    return view('adminlte::mail.check_your_mail');
});

Route::get('/', function () {
    return view('welcome');
});

/**
 * Se creó un middleware('permiso:user,view') el cual verifica antes de
 * acceder al recurso solicitado si el usuario tiene permiso de ver dicho recurso.
 * Este middleware es parte de la seguridad de la aplicación en conjunto cn los permisos
 * asignados a cada rol.
 * /
 // *********************************************************************************************************
    /*
    * Grupo Middleware para Autenticar y verifcar que tiene Permiso asociado a su Rol
    */
// *********************************************************************************************************
Route::group(['middleware' => 'auth'], function () {

    Route::get('/mail', 'Mail\MailController@index')->name('mail.index');
    Route::get('/homework', 'Tarea\TareaController@index')->name('homework.index');
    // *********************************************************************************************************
    /*
    * Rutas de Usuarios, para todas las operaciones, con el Middleware (permiso) Integrado, para cada caso.
    */
    Route::get('/notificaciones', 'Notification\NotificationController@index')->name('notificaciones.index');
    Route::get('/notificaciones/list', 'Notification\NotificationController@getNotifications')->name('notificaciones.list');
    Route::get('/notificaciones/read/{id}', 'Notification\NotificationController@setNotifications')->name('notificaciones.setNotifications');
    /*
    * Fin de las Rutas de Usuarios, para todas las operaciones
    */
    // *********************************************************************************************************
    // *********************************************************************************************************
    /*
    * Rutas de Usuarios, para todas las operaciones, con el Middleware (permiso) Integrado, para cada caso.
    */
    // Route::post('/login', 'Auth\LoginController@login')->name('login');
    // Route::post('/login2', 'Auth\LoginController@login2')->name('login2');
    Route::get('/users', 'User\UserController@index')->name('users.index')->middleware('permiso:user,view');
    Route::get('/users/create', 'User\UserController@create')->name('users.create')->middleware('permiso:user,add');
    Route::post('/users', 'User\UserController@store')->name('users.store')->middleware('permiso:user,add');
    Route::get('/users/{user}/view', 'User\UserController@view')->name('users.view')->middleware('permiso:user,view');
    Route::get('/users/{user}/edit', 'User\UserController@edit')->name('users.edit')->middleware('permiso:user,edit');
    Route::post('/users/{user}', 'User\UserController@update')->name('users.update')->middleware('permiso:user,update');
    Route::delete('/users/{user}', 'User\UserController@destroy')->name('users.destroy')->middleware('permiso:user,delete');
    Route::get('/users/{user}/delete', 'User\UserController@destroy')->name('users.destroy')->middleware('permiso:user,delete');
    Route::get('/users/list', 'User\UserController@getUsers')->name('users.list')->middleware('permiso:user,view');
    Route::get('/users/profile', 'User\UserController@profile')->name('users.profile');
    Route::post('/users/profile/{id}', 'User\UserController@update_avatar')->name('users.profile')->middleware('permiso:user,update');
    Route::get('/users/usuarioRol', 'User\UserController@usuarioRol')->name('users.usuarioRol');
    Route::get('/users/notificationsUser', 'User\UserController@notificationsUser')->name('users.notificationsUser');
    Route::get('/users/print', 'User\UserController@usersPrint')->name('users.usersPrint')->middleware('permiso:user,print');
    Route::get('/users/color_view', 'User\UserController@colorView')->name('users.colorView');
    Route::get('/users/color_change', 'User\UserController@colorChange')->name('users.colorChange');
    /*
    * Fin de las Rutas de Usuarios, para todas las operaciones
    */
    // *********************************************************************************************************
    // *********************************************************************************************************
    /*
    * Rutas de Permiso, para todas las operaciones, con el Middleware (permiso) Integrado, para cada caso.
    */
    Route::get('/permisos', 'Permiso\PermisoController@index')
    ->name('permisos.index')->middleware('permiso:permiso,view');

    Route::get('/permisos/list', 'Permiso\PermisoController@getModulos')
    ->name('permisos.list')->middleware('permiso:permiso,view');

    Route::get('/permisos/create', 'Permiso\PermisoController@create')
    ->name('permisos.create')->middleware('permiso:permiso,add');

    Route::post('/permisos','Permiso\PermisoController@store')
    ->name('permisos.store')->middleware('permiso:permiso,add');

    Route::post('/permisos/{permiso}','Permiso\PermisoController@show')
    ->name('permisos.show')->middleware('permiso:permiso,view');

    Route::get('/permisos/{permiso}/edit','Permiso\PermisoController@edit')
    ->name('permisos.edit')->middleware('permiso:permiso,edit');
// Parte importante para Actualizar los cambios realizados a los permisos .//////////////////
    Route::post('/permisos/{id}/{accion}/{allow_deny}','Permiso\PermisoController@update')
    ->name('permisos.update')->middleware('permiso:permiso,update');

    Route::post('/permisos/{id}/{allow_deny}','Permiso\PermisoController@updateAllPermisos')
    ->name('permisos.updateAllPermisos');

    Route::get('/permisos/{modulo_id}/{rol_id}','Permiso\PermisoController@getPermisos')
    ->name('permisos.getPermisos')->middleware('permiso:permiso,view');
// Parte importante para Actualizar los cambios realizados a los permisos .//////////////////
    Route::post('/permisos/{permiso}/delete','Permiso\PermisoController@destroy')
    ->name('permisos.destroy')->middleware('permiso:permiso,delete');

    Route::get('/permisos/print', 'Permiso\PermisoController@permisoPrint')
    ->name('permisos.permisoPrint')->middleware('permiso:permiso,print');
    /*
    * Fin de las Rutas de Permiso, para todas las operaciones
    */
    // *********************************************************************************************************
    // *********************************************************************************************************
    /*
    * Rutas de Roles, para todas las operaciones, con el Middleware (permiso) Integrado, para cada caso.
    */
    Route::get('/rols', 'Rol\RolController@index')->name('rols.index')->middleware('permiso:rol,view');
    Route::get('/rols/create', 'Rol\RolController@create')->name('rols.create')->middleware('permiso:rol,add');
    Route::post('/rols', 'Rol\RolController@store')->name('rols.store')->middleware('permiso:rol,add');
    Route::get('/rols/{rol}/show', 'Rol\RolController@show')->name('rols.show')->middleware('permiso:rol,view');
    Route::get('/rols/{rol}/edit', 'Rol\RolController@edit')->name('rols.edit')->middleware('permiso:rol,edit');
    Route::post('/rols/{rol}', 'Rol\RolController@update')->name('rols.update')->middleware('permiso:rol,update');
    Route::get('/rols/{rol}/delete', 'Rol\RolController@destroy')->name('rols.destroy')->middleware('permiso:rol,delete');
    Route::get('/rols/list', 'Rol\RolController@getRols')->name('rols.list')->middleware('permiso:rol,view');
    Route::get('/rols/print', 'Rol\RolController@rolsPrint')->name('rols.rolsPrint')->middleware('permiso:rol,print');
    /*
    * Fin de las Rutas de Usuarios, para todas las operaciones
    */
    // *********************************************************************************************************
    // *********************************************************************************************************
    /*
    * Rutas de Roles, para todas las operaciones, con el Middleware (permiso) Integrado, para cada caso.
    */
    Route::get('/modulos', 'Modulo\ModuloController@index')->name('modulos.index')->middleware('permiso:modulo,view');
    Route::get('/modulos/create', 'Modulo\ModuloController@create')->name('modulos.create')->middleware('permiso:modulo,add');
    Route::post('/modulos', 'Modulo\ModuloController@store')->name('modulos.store')->middleware('permiso:modulo,add');
    Route::get('/modulos/{modulo}/show', 'Modulo\ModuloController@show')->name('modulos.show')->middleware('permiso:modulo,view');
    Route::get('/modulos/{modulo}/edit', 'Modulo\ModuloController@edit')->name('modulos.edit')->middleware('permiso:modulo,edit');
    Route::post('/modulos/{modulo}', 'Modulo\ModuloController@update')->name('modulos.update')->middleware('permiso:modulo,update');
    Route::get('/modulos/{modulo}/delete', 'Modulo\ModuloController@destroy')->name('modulos.destroy')->middleware('permiso:modulo,delete');
    Route::get('/modulos/list', 'Modulo\ModuloController@getModulos')->name('modulos.list')->middleware('permiso:modulo,view');
    Route::get('/modulos/print', 'Modulo\ModuloController@modulosPrint')->name('modulos.modulosPrint')->middleware('permiso:modulo,print');
    /*
    * Fin de las Rutas de Usuarios, para todas las operaciones
    */
    // *********************************************************************************************************
    // *********************************************************************************************************

    Route::get('/dashboard', 'HomeController@dashboard')->name('dashboard.dashboard');

});
// *********************************************************************************************************
    /*
    * FIN del Grupo Middleware para Autenticar y verifcar que tiene Permiso asociado a su Roll
    */
// *********************************************************************************************************

// La presente Ruta se encarga de validar los datos enviados al Correo de Usuario que se Registró
Route::get('register/confirm/{confirmation_code}', 'Auth\RegisterController@confirm')->name('auth.confirm');
// *********************************************************************************************************

/* Bandeja de entrada */
Route::get('/bandeja', 'Bandeja\BandejaController@index')->name('bandeja.index');
Route::get('/bandeja/table', 'Solicitud\SolicitudController@getSolicitudesBandeja')->name('bandeja.list')->middleware('permiso:solicitud,view, edit');

/* GetData */
Route::get('/getcoordxdireccion', 'Coordinacion\CoordinacionController@getcoordxdireccion')->name('getcoordxdireccion');


/* REPORTES */
Route::get('/reporte/accionesgobierno', 'Reporte\ReporteController@viewReporteAccionesdeGobierno')->name('reporte.accionesview');
Route::get('/reporte/accionesgobierno2024', 'Reporte\ReporteController@viewReporteAccionesdeGobierno2024')->name('reporte.accionesview2024');
Route::get('/reporte/actividades2024excell', 'Reporte\ReporteController@vactividades2024excell')->name('reporte.actividades2024excell');

Route::get('/reporte/actividadesgobierno', 'Reporte\ReporteController@viewReporteActividadesdeGobierno')->name('reporte.actividadesview');
Route::get('/reporte/actividadesgobierno2024', 'Reporte\ReporteController@viewReporteActividadesdeGobierno2024')->name('reporte.actividadesview2024');
Route::get('/reporte/getdataacciones', 'Reporte\ReporteController@getdataacciones')->name('reporte.getdataacciones');
Route::get('/reporte/getdataaccionesnew', 'Reporte\ReporteController@getdataaccionesnew')->name('reporte.getdataaccionesnew');
Route::get('/reporte/getdataactividadesnew', 'Reporte\ReporteController@getdataactividadesnew')->name('reporte.getdataactividadesnew');
Route::get('/reporte/getdataactividades', 'Reporte\ReporteController@getdataactividades')->name('reporte.getdataactividades');
Route::get('/reporte/accionesgobiernototales', 'Reporte\ReporteController@totalesview')->name('reporte.accionesgobiernototales');
Route::get('/reporte/accionesgobiernototales2', 'Reporte\ReporteController@totalesview2')->name('reporte.accionesgobiernototales2');
Route::get('/reporte/tomosview', 'Reporte\ReporteController@tomosview')->name('reporte.accionesgtomos');


Route::get('/imprimiracciones', 'Reporte\ReporteController@imprimiracciones')->name('imprimiracciones');
Route::get('/imprimiraccionesnew', 'Reporte\ReporteController@imprimiraccionesnew')->name('imprimiraccionesnew');
Route::get('/imprimiraccionesExcel', 'Reporte\ReporteController@imprimiraccionesExcel')->name('imprimiraccionesExcel');
Route::get('/imprimiractividadesExcel', 'Reporte\ReporteController@imprimiractividadesExcel')->name('imprimiractividadesExcel');
Route::get('/imprimirnewExcel', 'Reporte\ReporteController@imprimir2024Excel')->name('imprimirnewExcel');
Route::get('/imprimiraccionesgobiernototales', 'Reporte\ReporteController@imprimiraccionesgobiernototales')->name('imprimiraccionesgobiernototales');
Route::get('/imprimiraccionesgobiernototalesInstitutos', 'Reporte\ReporteController@imprimiraccionesgobiernototalesInstitutos')->name('imprimiraccionesgobiernototalesInstitutos');


Route::get('/importar/importar', 'Importar\ImportarController@index')->name('importar.view');
Route::post('/importar/store', 'Importar\ImportarController@importarstore')->name('importar.store');
Route::post('/fecha/store', 'Fecha\FechaController@store')->name('fecha.store');


Route::get('/imprimiractividades', 'Reporte\ReporteController@imprimiractividades')->name('imprimiractividades');
Route::get('/imprimirdetallado', 'Reporte\ReporteController@imprimirdetallado')->name('imprimirdetallado');
Route::get('/imprimirtomo', 'Reporte\ReporteController@imprimirtomo')->name('imprimirtomo');

/* Sala Unidad / Direcciones */
Route::get('/get/unidades', 'SalaUnidad\SalaUnidadController@getUnidades')->name('get.unidades');
Route::get('/acciones/accionesano', 'Acciones\AccionesController@accionesano')->name('acciones.accionesano');
Route::get('/acciones/accionesano2', 'Acciones\AccionesController@accionesano2')->name('acciones.accionesano2');
Route::get('/acciones/accionesano3', 'Acciones\AccionesController@accionesano3')->name('acciones.accionesano3');
Route::get('/acciones/totales', 'Acciones\AccionesController@totales')->name('acciones.totales');
Route::get('/acciones/totales/filtrados', 'Acciones\AccionesController@totalesFiltrados')->name('acciones.totales.filtrados');
Route::get('/acciones/correlativo/{id}', 'Acciones\AccionesController@ObtenerCorrelativoImportar')->name('acciones.correlativo');
Route::get('/users/{user}/delete', 'Acciones\AccionesController@destroy')->name('acciones.destroy');

#################################################### Acciones

Route::get('/acciones/list', 'Acciones\AccionesController@index')->name('acciones.list');
Route::get('/acciones/getdataacciones', 'Acciones\AccionesController@getdataacciones')->name('acciones.getdataacciones');
Route::get('/acciones/create', 'Acciones\AccionesController@create')->name('acciones.create')->middleware('permisofecha');
Route::post('/acciones/store', 'Acciones\AccionesController@store')->name('acciones.store');
Route::get('/acciones/{accion}/edit', 'Acciones\AccionesController@edit')->name('acciones.edit');
Route::post('/acciones/{accion}', 'Acciones\AccionesController@update')->name('acciones.update');
Route::get('/acciones/totalaccionesyactividades', 'Acciones\AccionesController@totalaccionesyactividades')->name('total.accionesyactividades');
Route::get('/acciones/totalescomunidadxcomuna', 'Acciones\AccionesController@totalescomunidadxcomuna')->name('total.totalescomunidadxcomuna');
Route::get('/acciones/totalescomunidadxcomuna2', 'Acciones\AccionesController@totalescomunidadxcomuna2')->name('total.totalescomunidadxcomuna2');
Route::get('/acciones/totalestomos', 'Acciones\AccionesController@totalestomos')->name('total.totalestomos');


#################################################### Actividades

Route::get('/actividades/list', 'Actividades\ActividadesController@index')->name('actividades.list');
Route::get('/actividades/getdataactividades', 'Actividades\ActividadesController@getdataactividades')->name('actividades.getdataactividades');
Route::get('/actividades/create', 'Actividades\ActividadesController@create')->name('actividades.create');
Route::post('/actividades/store', 'Actividades\ActividadesController@store')->name('actividades.store');
Route::get('/actividades/{actividad}/edit', 'Actividades\ActividadesController@edit')->name('actividades.edit');
Route::post('/actividades/{actividad}', 'Actividades\ActividadesController@update')->name('actividades.update');
Route::get('/actividades/actividadesano3', 'Actividades\ActividadesController@actividadesano3')->name('actividades.actividadesano3');


// Fecha
Route::get('/fecha/setfecha', 'Fecha\FechaController@index')->name('fecha.index');










































/* Rutas de Control Diario, para todas las operaciones, con el Middleware (permiso) Integrado, para cada caso.
*/
Route::get('/solicitud', 'Solicitud\SolicitudController@index')->name('solicitud.index')->middleware('permiso:solicitud,view');
Route::post('/solicitud', 'Solicitud\SolicitudController@store')->name('solicitud.store')->middleware('permiso:solicitud,add');
Route::get('/solicitud/{solicitud}/view', 'Solicitud\SolicitudController@view')->name('solicitud.view')->middleware('permiso:solicitud,view');
Route::get('/solicitud/{solicitud}/edit', 'Solicitud\SolicitudController@edit')->name('solicitud.edit')->middleware('permiso:solicitud,edit');
Route::post('/solicitud/{solicitud}', 'Solicitud\SolicitudController@update')->name('solicitud.update')->middleware('permiso:solicitud,update');
Route::get('/solicitud/{solicitud}/delete', 'Solicitud\SolicitudController@destroy')->name('solicitud.destroy')->middleware('permiso:solicitud,delete');
Route::get('/solicitud/getComunas', 'Solicitud\SolicitudController@getComunas')->name('getComunas2')->middleware('permiso:solicitud,view');
Route::get('/solicitud/getComunidad', 'Solicitud\SolicitudController@getComunidad')->name('getComunidad2');

Route::get('/solicitud/getTelefonoJefeComunidad', 'JefeComunidad\JefeComunidadController@getJefeComunidad2')->name('getJefeComunidad2');

Route::get('/solicitud/getJefeComunidad', 'JefeComunidad\JefeComunidadController@getJefeComunidad')->name('getJefeComunidad');
Route::get('/solicitud/getCoodinacion', 'Solicitud\SolicitudController@getCoodinacion')->name('getCoodinacion2')->middleware('permiso:solicitud,view');
Route::get('/solicitud/list', 'Solicitud\SolicitudController@getSolicitud')->name('solicitud.list')->middleware('permiso:solicitud,view, edit');
Route::get('/solicitud/print', 'Solicitud\SolicitudController@solicitudPrint')->name('solicitud.solicitudPrint')->middleware('permiso:solicitud,print');
Route::get('/imprimir', 'Solicitud\SolicitudController@imprimir')->name('imprimir');

Route::get('/solicitud/buscarsolicitud', 'Solicitud\SolicitudController@BuscarIndex')->name('solicitud.buscarindex')->middleware('permiso:solicitud,view');
Route::get('/solicitud/buscargeneral', 'Solicitud\SolicitudController@getSolicitudGeneral')->name('solicitud.buscargeneral')->middleware('permiso:solicitud,view');

/* REPORTES FINALIZADAS */
Route::get('/solicitud/totalFinalizadas', 'Solicitud\SolicitudController@imprimir4')->name('solicitud.solicitudTotalFinalizadas');
Route::get('/solicitud/totalFinalizadas2', 'Solicitud\SolicitudController@getTotalSolicitudesFinalizadas2')->name('solicitud.solicitudTotalFinalizadas2');


/* SACWAN */
Route::get('/solicitud/solicitudTipo2', 'Solicitud\SolicitudController@solicitudTipo2')->name('solicitud.solicitudTipo2');
Route::get('/solicitud/solicitudTipo2PorFecha', 'Solicitud\SolicitudController@solicitudTipo2PorFecha')->name('solicitud.solicitudTipo2PorFecha');
Route::get('/solicitud/solicitudTipo3', 'Solicitud\SolicitudController@solicitudTipo3')->name('solicitud.solicitudTipo3');
Route::get('/solicitud/solicitudTipo4', 'Solicitud\SolicitudController@solicitudTipo4')->name('solicitud.solicitudTipo4');
Route::get('/solicitud/solicitudTipo4PorFecha', 'Solicitud\SolicitudController@solicitudTipo4PorFecha')->name('solicitud.solicitudTipo4PorFecha');
Route::get('/solicitud/solicitudTipo5', 'Solicitud\SolicitudController@solicitudTipo5')->name('solicitud.solicitudTipo5');
Route::get('/solicitud/solicitudTipo5PorFecha', 'Solicitud\SolicitudController@solicitudTipo5PorFecha')->name('solicitud.solicitudTipo5PorFecha');
Route::get('/solicitud/solicitudTotalTipo', 'Solicitud\SolicitudController@solicitudTotalTipo')->name('solicitud.solicitudTotalTipo');
Route::get('/solicitud/solicitudedad', 'Solicitud\SolicitudController@solicitudedad')->name('solicitud.solicitudedad');

Route::get('/solicitud/list2', 'Solicitud\SolicitudController@getSolicitud2')->name('solicitud.list2');
Route::get('/solicitud/list3', 'Solicitud\SolicitudController@getSolicitud3')->name('solicitud.list3');
Route::get('/solicitud/totalFinalizadas3', 'Solicitud\SolicitudController@getFinalizadas')->name('solicitud.solicitudTotalFinalizadas3');
Route::get('/solicitud/totalFinalizadasConFecha', 'Solicitud\SolicitudController@getFinalizadasConFecha')->name('solicitud.solicitudTotalFinalizadasConFecha');

/* LISTADO SOLICITUD */
Route::get('/getComunas', 'Comuna\ComunaController@getComunas')->name('getComunas');
Route::get('/solicitud/getSolicitudesWAN', 'Solicitud\SolicitudController@getSolicitudesWAN')->name('solicitud.getSolicitudesWAN');
Route::get('/imprimirWAN', 'Solicitud\SolicitudController@imprimirWAN')->name('solicitud.imprimirWAN');


/* REPORTE TOTALES FINALIZADAS POR FECHA  */
Route::get('/imprimir2', 'Solicitud\SolicitudController@imprimir2')->name('imprimir2');
/* REPORTE TOTALES EN ANALISIS Y REGISTRADAS POR FECHA  */
Route::get('/imprimir3', 'Solicitud\SolicitudController@imprimir3')->name('imprimir3');

// ##############################rutas del seguimiento de la solicitud

Route::get('/seguimiento', 'Seguimiento\SeguimientoController@index')->name('seguimiento.index')->middleware('permiso:seguimiento,view');
Route::get('/seguimiento/create', 'Seguimiento\SeguimientoController@create')->name('seguimiento.create')->middleware('permiso:seguimiento,add');
Route::post('/seguimiento', 'Seguimiento\SeguimientoController@store')->name('seguimiento.store')->middleware('permiso:seguimiento,add');
Route::get('/seguimiento/{seguimiento}/view', 'Seguimiento\SeguimientoController@view')->name('seguimiento.view')->middleware('permiso:seguimiento,view');
Route::get('/seguimiento/{seguimiento}/edit', 'Seguimiento\SeguimientoController@edit')->name('seguimiento.edit')->middleware('permiso:seguimiento,edit');
Route::post('/seguimiento/{seguimiento}', 'Seguimiento\SeguimientoController@update')->name('seguimiento.update')->middleware('permiso:seguimiento,update');
Route::get('/seguimiento/update2', 'Seguimiento\SeguimientoController@update2')->name('update2')->middleware('permiso:seguimiento,update');
Route::get('/seguimiento/{seguimiento}/delete', 'Seguimiento\SeguimientoController@destroy')->name('seguimiento.destroy')->middleware('permiso:seguimiento,delete');
Route::get('/seguimiento/getComunas', 'Seguimiento\SeguimientoController@getComunas')->name('getComunas');
Route::get('/seguimiento/getComunidad', 'Seguimiento\SeguimientoController@getComunidad')->name('getComunidad')->middleware('permiso:seguimiento,view');
Route::get('/seguimiento/getCoodinacion', 'Seguimiento\SeguimientoController@getCoodinacion')->name('getCoodinacion')->middleware('permiso:seguimiento,view');
Route::get('/seguimiento/list', 'Seguimiento\SeguimientoController@getSeguimiento')->name('seguimiento.list')->middleware('permiso:seguimiento,view');
Route::get('/seguimiento/print', 'Seguimiento\SeguimientoController@solicitudPrint')->name('seguimiento.solicitudPrint')->middleware('permiso:seguimiento,print');
Route::get('/seguimiento/solicitudTipo', 'Seguimiento\SeguimientoController@solicitudTipo')->name('seguimiento.solicitudTipo');
Route::get('/seguimiento/solicitudTotalTipo', 'Seguimiento\SeguimientoController@solicitudTotalTipo')->name('seguimiento.solicitudTotalTipo');
Route::post('/seguimiento/addSeguimiento', 'Seguimiento\SeguimientoController@addSeguimiento')->name('addSeguimiento')->middleware('permiso:seguimiento,view');
Route::get('/seguimientoapi', 'Seguimiento\SeguimientoController@segumientoJson')->name('seguimiento.segumientoapi');

/* REPORTES FINALIZADAS */
Route::get('/finalizadas', 'Seguimiento\SeguimientoController@finalizadas')->name('seguimiento.finalizadas')->middleware('permiso:seguimiento,view');
Route::get('/seguimiento/finalizadas', 'Seguimiento\SeguimientoController@getSeguimientoFinalizadas')->name('seguimiento.finalizadas')->middleware('permiso:seguimiento,view');

/* REPORTE TOTALES EN ANALISIS Y REGISTRADAS POR FECHA  */
Route::get('/finalizadas2', 'Seguimiento\SeguimientoController@finalizadas2')->name('seguimiento.finalizadas2')->middleware('permiso:seguimiento,view');
Route::get('/seguimiento/finalizadas2', 'Seguimiento\SeguimientoController@getSeguimientoFinalizadas2')->name('seguimiento.finalizadas2')->middleware('permiso:seguimiento,view');


Route::get('/seguimiento/list2', 'Seguimiento\SeguimientoController@getSeguimiento2')->name('seguimiento.list2');
// *********************************************************************************************************

/* Almacen de Productos  */
Route::get('/almacen', 'Almacen\AlmacenController@index')->name('almacen');
Route::get('/almacen/create', 'Almacen\AlmacenController@create')->name('almacen.create');
Route::get('/almacen/list', 'Almacen\AlmacenController@getAlmacen')->name('almacen.list');
Route::get('/almacen/{almacen}/edit', 'Almacen\AlmacenController@edit')->name('almacen.edit');
Route::get('/almacen/{almacen}/show', 'Almacen\AlmacenController@show')->name('almacen.view');
Route::post('/almacen/store', 'Almacen\AlmacenController@store')->name('almacen.store');
Route::post('/almacen/{almacen}', 'Almacen\AlmacenController@update')->name('almacen.update');

/*****Producto**********************/

Route::get('/dashproducto', 'Producto\ProductoController@index2')->name('dashproducto');
Route::get('/producto', 'Producto\ProductoController@index')->name('producto');
Route::get('/producto/create', 'Producto\ProductoController@create')->name('producto.create');
Route::get('/producto/list', 'Producto\ProductoController@getProducto')->name('producto.list');
Route::get('/producto/{producto}/edit', 'Producto\ProductoController@edit')->name('producto.edit');
Route::get('/producto/{producto}/show', 'Producto\ProductoController@show')->name('producto.view');
Route::post('/producto/store', 'Producto\ProductoController@store')->name('producto.store');
Route::post('/producto/{producto}', 'Producto\ProductoController@update')->name('producto.update');

/************ Servcio **************/

Route::get('/servicio', 'Servicio\ServicioController@index')->name('servicio');
Route::get('/servicio/create', 'Servicio\ServicioController@create')->name('servicio.create');
Route::get('/servicio/list', 'Servicio\ServicioController@getServicio')->name('servicio.list');
Route::get('/servicio/{servicio}/edit', 'Servicio\ServicioController@edit')->name('servicio.edit');
Route::get('/servicio/{servicio}/show', 'Servicio\ServicioController@show')->name('servicio.view');
Route::post('/servicio/store', 'Servicio\ServicioController@store')->name('servicio.store');
Route::post('/servicio/{servicio}', 'Servicio\ServicioController@update')->name('servicio.update');

/************ Inventario **************/
Route::get('/inventario', 'Inventario\InventarioController@index')->name('inventario');
Route::get('/inventario/create', 'Inventario\InventarioController@create')->name('inventario.create');
Route::get('/inventario/{inventario}/edit', 'Inventario\InventarioController@edit')->name('inventario.edit');
Route::get('/inventario/{inventario}/show', 'Inventario\InventarioController@show')->name('inventario.view');
Route::post('/inventario/store', 'Inventario\InventarioController@store')->name('inventario.store');
Route::post('/inventario/{inventario}', 'Inventario\InventarioController@update')->name('inventario.update');
Route::get('/inventario/list', 'Inventario\InventarioController@getInventario')->name('inventario.list');

####################################################
Route::post('/movimientoSolicitud/movimientoSolicitud', 'Seguimiento\SeguimientoController@addSeguimiento')->name('movimientoSolicitud');
Route::post('/movimientoSolicitud/store2', 'Seguimiento\SeguimientoController@store2')->name('seguimiento.store2');
Route::post('/movimientoSolicitud/store3', 'Seguimiento\SeguimientoController@store3')->name('seguimiento.store3');
Route::get('/movimientoSolicitud/existencia', 'Seguimiento\SeguimientoController@existencia')->name('existencia');


