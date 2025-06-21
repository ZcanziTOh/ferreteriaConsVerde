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
            'base_uri' => 'https://api.sunat.com/',
            'timeout'  => 10.0,
        ]);
        
        // Obtener token de autenticación (esto debería estar en variables de entorno)
        $this->token = env('SUNAT_API_TOKEN');
    }

    public function consultarRuc($ruc)
    {
        try {
            $response = $this->client->get("ruc/$ruc", [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->token,
                    'Accept' => 'application/json',
                ]
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (\Exception $e) {
            Log::error("Error al consultar RUC en SUNAT: " . $e->getMessage());
            return null;
        }
    }

    public function consultarDni($dni)
    {
        try {
            $response = $this->client->get("dni/$dni", [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->token,
                    'Accept' => 'application/json',
                ]
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (\Exception $e) {
            Log::error("Error al consultar DNI en SUNAT: " . $e->getMessage());
            return null;
        }
    }

    public function validarComprobante($comprobante)
    {
        try {
            $response = $this->client->post('comprobantes/validar', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->token,
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ],
                'json' => $comprobante
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (\Exception $e) {
            Log::error("Error al validar comprobante en SUNAT: " . $e->getMessage());
            return null;
        }
    }
}