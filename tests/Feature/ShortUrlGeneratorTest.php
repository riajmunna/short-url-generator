<?php

namespace Tests\Feature;

use App\Models\Url;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShortUrlGeneratorTest extends TestCase
{
    use RefreshDatabase;

    public function testOriginalUrlIsRequired()
    {
        $response = $this->postJson('/api/generate-short-url', []);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['originalUrl']);
    }

    public function testOriginalUrlIsNotAnUrl()
    {
        $response = $this->postJson('/api/generate-short-url', ['original_url' => 'https://www.sheba']);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['originalUrl']);
    }

    public function canGenerateShortUrlFromValidUrl()
    {
        $response = $this->postJson('/api/generate-short-url', ['original_url' => 'https://www.sheba.xyz']);
        $response->assertStatus(200);
        $response->assertJsonStructure(['short_url']);
        $this->assertDatabaseHas('urls', ['original_url' => 'https://www.sheba.xyz']);
    }

    public function testShortUrlShouldBeSixCharacters()
    {
        $response = $this->get('/api/redirect-original-url/dummyurl123');
        $response->assertStatus(400);
        $response->assertJson(['error' => 'Short URL must be 6 characters!']);
    }

    public function detectInvalidShortUrl()
    {
        $response = $this->get('/api/redirect-original-url/abcABC');
        $response->assertStatus(404);
        $response->assertJson(['error' => 'Short URL not found!']);
    }

    public function testRedirectToOriginalUrlForValidShortUrl()
    {
        $url = Url::create([
            'original_url' => 'https://www.sheba.xyz',
            'short_url' => 'abcXYZ'
        ]);

        $response = $this->get('/api/redirect-original-url/abcXYZ');
        $response->assertStatus(302);
        $response->assertRedirect($url->original_url);
    }

}
