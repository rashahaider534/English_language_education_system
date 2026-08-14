<?php

namespace App\Services;

use App\Models\Certificate;
use App\Models\User;
use App\Models\UserLevel;
use ArPHP\I18N\Arabic;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Typography\FontFactory;
use Intervention\Image\Alignment;
use Illuminate\Support\Str;

class CertificateService
{
    public function issueForUserLevel(UserLevel $userLevel): Certificate
    {
        $existing = Certificate::where('user_level_id', $userLevel->id)->first();
        if ($existing) {
            return $existing;
        }

        $user = $userLevel->user;
        $level = $userLevel->level;

        $certificate = Certificate::create([
            'user_level_id'      => $userLevel->id,
            'certificate_number' => $this->generateCertificateNumber(),
            'student_name'       => $user->full_name,
            'level_name'         => $level->name_en,
            'issued_at'          => now(),
        ]);

        $imagePath = $this->generateCertificateImage($certificate);

        try {
            $certificate->addMedia($imagePath)
                ->usingFileName($certificate->certificate_number . '.png')
                ->toMediaCollection('certificate');
        } finally {
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }

        return $certificate;
    }

    private function generateCertificateNumber(): string
    {
        do {
            $number = 'CERT-' . now()->format('Y') . '-' . strtoupper(Str::random(8));
        } while (Certificate::where('certificate_number', $number)->exists());

        return $number;
    }

    private function generateCertificateImage(Certificate $certificate): string
    {
        $manager = ImageManager::usingDriver(Driver::class);
        $image = $manager->decode(config('certificates.template_path'));

        $this->writeText($image, $certificate->student_name, config('certificates.student_name'));
        $this->writeText($image, $certificate->level_name, config('certificates.level_name'));
        $this->writeText($image, $certificate->certificate_number, config('certificates.certificate_number'));
        $this->writeText($image, $certificate->issued_at->format('Y-m-d'), config('certificates.issued_at'));

        $tempPath = storage_path('app/tmp/' . Str::uuid() . '.png');

        if (! is_dir(dirname($tempPath))) {
            mkdir(dirname($tempPath), 0755, true);
        }

        $image->save($tempPath);

        return $tempPath;
    }

//    private function writeText($image, string $text, array $cfg): void
//    {
//        $image->text($text, $cfg['x'], $cfg['y'], function (FontFactory $font) use ($cfg) {
//            $font->filepath(config('certificates.font_path'));
//            $font->size($cfg['size']);
//            $font->color($cfg['color']);
//            $font->align(
//                $cfg['align'] === 'center' ? Alignment::CENTER : Alignment::LEFT,
//                Alignment::CENTER
//            );
//        });
//    }
    private function writeText($image, string $text, array $cfg): void
    {
        $image->text($this->prepareText($text), $cfg['x'], $cfg['y'], function (FontFactory $font) use ($cfg) {
            $font->filepath(config('certificates.font_path'));
            $font->size($cfg['size']);
            $font->color($cfg['color']);
            $font->align(
                $cfg['align'] === 'center' ? Alignment::CENTER : Alignment::LEFT,
                Alignment::CENTER
            );
        });
    }

    private function prepareText(string $text): string
    {
        if (! $this->containsArabic($text)) {
            return $text;
        }
        $arabic = new Arabic();
        return $arabic->utf8Glyphs($text);
    }

    private function containsArabic(string $text): bool
    {
        return (bool) preg_match('/[\x{0600}-\x{06FF}]/u', $text);
    }

    public function getStudentCertificates(User $user)
    {
        return Certificate::query()
            ->whereHas('userLevel', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->latest('issued_at')
            ->get()
            ->map(function (Certificate $certificate) {
                return [
                    'id'                 => $certificate->id,
                    'certificate_number' => $certificate->certificate_number,
                    'level_name'         => $certificate->level_name,
                    'issued_at'          => $certificate->issued_at->format('Y-m-d'),
                    'download_url'       => $certificate->certificate_url,
                ];
            });
    }
}
