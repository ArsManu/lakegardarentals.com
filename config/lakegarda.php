<?php

return [
    'site_name' => env('SITE_NAME', 'Lake Garda Rentals'),
    'phone' => env('SITE_PHONE', '+393247836175'),
    'phone_display' => env('SITE_PHONE_DISPLAY', '+393247836175'),
    'email' => env('SITE_EMAIL', 'a.orchideagarda@gmail.com'),
    'whatsapp' => env('WHATSAPP_NUMBER'),
    'address_line' => env('SITE_ADDRESS', 'Garda (VR), Lake Garda, Italy'),
    'map_embed_url' => env('MAP_EMBED_URL'),
    'business_lat' => env('BUSINESS_LAT'),
    'business_lng' => env('BUSINESS_LNG'),
    'admin_email' => env('ADMIN_EMAIL', 'admin@lakegardarentals.com'),
    'admin_password' => env('ADMIN_PASSWORD', 'changeme'),
    'inquiry_notify_email' => env('INQUIRY_NOTIFY_EMAIL', env('MAIL_FROM_ADDRESS')),

    /** Max upload size for admin images (kilobytes). Default 20480 = 20 MB. Match PHP upload_max_filesize / post_max_size in production. */
    'admin_image_max_kb' => (int) env('ADMIN_IMAGE_MAX_KB', 120480),
];
