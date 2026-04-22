{{-- Same blocks as homepage: testimonials, home FAQs accordion, travel-dates CTA (driven by Home page CMS) --}}
<section class="border-t border-stone-200/60 bg-white py-12 md:py-16">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div @class([
            'flex flex-col gap-12 lg:grid lg:grid-cols-2 lg:items-start lg:gap-10 xl:gap-14' => $preFooterFaqs->isNotEmpty(),
        ])>
            <div class="min-w-0">
                <h2 class="font-display text-4xl font-semibold tracking-tight text-lake-950 md:text-[2.25rem]">{{ __('Guest impressions') }}</h2>
                <div class="@if($preFooterFaqs->isNotEmpty()) mt-8 space-y-6 lg:mt-10 @else mt-12 grid gap-6 md:grid-cols-2 md:gap-8 @endif">
                    @foreach($preFooterTestimonials as $t)
                        <blockquote class="relative overflow-hidden rounded-2xl border border-stone-200 bg-white p-8 pl-7 before:absolute before:inset-y-6 before:left-0 before:w-1 before:rounded-full before:bg-gradient-to-b before:from-gold-500 before:to-gold-600/70 before:content-[''] md:p-9 md:pl-8 md:before:inset-y-8">
                            <div class="prose prose-stone prose-lg max-w-none text-stone-800 prose-p:leading-relaxed">{!! $t->quote !!}</div>
                            <footer class="mt-6 border-t border-stone-100 pt-5 text-sm font-semibold text-lake-950">{{ $t->author_name }}@if($t->author_location)<span class="font-normal text-stone-500"> — {{ $t->author_location }}</span>@endif</footer>
                        </blockquote>
                    @endforeach
                </div>
            </div>

            @if($preFooterFaqs->isNotEmpty())
                <div class="min-w-0 lg:border-l lg:border-stone-200/70 lg:pl-10 xl:pl-14">
                    <div class="text-center lg:text-left">
                        <h2 class="font-display text-4xl font-semibold tracking-tight text-lake-950 md:text-[2.25rem]">{{ __('Questions') }}</h2>
                        <p class="mx-auto mt-3 max-w-xl text-base leading-relaxed text-stone-600 lg:mx-0">{{ __('Straightforward answers about booking and your stay.') }}</p>
                    </div>

                    <div class="mt-8 space-y-3 lg:mt-10" x-data="{ open: null }">
                        @foreach($preFooterFaqs as $i => $faq)
                            <div class="overflow-hidden rounded-2xl border border-stone-200/90 bg-white shadow-sm ring-1 ring-stone-900/[0.04] transition-[box-shadow,border-color] duration-200 hover:border-stone-300 hover:shadow-md">
                                <h3 class="m-0">
                                    <button
                                        type="button"
                                        class="group flex w-full items-center gap-4 px-5 py-5 text-left outline-none focus-visible:ring-2 focus-visible:ring-gold-500/50 focus-visible:ring-offset-2 sm:px-6 sm:py-[1.125rem]"
                                        id="faq-prefooter-heading-{{ $i }}"
                                        aria-controls="faq-prefooter-panel-{{ $i }}"
                                        @click="open = open === {{ $i }} ? null : {{ $i }}"
                                        :aria-expanded="(open === {{ $i }}).toString()"
                                    >
                                        <span class="min-w-0 flex-1 text-base font-semibold leading-snug text-lake-950 sm:text-[1.0625rem]">{{ $faq->question }}</span>
                                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full border border-stone-200/90 bg-white text-lake-800 shadow-sm transition group-hover:border-lake-900/20 group-hover:shadow-md" aria-hidden="true">
                                            <svg
                                                class="h-5 w-5 transition-transform duration-300 ease-out"
                                                :class="open === {{ $i }} ? 'rotate-180' : ''"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke-width="2"
                                                stroke="currentColor"
                                            >
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                            </svg>
                                        </span>
                                    </button>
                                </h3>
                                <div
                                    id="faq-prefooter-panel-{{ $i }}"
                                    role="region"
                                    aria-labelledby="faq-prefooter-heading-{{ $i }}"
                                    x-show="open === {{ $i }}"
                                    x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="opacity-0 -translate-y-0.5"
                                    x-transition:enter-end="opacity-100 translate-y-0"
                                    x-transition:leave="transition ease-in duration-150"
                                    x-transition:leave-start="opacity-100"
                                    x-transition:leave-end="opacity-0"
                                    x-cloak
                                    class="border-t border-stone-100 bg-white px-5 py-5 sm:px-6"
                                >
                                    <div class="prose prose-stone prose-sm max-w-none text-stone-600 prose-p:my-2 prose-a:font-medium prose-a:text-lake-800 prose-a:underline-offset-2 hover:prose-a:text-lake-950">
                                        {!! $faq->answer !!}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</section>

@if($preFooterCtaTitle !== '' || $preFooterCtaText !== '')
    <section class="relative isolate overflow-hidden border-t border-white/10 py-16 text-center text-white md:py-20">
        @if($preFooterCtaBgSrc !== '')
            <div class="pointer-events-none absolute inset-0 bg-cover bg-center bg-no-repeat" style="background-image: url('{{ $preFooterCtaBgSrc }}');" aria-hidden="true"></div>
            <div class="pointer-events-none absolute inset-0 bg-black/72" aria-hidden="true"></div>
            <div class="pointer-events-none absolute inset-0 bg-gradient-to-t from-black/70 via-black/15 to-black/50" aria-hidden="true"></div>
        @else
            <div class="pointer-events-none absolute inset-0 bg-gradient-to-br from-lake-950 via-lake-900 to-stone-950" aria-hidden="true"></div>
            <div class="pointer-events-none absolute inset-0 bg-black/50" aria-hidden="true"></div>
        @endif
        <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-2xl">
                <h2 class="font-display text-4xl font-semibold tracking-tight text-white drop-shadow-md md:text-[2.25rem]">{{ $preFooterCtaTitle }}</h2>
                {{-- cta_text from CMS is HTML (Quill); escaped {{ }} shows raw tags --}}
                <div class="mt-4 max-w-none text-lg text-white drop-shadow-sm [&_p]:m-0 [&_p+p]:mt-3 [&_*]:!text-white">{!! $preFooterCtaText !!}</div>
                <div class="mt-8 flex flex-wrap justify-center gap-4">
                    <a href="tel:{{ $sitePhoneTel }}" class="inline-flex items-center justify-center rounded-full bg-white px-8 py-5 text-base font-semibold leading-none text-lake-900 shadow-lg">{{ __('Call') }}</a>
                    <a href="{{ localized_route('contact') }}#inquiry" class="inline-flex items-center justify-center rounded-full border-2 border-white px-8 py-5 text-base font-semibold leading-none text-white shadow-sm">{{ __('Email your dates') }}</a>
                </div>
            </div>
        </div>
    </section>
@endif
