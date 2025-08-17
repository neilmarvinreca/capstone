<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\DeployedItem;
use App\Services\QRCodeService;

class GenerateQrCodesForDeployedItems extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'deployed-items:generate-qr-codes {--force : Force regeneration of existing QR codes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate QR code images for deployed items';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting QR code generation for deployed items...');

        $query = DeployedItem::query();
        
        if (!$this->option('force')) {
            $query->whereNull('qr_code_image');
        }

        $deployedItems = $query->get();
        
        if ($deployedItems->isEmpty()) {
            $this->info('No deployed items found that need QR code generation.');
            return 0;
        }

        $this->info("Found {$deployedItems->count()} deployed items to process.");
        
        $bar = $this->output->createProgressBar($deployedItems->count());
        $bar->start();

        $successCount = 0;
        $errorCount = 0;

        foreach ($deployedItems as $deployedItem) {
            try {
                if ($deployedItem->generateQrCodeImage()) {
                    $successCount++;
                } else {
                    $errorCount++;
                    $this->warn("Failed to generate QR code for item {$deployedItem->deployedID}");
                }
            } catch (\Exception $e) {
                $errorCount++;
                $this->error("Error generating QR code for item {$deployedItem->deployedID}: {$e->getMessage()}");
            }
            
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->info("QR code generation completed!");
        $this->info("Successfully generated: {$successCount}");
        $this->info("Failed: {$errorCount}");

        return 0;
    }
}
