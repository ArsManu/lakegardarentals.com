<?php

namespace App\Listeners;

use App\Jobs\TranslateModelContentJob;
use App\Models\Amenity;
use App\Models\Apartment;
use App\Models\Faq;
use App\Models\Page;
use App\Models\Testimonial;
use App\Services\ModelContentTranslator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class QueueTranslationOnModelSave
{
    /**
     * @return list<string>
     */
    private function modelClasses(): array
    {
        return [
            Page::class,
            Apartment::class,
            Faq::class,
            Testimonial::class,
            Amenity::class,
        ];
    }

    public function register(): void
    {
        foreach ($this->modelClasses() as $class) {
            $class::created($this->onCreated(...));
            $class::updated($this->onUpdated(...));
        }
    }

    public function onCreated(Model $model): void
    {
        if (! $this->shouldRun($model)) {
            return;
        }

        TranslateModelContentJob::dispatch($model::class, (int) $model->getKey(), null, null)
            ->afterCommit();
    }

    public function onUpdated(Model $model): void
    {
        if (! $this->shouldRun($model)) {
            return;
        }

        $translatable = $model->translatableContentFieldNames();
        $relevant = array_values(array_filter(
            $translatable,
            static fn (string $field) => $model->wasChanged($field)
        ));
        if ($relevant === []) {
            return;
        }

        $blocksBefore = $model instanceof Page
            && $model->wasChanged('blocks')
            ? $this->rawOriginalBlocks($model)
            : null;

        TranslateModelContentJob::dispatch(
            $model::class,
            (int) $model->getKey(),
            $relevant,
            $blocksBefore
        )->afterCommit();
    }

    private function shouldRun(Model $model): bool
    {
        if (! $this->isTrackedModel($model)) {
            return false;
        }
        if (! Schema::hasColumn($model->getTable(), 'translations')) {
            return false;
        }

        return app(ModelContentTranslator::class)->isConfigured();
    }

    /**
     * @return array<string, mixed>|null
     */
    private function rawOriginalBlocks(Page $page): ?array
    {
        $b = $page->getRawOriginal('blocks');
        if (is_string($b) && $b !== '') {
            $b = json_decode($b, true);
        }

        return is_array($b) ? $b : null;
    }

    private function isTrackedModel(Model $model): bool
    {
        return in_array($model::class, $this->modelClasses(), true);
    }
}
