<?php

namespace App\Http\Controllers;

use App\Models\ClienteJuridica;
use App\Models\ClienteNatural;
use App\Models\Producto;
use App\Models\Venta;
use App\Models\DetalleVenta;
use App\Models\Comprobante;
use App\Models\Devolucion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use App\Services\SunatService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class VendedorController extends Controller
{   
    protected $sunatService;
    public function __construct(SunatService $sunatService)
    {
        $this->middleware('auth');
        $this->middleware('role:vendedor');
        $this->sunatService = $sunatService;
    }

    public function dashboard()
    {
        $user = Auth::user();
        
        $ventasHoy = Venta::where('IDUsu', auth()->id())
            ->whereDate('fechVent', today())
            ->count();
        
        $totalVentasHoy = Venta::where('IDUsu', auth()->id())
            ->whereDate('fechVent', today())
            ->sum('totalVent');

        // Agregar consulta para las últimas ventas
        $ultimasVentas = Venta::where('IDUsu', auth()->id())
            ->orderBy('fechVent', 'desc')
            ->take(5) 
            ->get();

        return view('vendedor.dashboard', compact('ventasHoy', 'totalVentasHoy', 'ultimasVentas'));
    }

    // Métodos para cotizaciones
    public function cotizaciones()
    {
        $productos = Producto::where('estProd', 'activo')->where('stockProd', '>', 0)->get();
        $clientesNaturales = ClienteNatural::all();
        $clientesJuridicos = ClienteJuridica::all();
        
        return view('vendedor.cotizaciones.index', compact('productos', 'clientesNaturales', 'clientesJuridicos'));
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

        return view('vendedor.cotizaciones.show', compact(
            'productosSeleccionados',
            'subtotal',
            'igv',
            'total',
            'cliente',
            'cliente_id',
            'request'
        ));
    }

    // Métodos para ventas
    public function ventas()
    {
        $ventas = Venta::with(['clienteNatural', 'clienteJuridica', 'usuario.empleado'])
            ->orderBy('fechVent', 'desc')
            ->get();
            
        return view('vendedor.ventas.index', compact('ventas'));
    }

    public function crearVenta()
    {
        $productos = Producto::where('estProd', 'activo')->where('stockProd', '>', 0)->get();
        $clientesNaturales = ClienteNatural::all();
        $clientesJuridicos = ClienteJuridica::all();
        
        return view('vendedor.ventas.create', compact('productos', 'clientesNaturales', 'clientesJuridicos'));
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

        return redirect()->route('vendedor.ventas.show', $venta->IDVent)
            ->with('success', 'Venta registrada correctamente');
    }

    public function verVenta($id)
    {
        $venta = Venta::with(['detalleVentas.producto', 'usuario.empleado', 'clienteNatural', 'clienteJuridica', 'comprobantes'])
            
            ->findOrFail($id);
        
        return view('vendedor.ventas.show', compact('venta'));
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
    // Métodos para devoluciones
    public function devoluciones()
    {
        $devoluciones = Devolucion::with(['venta' => function($query) {
                $query->with(['clienteNatural', 'clienteJuridica']);
            }])
            ->orderBy('fechDev', 'desc')
            ->get();
        
        return view('vendedor.devoluciones.index', compact('devoluciones'));
    }

    public function crearDevolucion($ventaId)
    {
        $venta = Venta::with(['detalleVentas.producto'])->findOrFail($ventaId);
        return view('vendedor.devoluciones.create', compact('venta'));
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
            
            return redirect()->route('vendedor.devoluciones')
                ->with('success', 'Devolución registrada correctamente');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    // Métodos para cierre de caja
    public function cierreCaja()
    {
        $user = Auth::user();
        $hoy = now()->format('Y-m-d');
        
        $ventas = Venta::where('IDUsu', auth()->id())
            ->whereDate('fechVent', $hoy)
            ->get();

        $totalEfectivo = $ventas->where('metPagVent', 'efectivo')->sum('totalVent');
        $totalTarjeta = $ventas->where('metPagVent', 'tarjeta')->sum('totalVent');
        $totalYape = $ventas->where('metPagVent', 'yape')->sum('totalVent');
        $totalTransferencia = $ventas->where('metPagVent', 'transferencia')->sum('totalVent');
        $totalGeneral = $ventas->sum('totalVent');

        $boletas = Comprobante::where('tipCompr', 'boleta')
            ->whereHas('venta', function($q) use ($user, $hoy) {
                $q->where('IDUsu', $user->id)->whereDate('fechVent', $hoy);
            })
            ->count();

        $facturas = Comprobante::where('tipCompr', 'factura')
            ->whereHas('venta', function($q) use ($user, $hoy) {
                $q->where('IDUsu', $user->id)->whereDate('fechVent', $hoy);
            })
            ->count();
        $proformas = Comprobante::where('tipCompr', 'proforma')
            ->whereHas('venta', function($q) use ($user, $hoy) {
                $q->where('IDUsu', $user->id)->whereDate('fechVent', $hoy);
            })
            ->count();
        
        return view('vendedor.cierre-caja', compact(
            'ventas',
            'totalEfectivo',
            'totalTarjeta',
            'totalYape',
            'totalTransferencia',
            'totalGeneral',
            'boletas',
            'facturas',
            'proformas',
            'hoy'
        ));
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