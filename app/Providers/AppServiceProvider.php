<?php

namespace App\Providers;

use App\Models\Faq;
use App\Models\Page;
use App\Models\Testimonial;
use App\Support\MediaUrl;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        View::share('siteName', config('lakegarda.site_name'));
        View::share('sitePhone', config('lakegarda.phone_display'));
        View::share('sitePhoneTel', preg_replace('/\s+/', '', (string) config('lakegarda.phone')));
        View::share('siteEmail', config('lakegarda.email'));
        View::share('siteWhatsapp', config('lakegarda.whatsapp'));
        View::share('siteAddress', config('lakegarda.address_line'));

        View::composer('partials.site-pre-footer', function ($view) {
            $empty = [
                'preFooterTestimonials' => collect(),
                'preFooterFaqs' => collect(),
                'preFooterCtaTitle' => '',
                'preFooterCtaText' => '',
                'preFooterCtaBgSrc' => '',
            ];
            if (! Schema::hasTable('pages')) {
                $view->with($empty);

                return;
            }

            $homePage = Page::query()->where('slug', 'home')->first();
            $b = is_array($homePage?->blocks) ? $homePage->blocks : [];
            $ctaTitle = trim((string) ($b['cta_title'] ?? ''));
            $ctaText = trim((string) ($b['cta_text'] ?? ''));
            $heroSlides = is_array($b['hero_slides'] ?? null) ? $b['hero_slides'] : [];
            $bgSrc = '';
            foreach ($heroSlides as $slide) {
                if (is_array($slide) && ($slide['image_path'] ?? '') !== '') {
                    $bgSrc = MediaUrl::public($slide['image_path']);
                    break;
                }
            }

            $testimonials = collect();
            if (Schema::hasTable('testimonials')) {
                $testimonials = Testimonial::query()->published()->orderBy('sort_order')->limit(4)->get();
            }

            $faqs = collect();
            if (Schema::hasTable('faqs')) {
                // Same pre-footer block on every page: show every active FAQ (page_slug is for admin grouping only).
                $faqs = Faq::query()
                    ->active()
                    ->orderByRaw("CASE COALESCE(page_slug, '') WHEN 'home' THEN 0 WHEN 'lake-garda' THEN 1 WHEN 'contact' THEN 2 ELSE 3 END")
                    ->orderBy('sort_order')
                    ->orderBy('id')
                    ->get();
            }

            $view->with([
                'preFooterTestimonials' => $testimonials,
                'preFooterFaqs' => $faqs,
                'preFooterCtaTitle' => $ctaTitle,
                'preFooterCtaText' => $ctaText,
                'preFooterCtaBgSrc' => $bgSrc,
            ]);
        });

        // Merge contact page CMS overrides after migrations exist (not at provider boot).
        View::composer('*', function () {
            $guard = 'lakegarda.contact_site_vars.'.spl_object_id(request());
            if (app()->bound($guard)) {
                return;
            }
            app()->instance($guard, true);

            if (! Schema::hasTable('pages')) {
                return;
            }

            $contactPage = Page::query()->where('slug', 'contact')->first();
            if (! $contactPage || ! is_array($contactPage->blocks)) {
                return;
            }

            $blocks = $contactPage->blocks;
            $phoneFromCms = trim((string) ($blocks['contact_phone'] ?? ''));
            if ($phoneFromCms !== '') {
                View::share('sitePhone', $phoneFromCms);
                View::share('sitePhoneTel', preg_replace('/\s+/', '', $phoneFromCms));
            }
            $emailFromCms = trim((string) ($blocks['contact_email'] ?? ''));
            if ($emailFromCms !== '') {
                View::share('siteEmail', $emailFromCms);
            }
        });
    }
}
