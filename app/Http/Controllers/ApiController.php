<?php

namespace App\Http\Controllers;

use App\Services\SunatService;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    protected $sunatService;

    public function __construct(SunatService $sunatService)
    {
        $this->sunatService = $sunatService;
    }

    public function consultarRuc(Request $request)
    {
        $request->validate([
            'ruc' => 'required|digits:11'
        ]);

        $data = $this->sunatService->consultarRuc($request->ruc);

        if ($data) {
            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'No se pudo obtener información del RUC'
        ], 400);
    }

    public function consultarDni(Request $request)
    {
        $request->validate([
            'dni' => 'required|digits:8'
        ]);

        $data = $this->sunatService->consultarDni($request->dni);

        if ($data) {
            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'No se pudo obtener información del DNI'
        ], 400);
    }
}