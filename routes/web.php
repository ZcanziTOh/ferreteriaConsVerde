<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\VendedorController;

// Rutas de autenticación
Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Rutas para administrador
Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/dashboardTec', [AdminController::class, 'dashboardTec'])->name('admin.dashboardTec');
    
    // Nuevas rutas para consultas SUNAT/APISPERU
    Route::post('/consultar-dni', [AdminController::class, 'consultarDni'])->name('admin.consultar-dni');
    Route::post('/consultar-ruc', [AdminController::class, 'consultarRuc'])->name('admin.consultar-ruc');

     // Cotizaciones
    Route::get('/cotizaciones', [AdminController::class, 'cotizaciones'])->name('admin.cotizaciones.index');
    Route::post('/cotizaciones/generar', [AdminController::class, 'generarCotizacion'])->name('admin.cotizaciones.generar');
    
    // Devoluciones
    Route::get('/devoluciones', [AdminController::class, 'devoluciones'])->name('admin.devoluciones');
    Route::get('/devoluciones/crear/{ventaId}', [AdminController::class, 'crearDevolucion'])->name('admin.devoluciones.create');
    Route::post('/devoluciones/{ventaId}', [AdminController::class, 'guardarDevolucion'])->name('admin.devoluciones.store');

    // Productos
    Route::get('/productos', [AdminController::class, 'productos'])->name('admin.productos');
    Route::get('/productos/crear', [AdminController::class, 'crearProducto'])->name('admin.productos.create');
    Route::post('/productos', [AdminController::class, 'guardarProducto'])->name('admin.productos.store');
    Route::get('/productos/{id}/editar', [AdminController::class, 'editarProducto'])->name('admin.productos.edit');
    Route::put('/productos/{id}', [AdminController::class, 'actualizarProducto'])->name('admin.productos.update');
    Route::delete('/productos/{id}', [AdminController::class, 'eliminarProducto'])->name('admin.productos.destroy');
    
    // Categorías
    Route::get('/categorias', [AdminController::class, 'categorias'])->name('admin.categorias');
    Route::get('/categorias/crear', [AdminController::class, 'crearCategoria'])->name('admin.categorias.create');
    Route::post('/categorias', [AdminController::class, 'guardarCategoria'])->name('admin.categorias.store');
    Route::get('/categorias/{id}/editar', [AdminController::class, 'editarCategoria'])->name('admin.categorias.edit');
    Route::put('/categorias/{id}', [AdminController::class, 'actualizarCategoria'])->name('admin.categorias.update');
    Route::delete('/categorias/{id}', [AdminController::class, 'eliminarCategoria'])->name('admin.categorias.destroy');
    
    // Empleados
    Route::get('/empleados', [AdminController::class, 'empleados'])->name('admin.empleados');
    Route::get('/empleados/crear', [AdminController::class, 'crearEmpleado'])->name('admin.empleados.create');
    Route::post('/empleados', [AdminController::class, 'guardarEmpleado'])->name('admin.empleados.store');
    Route::get('/empleados/{id}/editar', [AdminController::class, 'editarEmpleado'])->name('admin.empleados.edit');
    Route::put('/empleados/{id}', [AdminController::class, 'actualizarEmpleado'])->name('admin.empleados.update');
    Route::delete('/empleados/{id}', [AdminController::class, 'eliminarEmpleado'])->name('admin.empleados.destroy');
    
    // Ventas
    Route::get('/ventas/crear', [AdminController::class, 'crearVenta'])->name('admin.ventas.create');
    Route::post('/ventas', [AdminController::class, 'guardarVenta'])->name('admin.ventas.store');
    Route::get('/ventas', [AdminController::class, 'ventas'])->name('admin.ventas');
    Route::get('/ventas/{id}', [AdminController::class, 'verVenta'])->name('admin.ventas.show');
    
    // Reportes
    Route::get('/reportes', [AdminController::class, 'reportes'])->name('admin.reportes');
    Route::get('/reportes/ventas', [AdminController::class, 'generarReporteVentas'])->name('admin.reportes.ventas');
    Route::get('/reportes/productos', [AdminController::class, 'generarReporteProductos'])->name('admin.reportes.productos');
    Route::get('/reportes/proveedores', [AdminController::class, 'generarReporteProveedores'])->name('admin.reportes.proveedores');
});

// Rutas para vendedor
Route::middleware(['auth', 'role:vendedor'])->prefix('vendedor')->group(function () {
    Route::get('/dashboard', [VendedorController::class, 'dashboard'])->name('vendedor.dashboard');

    // Nuevas rutas para consultas SUNAT/APISPERU
    Route::post('/consultar-dni', [VendedorController::class, 'consultarDni'])->name('vendedor.consultar-dni');
    Route::post('/consultar-ruc', [VendedorController::class, 'consultarRuc'])->name('vendedor.consultar-ruc');
    // Cotizaciones
    Route::get('/cotizaciones', [VendedorController::class, 'cotizaciones'])->name('vendedor.cotizaciones.index');
    Route::post('/cotizaciones/generar', [VendedorController::class, 'generarCotizacion'])->name('vendedor.cotizaciones.generar');
    
    // Ventas
    Route::get('/ventas', [VendedorController::class, 'ventas'])->name('vendedor.ventas.index');
    Route::get('/ventas/crear', [VendedorController::class, 'crearVenta'])->name('vendedor.ventas.create');
    Route::post('/ventas', [VendedorController::class, 'guardarVenta'])->name('vendedor.ventas.store');
    Route::get('/ventas/{id}', [VendedorController::class, 'verVenta'])->name('vendedor.ventas.show');
    
    // Clientes
    Route::get('/clientes/naturales', [VendedorController::class, 'clientesNaturales'])->name('vendedor.clientes.naturales');
    Route::get('/clientes/naturales/crear', [VendedorController::class, 'crearClienteNatural'])->name('vendedor.clientes.crear-natural');
    Route::post('/clientes/naturales', [VendedorController::class, 'guardarClienteNatural'])->name('vendedor.clientes.store-natural');
    
    Route::get('/clientes/juridicos', [VendedorController::class, 'clientesJuridicos'])->name('vendedor.clientes.juridicos');
    Route::get('/clientes/juridicos/crear', [VendedorController::class, 'crearClienteJuridico'])->name('vendedor.clientes.crear-juridico');
    Route::post('/clientes/juridicos', [VendedorController::class, 'guardarClienteJuridico'])->name('vendedor.clientes.store-juridico');
    
    // Devoluciones
    Route::get('/devoluciones', [VendedorController::class, 'devoluciones'])->name('vendedor.devoluciones');
    Route::get('/devoluciones/crear/{ventaId}', [VendedorController::class, 'crearDevolucion'])->name('vendedor.devoluciones.create');
    Route::post('/devoluciones/{ventaId}', [VendedorController::class, 'guardarDevolucion'])->name('vendedor.devoluciones.store');
    
    // Cierre de caja
    Route::get('/cierre-caja', [VendedorController::class, 'cierreCaja'])->name('vendedor.cierre-caja');
});