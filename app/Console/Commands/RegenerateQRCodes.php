<?php

namespace App\Console\Commands;

use App\Models\DigitalSticker;
use App\Services\QRCodeService;
use Illuminate\Console\Command;

class RegenerateQRCodes extends Command
{
    protected $signature = 'stickers:regenerate-qr';

    protected $description = 'Regenerate all QR code PNG images to encode the sticker URL instead of the old JSON payload';

    public function handle(QRCodeService $qrCodeService): int
    {
        $total = DigitalSticker::count();

        if ($total === 0) {
            $this->info('No stickers found. Nothing to do.');

            return self::SUCCESS;
        }

        $this->info("Regenerating QR codes for {$total} sticker(s)...");

        $bar = $this->output->createProgressBar($total);
        $bar->start();

        $regenerated = 0;
        $failed = 0;

        DigitalSticker::chunk(100, function ($stickers) use ($qrCodeService, $bar, &$regenerated, &$failed) {
            foreach ($stickers as $sticker) {
                try {
                    $url = url(route('student.sticker', ['token' => $sticker->qr_code_token], absolute: true));
                    $qrCodeService->generateQRImage($sticker->qr_code_token, $url);
                    $regenerated++;
                } catch (\Throwable $e) {
                    $failed++;
                    $this->newLine();
                    $this->error("Failed for token {$sticker->qr_code_token}: {$e->getMessage()}");
                }

                $bar->advance();
            }
        });

        $bar->finish();
        $this->newLine();
        $this->info("Done. {$regenerated} regenerated, {$failed} failed.");

        return $failed > 0 ? self::FAILURE : self::SUCCESS;
    }
}
