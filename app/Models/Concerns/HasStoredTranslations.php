<?php

namespace App\Models\Concerns;

trait HasStoredTranslations
{
    public function displayForLocale(?string $locale = null): static
    {
        $locale = $locale ?? app()->getLocale();
        if ($locale === (string) config('locales.default', 'en')) {
            return $this;
        }

        $all = $this->translations;
        if (! is_array($all)) {
            return $this;
        }

        $payload = $all[$locale] ?? null;
        if (! is_array($payload) || $payload === []) {
            return $this;
        }

        $m = $this->newInstance();
        $m->setRawAttributes($this->getAttributes());
        $m->exists = $this->exists;
        $m->connection = $this->connection;
        if ($this->getRelations() !== []) {
            $m->setRelations($this->getRelations());
        }

        foreach ($this->translatableFieldNames() as $field) {
            if (array_key_exists($field, $payload) && $this->translationValuePresent($payload[$field])) {
                $m->setAttribute($field, $payload[$field]);
            }
        }

        return $m;
    }

    protected function translationValuePresent(mixed $v): bool
    {
        if ($v === null) {
            return false;
        }
        if (is_string($v) && trim($v) === '') {
            return false;
        }

        return ! (is_array($v) && $v === []);
    }

    /**
     * @return list<string>
     */
    abstract protected function translatableFieldNames(): array;

    /**
     * @return list<string>
     */
    public function translatableContentFieldNames(): array
    {
        return $this->translatableFieldNames();
    }
}
