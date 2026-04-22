<?php

namespace App\Services;

use App\Support\HtmlTranslationSanity;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use InvalidArgumentException;
use RuntimeException;
use Throwable;

class OpenAILocaleTranslator
{
    private const BATCH_SIZE = 20;

    public function isConfigured(): bool
    {
        $key = config('services.openai.key');

        return is_string($key) && trim($key) !== '';
    }

    public function toTargetLabel(string $appLocale): string
    {
        $map = [
            'de' => 'German',
            'it' => 'Italian',
        ];
        $label = $map[$appLocale] ?? null;
        if ($label === null) {
            throw new InvalidArgumentException("Unsupported target locale: {$appLocale}.");
        }

        return $label;
    }

    /**
     * @param  list<string>  $texts
     * @return list<string>
     */
    public function translateMany(array $texts, string $targetAppLocale, bool $asHtml = false): array
    {
        if (! $this->isConfigured() || $texts === []) {
            return $texts;
        }

        $out = $texts;
        $targetLabel = $this->toTargetLabel($targetAppLocale);
        $nonEmptyIdx = [];
        $nonEmpty = [];
        foreach ($texts as $i => $t) {
            if (is_string($t) && $t !== '') {
                $nonEmptyIdx[] = $i;
                $nonEmpty[] = $t;
            }
        }
        if ($nonEmpty === []) {
            return $out;
        }
        for ($off = 0; $off < count($nonEmpty); $off += self::BATCH_SIZE) {
            $batch = array_slice($nonEmpty, $off, self::BATCH_SIZE);
            try {
                $translated = $this->callTranslateBatch($batch, $targetLabel, $asHtml);
            } catch (Throwable) {
                $translated = null;
            }
            if ($translated === null || count($translated) !== count($batch)) {
                foreach ($batch as $j => $origText) {
                    $origI = $nonEmptyIdx[$off + $j] ?? null;
                    if ($origI !== null) {
                        $out[$origI] = is_string($origText) ? $origText : (string) $origText;
                    }
                }

                continue;
            }
            foreach ($batch as $j => $origText) {
                $origI = $nonEmptyIdx[$off + $j] ?? null;
                if ($origI === null) {
                    continue;
                }
                $raw = (string) ($translated[$j] ?? $out[$origI]);
                if ($asHtml && $raw !== '' && ! HtmlTranslationSanity::isStructurallySane($raw)) {
                    $out[$origI] = is_string($origText) ? $origText : (string) $origText;
                } else {
                    $out[$origI] = $raw;
                }
            }
        }

        return $out;
    }

    /**
     * @param  list<array{value: string, is_html: bool}>  $segmentMeta
     * @return list<string>
     */
    public function translateSegmentsInOrder(array $segmentMeta, string $targetAppLocale): array
    {
        if (! $this->isConfigured() || $segmentMeta === []) {
            return array_map(fn (array $m) => is_string($m['value'] ?? null) ? $m['value'] : '', $segmentMeta);
        }
        $plain = [];
        $html = [];
        $pIdx = [];
        $hIdx = [];
        foreach ($segmentMeta as $i => $row) {
            $v = is_string($row['value'] ?? null) ? $row['value'] : '';
            if (($row['is_html'] ?? false) === true) {
                $hIdx[] = $i;
                $html[] = $v;
            } else {
                $pIdx[] = $i;
                $plain[] = $v;
            }
        }
        $plainT = $this->translateMany($plain, $targetAppLocale, false);
        $htmlT = $this->translateMany($html, $targetAppLocale, true);
        if (count($plainT) !== count($plain) || count($htmlT) !== count($html)) {
            return array_map(fn (array $m) => is_string($m['value'] ?? null) ? $m['value'] : '', $segmentMeta);
        }
        $result = array_fill(0, count($segmentMeta), '');
        $pi = 0;
        foreach ($pIdx as $pos) {
            $result[$pos] = $plainT[$pi] ?? $segmentMeta[$pos]['value'];
            $pi++;
        }
        $hi = 0;
        foreach ($hIdx as $pos) {
            $result[$pos] = $htmlT[$hi] ?? $segmentMeta[$pos]['value'];
            $hi++;
        }

        return $result;
    }

    /**
     * @param  list<string>  $batch
     * @return list<string>
     */
    private function callTranslateBatch(array $batch, string $targetLabel, bool $asHtml): array
    {
        if ($batch === []) {
            return [];
        }
        $system = 'You are a professional translator. Translate from English to '.$targetLabel.
            ' only. Return a JSON object with a single key "texts" whose value is a JSON array of strings.'.
            ' The output array must have exactly '.count($batch).' items in the same order as the input. No extra keys.';
        if ($asHtml) {
            $system .= ' Each string is HTML. Translate only human language between tags. Copy all tags, attributes, and '.
                'nesting from the input unless you must re-balance; keep tag names, order, and structure. '.
                'Write real angle brackets: use < and > only. Do not put a backslash before <, >, or / inside tags. '.
                'Do not output JSON string escapes (such as \\/, \\<) inside the HTML—the values are not JSON strings, they are '.
                'the final HTML. Never double-encode tags. The result must be valid, balanced HTML.';
        } else {
            $system .= ' For plain text, return plain text only.';
        }
        $user = "Input strings as JSON array (in order):\n".json_encode(array_values($batch), JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);

        $body = [
            'model' => (string) config('services.openai.model', 'gpt-5.4-mini'),
            'response_format' => ['type' => 'json_object'],
            'messages' => [
                ['role' => 'system', 'content' => $system],
                ['role' => 'user', 'content' => $user],
            ],
        ];

        try {
            $res = $this->client()->post('chat/completions', $body);
        } catch (Throwable $e) {
            if (app()->hasDebugModeEnabled() || app()->runningUnitTests()) {
                throw $e;
            }
            throw new RuntimeException('OpenAI HTTP error: '.$e->getMessage(), 0, $e);
        }
        if (! $res->successful()) {
            $msg = 'OpenAI error '.$res->status().': '.$res->body();
            if (app()->hasDebugModeEnabled() || app()->runningUnitTests()) {
                throw new RuntimeException($msg);
            }
            throw new RuntimeException($msg);
        }
        $content = $res->json('choices.0.message.content');
        if (! is_string($content) || $content === '') {
            throw new RuntimeException('OpenAI: empty response content');
        }
        $decoded = json_decode($content, true, 512, JSON_THROW_ON_ERROR);
        if (! is_array($decoded) || ! isset($decoded['texts']) || ! is_array($decoded['texts'])) {
            throw new RuntimeException('OpenAI: invalid JSON shape, expected {texts: [...]}');
        }
        $texts = $decoded['texts'];
        if (count($texts) !== count($batch)) {
            throw new RuntimeException('OpenAI: texts count mismatch, expected '.count($batch).', got '.count($texts));
        }

        $out = array_map(fn ($t) => is_string($t) ? $t : (string) $t, $texts);
        if ($asHtml) {
            $out = array_map(
                static fn (string $s) => HtmlTranslationSanity::stripLlmJsonEscapesFromHtml($s),
                $out
            );
        }

        return $out;
    }

    private function client(): PendingRequest
    {
        $base = rtrim((string) config('services.openai.base_url', 'https://api.openai.com/v1'), '/').'/';
        $timeout = (int) config('services.openai.request_timeout', 300);

        return Http::baseUrl($base)
            ->timeout($timeout)
            ->connectTimeout(20)
            ->withToken((string) config('services.openai.key'), 'Bearer');
    }
}
