<?php

namespace App\Helpers;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class KompresiFoto
{
    /**
     * Kompres, resize, dan simpan foto ke storage.
     *
     * @param UploadedFile $file      File yang diupload
     * @param string       $folder    Folder tujuan di storage/public (e.g. 'ceklis')
     * @param int          $maxLebar  Lebar maksimal piksel (default 1280)
     * @param int          $kualitas  Kualitas JPEG 0-100 (default 80)
     * @return string                 Path relatif dari storage/public
     */
    public static function simpan(
        UploadedFile $file,
        string $folder,
        int $maxLebar = 1280,
        int $kualitas = 80
    ): string {
        // Baca gambar menggunakan GD (built-in PHP)
        $mime = $file->getMimeType();
        $tmpPath = $file->getRealPath();

        $src = match ($mime) {
            'image/jpeg', 'image/jpg' => @imagecreatefromjpeg($tmpPath),
            'image/png'               => @imagecreatefrompng($tmpPath),
            'image/webp'              => @imagecreatefromwebp($tmpPath),
            'image/gif'               => @imagecreatefromgif($tmpPath),
            default                   => null,
        };

        // Jika GD gagal baca (format tidak dikenal), simpan langsung tanpa kompresi
        if (!$src) {
            return $file->store($folder, 'public');
        }

        $lebarAsli  = imagesx($src);
        $tinggiAsli = imagesy($src);

        // Hitung dimensi baru proporsional
        if ($lebarAsli > $maxLebar) {
            $rasio      = $maxLebar / $lebarAsli;
            $lebarBaru  = $maxLebar;
            $tinggiBaru = (int) round($tinggiAsli * $rasio);
        } else {
            // Tidak perlu resize, cukup kompres kualitas
            $lebarBaru  = $lebarAsli;
            $tinggiBaru = $tinggiAsli;
        }

        // Buat canvas baru
        $dst = imagecreatetruecolor($lebarBaru, $tinggiBaru);

        // Pertahankan transparansi untuk PNG
        if ($mime === 'image/png') {
            imagealphablending($dst, false);
            imagesavealpha($dst, true);
            $transparan = imagecolorallocatealpha($dst, 255, 255, 255, 127);
            imagefilledrectangle($dst, 0, 0, $lebarBaru, $tinggiBaru, $transparan);
        } else {
            // Latar putih untuk JPEG
            $putih = imagecolorallocate($dst, 255, 255, 255);
            imagefilledrectangle($dst, 0, 0, $lebarBaru, $tinggiBaru, $putih);
        }

        // Resize
        imagecopyresampled($dst, $src, 0, 0, 0, 0, $lebarBaru, $tinggiBaru, $lebarAsli, $tinggiAsli);

        // Simpan ke buffer
        $namaFile = $folder . '/' . Str::uuid() . '.jpg';
        $pathAbsolut = storage_path('app/public/' . $namaFile);

        // Pastikan direktori ada
        $dir = dirname($pathAbsolut);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        // Selalu simpan sebagai JPEG untuk konsistensi & ukuran kecil
        imagejpeg($dst, $pathAbsolut, $kualitas);

        // Cleanup memory
        imagedestroy($src);
        imagedestroy($dst);

        return $namaFile;
    }

    /**
     * Kompres banyak file sekaligus, return array of paths.
     *
     * @param UploadedFile[] $files
     * @param string         $folder
     * @param int            $maxLebar
     * @param int            $kualitas
     * @return string[]
     */
    public static function simpanBanyak(
        array $files,
        string $folder,
        int $maxLebar = 1280,
        int $kualitas = 80
    ): array {
        $paths = [];
        foreach ($files as $file) {
            if ($file && $file->isValid()) {
                $paths[] = self::simpan($file, $folder, $maxLebar, $kualitas);
            }
        }
        return $paths;
    }
}
