<?php

namespace App\Helpers;

class OdontogramaPdf
{
    /**
     * Genera el odontograma como imagen PNG (base64) usando GD.
     * Dibuja los mismos polígonos que el componente SVG del frontend.
     *
     * @param  array  $dientes   Array indexado por número FDI con estados por superficie
     * @param  array  $colores   Mapa estado → ['fill' => hex, 'stroke' => hex]
     * @param  array  $arcadas   ['sup' => [[izq], [der]], 'inf' => [[izq], [der]]]
     * @return string            data:image/png;base64,...  (vacío si GD no está disponible)
     */
    public static function imagen(array $dientes, array $colores, array $arcadas): string
    {
        if (!extension_loaded('gd')) {
            return '';
        }

        $ts = 22;  // tamaño del diente en px
        $gp = 2;   // espacio entre dientes
        $sp = 10;  // ancho separador central
        $nh = 9;   // alto etiqueta de número
        $mv = 4;   // margen vertical alrededor de la línea central

        $left  = count($arcadas['sup'][0]);
        $right = count($arcadas['sup'][1]);
        $total = $left + $right;

        $w = $total * ($ts + $gp) + $sp + 6;
        $h = $nh + $ts + $mv + 2 + $mv + $ts + $nh + 6;

        $img = imagecreatetruecolor($w, $h);

        $cache = [];
        $gc = function (string $hex) use ($img, &$cache): int {
            $hex = ltrim($hex, '#');
            if (strlen($hex) === 3) {
                $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
            }
            if (!isset($cache[$hex])) {
                $cache[$hex] = imagecolorallocate(
                    $img,
                    (int) hexdec(substr($hex, 0, 2)),
                    (int) hexdec(substr($hex, 2, 2)),
                    (int) hexdec(substr($hex, 4, 2))
                );
            }
            return $cache[$hex];
        };

        imagefill($img, 0, 0, $gc('ffffff'));
        $gray   = $gc('9ca3af');
        $purple = $gc('a78bfa');
        $f = $ts / 100.0;

        // Polígonos en viewBox 100×100 — mismos que el SVG del frontend
        $polys = [
            'vestibular' => [2, 2, 98, 2, 70, 30, 30, 30],
            'lingual'    => [30, 70, 70, 70, 98, 98, 2, 98],
            'mesial'     => [2, 2, 30, 30, 30, 70, 2, 98],
            'distal'     => [70, 30, 98, 2, 98, 98, 70, 70],
            'oclusal'    => [30, 30, 70, 30, 70, 70, 30, 70],
        ];

        $drawTooth = function (int $ox, int $oy, int $num) use ($img, $dientes, $colores, $f, $gc, $polys, $ts) {
            $d  = $dientes[$num] ?? [];
            $ec = $d['estado_completo'] ?? null;

            foreach ($polys as $sup => $pts) {
                $est    = $ec ?? ($d[$sup] ?? 'sano');
                $c      = $colores[$est] ?? $colores['sano'];
                $fill   = $gc($c['fill']);
                $stroke = $gc($c['stroke']);

                $sc = [];
                for ($i = 0; $i < count($pts); $i += 2) {
                    $sc[] = (int) round($ox + $pts[$i] * $f);
                    $sc[] = (int) round($oy + $pts[$i + 1] * $f);
                }

                imagefilledpolygon($img, $sc, $fill);
                imagepolygon($img, $sc, $stroke);
            }

            // Cruz para dientes extraídos / ausentes
            if ($ec && in_array($ec, ['extraido', 'ausente'])) {
                $xc = $gc('6b7280');
                imagesetthickness($img, 2);
                imageline($img, $ox + 3, $oy + 3, $ox + $ts - 3, $oy + $ts - 3, $xc);
                imageline($img, $ox + $ts - 3, $oy + 3, $ox + 3, $oy + $ts - 3, $xc);
                imagesetthickness($img, 1);
            }
        };

        // ── Arcada superior (números arriba) ──
        $supY = $nh + 2;
        $x = 2;
        foreach ($arcadas['sup'][0] as $num) {
            $drawTooth($x, $supY, $num);
            $nx = $x + (int) floor($ts / 2) - (int) (strlen((string) $num) * 2);
            imagestring($img, 1, $nx, 1, (string) $num, $gray);
            $x += $ts + $gp;
        }
        $x += $sp;
        foreach ($arcadas['sup'][1] as $num) {
            $drawTooth($x, $supY, $num);
            $nx = $x + (int) floor($ts / 2) - (int) (strlen((string) $num) * 2);
            imagestring($img, 1, $nx, 1, (string) $num, $gray);
            $x += $ts + $gp;
        }

        // ── Línea central ──
        $lineY = $nh + $ts + $mv + 3;
        imagesetthickness($img, 2);
        imageline($img, 2, $lineY, $w - 2, $lineY, $purple);
        imagesetthickness($img, 1);

        // ── Arcada inferior (números abajo) ──
        $infY = $lineY + $mv + 1;
        $x = 2;
        foreach ($arcadas['inf'][0] as $num) {
            $drawTooth($x, $infY, $num);
            $nx = $x + (int) floor($ts / 2) - (int) (strlen((string) $num) * 2);
            imagestring($img, 1, $nx, $infY + $ts + 1, (string) $num, $gray);
            $x += $ts + $gp;
        }
        $x += $sp;
        foreach ($arcadas['inf'][1] as $num) {
            $drawTooth($x, $infY, $num);
            $nx = $x + (int) floor($ts / 2) - (int) (strlen((string) $num) * 2);
            imagestring($img, 1, $nx, $infY + $ts + 1, (string) $num, $gray);
            $x += $ts + $gp;
        }

        ob_start();
        imagepng($img);
        $data = ob_get_clean();
        imagedestroy($img);

        return 'data:image/png;base64,' . base64_encode($data);
    }

    public static function colores(): array
    {
        return [
            'sano'                => ['fill' => '#FFFFFF', 'stroke' => '#9ca3af'],
            'caries'              => ['fill' => '#FFC107', 'stroke' => '#d97706'],
            'restaurado_resina'   => ['fill' => '#17A2B8', 'stroke' => '#0e7490'],
            'restaurado_amalgama' => ['fill' => '#495057', 'stroke' => '#1f2937'],
            'corona'              => ['fill' => '#0D6EFD', 'stroke' => '#1d4ed8'],
            'endodoncia'          => ['fill' => '#a855f7', 'stroke' => '#7c3aed'],
            'extraccion_indicada' => ['fill' => '#DC3545', 'stroke' => '#991b1b'],
            'extraido'            => ['fill' => '#adb5bd', 'stroke' => '#6b7280'],
            'implante'            => ['fill' => '#7c3aed', 'stroke' => '#4c1d95'],
            'fractura'            => ['fill' => '#FD7E14', 'stroke' => '#c2410c'],
            'sellante'            => ['fill' => '#28A745', 'stroke' => '#166534'],
            'ausente'             => ['fill' => '#F9FAFB', 'stroke' => '#9ca3af'],
            'temporal'            => ['fill' => '#f9d5b3', 'stroke' => '#d97706'],
        ];
    }
}
