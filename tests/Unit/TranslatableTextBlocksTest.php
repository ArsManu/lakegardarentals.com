<?php

namespace Tests\Unit;

use App\Services\TranslatableTextBlocks;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(TranslatableTextBlocks::class)]
class TranslatableTextBlocksTest extends TestCase
{
    public function test_subtitle_with_wysiwyg_html_is_treated_as_html(): void
    {
        $data = [
            'flex' => [
                [
                    'type' => 'test',
                    'data' => [
                        'subtitle' => '<p><span class="x">Hello</span></p>',
                    ],
                ],
            ],
        ];
        $segments = TranslatableTextBlocks::extractSegments($data);
        $this->assertCount(1, $segments);
        $this->assertTrue($segments[0]['is_html'] ?? false);
    }

    public function test_title_without_tags_is_plain(): void
    {
        $data = [
            'flex' => [
                [
                    'type' => 'test',
                    'data' => [
                        'title' => 'Plain',
                    ],
                ],
            ],
        ];
        $segments = TranslatableTextBlocks::extractSegments($data);
        $this->assertCount(1, $segments);
        $this->assertFalse($segments[0]['is_html'] ?? true);
    }

    public function test_contact_page_block_keys_are_extracted(): void
    {
        $data = [
            'hero_title' => 'Contact page title',
            'hero_subtitle' => '<p>Sub</p>',
            'reassurance' => '<p>We reply fast</p>',
        ];
        $segments = TranslatableTextBlocks::extractSegments($data);
        $this->assertCount(3, $segments);
        $this->assertStringContainsString('title', (string) $segments[0]['value']);
        $this->assertTrue($segments[1]['is_html'] ?? false);
        $this->assertTrue($segments[2]['is_html'] ?? false);
    }
}
