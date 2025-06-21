<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Empleado;
use App\Models\Producto;
use App\Models\Proveedor;
use App\Models\Venta;
use App\Models\Pedido;
use App\Models\DetallePedido;
use App\Models\ClienteNatural;
use App\Models\ClienteJuridica;
use App\Models\Devolucion;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin');
    }

    public function dashboard()
    {
        $totalProductos = Producto::count();
        $totalProveedores = Proveedor::count();
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
            'totalProveedores',
            'totalVentas',
            'totalEmpleados',
            'productosBajoStock',
            'ultimasVentas'
        ));
    }

    // Métodos para productos
    public function productos()
    {
        $productos = Producto::with(['categoria', 'proveedor'])
            ->orderBy('nomProd', 'asc')
            ->get();
            
        return view('admin.productos.index', compact('productos'));
    }

    public function crearProducto()
    {
        $categorias = Categoria::orderBy('nomCat', 'asc')->get();
        $proveedores = Proveedor::orderBy('razonSocialProv', 'asc')->get();
        
        return view('admin.productos.create', compact('categorias', 'proveedores'));
    }

    public function guardarProducto(Request $request)
    {
        $request->validate([
            'nomProd' => 'required|string|max:100|unique:productos,nomProd',
            'estProd' => 'required|in:activo,inactivo',
            'uniMedProd' => 'required|string|max:20',
            'precUniProd' => 'required|numeric|min:0',
            'stockProd' => 'required|integer|min:0',
            'stockMinProd' => 'required|integer|min:0',
            'IDCat' => 'required|exists:categorias,IDCat',
            'IDprov' => 'required|exists:proveedores,IDprov',
        ]);

        Producto::create($request->all());

        return redirect()->route('admin.productos')
            ->with('success', 'Producto creado correctamente');
    }

    public function editarProducto($id)
    {
        $producto = Producto::findOrFail($id);
        $categorias = Categoria::orderBy('nomCat', 'asc')->get();
        $proveedores = Proveedor::orderBy('razonSocialProv', 'asc')->get();
        
        return view('admin.productos.edit', compact('producto', 'categorias', 'proveedores'));
    }

    public function actualizarProducto(Request $request, $id)
    {
        $request->validate([
            'nomProd' => 'required|string|max:100|unique:productos,nomProd,'.$id.',IDProd',
            'estProd' => 'required|in:activo,inactivo',
            'uniMedProd' => 'required|string|max:20',
            'precUniProd' => 'required|numeric|min:0',
            'stockProd' => 'required|integer|min:0',
            'stockMinProd' => 'required|integer|min:0',
            'IDCat' => 'required|exists:categorias,IDCat',
            'IDprov' => 'required|exists:proveedores,IDprov',
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

    public function guardarCategoria(Request $request)
    {
        $request->validate([
            'nomCat' => 'required|string|max:100|unique:categorias,nomCat',
            'descCat' => 'nullable|string',
        ]);

        Categoria::create($request->all());

        return redirect()->route('admin.categorias')
            ->with('success', 'Categoría creada correctamente');
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

    // Métodos para proveedores
    public function proveedores()
    {
        $proveedores = Proveedor::orderBy('razonSocialProv', 'asc')->get();
        return view('admin.proveedores.index', compact('proveedores'));
    }

    public function crearProveedor()
    {
        return view('admin.proveedores.create');
    }

    public function guardarProveedor(Request $request)
    {
        $request->validate([
            'razonSocialProv' => 'required|string|max:100',
            'rucProv' => 'required|string|size:11|unique:proveedores,rucProv',
            'telProv' => 'nullable|string|max:15',
            'emailProv' => 'nullable|email|max:100',
        ]);

        Proveedor::create($request->all());

        return redirect()->route('admin.proveedores')
            ->with('success', 'Proveedor creado correctamente');
    }

    public function editarProveedor($id)
    {
        $proveedor = Proveedor::findOrFail($id);
        return view('admin.proveedores.edit', compact('proveedor'));
    }

    public function actualizarProveedor(Request $request, $id)
    {
        $request->validate([
            'razonSocialProv' => 'required|string|max:100',
            'rucProv' => 'required|string|size:11|unique:proveedores,rucProv,'.$id.',IDprov',
            'telProv' => 'nullable|string|max:15',
            'emailProv' => 'nullable|email|max:100',
        ]);

        $proveedor = Proveedor::findOrFail($id);
        $proveedor->update($request->all());

        return redirect()->route('admin.proveedores')
            ->with('success', 'Proveedor actualizado correctamente');
    }

    public function eliminarProveedor($id)
    {
        $proveedor = Proveedor::findOrFail($id);
        
        // Verificar si el proveedor tiene productos asociados
        if ($proveedor->productos()->count() > 0) {
            return redirect()->route('admin.proveedores')
                ->with('error', 'No se puede eliminar el proveedor porque tiene productos asociados');
        }
        
        // Verificar si el proveedor tiene pedidos asociados
        if ($proveedor->pedidos()->count() > 0) {
            return redirect()->route('admin.proveedores')
                ->with('error', 'No se puede eliminar el proveedor porque tiene pedidos asociados');
        }
        
        $proveedor->delete();

        return redirect()->route('admin.proveedores')
            ->with('success', 'Proveedor eliminado correctamente');
    }

    // Métodos para empleados
    public function empleados()
    {
        $empleados = Empleado::with('user')
            ->orderBy('apelEmp', 'asc')
            ->get();
            
        return view('admin.empleados.index', compact('empleados'));
    }

    public function crearEmpleado()
    {
        return view('admin.empleados.create');
    }

    public function guardarEmpleado(Request $request)
    {
        $request->validate([
            'nomEmp' => 'required|string|max:100',
            'apelEmp' => 'required|string|max:100',
            'docIdenEmp' => 'required|string|max:15|unique:empleados,docIdenEmp',
            'telEmp' => 'nullable|string|max:15',
            'dirEmp' => 'nullable|string',
            'usuario' => 'required|string|max:50|unique:users,usuario',
            'contraUsu' => 'required|string|min:8',
            'rolUsu' => 'required|in:admin,vendedor',
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

    // Métodos para pedidos
    public function pedidos()
    {
        $pedidos = Pedido::with(['proveedor', 'empleado.user'])
            ->orderBy('fechPed', 'desc')
            ->get();
            
        return view('admin.pedidos.index', compact('pedidos'));
    }

    public function crearPedido()
    {
        $proveedores = Proveedor::orderBy('razonSocialProv', 'asc')->get();
        $empleados = Empleado::with('user')
            ->whereHas('user', function($query) {
                $query->where('rolUsu', 'admin');
            })
            ->orderBy('apelEmp', 'asc')
            ->get();
            
        $productos = Producto::where('estProd', 'activo')
            ->orderBy('nomProd', 'asc')
            ->get();
            
        return view('admin.pedidos.create', compact('proveedores', 'empleados', 'productos'));
    }

    public function guardarPedido(Request $request)
    {
        $request->validate([
            'fechPed' => 'required|date',
            'fechEntrPed' => 'nullable|date|after_or_equal:fechPed',
            'IDprov' => 'required|exists:proveedores,IDprov',
            'IDEmp' => 'required|exists:empleados,IDEmp',
            'productos' => 'required|array|min:1',
            'productos.*.IDProd' => 'required|exists:productos,IDProd',
            'productos.*.cant' => 'required|integer|min:1',
            'productos.*.precUni' => 'required|numeric|min:0',
        ]);

        $total = collect($request->productos)->sum(function($item) {
            return $item['cant'] * $item['precUni'];
        });

        $pedido = Pedido::create([
            'fechPed' => $request->fechPed,
            'totalProd' => $total,
            'fechEntrPed' => $request->fechEntrPed,
            'estadPed' => 'pendiente',
            'IDprov' => $request->IDprov,
            'IDEmp' => $request->IDEmp,
        ]);

        foreach ($request->productos as $producto) {
            DetallePedido::create([
                'cant' => $producto['cant'],
                'precUni' => $producto['precUni'],
                'nomProd' => Producto::find($producto['IDProd'])->nomProd,
                'IDPed' => $pedido->IDPed,
                'IDProd' => $producto['IDProd'],
            ]);

            // Actualizar stock del producto
            $prod = Producto::find($producto['IDProd']);
            $prod->stockProd += $producto['cant'];
            $prod->save();
        }

        return redirect()->route('admin.pedidos')
            ->with('success', 'Pedido creado correctamente');
    }

    public function verPedido($id)
    {
        $pedido = Pedido::with(['proveedor', 'empleado.user', 'detallePedidos.producto'])
            ->findOrFail($id);
            
        return view('admin.pedidos.show', compact('pedido'));
    }

    public function actualizarEstadoPedido(Request $request, $id)
    {
        $request->validate([
            'estadPed' => 'required|in:pendiente,recibido,cancelado',
        ]);

        $pedido = Pedido::findOrFail($id);
        
        // Si el estado cambia a recibido, actualizar stock
        if ($request->estadPed === 'recibido' && $pedido->estadPed !== 'recibido') {
            foreach ($pedido->detallePedidos as $detalle) {
                $producto = Producto::find($detalle->IDProd);
                $producto->stockProd += $detalle->cant;
                $producto->save();
            }
        }
        
        // Si el estado cambia de recibido a cancelado, revertir stock
        if ($request->estadPed === 'cancelado' && $pedido->estadPed === 'recibido') {
            foreach ($pedido->detallePedidos as $detalle) {
                $producto = Producto::find($detalle->IDProd);
                $producto->stockProd -= $detalle->cant;
                $producto->save();
            }
        }
        
        $pedido->update(['estadPed' => $request->estadPed]);

        return redirect()->route('admin.pedidos.show', $id)
            ->with('success', 'Estado del pedido actualizado');
    }
    // Métodos para categorías
    public function crearCategoria()
    {
        return view('admin.categorias.create');
    }

    public function editarCategoria($id)
    {
        $categoria = Categoria::findOrFail($id);
        return view('admin.categorias.edit', compact('categoria'));
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
            ->orderBy('fechVent', 'asc')
            ->get();
            
        $totalVentas = $ventas->sum('totalVent');
        $totalProductosVendidos = $ventas->sum(function($venta) {
            return $venta->detalleVentas->sum('cant');
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

        $productos = Producto::with(['categoria', 'proveedor'])
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
                $query->selectRaw('sum(sub_total)')
                    ->whereHas('venta', function($q) use ($fechaInicio, $fechaFin) {
                        $q->whereBetween('fechVent', [$fechaInicio, $fechaFin]);
                    });
            }])
            ->orderBy('ventas_count', 'desc')
            ->get();

        return view('admin.reportes.productos', compact(
            'productos',
            'fechaInicio',
            'fechaFin'
        ));
    }

    public function generarReporteProveedores(Request $request)
    {
        $request->validate([
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
        ]);

        $fechaInicio = $request->fecha_inicio;
        $fechaFin = $request->fecha_fin;

        $proveedores = Proveedor::with(['productos' => function($query) use ($fechaInicio, $fechaFin) {
                $query->when($fechaInicio && $fechaFin, function($q) use ($fechaInicio, $fechaFin) {
                    $q->whereHas('detalleVentas', function($q2) use ($fechaInicio, $fechaFin) {
                        $q2->whereHas('venta', function($q3) use ($fechaInicio, $fechaFin) {
                            $q3->whereBetween('fechVent', [$fechaInicio, $fechaFin]);
                        });
                    });
                });
            }])
            ->withCount(['pedidos as pedidos_count' => function($query) use ($fechaInicio, $fechaFin) {
                $query->when($fechaInicio && $fechaFin, function($q) use ($fechaInicio, $fechaFin) {
                    $q->whereBetween('fechPed', [$fechaInicio, $fechaFin]);
                });
            }])
            ->withSum(['pedidos as pedidos_total' => function($query) use ($fechaInicio, $fechaFin) {
                $query->when($fechaInicio && $fechaFin, function($q) use ($fechaInicio, $fechaFin) {
                    $q->whereBetween('fechPed', [$fechaInicio, $fechaFin]);
                });
            }], 'totalProd')
            ->orderBy('razonSocialProv', 'asc')
            ->get();

        return view('admin.reportes.proveedores', compact(
            'proveedores',
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

    public function verVenta($id)
    {
        $venta = Venta::with(['clienteNatural', 'clienteJuridica', 'usuario.empleado', 'detalleVentas.producto', 'comprobantes'])
            ->findOrFail($id);
            
        return view('admin.ventas.show', compact('venta'));
    }

    // Métodos para devoluciones
    public function devoluciones()
    {
        $devoluciones = Devolucion::with(['venta', 'usuario.empleado'])
            ->orderBy('fechDev', 'desc')
            ->get();
            
        return view('admin.devoluciones.index', compact('devoluciones'));
    }
}