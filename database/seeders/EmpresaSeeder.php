<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Empresa;
use Illuminate\Support\Facades\Storage;

class EmpresaSeeder extends Seeder
{
    public function run(): void
    {
        // Garante que exista a pasta e um logo fictício
        if (!Storage::disk('public')->exists('logos')) {
            Storage::disk('public')->makeDirectory('logos');
        }

        $logoPath = 'logos/logo-fake.png';
        if (!Storage::disk('public')->exists($logoPath)) {
            $img = imagecreate(150, 80);
            $bg = imagecolorallocate($img, 0, 123, 255);
            $textColor = imagecolorallocate($img, 255, 255, 255);
            imagestring($img, 5, 25, 30, 'LOGO', $textColor);

            ob_start();
            imagepng($img);
            $imageData = ob_get_clean();
            imagedestroy($img);

            Storage::disk('public')->put($logoPath, $imageData);
        }

        Empresa::updateOrCreate(
            ['nome' => 'Estoque SyntaxWeb'],
            [
                'nome' => 'Estoque SyntaxWeb',
                'razao_social' => 'Estoque SyntaxWeb',
                'cnpj' => '12.345.678/0001-99',
                'telefone' => '(21) 98765-4321',
                'email' => 'contato@syntaxweb.com.br',
                'endereco' => '',
                'logo' => $logoPath,
            ]
        );
    }
}
