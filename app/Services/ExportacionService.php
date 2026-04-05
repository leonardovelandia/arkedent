<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ExportacionService
{
    public static function registrarLog(
        string $modulo,
        string $formato,
        bool   $incluyoSensibles,
        array  $camposExportados,
        array  $filtros,
        int    $totalRegistros
    ): void {
        try {
            DB::table('logs_exportacion')->insert([
                'user_id'           => auth()->id(),
                'user_nombre'       => auth()->user()?->name ?? 'Sistema',
                'modulo'            => $modulo,
                'formato'           => $formato,
                'incluyo_sensibles' => $incluyoSensibles,
                'campos_exportados' => json_encode($camposExportados),
                'filtros_aplicados' => json_encode($filtros),
                'total_registros'   => $totalRegistros,
                'ip'                => request()->ip(),
                'created_at'        => now(),
            ]);

            Log::channel('auditoria')->info('Exportación realizada', [
                'modulo'    => $modulo,
                'formato'   => $formato,
                'sensibles' => $incluyoSensibles,
                'registros' => $totalRegistros,
                'usuario'   => auth()->user()?->name,
                'ip'        => request()->ip(),
            ]);
        } catch (\Exception $e) {
            Log::error('Error registrando log exportación: ' . $e->getMessage());
        }
    }

    public static function generarExcel(
        array  $headers,
        array  $datos,
        string $nombreArchivo
    ): \Symfony\Component\HttpFoundation\StreamedResponse {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();

        $colFin = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($headers));

        $sheet->getStyle("A1:{$colFin}1")->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
            'fill'      => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF4C1D95']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
        ]);

        foreach ($headers as $i => $header) {
            $col = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i + 1);
            $sheet->setCellValue("{$col}1", $header);
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        foreach ($datos as $rowIndex => $fila) {
            foreach (array_values($fila) as $colIndex => $valor) {
                $col = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex + 1);
                $sheet->setCellValue("{$col}" . ($rowIndex + 2), $valor ?? '');
            }
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $nombreArchivo . '.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public static function generarPDF(
        array  $headers,
        array  $datos,
        string $nombreArchivo,
        string $titulo = 'Exportación'
    ): \Illuminate\Http\Response {
        $html = '<!DOCTYPE html><html><head>
<meta charset="UTF-8">
<style>
  body { font-family: DejaVu Sans, sans-serif; font-size: 9px; color: #1f1f1f; }
  h2 { font-size: 13px; color: #4c1d95; margin: 0 0 4px 0; }
  p.sub { font-size: 8px; color: #6b7280; margin: 0 0 10px 0; }
  table { width: 100%; border-collapse: collapse; }
  thead th { background: #4c1d95; color: #fff; padding: 5px 6px; text-align: left; font-size: 8px; text-transform: uppercase; }
  tbody tr:nth-child(even) { background: #f5f3ff; }
  tbody td { padding: 4px 6px; border-bottom: 1px solid #ede9fe; font-size: 8px; }
</style>
</head><body>';
        $html .= '<h2>' . htmlspecialchars($titulo) . '</h2>';
        $html .= '<p class="sub">Generado: ' . now()->format('d/m/Y H:i') . ' &nbsp;|&nbsp; Total registros: ' . count($datos) . '</p>';
        $html .= '<table><thead><tr>';
        foreach ($headers as $h) {
            $html .= '<th>' . htmlspecialchars((string)$h) . '</th>';
        }
        $html .= '</tr></thead><tbody>';
        foreach ($datos as $fila) {
            $html .= '<tr>';
            foreach (array_values($fila) as $celda) {
                $html .= '<td>' . htmlspecialchars((string)($celda ?? '')) . '</td>';
            }
            $html .= '</tr>';
        }
        $html .= '</tbody></table></body></html>';

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html)
            ->setPaper('a4', 'landscape');

        return $pdf->download($nombreArchivo . '.pdf');
    }

    public static function generarCSV(
        array  $headers,
        array  $datos,
        string $nombreArchivo
    ): \Symfony\Component\HttpFoundation\StreamedResponse {
        return response()->streamDownload(function () use ($headers, $datos) {
            $output = fopen('php://output', 'w');
            fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($output, $headers, ';');
            foreach ($datos as $fila) {
                fputcsv($output, array_values($fila), ';');
            }
            fclose($output);
        }, $nombreArchivo . '.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
