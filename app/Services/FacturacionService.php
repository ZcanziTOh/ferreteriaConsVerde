<?php

namespace App\Services;

use Greenter\Report\XmlUtils;
use Greenter\Ws\Services\SunatEndpoints;
use Greenter\See;

class FacturacionService
{
    protected $see;
    protected $company;

    public function __construct()
    {
        $this->company = [
            'ruc' => env('EMPRESA_RUC'),
            'razonSocial' => env('EMPRESA_RAZON_SOCIAL'),
            'direccion' => env('EMPRESA_DIRECCION'),
            'usuarioSol' => env('SUNAT_USUARIO_SOL'),
            'claveSol' => env('SUNAT_CLAVE_SOL'),
        ];

        $this->see = new See();
        $this->see->setCertificate(file_get_contents(storage_path(env('SUNAT_CERTIFICADO'))));
        $this->see->setService(SunatEndpoints::FE_BETA);
        $this->see->setClaveSOL(
            $this->company['ruc'],
            $this->company['usuarioSol'],
            $this->company['claveSol']
        );
    }

    public function generarFactura($venta, $cliente)
    {
        $invoice = new \Greenter\Model\Sale\Invoice();
        $invoice
            ->setUblVersion('2.1')
            ->setTipoOperacion('0101') // Venta interna
            ->setTipoDoc('01') // Factura
            ->setSerie('F001')
            ->setCorrelativo($venta->IDVent)
            ->setFechaEmision(new \DateTime($venta->fechVent))
            ->setTipoMoneda('PEN')
            ->setCompany($this->getCompany())
            ->setClient($this->getClient($cliente));

        // Agregar items
        foreach ($venta->detalleVentas as $detalle) {
            $item = new \Greenter\Model\Sale\SaleDetail();
            $item->setCodProducto($detalle->producto->IDProd)
                ->setUnidad('NIU') // Unidad (NIU: Unidad)
                ->setDescripcion($detalle->producto->nomProd)
                ->setCantidad($detalle->cantidad)
                ->setMtoValorUnitario($detalle->prec_uni)
                ->setMtoValorVenta($detalle->subtotal)
                ->setMtoBaseIgv($detalle->subtotal)
                ->setPorcentajeIgv(18.00)
                ->setIgv($detalle->subtotal * 0.18)
                ->setTipAfeIgv('10') // Gravado
                ->setTotalImpuestos($detalle->subtotal * 0.18)
                ->setMtoPrecioUnitario($detalle->prec_uni * 1.18);

            $invoice->setDetails([$item]);
        }

        // Totales
        $invoice->setMtoOperGravadas($venta->totalVent / 1.18)
            ->setMtoIGV($venta->totalVent - ($venta->totalVent / 1.18))
            ->setValorVenta($venta->totalVent / 1.18)
            ->setTotalImpuestos($venta->totalVent - ($venta->totalVent / 1.18))
            ->setMtoImpVenta($venta->totalVent);

        return $invoice;
    }

    public function enviarASunat($invoice)
    {
        $result = $this->see->send($invoice);

        if (!$result->isSuccess()) {
            return [
                'success' => false,
                'error' => $result->getError()->getMessage()
            ];
        }

        // Guardar CDR
        $cdr = $result->getCdrResponse();
        $filename = $invoice->getName().'-'.$cdr->getId().'.xml';
        file_put_contents(storage_path('app/cdr/'.$filename), $cdr->getCdrZip());

        return [
            'success' => true,
            'cdr' => $cdr,
            'xml' => $this->see->getFactory()->getLastXml(),
            'filename' => $filename
        ];
    }

    protected function getCompany()
    {
        return (new \Greenter\Model\Company\Company())
            ->setRuc($this->company['ruc'])
            ->setRazonSocial($this->company['razonSocial'])
            ->setDireccion($this->company['direccion']);
    }

    protected function getClient($cliente)
    {
        if ($cliente instanceof \App\Models\ClienteJuridica) {
            $tipoDoc = '6'; // RUC
            $numDoc = $cliente->rucClieJuri;
            $nombre = $cliente->razSociClieJuri;
        } else {
            $tipoDoc = '1'; // DNI
            $numDoc = $cliente->docIdenClieNat;
            $nombre = $cliente->nomClieNat . ' ' . $cliente->apelClieNat;
        }

        return (new \Greenter\Model\Client\Client())
            ->setTipoDoc($tipoDoc)
            ->setNumDoc($numDoc)
            ->setRznSocial($nombre);
    }
}