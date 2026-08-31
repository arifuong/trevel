<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CleanTemporaryUploadsTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_cleans_expired_temporary_files(): void
    {
        Storage::fake('public');

        // Create old temp file
        Storage::disk('public')->put('temp/jamaah/old_temp.jpg', 'fake-image-content');
        
        // Travel 3 hours into future
        $this->travel(3)->hours();

        $this->artisan('jamaah:clean-temp-files', ['--hours' => 2])
            ->assertSuccessful()
            ->expectsOutputToContain('Pembersihan selesai');

        Storage::disk('public')->assertMissing('temp/jamaah/old_temp.jpg');
    }
}
