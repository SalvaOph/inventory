<?php

namespace App\Services;

use Barryvdh\DomPDF\Facade\Pdf;

class PdfExportService
{
    public function generate($view, $data, $fileName = 'report.pdf')
    {
        $pdf = Pdf::loadView($view, $data);
        return $pdf->download($fileName);
    }
}
