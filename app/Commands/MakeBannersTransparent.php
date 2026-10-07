<?php

declare(strict_types=1);

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class MakeBannersTransparent extends BaseCommand
{
    protected $group       = 'BocaSanta';
    protected $name        = 'bocasanta:make-transparent';
    protected $description = 'Remove o fundo quadriculado das imagens dos banners e converte para PNG transparente';

    public function run(array $params)
    {
        $dir = ROOTPATH . 'public/upimg/banners/';

        $this->processImage(
            $dir . 'banner_ofertas_3d.jpg',
            $dir . 'banner_ofertas_3d.png',
            'dark_checkerboard'
        );

        $this->processImage(
            $dir . 'banner_linefast_3d.jpg',
            $dir . 'banner_linefast_3d.png',
            'light_checkerboard'
        );

        $this->processImage(
            $dir . 'banner_parceiros_3d.jpg',
            $dir . 'banner_parceiros_3d.png',
            'light_solid'
        );

        // Atualizar banco de dados para usar as imagens PNG
        $db = \Config\Database::connect();
        $db->table('tb_banners')->where('ban_ordem', 1)->update(['ban_imagem_direita' => 'upimg/banners/banner_ofertas_3d.png']);
        $db->table('tb_banners')->where('ban_ordem', 2)->update(['ban_imagem_direita' => 'upimg/banners/banner_linefast_3d.png']);
        $db->table('tb_banners')->where('ban_ordem', 3)->update(['ban_imagem_direita' => 'upimg/banners/banner_parceiros_3d.png']);

        CLI::write('Banners convertidos para PNG transparente e banco atualizado!', 'green');
    }

    private function processImage(string $srcPath, string $dstPath, string $mode): void
    {
        if (!file_exists($srcPath)) {
            CLI::error("Arquivo não encontrado: {$srcPath}");
            return;
        }

        $src = imagecreatefromjpeg($srcPath);
        $w = imagesx($src);
        $h = imagesy($src);

        $dst = imagecreatetruecolor($w, $h);
        imagealphablending($dst, false);
        imagesavealpha($dst, true);

        // Preenche tudo com transparência total inicial
        $transparent = imagecolorallocatealpha($dst, 0, 0, 0, 127);
        imagefilledrectangle($dst, 0, 0, $w, $h, $transparent);

        // Algoritmo de flood fill / detecção de fundo a partir das bordas
        // Cria máscara booleana de fundo
        $isBg = array_fill(0, $h, array_fill(0, $w, false));
        $visited = array_fill(0, $h, array_fill(0, $w, false));

        // Fila para BFS a partir das 4 bordas
        $queue = new \SplQueue();

        for ($x = 0; $x < $w; $x++) {
            $queue->enqueue([$x, 0]);
            $queue->enqueue([$x, $h - 1]);
            $visited[0][$x] = true;
            $visited[$h - 1][$x] = true;
        }
        for ($y = 0; $y < $h; $y++) {
            $queue->enqueue([0, $y]);
            $queue->enqueue([$w - 1, $y]);
            $visited[$y][0] = true;
            $visited[$y][$w - 1] = true;
        }

        while (!$queue->isEmpty()) {
            [$x, $y] = $queue->dequeue();

            $rgb = imagecolorat($src, $x, $y);
            $r = ($rgb >> 16) & 0xFF;
            $g = ($rgb >> 8) & 0xFF;
            $b = $rgb & 0xFF;

            $isPixelBg = false;

            if ($mode === 'dark_checkerboard') {
                // Quadriculado escuro: tons de cinza escuro/preto (R, G, B muito próximos e luminância baixa)
                $maxDiff = max(abs($r - $g), abs($r - $b), abs($g - $b));
                $brightness = ($r + $g + $b) / 3;
                // Quadriculado escuro tem saturação quase zero ($maxDiff < 25) e brilho < 85
                if ($maxDiff <= 28 && $brightness < 90) {
                    $isPixelBg = true;
                }
            } elseif ($mode === 'light_checkerboard') {
                // Quadriculado claro: tons de cinza/branco neutro
                $maxDiff = max(abs($r - $g), abs($r - $b), abs($g - $b));
                $brightness = ($r + $g + $b) / 3;
                if ($maxDiff <= 25 && $brightness > 110) {
                    $isPixelBg = true;
                }
            } elseif ($mode === 'light_solid') {
                // Fundo claro quase branco/cinza claro das bordas
                $maxDiff = max(abs($r - $g), abs($r - $b), abs($g - $b));
                $brightness = ($r + $g + $b) / 3;
                if ($maxDiff <= 20 && $brightness > 220) {
                    $isPixelBg = true;
                }
            }

            if ($isPixelBg) {
                $isBg[$y][$x] = true;

                // Expandir para vizinhos (4 direções)
                $neighbors = [
                    [$x + 1, $y],
                    [$x - 1, $y],
                    [$x, $y + 1],
                    [$x, $y - 1]
                ];

                foreach ($neighbors as [$nx, $ny]) {
                    if ($nx >= 0 && $nx < $w && $ny >= 0 && $ny < $h && !$visited[$ny][$nx]) {
                        $visited[$ny][$nx] = true;
                        $queue->enqueue([$nx, $ny]);
                    }
                }
            }
        }

        // Renderiza dst com suavização de bordas (alpha blending gradual)
        for ($y = 0; $y < $h; $y++) {
            for ($x = 0; $x < $w; $x++) {
                if ($isBg[$y][$x]) {
                    // Totalmente transparente
                    imagesetpixel($dst, $x, $y, $transparent);
                } else {
                    // Verifica se é vizinho imediato de pixel transparente para aplicar anti-aliasing
                    $hasBgNeighbor = false;
                    for ($dy = -1; $dy <= 1; $dy++) {
                        for ($dx = -1; $dx <= 1; $dx++) {
                            $nx = $x + $dx;
                            $ny = $y + $dy;
                            if ($nx >= 0 && $nx < $w && $ny >= 0 && $ny < $h && $isBg[$ny][$nx]) {
                                $hasBgNeighbor = true;
                                break 2;
                            }
                        }
                    }

                    $rgb = imagecolorat($src, $x, $y);
                    $r = ($rgb >> 16) & 0xFF;
                    $g = ($rgb >> 8) & 0xFF;
                    $b = $rgb & 0xFF;

                    if ($hasBgNeighbor) {
                        // Suavização leve nas bordas
                        $col = imagecolorallocatealpha($dst, $r, $g, $b, 30);
                    } else {
                        $col = imagecolorallocatealpha($dst, $r, $g, $b, 0);
                    }
                    imagesetpixel($dst, $x, $y, $col);
                }
            }
        }

        imagepng($dst, $dstPath, 9);
        imagedestroy($src);
        imagedestroy($dst);

        CLI::write("Salvo PNG transparente: {$dstPath}", 'cyan');
    }
}
