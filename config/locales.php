<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Supported public locales
    |--------------------------------------------------------------------------
    |
    | "default" is shown at unprefixed URLs (e.g. /contact). "prefixed" locales
    | use a first path segment: /de/contact, /it/contact.
    |
    */

    'default' => 'en',

    'supported' => ['en', 'de', 'it'],

    /** Locales that use a URL prefix (all except default). */
    'prefixed' => ['de', 'it'],

    /** BCP 47 / HTML lang attribute values. */
    'html_lang' => [
        'en' => 'en',
        'de' => 'de',
        'it' => 'it',
    ],
];
