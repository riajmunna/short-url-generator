<?php

namespace App\Http\Controllers;

use App\Models\Url;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ShortUrlGeneratorController extends Controller
{
    public function generateShortUrl(Request $request){

        $request->validate([
            'originalUrl' => 'required|url',
        ]);

        $originalUrl = $request->originalUrl;

        $savedUrl = Url::where('original_url',$originalUrl)->first();

        if($savedUrl){
            return response()->json([
                'short_url' => $savedUrl->short_url
            ]);
        }

        do {
            $newShortUrl = Str::random(6);
        } while (Url::where('short_url', $newShortUrl)->exists());

        $url = Url::create([
            'original_url' => $originalUrl,
            'short_url' => $newShortUrl,
        ]);

        return response()->json([
            'short_url' => $url->short_url,
        ], 200);
    }

    public function redirectToOriginalUrl($shortUrl){

        if(strlen($shortUrl) != 6){
            return response()->json([
                'error' => 'Short URL must be 6 characters!'
            ], 400);
        }

        $url = Url::where('short_url',$shortUrl)->first();

        if (!$url) {
            return response()->json([
                'error' => 'Short URL not found!'
            ], 404);
        }

        return redirect()->away($url->original_url);
    }

}
