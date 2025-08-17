<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QRCodeService
{
    /**
     * Generate a QR code image and store it in storage
     *
     * @param string $data The data to encode in the QR code
     * @param string $filename Optional custom filename
     * @return string The stored file path
     */
    public static function generateAndStore($data, $filename = null)
    {
        // Generate filename if not provided
        if (!$filename) {
            $filename = 'qr_' . time() . '_' . str_random(8) . '.png';
        }

        try {
            // Generate QR code as PNG using the SimpleSoftwareIO package
            $qrCodeImage = QrCode::format('png')
                ->size(300)
                ->margin(10)
                ->errorCorrection('H')
                ->generate($data);

            // Debug logging
            \Log::info('QR Code generation debug', [
                'data' => $data,
                'image_size' => strlen($qrCodeImage),
                'image_start' => substr($qrCodeImage, 0, 20),
                'is_png' => strpos($qrCodeImage, "\x89PNG") === 0
            ]);

            // Check if we got valid image data
            if (empty($qrCodeImage) || strlen($qrCodeImage) < 100) {
                throw new \Exception('Generated QR code image is too small or empty');
            }

            // Check if it's a valid PNG file
            if (strpos($qrCodeImage, "\x89PNG") !== 0) {
                throw new \Exception('Generated image is not a valid PNG file');
            }

            // Store in public/qr-codes directory
            $path = 'qr-codes/' . $filename;
            Storage::disk('public')->put($path, $qrCodeImage);

            return $path;
        } catch (\Exception $e) {
            \Log::error('QR Code generation failed: ' . $e->getMessage());
            
            // Fallback: create a simple text-based image
            $fallbackImage = self::createFallbackImage($data);
            $path = 'qr-codes/' . $filename;
            Storage::disk('public')->put($path, $fallbackImage);
            
            return $path;
        }
    }

    /**
     * Create a fallback image when QR code generation fails
     */
    private static function createFallbackImage($data)
    {
        // Create a simple 300x300 PNG image with the QR code data
        $width = 300;
        $height = 300;
        
        // Create image
        $image = imagecreate($width, $height);
        
        // Define colors
        $white = imagecolorallocate($image, 255, 255, 255);
        $black = imagecolorallocate($image, 0, 0, 0);
        $blue = imagecolorallocate($image, 59, 130, 246);
        
        // Fill background
        imagefill($image, 0, 0, $white);
        
        // Draw border
        imagerectangle($image, 0, 0, $width-1, $height-1, $blue);
        
        // Add title
        imagestring($image, 4, 20, 20, 'QR Code', $black);
        
        // Add the QR code data
        $text = $data;
        $lines = str_split($text, 15); // Split long text into lines
        $y = 80;
        
        foreach ($lines as $line) {
            imagestring($image, 3, 20, $y, $line, $black);
            $y += 25;
        }
        
        // Add note
        imagestring($image, 2, 20, $height - 40, 'Scan this code', $blue);
        
        // Output as PNG
        ob_start();
        imagepng($image);
        $imageData = ob_get_contents();
        ob_end_clean();
        
        // Clean up
        imagedestroy($image);
        
        return $imageData;
    }

    /**
     * Generate a QR code image for a deployed item
     *
     * @param \App\Models\DeployedItem $deployedItem
     * @return string The stored file path
     */
    public static function generateForDeployedItem($deployedItem)
    {
        // Use the QR code text directly (e.g., "DP-BUBXNJB5KD")
        $qrData = $deployedItem->qrCode;

        // Generate filename based on deployed item
        $filename = 'deployed_item_' . $deployedItem->deployedID . '_' . time() . '.png';

        return self::generateAndStore($qrData, $filename);
    }

    /**
     * Delete a QR code image from storage
     *
     * @param string $path The file path to delete
     * @return bool
     */
    public static function delete($path)
    {
        if (Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->delete($path);
        }
        return false;
    }

    /**
     * Get the full URL for a QR code image
     *
     * @param string $path The stored file path
     * @return string The full URL
     */
    public static function getUrl($path)
    {
        if (!$path) {
            return null;
        }
        
        // Check if file exists in storage
        if (!Storage::disk('public')->exists($path)) {
            \Log::warning('QR code image not found in storage: ' . $path);
            return null;
        }
        
        try {
            // Use asset() helper for public storage
            $url = asset('storage/' . $path);
            
            // Ensure we have a proper URL
            if (strpos($url, 'http') !== 0) {
                $url = url($url);
            }
            
            \Log::info('QR code image URL generated: ' . $url);
            return $url;
        } catch (\Exception $e) {
            \Log::error('Failed to generate QR code image URL: ' . $e->getMessage());
            return null;
        }
    }
}
