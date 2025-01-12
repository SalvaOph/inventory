<?php

namespace App\Http\Controllers;

use App\Exports\DynamicExport;
use App\Services\PdfExportService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    protected $pdfExportService;

    public function __construct(PdfExportService $pdfExportService)
    {
        $this->pdfExportService = $pdfExportService;
    }

    // Exportar Excel
    public function exportToExcel($entity)
    {
        $data = $this->getDataForEntity($entity); // Método genérico para obtener datos
        return Excel::download(new DynamicExport($data), "{$entity}_report.xlsx");
    }

    // Exportar PDF
    public function exportToPDF($entity)
    {
        $data = ['data' => $this->getDataForEntity($entity)];
        $view = "reports.{$entity}"; // Define una vista dinámica
        return $this->pdfExportService->generate($view, $data, "{$entity}_report.pdf");
    }

    // Obtener datos según la entidad
    private function getDataForEntity($entity)
    {
        switch ($entity) {
            case 'products':
                return \App\Models\Product::all();
            case 'inventories':
                return \App\Models\Inventory::all();
            case 'providers':
                return \App\Models\Provider::all();
            case 'clients':
                return \App\Models\Client::all();
            case 'warehouses':
                return \App\Models\Warehouse::all();
            case 'purchases':
                return \App\Models\Purchase::all();
            case 'sales':
                return \App\Models\Sale::all();            
            default:
                abort(404, "Entidad no encontrada");
        }
    }
}
