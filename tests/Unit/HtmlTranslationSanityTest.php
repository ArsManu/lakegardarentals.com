<?php

namespace Tests\Unit;

use App\Support\HtmlTranslationSanity;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(HtmlTranslationSanity::class)]
class HtmlTranslationSanityTest extends TestCase
{
    public function test_sane_wysiwyg_passes(): void
    {
        $html = '<p><span class="x">Hello</span></p>';
        $this->assertTrue(HtmlTranslationSanity::isStructurallySane($html));
        $this->assertSame($html, HtmlTranslationSanity::toDisplayableHtml($html));
    }

    public function test_orphan_closings_fail_and_display_as_safe_plain_in_p(): void
    {
        $html = 'Entspannung und Abenteuer.</span></p>';
        $this->assertFalse(HtmlTranslationSanity::isStructurallySane($html));
        $out = HtmlTranslationSanity::toDisplayableHtml($html);
        $this->assertStringNotContainsString('</span>', $out);
        $this->assertSame('<p>Entspannung und Abenteuer.</p>', $out);
    }

    public function test_strip_json_style_escapes_from_model_html(): void
    {
        $html = '<p>Hello<\/span><\/p>';
        $this->assertSame('<p>Hello</span></p>', HtmlTranslationSanity::stripLlmJsonEscapesFromHtml($html));
    }

    public function test_to_displayable_html_fixes_escaped_closers_and_marks_bold_artifacts_from_llm(): void
    {
        $html = '<p>Teilen Sie uns Ihre Reisedaten mit und wir melden uns schnell bei Ihnen.</p><p>Optionen.**<\/span><\/p>**';
        $out = HtmlTranslationSanity::toDisplayableHtml($html);
        $this->assertStringNotContainsString('\\/', $out);
        $this->assertStringNotContainsString('**</', $out, 'markdown-style wrappers around spurious closings should not appear in output');
    }
}
