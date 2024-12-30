<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use thiagoalessio\TesseractOCR\TesseractOCR;
use App\Models\Product;
use Spatie\Image\Image;

class OCRController extends Controller
{
    public function processPrescription(Request $request)
    {
        try {
            $request->validate([
                'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            $imagePath = $request->file('image')->store('public/prescriptions');
            $absolutePath = storage_path('app/' . $imagePath);

            $originalCopyPath = str_replace('.png', '_original.png', $absolutePath);
            copy($absolutePath, $originalCopyPath);

            Image::load($absolutePath)
                ->brightness(20)
                ->contrast(15)
                ->sharpen(10)
                ->save($absolutePath);

            $ocr = new TesseractOCR($absolutePath);
            $ocr->executable('/home/linuxbrew/.linuxbrew/bin/tesseract');
            $extractedText = $ocr->run();

            $rows = preg_split('/\r\n|\r|\n/', $extractedText);
            $rows = array_filter($rows, fn($row) => trim($row) !== '');

            $matchedRows = [];
            foreach ($rows as $row) {
                $cleanRow = preg_replace('/[^\w\s]/', '', $row);
                $cleanRow = trim($cleanRow);
                if (empty($cleanRow)) {
                    continue;
                }

                $words = preg_split('/\s+/', $cleanRow);
                $phrase = '';
                foreach ($words as $word) {
                    $excludedWords = ['name', 'age', 'sex', 'date', 'address'];
                    if (in_array(strtolower($word), $excludedWords) || strlen($word) == 1) {
                        continue;
                    }

                    $phrase = trim($phrase . ' ' . $word);

                    if (Product::where('name', 'like', '%' . $phrase . '%')->exists()) {
                        $matchedRows[] = $phrase;
                        break;
                    }
                }
            }

            $finalQueryString = implode(', ', $matchedRows);

            if (empty($matchedRows)) {
                $finalQueryString = 'No matching product found';
            }

            \Log::info('Final QueryString Text: ' . $finalQueryString);
            return response()->json([
                'success' => true,
                'extractedText' => $finalQueryString,
            ]);
        } catch (\Exception $e) {
            \Log::error('OCR processing error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing the image.',
            ]);
        }
    }
}
