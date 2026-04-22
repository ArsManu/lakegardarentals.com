<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\TranslateModelContentJob;
use App\Models\Amenity;
use App\Models\Apartment;
use App\Models\Faq;
use App\Models\Page;
use App\Models\Testimonial;
use App\Services\ModelContentTranslator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class TriggerTranslationController extends Controller
{
    public function __invoke(Request $request, ModelContentTranslator $translator): RedirectResponse
    {
        $data = $request->validate([
            'type' => 'required|in:page,apartment,faq,testimonial,amenity',
            'id' => 'required|integer|min:1',
        ]);

        $class = $this->resolveModelClass($data['type']);
        $model = $class::query()->find($data['id']);
        if ($model === null) {
            abort(404);
        }

        if (! $translator->isConfigured()) {
            return back()->with('warning', __('Set OPENAI_API_KEY in your environment to enable translations.'));
        }

        TranslateModelContentJob::dispatch($class, (int) $model->getKey(), null, null)
            ->afterCommit()
            ->afterResponse();

        return back()->with('success', __('Translation to German and Italian is running on the server. You can keep using the admin; run a queue worker (php artisan queue:work) if jobs are not processing.'));
    }

    /**
     * @return class-string
     */
    private function resolveModelClass(string $type): string
    {
        return match ($type) {
            'page' => Page::class,
            'apartment' => Apartment::class,
            'faq' => Faq::class,
            'testimonial' => Testimonial::class,
            'amenity' => Amenity::class,
        };
    }
}
