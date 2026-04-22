<?php

namespace App\Services;

use App\Models\Amenity;
use App\Models\Apartment;
use App\Models\Faq;
use App\Models\Page;
use App\Models\Testimonial;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class ModelContentTranslator
{
    private const PAGE_SCALAR_KEYS = ['title', 'meta_title', 'meta_description', 'og_title', 'og_description'];

    public function __construct(
        private readonly OpenAILocaleTranslator $openai
    ) {}

    public function isConfigured(): bool
    {
        return $this->openai->isConfigured();
    }

    /**
     * @param  list<string>|null  $onlyAttributes  null = full retranslate (backfill, manual button, new record)
     * @param  array<string, mixed>|null  $blocksBefore  English blocks before this save (for Page segment reuse)
     */
    public function translateModel(Model $model, ?array $onlyAttributes = null, ?array $blocksBefore = null): void
    {
        if (! $this->openai->isConfigured()) {
            return;
        }
        if (! Schema::hasColumn($model->getTable(), 'translations')) {
            return;
        }
        if ($model instanceof Page) {
            $this->translatePage($model, $onlyAttributes, $blocksBefore);

            return;
        }
        if ($model instanceof Apartment) {
            $this->translateApartment($model, $onlyAttributes);

            return;
        }
        if ($model instanceof Faq) {
            $this->translateFaq($model, $onlyAttributes);

            return;
        }
        if ($model instanceof Testimonial) {
            $this->translateTestimonial($model, $onlyAttributes);

            return;
        }
        if ($model instanceof Amenity) {
            $this->translateAmenity($model, $onlyAttributes);
        }
    }

    /**
     * @param  list<string>|null  $only
     * @param  array<string, mixed>|null  $blocksBefore
     */
    public function translatePage(Page $page, ?array $only = null, ?array $blocksBefore = null): void
    {
        if ($only === null) {
            $t = [
                'de' => $this->pageTargetLocalePayload($page, 'de', null, null, []),
                'it' => $this->pageTargetLocalePayload($page, 'it', null, null, []),
            ];
        } else {
            $ex = is_array($page->translations) ? $page->translations : [];
            $dNew = $this->pageTargetLocalePayload($page, 'de', $only, $blocksBefore, is_array($ex['de'] ?? null) ? $ex['de'] : []);
            $iNew = $this->pageTargetLocalePayload($page, 'it', $only, $blocksBefore, is_array($ex['it'] ?? null) ? $ex['it'] : []);
            $t = [
                'de' => array_merge($ex['de'] ?? [], $dNew),
                'it' => array_merge($ex['it'] ?? [], $iNew),
            ];
        }
        Page::query()->whereKey($page->getKey())->update(['translations' => $t]);
    }

    /**
     * @param  list<string>|null  $only
     * @param  array<string, mixed>  $prevLocale  Existing stored payload for this target locale
     * @return array<string, mixed>
     */
    private function pageTargetLocalePayload(Page $page, string $targetLocale, ?array $only, ?array $blocksBefore, array $prevLocale): array
    {
        $full = $only === null;
        $scalarToTranslate = $full
            ? self::PAGE_SCALAR_KEYS
            : array_values(array_intersect(self::PAGE_SCALAR_KEYS, $only));
        $payload = [];
        if ($scalarToTranslate !== []) {
            $in = [];
            foreach ($scalarToTranslate as $k) {
                $in[] = (string) ($page->getAttribute($k) ?? '');
            }
            $out = $this->openai->translateMany($in, $targetLocale, false);
            if (count($out) < count($scalarToTranslate)) {
                $out = array_pad($out, count($scalarToTranslate), '');
            }
            foreach ($scalarToTranslate as $i => $k) {
                if (($out[$i] ?? '') !== '') {
                    $payload[$k] = $out[$i];
                }
            }
        }
        $enBlocks = is_array($page->blocks) ? $page->blocks : [];
        $doBlocks = $full || in_array('blocks', $only ?? [], true);
        if (! $doBlocks) {
            return $payload;
        }
        if ($enBlocks === []) {
            if (! $full) {
                $payload['blocks'] = [];
            }

            return $payload;
        }
        $prevBlocks = is_array($prevLocale['blocks'] ?? null) ? $prevLocale['blocks'] : null;
        if (! $full && in_array('blocks', $only ?? [], true) && is_array($blocksBefore) && is_array($prevBlocks)) {
            $merged = $this->translateBlockTreeWithReuse($enBlocks, $blocksBefore, $prevBlocks, $targetLocale);
            if ($merged !== null) {
                $payload['blocks'] = $merged;

                return $payload;
            }
        }
        $segs = TranslatableTextBlocks::extractSegments($enBlocks);
        if ($segs === []) {
            if (! $full) {
                $payload['blocks'] = $enBlocks;
            }

            return $payload;
        }
        $trans = $this->openai->translateSegmentsInOrder($segs, $targetLocale);
        if (count($trans) === count($segs)) {
            $payload['blocks'] = TranslatableTextBlocks::apply($enBlocks, $trans);
        }

        return $payload;
    }

    /**
     * @param  array<string, mixed>  $enBlocks
     * @param  array<string, mixed>  $enBlocksBefore
     * @param  array<string, mixed>  $targetBlocksBefore
     * @return array<string, mixed>|null
     */
    private function translateBlockTreeWithReuse(
        array $enBlocks,
        array $enBlocksBefore,
        array $targetBlocksBefore,
        string $targetLocale
    ): ?array {
        $segsN = TranslatableTextBlocks::extractSegments($enBlocks);
        $segsO = TranslatableTextBlocks::extractSegments($enBlocksBefore);
        $segsT = TranslatableTextBlocks::extractSegments($targetBlocksBefore);
        if ($segsN === [] && $segsO === [] && $segsT === []) {
            return $enBlocks;
        }
        if (count($segsN) !== count($segsO) || count($segsN) !== count($segsT)) {
            return null;
        }
        $n = count($segsN);
        $toTranslate = [];
        $ordered = array_fill(0, $n, '');
        for ($i = 0; $i < $n; $i++) {
            if (($segsN[$i]['value'] ?? '') === ($segsO[$i]['value'] ?? '')) {
                $ordered[$i] = (string) ($segsT[$i]['value'] ?? '');
            } else {
                $toTranslate[] = $segsN[$i];
            }
        }
        if ($toTranslate === []) {
            return TranslatableTextBlocks::apply($enBlocks, $ordered);
        }
        $tr = $this->openai->translateSegmentsInOrder($toTranslate, $targetLocale);
        if (count($tr) !== count($toTranslate)) {
            return null;
        }
        $j = 0;
        for ($i = 0; $i < $n; $i++) {
            if (($segsN[$i]['value'] ?? '') === ($segsO[$i]['value'] ?? '')) {
                continue;
            }
            $ordered[$i] = (string) ($tr[$j] ?? '');
            $j++;
        }

        return TranslatableTextBlocks::apply($enBlocks, $ordered);
    }

    /**
     * @param  list<string>|null  $only
     */
    public function translateApartment(Apartment $apartment, ?array $only = null): void
    {
        if ($only === null) {
            $t = [
                'de' => $this->apartmentTargetLocalePayload($apartment, 'de', null),
                'it' => $this->apartmentTargetLocalePayload($apartment, 'it', null),
            ];
        } else {
            $ex = is_array($apartment->translations) ? $apartment->translations : [];
            $dNew = $this->apartmentTargetLocalePayload($apartment, 'de', $only);
            $iNew = $this->apartmentTargetLocalePayload($apartment, 'it', $only);
            $t = [
                'de' => array_merge($ex['de'] ?? [], $dNew),
                'it' => array_merge($ex['it'] ?? [], $iNew),
            ];
        }
        Apartment::query()->whereKey($apartment->getKey())->update(['translations' => $t]);
    }

    /**
     * @param  list<string>|null  $only
     * @return array<string, mixed>
     */
    private function apartmentTargetLocalePayload(Apartment $a, string $targetLocale, ?array $only): array
    {
        $keys = [
            'name',
            'short_description',
            'full_description',
            'ideal_for',
            'location_text',
            'check_in_out_note',
            'availability_note',
            'address',
            'meta_title',
            'meta_description',
            'og_title',
            'og_description',
        ];
        $toDo = $only === null ? $keys : array_values(array_intersect($keys, $only));
        $payload = [];
        foreach ($toDo as $k) {
            $v = (string) ($a->getAttribute($k) ?? '');
            if ($v === '') {
                continue;
            }
            $isHtml = in_array($k, ['full_description', 'short_description'], true) && $v !== strip_tags($v);
            $r = (string) (($this->openai->translateMany([$v], $targetLocale, $isHtml)[0] ?? ''));
            if ($r !== '' && $r !== $v) {
                $payload[$k] = $r;
            }
        }

        return $payload;
    }

    /**
     * @param  list<string>|null  $only
     */
    public function translateFaq(Faq $faq, ?array $only = null): void
    {
        if ($only === null) {
            $t = [
                'de' => $this->faqTargetLocalePayload($faq, 'de', null),
                'it' => $this->faqTargetLocalePayload($faq, 'it', null),
            ];
        } else {
            $ex = is_array($faq->translations) ? $faq->translations : [];
            $t = [
                'de' => array_merge($ex['de'] ?? [], $this->faqTargetLocalePayload($faq, 'de', $only)),
                'it' => array_merge($ex['it'] ?? [], $this->faqTargetLocalePayload($faq, 'it', $only)),
            ];
        }
        Faq::query()->whereKey($faq->getKey())->update(['translations' => $t]);
    }

    /**
     * @param  list<string>|null  $only
     * @return array<string, string>
     */
    private function faqTargetLocalePayload(Faq $faq, string $targetLocale, ?array $only): array
    {
        $fields = ['question', 'answer'];
        $toDo = $only === null ? $fields : array_values(array_intersect($fields, $only));
        $out = [];
        foreach ($toDo as $field) {
            if ($field === 'question') {
                $q = (string) ($faq->question ?? '');
                $qT = (string) (($this->openai->translateMany([$q], $targetLocale, false)[0] ?? $q));
                if ($qT !== '' && $qT !== $q) {
                    $out['question'] = $qT;
                }
            }
            if ($field === 'answer') {
                $a = (string) ($faq->answer ?? '');
                $aHtml = $a !== '' && $a !== strip_tags($a);
                $aT = (string) (($this->openai->translateMany([$a], $targetLocale, $aHtml)[0] ?? $a));
                if ($aT !== '' && $aT !== $a) {
                    $out['answer'] = $aT;
                }
            }
        }

        return $out;
    }

    /**
     * @param  list<string>|null  $only
     */
    public function translateTestimonial(Testimonial $t, ?array $only = null): void
    {
        if ($only === null) {
            $payloads = [
                'de' => $this->testimonialTargetLocalePayload($t, 'de', null),
                'it' => $this->testimonialTargetLocalePayload($t, 'it', null),
            ];
        } else {
            $ex = is_array($t->translations) ? $t->translations : [];
            $payloads = [
                'de' => array_merge($ex['de'] ?? [], $this->testimonialTargetLocalePayload($t, 'de', $only)),
                'it' => array_merge($ex['it'] ?? [], $this->testimonialTargetLocalePayload($t, 'it', $only)),
            ];
        }
        Testimonial::query()->whereKey($t->getKey())->update(['translations' => $payloads]);
    }

    /**
     * @param  list<string>|null  $only
     * @return array<string, string>
     */
    private function testimonialTargetLocalePayload(Testimonial $t, string $targetLocale, ?array $only): array
    {
        $keys = ['author_name', 'author_location', 'quote'];
        $toDo = $only === null ? $keys : array_values(array_intersect($keys, $only));
        $p = [];
        foreach ($toDo as $k) {
            $v = (string) ($t->getAttribute($k) ?? '');
            if ($v === '') {
                continue;
            }
            $html = $k === 'quote' && $v !== strip_tags($v);
            $r = (string) (($this->openai->translateMany([$v], $targetLocale, $html)[0] ?? ''));
            if ($r !== '' && $r !== $v) {
                $p[$k] = $r;
            }
        }

        return $p;
    }

    /**
     * @param  list<string>|null  $only
     */
    public function translateAmenity(Amenity $amenity, ?array $only = null): void
    {
        if ($only === null) {
            $payloads = [
                'de' => $this->amenityTargetLocalePayload($amenity, 'de', null),
                'it' => $this->amenityTargetLocalePayload($amenity, 'it', null),
            ];
        } else {
            if (! in_array('name', $only, true)) {
                return;
            }
            $ex = is_array($amenity->translations) ? $amenity->translations : [];
            $payloads = [
                'de' => array_merge($ex['de'] ?? [], $this->amenityTargetLocalePayload($amenity, 'de', $only)),
                'it' => array_merge($ex['it'] ?? [], $this->amenityTargetLocalePayload($amenity, 'it', $only)),
            ];
        }
        Amenity::query()->whereKey($amenity->getKey())->update(['translations' => $payloads]);
    }

    /**
     * @param  list<string>|null  $only
     * @return array<string, string>
     */
    private function amenityTargetLocalePayload(Amenity $a, string $targetLocale, ?array $only): array
    {
        if ($only !== null && ! in_array('name', $only, true)) {
            return [];
        }
        $name = (string) ($a->name ?? '');
        if ($name === '') {
            return [];
        }
        $n = (string) (($this->openai->translateMany([$name], $targetLocale, false)[0] ?? $name));
        if ($n === '' || $n === $name) {
            return [];
        }

        return ['name' => $n];
    }
}
