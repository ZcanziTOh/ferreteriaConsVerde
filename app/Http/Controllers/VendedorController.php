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

class VendedorController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:vendedor');
    }

    public function dashboard()
    {
        $user = Auth::user();
        
        $ventasHoy = Venta::where('IDUsu', $user->id)
            ->whereDate('fechVent', today())
            ->count();
        
        $totalVentasHoy = Venta::where('IDUsu', $user->id)
            ->whereDate('fechVent', today())
            ->sum('totalVent');

        // Agregar consulta para las últimas ventas
        $ultimasVentas = Venta::where('IDUsu', $user->id)
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
            'tipo_cliente' => 'required|in:natural,juridico',
            'cliente_id' => 'nullable|string|regex:/^([NJ]-\d+)$/',
            'observaciones' => 'nullable|string',
        ]);

        $productosSeleccionados = [];
        $subtotal = 0;

        foreach ($request->productos as $item) {
            $producto = Producto::find($item['IDProd']);
            $cantidad = $item['cantidad'];

            if ($producto->stockProd < $cantidad) {
                return back()->withErrors(['stock' => "No hay suficiente stock para el producto {$producto->nomProd}"]);
            }

            $productosSeleccionados[] = [
                'producto' => $producto,
                'cantidad' => $cantidad,
                'precio' => $producto->precUniProd,
                'subtotal' => $cantidad * $producto->precUniProd,
            ];

            $subtotal += $cantidad * $producto->precUniProd;
        }

        $igv = $subtotal * 0.18;
        $total = $subtotal + $igv;

        $cliente = null;
        if ($request->tipo_cliente === 'natural' && $request->cliente_id) {
            $cliente = ClienteNatural::find($request->cliente_id);
        } elseif ($request->tipo_cliente === 'juridico' && $request->cliente_id) {
            $cliente = ClienteJuridica::find($request->cliente_id);
        }

        return view('vendedor.cotizaciones.show', compact(
            'productosSeleccionados',
            'subtotal',
            'igv',
            'total',
            'cliente',
            'request'
        ));
    }

    // Métodos para ventas
    public function ventas()
    {
        $user = Auth::user();
        $ventas = Venta::with(['clienteNatural', 'clienteJuridica', 'comprobantes'])
            ->where('IDUsu', $user->id)
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
            'metPagVent' => 'required|in:efectivo,tarjeta,yape,transferencia',
            'tipo_cliente' => 'required|in:natural,juridico,none',
            'cliente_id' => 'nullable|string|regex:/^([NJ]-\d+)$/',
            'tipo_comprobante' => 'required|in:boleta,factura',
        ]);

        $user = Auth::user();
        $subtotal = 0;
        $detalles = [];

        // Verificar stock y calcular subtotal
        foreach ($request->productos as $item) {
            $producto = Producto::find($item['IDProd']);
            $cantidad = $item['cantidad'];

            if ($producto->stockProd < $cantidad) {
                return back()->withErrors(['stock' => "No hay suficiente stock para el producto {$producto->nomProd}"]);
            }

            $subtotal += $cantidad * $producto->precUniProd;
        }

        $igv = $subtotal * 0.18;
        $total = $subtotal + $igv;

        // Crear la venta
        $venta = new Venta();
        $venta->fechVent = now();
        $venta->totalVent = $total;
        $venta->metPagVent = $request->metPagVent;
        $venta->IDUsu = auth()->id();

        if ($request->tipo_cliente === 'natural' && $request->cliente_id) {
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
            $precio = $producto->precUniProd;
            $subtotalItem = $cantidad * $precio;

            DetalleVenta::create([
                'prec_uni' => $precio,
                'subtotal' => $subtotalItem,
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

    // Métodos para clientes
    public function clientesNaturales()
    {
        $clientes = ClienteNatural::all();
        return view('vendedor.clientes.naturales', compact('clientes'));
    }

    public function crearClienteNatural()
    {
        return view('vendedor.clientes.crear-natural');
    }

    public function guardarClienteNatural(Request $request)
    {
        $request->validate([
            'docIdenClieNat' => 'required|string|max:15|unique:cliente_natural,docIdenClieNat',
            'nomClieNat' => 'required|string|max:100',
            'apelClieNat' => 'required|string|max:100',
        ]);

        ClienteNatural::create($request->all());

        return redirect()->route('vendedor.clientes.naturales')
            ->with('success', 'Cliente natural registrado correctamente');
    }

    public function clientesJuridicos()
    {
        $clientes = ClienteJuridica::all();
        return view('vendedor.clientes.juridicos', compact('clientes'));
    }

    public function crearClienteJuridico()
    {
        return view('vendedor.clientes.crear-juridico');
    }

    public function guardarClienteJuridico(Request $request)
    {
        $request->validate([
            'razSociClieJuri' => 'required|string|max:100',
            'dirfiscClieJuri' => 'required|string',
            'rucClieJuri' => 'required|string|size:11|unique:cliente_juridica,rucCieJuri',
            'nomComClieJuri' => 'nullable|string|max:100',
            'persRespClieJuri' => 'nullable|string|max:100',
            'rubrClieJuri' => 'nullable|string|max:100',
        ]);

        ClienteJuridica::create($request->all());

        return redirect()->route('vendedor.clientes.juridicos')
            ->with('success', 'Cliente jurídico registrado correctamente');
    }

    // Métodos para devoluciones
    public function devoluciones()
    {
        $user = Auth::user();
        $devoluciones = Devolucion::with(['venta', 'venta.clienteNatural', 'venta.clienteJuridica'])
            ->where('IDUsu', $user->id)
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

        $venta = Venta::findOrFail($ventaId);
        $user = Auth::user();
        $totalRembolso = 0;

        // Verificar que los productos pertenezcan a la venta
        foreach ($request->productos as $item) {
            $detalle = DetalleVenta::where('IDVent', $ventaId)
                ->where('IDProd', $item['IDProd'])
                ->first();

            if (!$detalle) {
                return back()->withErrors(['productos' => 'Uno o más productos no pertenecen a esta venta']);
            }

            $totalRembolso += $item['cantidad'] * $detalle->prec_uni;
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
        foreach ($request->productos as $item) {
            $producto = Producto::find($item['IDProd']);
            $producto->stockProd += $item['cantidad'];
            $producto->save();
        }

        return redirect()->route('vendedor.devoluciones')
            ->with('success', 'Devolución registrada correctamente');
    }

    // Métodos para cierre de caja
    public function cierreCaja()
    {
        $user = Auth::user();
        $hoy = now()->format('Y-m-d');
        
        $ventas = Venta::where('IDUsu', $user->id)
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

        return view('vendedor.cierre-caja', compact(
            'ventas',
            'totalEfectivo',
            'totalTarjeta',
            'totalYape',
            'totalTransferencia',
            'totalGeneral',
            'boletas',
            'facturas',
            'hoy'
        ));
    }

    // Método para validar con SUNAT
    protected function validarConSunat(Venta $venta, Comprobante $comprobante)
    {
        // Aquí implementarías la lógica para conectar con la API de SUNAT
        // Esto es un ejemplo simplificado
        
        $cliente = null;
        $tipoDocumento = '';
        $numeroDocumento = '';
        
        if ($venta->IDClieNat) {
            $cliente = ClienteNatural::find($venta->IDClieNat);
            $tipoDocumento = 'DNI';
            $numeroDocumento = $cliente->docIdenClieNat;
        } elseif ($venta->IDCieJuri) {
            $cliente = ClienteJuridica::find($venta->IDCieJuri);
            $tipoDocumento = 'RUC';
            $numeroDocumento = $cliente->rucCieJuri;
        }
        
        // Datos para enviar a SUNAT
        $datosSunat = [
            'tipo_comprobante' => $comprobante->tipCompr,
            'serie' => 'F001',
            'numero' => $comprobante->IDCompr,
            'fecha_emision' => $venta->fechVent->format('Y-m-d'),
            'total' => $venta->totalVent,
            'cliente_tipo_documento' => $tipoDocumento,
            'cliente_numero_documento' => $numeroDocumento,
            'cliente_denominacion' => $cliente ? ($cliente instanceof ClienteNatural ? 
                $cliente->nomClieNat . ' ' . $cliente->apelClieNat : 
                $cliente->raz_socCieJuri) : '',
        ];
        
        // En producción, aquí harías una petición real a la API de SUNAT
        // $response = Http::post('https://api.sunat.com/validar', $datosSunat);
        
        // Simulamos una respuesta exitosa
        $response = [
            'success' => true,
            'codigo' => 'SUNAT-' . strtoupper(uniqid()),
            'mensaje' => 'Comprobante validado correctamente',
        ];
        
        if ($response['success']) {
            $venta->codSunatVent = $response['codigo'];
            $venta->save();
            
            return true;
        }
        
        return false;
    }
}