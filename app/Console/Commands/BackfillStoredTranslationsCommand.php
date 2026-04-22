<?php

namespace App\Console\Commands;

use App\Jobs\TranslateModelContentJob;
use App\Models\Amenity;
use App\Models\Apartment;
use App\Models\Faq;
use App\Models\Page;
use App\Models\Testimonial;
use Illuminate\Console\Command;

class BackfillStoredTranslationsCommand extends Command
{
    protected $signature = 'translations:backfill
                            {--queue : Push jobs to the queue instead of running synchronously}';

    protected $description = 'Dispatch OpenAI translation for all translatable content (EN → stored DE/IT)';

    public function handle(): int
    {
        if (! (bool) config('services.openai.key')) {
            $this->warn('OPENAI_API_KEY is not set. Nothing to do.');

            return self::SUCCESS;
        }
        foreach (Page::query()->cursor() as $p) {
            $this->runJob(Page::class, (int) $p->getKey());
        }
        foreach (Apartment::query()->cursor() as $m) {
            $this->runJob(Apartment::class, (int) $m->getKey());
        }
        foreach (Faq::query()->cursor() as $m) {
            $this->runJob(Faq::class, (int) $m->getKey());
        }
        foreach (Testimonial::query()->cursor() as $m) {
            $this->runJob(Testimonial::class, (int) $m->getKey());
        }
        foreach (Amenity::query()->cursor() as $m) {
            $this->runJob(Amenity::class, (int) $m->getKey());
        }
        $this->info('Done. Jobs dispatched or run.');

        return self::SUCCESS;
    }

    private function runJob(string $class, int $id): void
    {
        if ($this->option('queue')) {
            TranslateModelContentJob::dispatch($class, $id, null, null);
        } else {
            TranslateModelContentJob::dispatchSync($class, $id, null, null);
        }
    }
}
