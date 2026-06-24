<?php

namespace App\Exports;

use App\Models\Reporte\Reporte;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ActividadesNewExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $fechaDesde;
    protected $fechaHasta;
    protected $comuna;
    protected $comunidad;
    protected $direcciones;

    function __construct($fechaDesde, $fechaHasta, $comuna, $comunidad, $direcciones) {
        $this->fechaDesde = $fechaDesde;
        $this->fechaHasta = $fechaHasta;
        $this->comuna = $comuna;
        $this->comunidad = $comunidad;
        $this->direcciones = $direcciones;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return (new Reporte)->actividades2024excell($this->fechaDesde, $this->fechaHasta, $this->comuna, $this->comunidad, $this->direcciones);
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'Fecha',
            'Direccion Asignada',
            'Actividad',
            'Status',
        ];
    }

    /**
    * @var Reporte $accion
    */
    public function map($accion): array
    {
        return [
            $accion->fecha,
            $accion->Direccion,
            $accion->descripcion,
            $accion->state,
        ];
    }
}
