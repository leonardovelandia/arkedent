<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

trait TrazabilidadFirma
{
    public static function generarTrazabilidad(
        Request $request,
        string $firmaData,
        array $datosDocumento
    ): array {
        $ip        = $request->ip();
        $userAgent = $request->userAgent() ?? '';
        $timestamp = Carbon::now()->setTimezone('America/Bogota');

        $dispositivo = self::parsearDispositivo($userAgent);
        $navegador   = self::parsearNavegador($userAgent);

        $contenidoHash = implode('|', array_merge(
            array_values($datosDocumento),
            [$timestamp->toIso8601String(), $ip, $userAgent]
        ));
        $documentoHash = hash('sha256', $contenidoHash);
        $firmaHash     = hash('sha256', $firmaData);
        $verificacionToken = hash('sha256', $documentoHash . $firmaHash . Str::random(32));

        return [
            'firma_user_agent'         => $userAgent,
            'firma_timestamp'          => $timestamp,
            'firma_timezone'           => 'America/Bogota',
            'firma_hash'               => $firmaHash,
            'documento_hash'           => $documentoHash,
            'firma_dispositivo'        => $dispositivo,
            'firma_navegador'          => $navegador,
            'firma_verificacion_token' => $verificacionToken,
        ];
    }

    private static function parsearDispositivo(string $ua): string
    {
        if (preg_match('/iPad/i', $ua))    return 'Tablet (iPad)';
        if (preg_match('/iPhone/i', $ua))  return 'Móvil (iPhone)';
        if (preg_match('/Android/i', $ua)) return preg_match('/Mobile/i', $ua) ? 'Móvil (Android)' : 'Tablet (Android)';
        if (preg_match('/Windows/i', $ua)) return 'PC (Windows)';
        if (preg_match('/Macintosh/i', $ua)) return 'PC (Mac)';
        if (preg_match('/Linux/i', $ua))   return 'PC (Linux)';
        return 'Dispositivo desconocido';
    }

    private static function parsearNavegador(string $ua): string
    {
        if (preg_match('/Edg\/(\d+)/i', $ua, $m))     return "Microsoft Edge {$m[1]}";
        if (preg_match('/OPR\/(\d+)/i', $ua, $m))     return "Opera {$m[1]}";
        if (preg_match('/Chrome\/(\d+)/i', $ua, $m))  return "Google Chrome {$m[1]}";
        if (preg_match('/Firefox\/(\d+)/i', $ua, $m)) return "Mozilla Firefox {$m[1]}";
        if (preg_match('/Safari\/(\d+)/i', $ua, $m))  return "Safari {$m[1]}";
        return 'Navegador desconocido';
    }

    private static function generarQrImgTag(string $url, int $size = 90): string
    {
        try {
            $renderer = new ImageRenderer(
                new RendererStyle($size),
                new SvgImageBackEnd()
            );
            $writer = new Writer($renderer);
            $svg    = $writer->writeString($url);
            // Embed as base64 data URI so Dompdf handles it correctly
            $b64    = base64_encode($svg);
            return "<img src=\"data:image/svg+xml;base64,{$b64}\" width=\"{$size}\" height=\"{$size}\" alt=\"QR\">";
        } catch (\Throwable $e) {
            return '';
        }
    }

    public static function generarConstanciaFirmaPDF(
        array $datosFirma,
        string $nombreFirmante,
        string $tipoDoc,
        string $numDoc,
        string $colorPDF = '#6B21A8'
    ): string {
        $ts    = $datosFirma['firma_timestamp'] ?? null;
        $tsStr = $ts ? (($ts instanceof \Carbon\Carbon ? $ts : \Carbon\Carbon::parse($ts))->format('d/m/Y H:i:s')) : '—';
        $hash  = $datosFirma['documento_hash'] ?? '—';
        $token = $datosFirma['firma_verificacion_token'] ?? '—';
        $ip    = $datosFirma['firma_ip'] ?? '—';
        $disp  = $datosFirma['firma_dispositivo'] ?? '—';
        $nav   = $datosFirma['firma_navegador'] ?? '—';
        $hashCorto  = strlen($hash) > 32 ? substr($hash, 0, 32).'...' : $hash;
        $tokenCorto = strlen($token) > 40 ? substr($token, 0, 40).'...' : $token;

        $urlVerificacion = url('/verificar/' . $token);
        $qrImg = self::generarQrImgTag($urlVerificacion, 90);

        $qrBlock = $qrImg
            ? "<td style='width:100px;padding-left:10px;vertical-align:middle;text-align:center;border-left:1px solid #e5e7eb;'>
                    {$qrImg}
                    <div style='font-size:6px;color:#999;margin-top:3px;line-height:1.3;'>Escanear<br>para verificar</div>
               </td>"
            : '';

        return "
        <div style='border:1.5px solid {$colorPDF};border-radius:4px;margin-top:16px;overflow:hidden;font-family:Arial,sans-serif;page-break-inside:avoid;'>
            <div style='background:{$colorPDF};color:white;padding:5px 10px;font-size:7.5px;font-weight:bold;text-transform:uppercase;letter-spacing:.08em;'>
                CONSTANCIA DE FIRMA ELECTRÓNICA — LEY 527/1999 COLOMBIA
            </div>
            <div style='padding:8px 10px;background:#fafafa;'>
                <table style='width:100%;border-collapse:collapse;'>
                    <tr>
                        <td style='vertical-align:top;'>
                            <table style='width:100%;border-collapse:collapse;font-size:7.5px;'>
                                <tr>
                                    <td style='color:#666;padding:2px 0;width:30%;'>Firmado por:</td>
                                    <td style='color:#1c2b22;font-weight:bold;'>{$nombreFirmante}</td>
                                    <td style='color:#666;padding:2px 0;width:22%;'>Documento:</td>
                                    <td style='color:#1c2b22;font-weight:bold;'>{$tipoDoc} {$numDoc}</td>
                                </tr>
                                <tr>
                                    <td style='color:#666;padding:2px 0;'>Fecha/Hora:</td>
                                    <td style='color:#1c2b22;'>{$tsStr} (UTC-5)</td>
                                    <td style='color:#666;padding:2px 0;'>IP origen:</td>
                                    <td style='color:#1c2b22;'>{$ip}</td>
                                </tr>
                                <tr>
                                    <td style='color:#666;padding:2px 0;'>Dispositivo:</td>
                                    <td style='color:#1c2b22;'>{$disp}</td>
                                    <td style='color:#666;padding:2px 0;'>Navegador:</td>
                                    <td style='color:#1c2b22;'>{$nav}</td>
                                </tr>
                                <tr>
                                    <td style='color:#666;padding:2px 0;'>Hash doc:</td>
                                    <td colspan='3' style='color:#4C1D95;font-family:monospace;font-size:6.5px;'>{$hashCorto}</td>
                                </tr>
                                <tr>
                                    <td style='color:#666;padding:2px 0;'>Token:</td>
                                    <td colspan='3' style='color:#4C1D95;font-family:monospace;font-size:6.5px;'>{$tokenCorto}</td>
                                </tr>
                            </table>
                            <div style='margin-top:5px;padding-top:4px;border-top:1px solid #eee;font-size:6.5px;color:#999;line-height:1.4;'>
                                Firmado electrónicamente conforme a la Ley 527/1999, Decreto 2364/2012 y Ley 1581/2012 de Colombia.
                                Verificar en: <strong style='color:{$colorPDF};'>{$urlVerificacion}</strong>
                            </div>
                        </td>
                        {$qrBlock}
                    </tr>
                </table>
            </div>
        </div>";
    }

    public static function generarConstanciaFirmaHTML(array $d): string
    {
        if (empty($d['firma_timestamp'])) return '';
        $ts    = $d['firma_timestamp'] instanceof \Carbon\Carbon ? $d['firma_timestamp'] : \Carbon\Carbon::parse($d['firma_timestamp']);
        $tsStr = $ts->setTimezone('America/Bogota')->format('d/m/Y H:i:s');
        $ip    = $d['firma_ip'] ?? '—';
        $disp  = $d['firma_dispositivo'] ?? '—';
        $nav   = $d['firma_navegador'] ?? '—';
        $hash  = $d['documento_hash'] ?? '—';
        $hashCorto = strlen($hash) > 20 ? substr($hash, 0, 20).'...' : $hash;
        $token = $d['firma_verificacion_token'] ?? null;

        $html = "
        <div style='background:#f0fdf4;border:1px solid #16a34a;border-radius:8px;padding:.875rem 1.25rem;margin-top:1rem;font-size:.78rem;'>
            <div style='font-weight:600;color:#15803d;margin-bottom:.5rem;'>
                <i class=\"bi bi-shield-check\"></i> Firma electrónica verificada — Ley 527/1999
            </div>
            <div style='display:grid;grid-template-columns:1fr 1fr;gap:.25rem;color:#374151;'>
                <div><span style='color:#6b7280;'>Fecha y hora:</span> {$tsStr} (Bogotá)</div>
                <div><span style='color:#6b7280;'>IP de origen:</span> {$ip}</div>
                <div><span style='color:#6b7280;'>Dispositivo:</span> {$disp}</div>
                <div><span style='color:#6b7280;'>Navegador:</span> {$nav}</div>
                <div style='grid-column:span 2;'><span style='color:#6b7280;'>Hash:</span>
                    <code style='font-size:.72rem;color:#4c1d95;'>{$hashCorto}</code>
                </div>
            </div>";
        if ($token) {
            $html .= "
            <div style='margin-top:.5rem;padding-top:.5rem;border-top:1px solid #d1fae5;'>
                <a href='" . url('/verificar/' . $token) . "' target='_blank' style='font-size:.72rem;color:#15803d;text-decoration:none;'>
                    <i class=\"bi bi-box-arrow-up-right\"></i> Verificar autenticidad del documento
                </a>
            </div>";
        }
        $html .= "</div>";
        return $html;
    }
}
