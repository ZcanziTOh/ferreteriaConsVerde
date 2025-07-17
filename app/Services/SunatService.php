<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class SunatService
{
    protected $client;
    protected $token;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => 'https://api.apis.net.pe/', // Nueva URL base
            'timeout'  => 15.0,
        ]);
        
        $this->token = env('APISPERU_TOKEN');
    }

    public function consultarDni($dni)
    {
        try {
            $response = $this->client->get("v1/dni", [
                'query' => [
                    'numero' => $dni
                ],
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->token,
                    'Accept' => 'application/json',
                ],
                'timeout' => 15
            ]);

            $data = json_decode($response->getBody(), true);
            
            // Validar estructura de respuesta
            if (!isset($data['nombres'])) {
                throw new \Exception("Estructura de respuesta inválida");
            }

            return [
                'nombres' => $data['nombres'],
                'apellidoPaterno' => $data['apellidoPaterno'] ?? '',
                'apellidoMaterno' => $data['apellidoMaterno'] ?? '',
                'direccion' => '', // Esta API no devuelve dirección
                'tipoDocumento' => $data['tipoDocumento'] ?? 'DNI'
            ];

        } catch (\Exception $e) {
            Log::error("Error consultando DNI {$dni}: " . $e->getMessage());
            return [
                'error' => 'No se pudo obtener datos del DNI',
                'api_error' => $e->getMessage()
            ];
        }
    }

    public function consultarRuc($ruc)
    {
        try {
            $response = $this->client->get("v1/ruc", [
                'query' => ['numero' => $ruc],
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->token,
                    'Accept' => 'application/json',
                ],
                'timeout' => 15
            ]);

            $data = json_decode($response->getBody(), true);
            
            // Validación más flexible de la respuesta
            if (!isset($data['razonSocial']) && !isset($data['nombre'])) {
                throw new \Exception("Estructura de respuesta inválida: " . json_encode($data));
            }

            return [
                'razonSocial' => $data['razonSocial'] ?? $data['nombre'] ?? '',
                'nombreComercial' => $data['nombreComercial'] ?? '',
                'direccion' => $data['direccion'] ?? $data['direccionCompleta'] ?? '',
                'estado' => $data['estado'] ?? $data['condicion'] ?? 'ACTIVO',
                'tipoDocumento' => $data['tipoDocumento'] ?? 'RUC'
            ];

        } catch (\Exception $e) {
            Log::error("Error consultando RUC {$ruc}: " . $e->getMessage());
            return [
                'error' => 'No se pudo obtener datos del RUC',
                'api_error' => $e->getMessage()
            ];
        }
    }
}