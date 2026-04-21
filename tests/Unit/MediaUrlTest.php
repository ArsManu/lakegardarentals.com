<?php

namespace Tests\Unit;

use App\Support\MediaUrl;
use Tests\TestCase;

class MediaUrlTest extends TestCase
{
    public function test_local_public_paths_use_relative_storage_url(): void
    {
        $this->assertSame('/storage/pages/1/hero/test.jpg', MediaUrl::public('pages/1/hero/test.jpg'));
    }

    public function test_absolute_urls_pass_through(): void
    {
        $url = 'https://example.com/photo.jpg';

        $this->assertSame($url, MediaUrl::public($url));
    }
}
