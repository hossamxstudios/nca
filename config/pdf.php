<?php

return [
    /*
    |--------------------------------------------------------------------------
    | PDF Processing Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration options for PDF processing using Imagick
    |
    */

    // Resolution for PDF to image conversion (DPI)
    'resolution' => env('PDF_RESOLUTION', 150),

    // Image format for extracted pages
    'format' => env('PDF_FORMAT', 'jpg'),

    // Image quality (1-100)
    'quality' => env('PDF_QUALITY', 85),

    // Maximum pages to process per PDF
    'max_pages' => env('PDF_MAX_PAGES', 500),

    // Memory limit for processing (in MB)
    'memory_limit' => env('PDF_MEMORY_LIMIT', 512),

    // Timeout for processing (in seconds)
    'timeout' => env('PDF_TIMEOUT', 300),

    // Enable background color for transparency
    'background_color' => env('PDF_BACKGROUND', 'white'),

    // Ghostscript path (if not in system PATH)
    'ghostscript_path' => env('GHOSTSCRIPT_PATH', null),

    // Storage disk for processed pages
    'storage_disk' => env('PDF_STORAGE_DISK', 'public'),

    // Directory for processed pages
    'pages_directory' => 'pdf-pages',
];
