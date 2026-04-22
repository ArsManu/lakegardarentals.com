<?php

namespace Tests\Unit;

use Tests\TestCase;

class TransPageStringTest extends TestCase
{
    public function test_translates_plain_english_key_when_locale_is_italian(): void
    {
        app()->setLocale('it');
        $this->assertSame('I nostri appartamenti', trans_page_string('Our apartments', ''));
    }

    public function test_leaves_html_unchanged(): void
    {
        app()->setLocale('it');
        $html = '<p>Hello</p>';
        $this->assertSame($html, trans_page_string($html, ''));
    }

    public function test_uses_fallback_when_value_empty(): void
    {
        app()->setLocale('it');
        $this->assertStringContainsString('Contatti', trans_page_string(null, 'Contact & booking'));
    }
}
