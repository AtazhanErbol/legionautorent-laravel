<?php

namespace App\Services;

use App\Models\SiteSettings;
use Symfony\Component\Process\Process;

class HeroMedia
{
    public function prepare(SiteSettings $site): void
    {
        foreach (['hero_video_file' => 'hero_video_path', 'hero_mobile_video_file' => 'hero_mobile_video_path', 'hero_image' => 'hero_poster_path', 'hero_mobile_image' => 'hero_mobile_poster_path', 'hero_ending_image' => 'hero_ending_path'] as $upload => $active) {
            if ($site->isDirty($upload) && $site->$upload) {
                if (str_contains($upload, 'video')) {
                    $this->validateVideo(public_path('media/'.$site->$upload), $upload, (int) $site->hero_video_fps);
                }
                $site->$active = '/media/'.$site->$upload;
            }
        }
        foreach (['hero_video_path', 'hero_mobile_video_path', 'hero_poster_path', 'hero_mobile_poster_path', 'hero_ending_path'] as $field) {
            $value = $site->$field;
            if ($value && (! preg_match('#^/(static|media)/[a-zA-Z0-9_./-]+$#', $value) || str_contains($value, '..'))) {
                CmsValidation::fail($field, 'Выберите существующий файл в /static/ или /media/.');
            }
            if ($value && $site->isDirty($field) && ! is_file(public_path(ltrim($value, '/')))) {
                CmsValidation::fail($field, 'Файл не найден.');
            }
        }
    }

    public function validateVideo(string $path, string $field, int $fps): void
    {
        if (! is_file($path) || filesize($path) > 20 * 1024 * 1024) {
            CmsValidation::fail($field, 'MP4 не должен превышать 20 МБ.');
        }
        $probe = new Process([config('legion.ffprobe'), '-v', 'error', '-show_streams', '-show_format', '-of', 'json', $path]);
        $probe->setTimeout(30);
        try {
            $probe->mustRun();
            $metadata = json_decode($probe->getOutput(), true, 512, JSON_THROW_ON_ERROR);
        } catch (\Throwable) {
            CmsValidation::fail($field, 'Не удалось проверить MP4. Настройте FFPROBE_PATH на сервере.');
        }
        $video = null;
        foreach ($metadata['streams'] ?? [] as $stream) {
            if (($stream['codec_type'] ?? '') === 'audio') {
                CmsValidation::fail($field, 'Уберите звуковую дорожку перед загрузкой.');
            }if (($stream['codec_type'] ?? '') === 'video') {
                $video = $stream;
            }
        }
        if (! $video || ($video['codec_name'] ?? '') !== 'h264') {
            CmsValidation::fail($field, 'Нужен H.264 MP4.');
        }
        [$num,$den] = array_pad(explode('/', $video['avg_frame_rate'] ?? '0/1'), 2, 1);
        $actual = (int) round((float) $num / max(1, (float) $den));
        if ($fps !== $actual) {
            CmsValidation::fail('hero_video_fps', 'Фактическая частота кадров: '.$actual.'.');
        }
    }
}
