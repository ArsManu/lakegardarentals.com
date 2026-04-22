<?php

namespace App\Jobs;

use App\Models\Amenity;
use App\Models\Apartment;
use App\Models\Faq;
use App\Models\Page;
use App\Models\Testimonial;
use App\Services\ModelContentTranslator;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class TranslateModelContentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public string $modelType,
        public int $id,
        public ?array $onlyAttributes = null,
        public ?array $blocksBefore = null
    ) {}

    public function handle(ModelContentTranslator $translator): void
    {
        if (! $translator->isConfigured()) {
            return;
        }

        /** @var class-string $class */
        $class = $this->modelType;
        if (! in_array(
            $class,
            [Page::class, Apartment::class, Faq::class, Testimonial::class, Amenity::class],
            true
        )) {
            return;
        }
        $model = $class::query()->find($this->id);
        if ($model === null) {
            return;
        }
        $translator->translateModel($model, $this->onlyAttributes, $this->blocksBefore);
    }
}
