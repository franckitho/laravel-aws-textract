<?php

// config for Franckitho/Textract
return [
    'region' => env('AWS_REGION', 'us-east-1'),
    'version' => env('AWS_TEXTRACT_VERSION', 'latest'),
    'credentials' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
    ],
];
