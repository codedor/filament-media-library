<?php

use Codedor\MediaLibrary\Conversions\LocalConversion;

return [
    'conversion' => LocalConversion::class,
    'enable-format-generate-action' => true,
    'enable-webp-generation' => true,
    'format-queue' => 'default',
    'extensions' => [
        'image' => [
            'jpg',
            'jpeg',
            'svg',
            'png',
            'webp',
            'gif',
        ],
        'document' => [
            'txt',
            'pdf',
            'doc',
            'docx',
            'xls',
            'xlsx',
            'ppt',
            'pptx',
            'zip',
            'odf',
        ],
        'video' => [
            'mp4',
            'm4v',
            'webm',
            'ogg',
        ],
    ],
];
