<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use thiagoalessio\TesseractOCR\TesseractOCR;
use App\Models\Product;
class OCRController extends Controller
{
    public function processPrescription(Request $request)
    {
        try {
            $request->validate([
                'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);
    
            $imagePath = $request->file('image')->store('public/prescriptions');
    
            $ocr = new TesseractOCR(storage_path('app/' . $imagePath));
            $ocr->executable('C:\\Program Files\\Tesseract-OCR\\tesseract.exe');
            $extractedText = $ocr->run();
        
            $words = preg_split('/[\s,]+/', $extractedText);
            $words = array_map(function ($word) {
                return preg_replace('/[^a-zA-Z]/', '', $word);
            }, $words);
            $words = array_filter($words);
        
            $queryWords = [];
    
            foreach ($words as $word) {
                $isMatched = Product::where('name', 'like', '%' . $word . '%')
                    ->where(['status' => 1, 'is_approved' => 1])
                    ->exists();
    
                if ($isMatched) {
                    $queryWords[] = $word;
                }
            }
    
            if (empty($queryWords)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No matching product found',
                ]);
            }
    
            $finalQuery = implode(', ', array_unique($queryWords)); // Remove duplicates and join
        
            return response()->json([
                'success' => true,
                'extractedText' => $finalQuery,
                'queryString' => $finalQuery,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing the image.',
            ]);
        }
    }
}