<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Empleado;
use App\Models\Producto;
use App\Models\Venta;
use App\Models\DetalleVenta;
use App\Models\ClienteNatural;
use App\Models\ClienteJuridica;
use App\Models\Devolucion;
use App\Models\Comprobante;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Services\SunatService;
use Carbon\Carbon; 

class AdminController extends Controller
{
    protected $sunatService;
    
    public function __construct(SunatService $sunatService)
    {
        $this->middleware('auth');
        $this->middleware('role:admin');
        $this->sunatService = $sunatService;
    }

    public function dashboardTec()
    {
        // Estadísticas básicas
        $totalProductos = Producto::count();
        $totalVentas = Venta::count();
        $ingresosTotales = Venta::sum('totalVent');
        $totalEmpleados = Empleado::count();

        // Métodos de pago
        $metodosPago = $this->getMetodosPago();

        // Tendencias (comparación con el periodo anterior)
        $productosTrend = $this->calculateTrend(Producto::class);
        $ingresosTrend = $this->calculateTrend(Venta::class, 'totalVent');
        
        // Datos para gráficos avanzados
        $tendenciasVentas = $this->getSalesTrends();
        $productosHeatmap = $this->getProductHeatmap();
        $comprobanteData = $this->getComprobanteAnalysis();
        $inventarioForecast = $this->getInventoryForecast();
        $embudoConversion = $this->getConversionFunnel();
        $datosGrafico = $this->obtenerDatosGrafico();
        
        // Productos con bajo stock (con datos adicionales)
        $productosBajoStock = Producto::with('categoria')
            ->whereColumn('stockProd', '<=', 'stockMinProd')
            ->orWhere('stockProd', '<', DB::raw('stockMinProd * 1.5'))
            ->orderByRaw('(stockProd / stockMinProd) ASC')
            ->limit(10)
            ->get()
            ->each(function($producto) {
                // Calcular días restantes basado en ventas promedio
                $ventasDiarias = DetalleVenta::where('IDProd', $producto->IDProd)
                    ->whereDate('created_at', '>=', now()->subDays(30))
                    ->sum('subtotal') / 30;
                
                $producto->ventasDiariasPromedio = $ventasDiarias > 0 ? 
                    $ventasDiarias / $producto->precUniProd : 1;
                
                // Calcular índice de rotación
                $producto->indiceRotacion = $ventasDiarias / ($producto->stockProd ?: 1);
            });
        
        // Últimas ventas con margen calculado
        $ultimasVentas = Venta::with(['clienteNatural', 'clienteJuridica', 'usuario.empleado', 'detalleVentas.producto'])
            ->orderBy('fechVent', 'desc')
            ->limit(10)
            ->get()
            ->each(function($venta) {
                $costoTotal = $venta->detalleVentas->sum(function($detalle) {
                    return $detalle->producto->precUniProd * 0.7 * $detalle->subtotal / $detalle->prec_uni;
                });
                
                $venta->margen = $venta->totalVent > 0 ? 
                    ($venta->totalVent - $costoTotal) / $venta->totalVent : 0;
            });
        
        // Datos para filtros
        $regiones = []; // Aquí iría tu lógica para obtener regiones si las tienes
        $categorias = Categoria::all();
        
        return view('admin.dashboardTec', compact(
            'totalProductos', 'totalVentas', 'ingresosTotales', 'totalEmpleados',
            'productosTrend', 'ingresosTrend', 'metodosPago', 'datosGrafico',
            'tendenciasVentas', 'productosHeatmap', 'comprobanteData', 'inventarioForecast',
            'embudoConversion', 'productosBajoStock', 'ultimasVentas',
            'regiones', 'categorias'
        ));
    }
    
    public function obtenerDatosGrafico()
    {
        // 1. Obtener VENTAS agrupadas por mes (como ya lo haces)
        $ventasPorMes = Venta::selectRaw('
                MONTH(fechVent) as mes,
                YEAR(fechVent) as año,
                SUM(totalVent) as total_ventas
            ')
            ->groupBy('año', 'mes')
            ->orderBy('año', 'asc')
            ->orderBy('mes', 'asc')
            ->get();

        // 2. Obtener COMPRAS agrupadas por mes (según fecha de creación del producto)
        $comprasPorMes = Producto::selectRaw('
                MONTH(created_at) as mes,
                YEAR(created_at) as año,
                SUM(totalComp) as total_compras
            ')
            ->groupBy('año', 'mes')
            ->orderBy('año', 'asc')
            ->orderBy('mes', 'asc')
            ->get();

        // 3. Combinar ambos conjuntos de datos
        $datosGrafico = [];
        
        // Llenar ventas
        foreach ($ventasPorMes as $venta) {
            $key = $venta->mes . '-' . $venta->año;
            $datosGrafico[$key] = [
                'periodo' => Carbon::create()->month($venta->mes)->locale('es')->monthName . ' ' . $venta->año,
                'ventas' => $venta->total_ventas,
                'compras' => 0, // Inicializar en 0
                'ganancia' => 0,
            ];
        }

        // Llenar compras (y actualizar ganancias)
        foreach ($comprasPorMes as $compra) {
            $key = $compra->mes . '-' . $compra->año;
            if (isset($datosGrafico[$key])) {
                $datosGrafico[$key]['compras'] = $compra->total_compras;
                $datosGrafico[$key]['ganancia'] = $datosGrafico[$key]['ventas'] - $compra->total_compras;
            } else {
                // Si no hay ventas ese mes, solo mostrar compras
                $datosGrafico[$key] = [
                    'periodo' => Carbon::create()->month($compra->mes)->locale('es')->monthName . ' ' . $compra->año,
                    'ventas' => 0,
                    'compras' => $compra->total_compras,
                    'ganancia' => -$compra->total_compras,
                ];
            }
        }

        // Ordenar por año y mes
        usort($datosGrafico, function ($a, $b) {
            return strtotime($a['periodo']) <=> strtotime($b['periodo']);
        });

        return array_values($datosGrafico);
    }

    private function getMetodosPago()
    {
        $metodos = ['Efectivo', 'Tarjeta', 'Transferencia', 'Yape'];
        $cantidades = [];

        foreach ($metodos as $metodo) {
            $cantidad = Venta::where('metPagVent', $metodo)->count();
            $cantidades[] = $cantidad;
        }

        return [
            'metodos' => $metodos,
            'cantidades' => $cantidades
        ];
    }

    private function calculateTrend($model, $column = null)
    {
        $currentPeriod = $model::when($column, function($query) use ($column) {
                return $query->sum($column);
            }, function($query) {
                return $query->count();
            });
            
        $previousPeriod = $model::when($column, function($query) use ($column) {
                return $query->whereDate('created_at', '<', now()->subDays(30))
                    ->sum($column);
            }, function($query) {
                return $query->whereDate('created_at', '<', now()->subDays(30))
                    ->count();
            });
            
        return $previousPeriod > 0 ? 
            (($currentPeriod - $previousPeriod) / $previousPeriod) * 100 : 0;
    }
    
    private function getSalesTrends()
    {
        $current = [];  
        $previous = [];
        $labels = [];
        
        // Nombres de los días en español
        $diasSemana = [
            'Sun' => 'Dom',
            'Mon' => 'Lun',
            'Tue' => 'Mar',
            'Wed' => 'Mié',
            'Thu' => 'Jue',
            'Fri' => 'Vie',
            'Sat' => 'Sáb'
        ];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $dayName = $date->format('D');
            $labels[] = $diasSemana[$dayName] ?? $dayName;
            
            $current[] = Venta::whereDate('fechVent', $date->format('Y-m-d'))
                ->sum('totalVent');
                
            $previous[] = Venta::whereDate('fechVent', $date->copy()->subDays(7)->format('Y-m-d'))
                ->sum('totalVent');
        }
        
        return [
            'labels' => $labels,
            'actual' => $current,
            'anterior' => $previous
        ];
    }
    
    private function getProductHeatmap()
    {
        $productos = Producto::with('categoria')
            ->withSum('detalleVentas as ventas_totales', 'subtotal')
            ->withCount('detalleVentas')
            ->orderBy('ventas_totales', 'desc')
            ->limit(10)
            ->get()
            ->each(function($producto) {
                $producto->margen = 0.3; // Esto debería calcularse basado en costos reales
            });
            
        return [
            'productos' => $productos->pluck('nomProd')->toArray(),
            'ventas' => $productos->pluck('ventas_totales')->toArray(),
            'margenes' => $productos->pluck('margen')->toArray()
        ];
    }
    
    private function getComprobanteAnalysis()
    {
        $results = Venta::with('comprobantes')
            ->get()
            ->groupBy(function($venta) {
                if ($venta->comprobantes->isEmpty()) {
                    return 'Proforma';
                }
                
                // Tomamos el primer comprobante (o iteramos según necesidad)
                $primerComprobante = $venta->comprobantes->first();
                $tipo = strtolower($primerComprobante->tipCompr ?? '');
                
                if (str_contains($tipo, 'boleta') || $tipo === 'b') {
                    return 'Boleta';
                } elseif (str_contains($tipo, 'factura') || $tipo === 'f') {
                    return 'Factura';
                } elseif (str_contains($tipo, 'proforma') || $tipo === 'p') {
                    return 'Proforma';
                }
                
                return 'Proforma'; // Valor por defecto
            })
            ->map->count();

        return [
            'labels' => ['Boleta', 'Factura', 'Proforma'],
            'data' => [
                $results['Boleta'] ?? 0,
                $results['Factura'] ?? 0,
                $results['Proforma'] ?? 0
            ]
        ];
    }
    
    private function getInventoryForecast()
    {
        // Datos de ejemplo - deberías implementar tu propia lógica de previsión
        $dias = range(1, 30);
        $stockInicial = 100;
        $consumoDiario = 3.5;
        $puntoPedido = 20;
        
        $stock = array_map(function($dia) use ($stockInicial, $consumoDiario) {
            return max(0, $stockInicial - ($dia * $consumoDiario));
        }, $dias);
        
        $forecast = array_map(function($dia) use ($stockInicial, $consumoDiario) {
            return max(0, $stockInicial - ($dia * $consumoDiario * 1.1)); // 10% más de consumo
        }, $dias);
        
        $reorder = array_fill(0, 30, $puntoPedido);
        
        return [
            'dias' => $dias,
            'stock' => $stock,
            'forecast' => $forecast,
            'reorder' => $reorder
        ];
    }
    
    private function getConversionFunnel()
    {
        // Datos de ejemplo - implementa según tus métricas reales
        return [
            'valores' => [1000, 500, 100, 50],
            'tasas' => [50, 20, 50]
        ];
    }
    
    public function dashboard()
    {
        $totalProductos = Producto::count();
        $totalVentas = Venta::count();
        $totalEmpleados = Empleado::count();
        
        $productosBajoStock = Producto::where('stockProd', '<', 10)
            ->orderBy('stockProd', 'asc')
            ->limit(5)
            ->get();
            
        $ultimasVentas = Venta::with('usuario.empleado')
            ->orderBy('fechVent', 'desc')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalProductos',
            'totalVentas',
            'totalEmpleados',
            'productosBajoStock',
            'ultimasVentas'
        ));
    }

    // Métodos para cotizaciones
    public function cotizaciones()
    {
        $productos = Producto::where('estProd', 'activo')->where('stockProd', '>', 0)->get();
        $clientesNaturales = ClienteNatural::all();
        $clientesJuridicos = ClienteJuridica::all();
        
        return view('admin.cotizaciones.index', compact('productos', 'clientesNaturales', 'clientesJuridicos'));
    }

    public function generarCotizacion(Request $request)
    {
        $request->validate([
            'productos' => 'required|array|min:1',
            'productos.*.IDProd' => 'required|exists:productos,IDProd',
            'productos.*.cantidad' => 'required|integer|min:1',
            'productos.*.descuento' => 'nullable|numeric|min:0|max:100',
            'tipo_cliente' => 'required|in:natural,juridico,none',
            'cliente_id' => 'nullable|string|regex:/^([NJ]-\d+)$/',
            'observaciones' => 'nullable|string',
            'docIdenClieNat' => 'nullable|string|max:15|unique:cliente_natural,docIdenClieNat',
            'nomClieNat' => 'nullable|string|max:100',
            'apelClieNat' => 'nullable|string|max:100',
            'direccion' => 'nullable|string|max:200',
            'rucClieJuri' => 'nullable|string|size:11|unique:cliente_juridica,rucClieJuri',
            'razSociClieJuri' => 'nullable|string|max:100',
            'dirfiscClieJuri' => 'nullable|string|max:200',
        ]);

        // Guardar nuevo cliente si es necesario
        $cliente = null;
        $cliente_id = null;
        
        if ($request->tipo_cliente === 'natural') {
            if ($request->filled('docIdenClieNat')) {
                // Crear nuevo cliente natural
                $cliente = ClienteNatural::create([
                    'docIdenClieNat' => $request->docIdenClieNat,
                    'nomClieNat' => $request->nomClieNat,
                    'apelClieNat' => $request->apelClieNat,
                    'direccion' => $request->direccion,
                ]);
                $cliente_id = 'N-'.$cliente->IDClieNat;
            } elseif ($request->cliente_id) {
                $cliente = ClienteNatural::find(explode('-', $request->cliente_id)[1]);
                $cliente_id = $request->cliente_id;
            }
        } elseif ($request->tipo_cliente === 'juridico') {
            if ($request->filled('rucClieJuri')) {
                // Crear nuevo cliente jurídico
                $cliente = ClienteJuridica::create([
                    'rucClieJuri' => $request->rucClieJuri,
                    'razSociClieJuri' => $request->razSociClieJuri,
                    'dirfiscClieJuri' => $request->dirfiscClieJuri,
                    'nomComClieJuri' => $request->razSociClieJuri, // Usamos razón social como nombre comercial por defecto
                ]);
                $cliente_id = 'J-'.$cliente->IDClieJuri;
            } elseif ($request->cliente_id) {
                $cliente = ClienteJuridica::find(explode('-', $request->cliente_id)[1]);
                $cliente_id = $request->cliente_id;
            }
        }

        $productosSeleccionados = [];
        $subtotal = 0;

        foreach ($request->productos as $item) {
            $producto = Producto::find($item['IDProd']);
            $cantidad = $item['cantidad'];
            $descuento = $item['descuento'] ?? 0;

            if ($producto->stockProd < $cantidad) {
                return back()->withErrors(['stock' => "No hay suficiente stock para el producto {$producto->nomProd}"]);
            }

            $precioConDescuento = $producto->precUniProd * (1 - $descuento / 100);

            $productosSeleccionados[] = [
                'producto' => $producto,
                'cantidad' => $cantidad,
                'descuento' => $descuento,
                'precio' => $producto->precUniProd,
                'precio_con_descuento' => $precioConDescuento,
                'subtotal' => $cantidad * $precioConDescuento,
            ];

            $subtotal += $cantidad * $precioConDescuento;
        }

        $igv = $subtotal * 0.18;
        $total = $subtotal + $igv;

        return view('admin.cotizaciones.show', compact(
            'productosSeleccionados',
            'subtotal',
            'igv',
            'total',
            'cliente',
            'cliente_id',
            'request'
        ));
    }

    // Métodos para devoluciones
    public function devoluciones()
    {
        $devoluciones = Devolucion::with(['venta' => function($query) {
                $query->with(['clienteNatural', 'clienteJuridica']);
            }])
            ->orderBy('fechDev', 'desc')
            ->get();
        
        return view('admin.devoluciones.index', compact('devoluciones'));
    }

    public function crearDevolucion($ventaId)
    {
        $venta = Venta::with(['detalleVentas.producto'])->findOrFail($ventaId);
        return view('admin.devoluciones.create', compact('venta'));
    }

    public function guardarDevolucion(Request $request, $ventaId)
    {
        $request->validate([
            'productos' => 'required|array|min:1',
            'productos.*.IDProd' => 'required|exists:productos,IDProd',
            'productos.*.cantidad' => 'required|integer|min:1',
            'motivDev' => 'required|string',
        ]);

        DB::beginTransaction();
        try {
            if (Devolucion::where('IDVent', $ventaId)->exists()) {
                throw new \Exception("Ya existe una devolución registrada para esta venta");
            }
            $venta = Venta::findOrFail($ventaId);
            $user = Auth::user();
            $totalRembolso = 0;

            // Verificar que los productos pertenezcan a la venta
            foreach ($request->productos as $producto) {
                $productoId = $producto['IDProd'];
                $cantidad = $producto['cantidad'];

                $detalle = DetalleVenta::where('IDVent', $ventaId)
                    ->where('IDProd', $productoId)
                    ->first();

                if (!$detalle) {
                    $nombreProducto = Producto::find($productoId)->nombreProd ?? 'ID '.$productoId;
                    throw new \Exception("El producto '$nombreProducto' no pertenece a esta venta");
                }

                if ($cantidad >  $cantidadSinDescuento = $detalle->prec_uni != 0 
                                ? round(($detalle->subtotal / (1 - ($detalle->descuento ?? 0)/100)) / $detalle->prec_uni)
                                : 0 ) {
                    throw new \Exception("La cantidad a devolver ($cantidad) no puede ser mayor a la vendida ({intval($detalle->subtotal / $detalle->prec_uni)})");
                }

                $subtotal = $cantidad * $detalle->prec_uni;
                $totalRembolso += $subtotal;
            }

            // Crear la devolución
            $devolucion = new Devolucion();
            $devolucion->fechDev = now();
            $devolucion->motivDev = $request->motivDev;
            $devolucion->totalRembDev = $totalRembolso;
            $devolucion->IDUsu = $user->id;
            $devolucion->IDVent = $ventaId;
            
            $devolucion->save();

            // Actualizar stock de productos
            foreach ($request->productos as $producto) {
                Producto::where('IDProd', $producto['IDProd'])
                    ->increment('stockProd', $producto['cantidad']);
            }

            DB::commit();
            
            return redirect()->route('admin.devoluciones')
                ->with('success', 'Devolución registrada correctamente');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    // Métodos para productos
    public function productos(Request $request)
    {
        $query = Producto::with(['categoria'])
            ->orderBy('nomProd', 'asc');
            
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where('nomProd', 'like', "%{$search}%");
        }
        
        $productos = $query->get();
            
        return view('admin.productos.index', compact('productos'));
    }

    public function crearProducto()
    {
        $categorias = Categoria::orderBy('nomCat', 'asc')->get();
        
        return view('admin.productos.create', compact('categorias'));
    }

    public function guardarProducto(Request $request)
    {
        $request->validate([
            'nomProd' => 'required|string|max:100|unique:productos,nomProd',
            'estProd' => 'required|in:activo,inactivo',
            'uniMedProd' => 'required|string|max:20',
            'precUniProd' => 'required|numeric|min:0',
            'precUniComProd' => 'required|numeric|min:0',
            'stockProd' => 'required|integer|min:0',
            'cantComProd' => 'required|integer|min:1',
            'stockMinProd' => 'required|integer|min:0',
            'IDCat' => 'required|exists:categorias,IDCat',
        ], [
            // Mensajes personalizados de error
            'nomProd.unique' => 'El nombre del producto ya existe en el sistema.',
            'nomProd.required' => 'El nombre del producto es obligatorio.',
            'precUniProd.min' => 'El precio debe ser mayor o igual a 0.',
            'stockProd.min' => 'El stock debe ser mayor o igual a 0.',
        ]);
     
        Producto::create($request->all());

        return redirect()->route('admin.productos')
            ->with('success', 'Producto creado correctamente');
    }

    public function editarProducto($id)
    {
        $producto = Producto::findOrFail($id);
        $categorias = Categoria::orderBy('nomCat', 'asc')->get();
        
        return view('admin.productos.edit', compact('producto', 'categorias'));
    }

    public function actualizarProducto(Request $request, $id)
    {
        $request->validate([
            'nomProd' => 'required|string|max:100|unique:productos,nomProd,'.$id.',IDProd',
            'estProd' => 'required|in:activo,inactivo',
            'uniMedProd' => 'required|string|max:20',
            'precUniProd' => 'required|numeric|min:0',
            'precUniComProd' => 'required|numeric|min:0',
            'stockProd' => 'required|integer|min:0',
            'stockMinProd' => 'required|integer|min:0',
            'IDCat' => 'required|exists:categorias,IDCat',
        ]);

        $producto = Producto::findOrFail($id);
        $producto->update($request->all());

        return redirect()->route('admin.productos')
            ->with('success', 'Producto actualizado correctamente');
    }

    public function eliminarProducto($id)
    {
        $producto = Producto::findOrFail($id);
        
        // Verificar si el producto tiene ventas asociadas
        if ($producto->detalleVentas()->count() > 0) {
            return redirect()->route('admin.productos')
                ->with('error', 'No se puede eliminar el producto porque tiene ventas asociadas');
        }
        
        $producto->delete();

        return redirect()->route('admin.productos')
            ->with('success', 'Producto eliminado correctamente');
    }

    // Métodos para categorías
    public function categorias()
    {
        $categorias = Categoria::orderBy('nomCat', 'asc')->get();
        return view('admin.categorias.index', compact('categorias'));
    }

    public function crearCategoria()
    {
        return view('admin.categorias.create');
    }

    public function guardarCategoria(Request $request)
    {
        $request->validate([
            'nomCat' => 'required|string|max:100|unique:categorias,nomCat',
            'descCat' => 'nullable|string',
        ], [
            'nomCat.required' => 'El nombre de la categoría es obligatorio.',
            'nomCat.unique' => 'El nombre de la categoría ya existe en el sistema.',
        ]);

        Categoria::create($request->all());

        return redirect()->route('admin.categorias')
            ->with('success', 'Categoría creada correctamente');
    }

    public function editarCategoria($id)
    {
        $categoria = Categoria::findOrFail($id);
        return view('admin.categorias.edit', compact('categoria'));
    }

    public function actualizarCategoria(Request $request, $id)
    {
        $request->validate([
            'nomCat' => 'required|string|max:100|unique:categorias,nomCat,'.$id.',IDCat',
            'descCat' => 'nullable|string',
        ]);

        $categoria = Categoria::findOrFail($id);
        $categoria->update($request->all());

        return redirect()->route('admin.categorias')
            ->with('success', 'Categoría actualizada correctamente');
    }

    public function eliminarCategoria($id)
    {
        $categoria = Categoria::findOrFail($id);
        
        // Verificar si la categoría tiene productos asociados
        if ($categoria->productos()->count() > 0) {
            return redirect()->route('admin.categorias')
                ->with('error', 'No se puede eliminar la categoría porque tiene productos asociados');
        }
        
        $categoria->delete();

        return redirect()->route('admin.categorias')
            ->with('success', 'Categoría eliminada correctamente');
    }

    // Métodos para empleados
    public function empleados(Request $request)
    {
        $query = Empleado::with('user')
            ->orderBy('apelEmp', 'asc');
            
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nomEmp', 'like', "%{$search}%")
                  ->orWhere('apelEmp', 'like', "%{$search}%")
                  ->orWhere('docIdenEmp', 'like', "%{$search}%");
            });
        }
        
        $empleados = $query->get();
            
        return view('admin.empleados.index', compact('empleados'));
    }

    public function crearEmpleado()
    {
        return view('admin.empleados.create');
    }

    public function guardarEmpleado(Request $request)
    {
        $request->validate([
            'nomEmp' => 'required|string|max:20',
            'apelEmp' => 'required|string|max:25',
            'docIdenEmp' => 'required|string|max:12|unique:empleados,docIdenEmp',
            'telEmp' => 'nullable|string|max:12',
            'dirEmp' => 'nullable|string',
            'usuario' => 'required|string|max:50|unique:users,usuario',
            'contraUsu' => 'required|string|min:8',
            'rolUsu' => 'required|in:admin,vendedor',
        ], [
            'nomEmp.required' => 'El nombre del empleado es obligatorio.',
            'apelEmp.required' => 'El apellido del empleado es obligatorio.',
            'docIdenEmp.required' => 'El documento de identidad es obligatorio.',
            'docIdenEmp.max' => 'El documento no debe exceder los 12 caracteres.',
            'docIdenEmp.unique' => 'Este documento de identidad ya está registrado.',
            'usuario.required' => 'El nombre de usuario es obligatorio.',
            'usuario.unique' => 'Este nombre de usuario ya está en uso.',
            'contraUsu.required' => 'La contraseña es obligatoria.',
            'contraUsu.min' => 'La contraseña debe tener al menos 8 caracteres.',
        ]);

        // Crear empleado
        $empleado = Empleado::create([
            'nomEmp' => $request->nomEmp,
            'apelEmp' => $request->apelEmp,
            'docIdenEmp' => $request->docIdenEmp,
            'telEmp' => $request->telEmp,
            'dirEmp' => $request->dirEmp,
        ]);

        // Crear usuario asociado
        User::create([
            'usuario' => $request->usuario,
            'contraUsu' => Hash::make($request->contraUsu),
            'rolUsu' => $request->rolUsu,
            'IDEmp' => $empleado->IDEmp,
        ]);

        return redirect()->route('admin.empleados')
            ->with('success', 'Empleado y usuario creados correctamente');
    }

    public function editarEmpleado($id)
    {
        $empleado = Empleado::with('user')->findOrFail($id);
        return view('admin.empleados.edit', compact('empleado'));
    }

    public function actualizarEmpleado(Request $request, $id)
    {
        $empleado = Empleado::with('user')->findOrFail($id);
        
        $request->validate([
            'nomEmp' => 'required|string|max:100',
            'apelEmp' => 'required|string|max:100',
            'docIdenEmp' => 'required|string|max:15|unique:empleados,docIdenEmp,'.$id.',IDEmp',
            'telEmp' => 'nullable|string|max:15',
            'dirEmp' => 'nullable|string',
            'usuario' => 'required|string|max:50|unique:users,usuario,'.$empleado->user->IDUsu.',IDUsu',
            'rolUsu' => 'required|in:admin,vendedor',
        ]);

        // Actualizar empleado
        $empleado->update([
            'nomEmp' => $request->nomEmp,
            'apelEmp' => $request->apelEmp,
            'docIdenEmp' => $request->docIdenEmp,
            'telEmp' => $request->telEmp,
            'dirEmp' => $request->dirEmp,
        ]);

        // Actualizar usuario
        $empleado->user->update([
            'usuario' => $request->usuario,
            'rolUsu' => $request->rolUsu,
        ]);

        // Actualizar contraseña si se proporcionó
        if ($request->filled('contraUsu')) {
            $empleado->user->update([
                'contraUsu' => Hash::make($request->contraUsu),
            ]);
        }

        return redirect()->route('admin.empleados')
            ->with('success', 'Empleado actualizado correctamente');
    }

    public function eliminarEmpleado($id)
    {
        $empleado = Empleado::with('user')->findOrFail($id);
        
        // Verificar si el empleado tiene ventas asociadas
        if ($empleado->user && $empleado->user->ventas()->count() > 0) {
            return redirect()->route('admin.empleados')
                ->with('error', 'No se puede eliminar el empleado porque tiene ventas asociadas');
        }
        
        // Eliminar usuario primero
        if ($empleado->user) {
            $empleado->user->delete();
        }
        
        // Luego eliminar empleado
        $empleado->delete();

        return redirect()->route('admin.empleados')
            ->with('success', 'Empleado eliminado correctamente');
    }

    // Métodos para reportes
    public function reportes()
    {
        return view('admin.reportes.index');
    }

    public function generarReporteVentas(Request $request)
    {
        $request->validate([
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
        ]);

        $fechaInicio = $request->fecha_inicio;
        $fechaFin = $request->fecha_fin;

        $ventas = Venta::with(['clienteNatural', 'clienteJuridica', 'usuario.empleado', 'detalleVentas.producto'])
            ->whereBetween('fechVent', [$fechaInicio, $fechaFin])
            ->has('usuario')
            ->orderBy('fechVent', 'asc')
            ->get();
            
        $totalVentas = $ventas->sum('totalVent');
        $totalProductosVendidos = $ventas->sum(function($venta) {
            return $venta->detalleVentas->sum(function($detalle) {
                return $detalle->subtotal / $detalle->prec_uni; // Cálculo dinámico
            });
        });

        return view('admin.reportes.ventas', compact(
            'ventas',
            'fechaInicio',
            'fechaFin',
            'totalVentas',
            'totalProductosVendidos'
        ));
    }

    public function generarReporteProductos(Request $request)
    {
        $request->validate([
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
        ]);

        $fechaInicio = $request->fecha_inicio;
        $fechaFin = $request->fecha_fin;

        $productos = Producto::with(['categoria'])
            ->whereHas('detalleVentas', function($query) use ($fechaInicio, $fechaFin) {
                $query->whereHas('venta', function($q) use ($fechaInicio, $fechaFin) {
                    $q->whereBetween('fechVent', [$fechaInicio, $fechaFin]);
                });
            })
            ->withCount(['detalleVentas as ventas_count' => function($query) use ($fechaInicio, $fechaFin) {
                $query->selectRaw('sum(cant)')
                    ->whereHas('venta', function($q) use ($fechaInicio, $fechaFin) {
                        $q->whereBetween('fechVent', [$fechaInicio, $fechaFin]);
                    });
            }])
            ->withSum(['detalleVentas as ventas_total' => function($query) use ($fechaInicio, $fechaFin) {
                $query->selectRaw('sum(subtotal)')
                    ->whereHas('venta', function($q) use ($fechaInicio, $fechaFin) {
                        $q->whereBetween('fechVent', [$fechaInicio, $fechaFin]);
                    });
            }], 'subtotal')
            
            ->orderBy('ventas_count', 'desc')
            ->get();

        return view('admin.reportes.productos', compact(
            'productos',
            'fechaInicio',
            'fechaFin'
        ));
    }

    // Métodos para clientes
    public function clientesNaturales()
    {
        $clientes = ClienteNatural::orderBy('apelClieNat', 'asc')->get();
        return view('admin.clientes.naturales', compact('clientes'));
    }

    public function clientesJuridicos()
    {
        $clientes = ClienteJuridica::orderBy('razSociCieJuri', 'asc')->get();
        return view('admin.clientes.juridicos', compact('clientes'));
    }

    // Métodos para ventas
    public function ventas()
    {
        $ventas = Venta::with(['clienteNatural', 'clienteJuridica', 'usuario.empleado'])
            ->orderBy('fechVent', 'desc')
            ->get();
            
        return view('admin.ventas.index', compact('ventas'));
    }

    public function crearVenta()
    {
        $productos = Producto::where('estProd', 'activo')->where('stockProd', '>', 0)->get();
        $clientesNaturales = ClienteNatural::all();
        $clientesJuridicos = ClienteJuridica::all();
        
        return view('admin.ventas.create', compact('productos', 'clientesNaturales', 'clientesJuridicos'));
    }

    public function guardarVenta(Request $request)
    {
        $request->validate([
            'productos' => 'required|array|min:1',
            'productos.*.IDProd' => 'required|exists:productos,IDProd',
            'productos.*.cantidad' => 'required|integer|min:1',
            'productos.*.descuento' => 'nullable|numeric|min:0|max:100',
            'metPagVent' => 'required|in:efectivo,tarjeta,yape,transferencia',
            'tipo_cliente' => 'required|in:natural,juridico,none',
            'cliente_id' => 'nullable|string|regex:/^([NJ]-\d+)$/',
            'tipo_comprobante' => 'required|in:boleta,factura,proforma',
        ]);
        
        $user = Auth::user();
        $subtotal = 0;
        $detalles = [];

        // Verificar stock y calcular subtotal
        foreach ($request->productos as $item) {
            $producto = Producto::find($item['IDProd']);
            $cantidad = $item['cantidad'];
            $descuento = $item['descuento'] ?? 0;

            if ($producto->stockProd < $cantidad) {
                return back()->withErrors(['stock' => "No hay suficiente stock para el producto {$producto->nomProd}"]);
            }

            $subtotalSinDescuento = $cantidad * $producto->precUniProd;
            $montoDescuento = $subtotalSinDescuento * ($descuento / 100);
            $subtotal += $subtotalSinDescuento - $montoDescuento;
            
        }

        $igv = $subtotal * 0.18;
        $total = $subtotal + $igv;

        // Crear la venta
        $venta = new Venta();
        $venta->fechVent = now();
        $venta->totalVent = $total;
        $venta->metPagVent = $request->metPagVent;
        $venta->IDUsu = auth()->id();

        // Si es cliente no registrado
        if ($request->tipo_cliente === 'none') {
            session([
                'cliente_temporal' => [
                    'nombre' => $request->nombre_cliente,
                    'apellido' => $request->apellido_cliente,
                ]
            ]);
        }
        elseif ($request->tipo_cliente === 'natural' && $request->cliente_id) {
            $idParts = explode('-', $request->cliente_id);
            if (count($idParts) === 2 && $idParts[0] === 'N') {
                $venta->IDClieNat = (int)$idParts[1]; 
            }
        } elseif ($request->tipo_cliente === 'juridico' && $request->cliente_id) {
            $idParts = explode('-', $request->cliente_id);
            if (count($idParts) === 2 && $idParts[0] === 'J') {
                $venta->IDClieJuri = (int)$idParts[1]; 
            }
        }

        $venta->save();

        // Crear detalles de venta y actualizar stock
        foreach ($request->productos as $item) {
            $producto = Producto::find($item['IDProd']);
            $cantidad = $item['cantidad'];
            $descuento = $item['descuento'] ?? 0;
            $precio = $producto->precUniProd;
            
            $subtotalSinDescuento = $cantidad * $precio;
            $montoDescuento = $subtotalSinDescuento * ($descuento / 100);
            $subtotalItem = $subtotalSinDescuento - $montoDescuento;

            DetalleVenta::create([
                'prec_uni' => $precio,
                'subtotal' => $subtotalItem,
                'descuento' => $descuento,
                'IDProd' => $producto->IDProd,
                'IDVent' => $venta->IDVent,
            ]);

            // Actualizar stock
            $producto->stockProd -= $cantidad;
            $producto->save();
        }
        
        // Crear comprobante
        $comprobante = new Comprobante();
        $comprobante->tipCompr = $request->tipo_comprobante;
        $comprobante->IDVent = $venta->IDVent;
        $comprobante->save();

        // Validar con SUNAT si es necesario
        if (in_array($request->tipo_comprobante, ['boleta', 'factura'])) {
            $this->validarConSunat($venta, $comprobante);
        }

        return redirect()->route('admin.ventas.show', $venta->IDVent)
            ->with('success', 'Venta registrada correctamente');
    }

    public function verVenta($id)
    {
        $venta = Venta::with(['clienteNatural', 'clienteJuridica', 'usuario.empleado', 'detalleVentas.producto', 'comprobantes'])
            ->has('usuario')    
            ->findOrFail($id);
            
        return view('admin.ventas.show', compact('venta'));
    }

    // Método para consultar DNI (API)
    public function consultarDni(Request $request)
    {
        $request->validate(['dni' => 'required|digits:8']);
        
        $data = $this->sunatService->consultarDni($request->dni);
        
        if (isset($data['error'])) {
            \Log::warning("Fallo consulta DNI {$request->dni}: " . ($data['api_error'] ?? ''));
            return response()->json([
                'success' => false,
                'error' => $data['error'],
                'debug' => $data['api_error'] ?? null
            ], 400);
        }
        
        return response()->json([
            'success' => true,
            'nombres' => $data['nombres'],
            'apellidos' => trim($data['apellidoPaterno'] . ' ' . $data['apellidoMaterno']),
            'direccion' => $data['direccion']
        ]);
    }

    // Método para consultar RUC (API)
    public function consultarRuc(Request $request)
    {
        $request->validate(['ruc' => 'required|digits:11']);
        
        $data = $this->sunatService->consultarRuc($request->ruc);
        
        if (isset($data['error'])) {
            \Log::error("Fallo consulta RUC {$request->ruc}: " . ($data['api_error'] ?? ''));
            return response()->json([
                'success' => false,
                'error' => $data['error'],
                'debug' => $data['api_error'] ?? null
            ], 400);
        }
        
        return response()->json([
            'success' => true,
            'razon_social' => $data['razonSocial'] ?? $data['nombre'] ?? '',
            'direccion' => $data['direccion'] ?? $data['direccionCompleta'] ?? '',
            'estado' => $data['estado'] ?? $data['condicion'] ?? 'ACTIVO'
        ]);
    }

    // Método para validar con SUNAT
    protected function validarConSunat(Venta $venta, Comprobante $comprobante)
    {
        $cliente = $venta->IDClieNat 
            ? ClienteNatural::find($venta->IDClieNat)
            : ClienteJuridica::find($venta->IDClieJuri);

        if (!$cliente) {
            Log::error('Cliente no encontrado para la venta ID: ' . $venta->IDVent);
            return false;
        }

        $nombreCliente = $venta->IDClieNat 
            ? $cliente->nomClieNat . ' ' . $cliente->apelClieNat 
            : $cliente->razSociClieJuri;

        $tipoDoc = $venta->IDClieNat ? '1' : '6'; // 1 = DNI, 6 = RUC
        $nroDoc = $venta->IDClieNat ? $cliente->docIdenClieNat : $cliente->rucClieJuri;

        $serie = $comprobante->tipCompr === 'factura' ? 'FFF1' : 'BBB1';
        $tipoComprobante = $comprobante->tipCompr === 'factura' ? '1' : '2';

        $items = [];
        $total_gravada = 0;
        $total_igv = 0;

        foreach ($venta->detalleVentas as $detalle) {
            $producto = $detalle->producto;
            $cantidad = $detalle->subtotal / $detalle->prec_uni;

            $valor_unitario = round($detalle->prec_uni / 1.18, 2);
            $subtotal = round($valor_unitario * $cantidad, 2);
            $igv = round($subtotal * 0.18, 2);
            $total = round($subtotal + $igv, 2);

            $total_gravada += $subtotal;
            $total_igv += $igv;

            $items[] = [
                'unidad_de_medida' => 'NIU',
                'codigo' => $producto->IDProd,
                'descripcion' => $producto->nomProd,
                'cantidad' => $cantidad,
                'valor_unitario' => number_format($valor_unitario, 2, '.', ''),
                'precio_unitario' => number_format($detalle->prec_uni, 2, '.', ''),
                'subtotal' => number_format($subtotal, 2, '.', ''),
                'tipo_de_igv' => '1',
                'igv' => number_format($igv, 2, '.', ''),
                'total' => number_format($total, 2, '.', ''),
            ];
        }

        $data = [
            'operacion' => 'generar_comprobante',
            'tipo_de_comprobante' => $tipoComprobante,
            'serie' => $serie,
            'numero' => $comprobante->IDCompr,
            'sunat_transaction' => '1',
            'cliente_tipo_de_documento' => $tipoDoc,
            'cliente_numero_de_documento' => $nroDoc,
            'cliente_denominacion' => $nombreCliente,
            'fecha_de_emision' => $venta->fechVent->format('Y-m-d'),
            'moneda' => '1',
            'total_gravada' => number_format($total_gravada, 2, '.', ''),
            'total_igv' => number_format($total_igv, 2, '.', ''),
            'total' => number_format($total_gravada + $total_igv, 2, '.', ''),
            'items' => $items,
        ];

        $response = Http::withHeaders([
            'Authorization' => 'Token ' . env('NUBEFACT_TOKEN'),
        ])->post(env('NUBEFACT_URL'), $data);

        $respuesta = $response->json();
            

        if (isset($respuesta['errors'])) {
            Log::error('Error al emitir comprobante SUNAT: ', $respuesta);
            return false;
        }

        $venta->codSunatVent = $respuesta['serie'] . '-' . str_pad($respuesta['numero'], 8, '0', STR_PAD_LEFT);
        $venta->save();

        return true;
    }
}