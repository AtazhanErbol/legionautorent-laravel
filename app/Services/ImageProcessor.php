<?php

namespace App\Services;

use App\Models\CarImage;
use Illuminate\Support\Facades\Storage;

class ImageProcessor
{
    public function process(CarImage $photo): void
    {
        if (! $photo->original || ($photo->image && ! $photo->wasChanged('original'))) {
            return;
        }
        $path = Storage::disk('media')->path($photo->original);
        if (! is_file($path)) {
            return;
        }$raw = file_get_contents($path);
        $source = @imagecreatefromstring($raw);
        if (! $source) {
            CmsValidation::fail('original', 'Загрузите корректное изображение.');
        }
        if (function_exists('exif_read_data') && ($exif = @exif_read_data($path)) && isset($exif['Orientation'])) {
            $o = $exif['Orientation'];
            if (in_array($o, [3, 5, 6, 7, 8])) {
                $source = imagerotate($source, [3 => 180, 5 => -90, 6 => -90, 7 => -90, 8 => 90][$o], 0);
            }if (in_array($o, [2, 4, 5, 7])) {
                imageflip($source, in_array($o, [2, 5]) ? IMG_FLIP_HORIZONTAL : IMG_FLIP_VERTICAL);
            }
        }
        $sw = imagesx($source);
        $sh = imagesy($source);
        $stem = pathinfo($photo->original, PATHINFO_FILENAME).'-'.substr(hash('sha256', $raw), 0, 12);
        $variants = [];
        $fields = [];
        foreach ([['image', 1200, false, 82], ['small', 640, false, 82], ['card_image', 960, true, 76], ['card_small', 640, true, 76]] as [$key,$width,$crop,$quality]) {
            $width = min($width, $sw);
            $height = $crop ? (int) round($width / 1.55) : (int) round($sh * $width / $sw);
            $im = imagecreatetruecolor($width, $height);
            $background = imagecolorallocate($im, 16, 16, 15);
            imagefill($im, 0, 0, $background);
            $srcw = $sw;
            $srch = $sh;
            $sx = 0;
            $sy = 0;
            if ($crop) {
                if ($sw / $sh > 1.55) {
                    $srcw = (int) ($sh * 1.55);
                    $sx = (int) (($sw - $srcw) / 2);
                } else {
                    $srch = (int) ($sw / 1.55);
                    $sy = (int) (($sh - $srch) / 2);
                }
            }imagecopyresampled($im, $source, 0, 0, $sx, $sy, $width, $height, $srcw, $srch);
            $rel = 'cars/webp/'.$stem.'-'.$key.'.webp';
            Storage::disk('media')->makeDirectory('cars/webp');
            imagewebp($im, Storage::disk('media')->path($rel), $quality);
            $fields[$key] = $rel;
            if ($key === 'image') {
                $fields['width'] = $width;
                $fields['height'] = $height;
            }if ($key === 'card_image') {
                $fields['card_width'] = $width;
                $fields['card_height'] = $height;
            }
            if (function_exists('imageavif')) {
                $avif = 'cars/webp/'.$stem.'-'.$key.'.avif';
                if (@imageavif($im, Storage::disk('media')->path($avif), 55, 6)) {
                    $variants[$crop ? 'card_avif' : 'avif'][(string) $width] = $avif;
                }
            }imagedestroy($im);
        }imagedestroy($source);
        $fields['variants'] = $variants;
        $photo->forceFill($fields)->saveQuietly();
    }
}
