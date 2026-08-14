<?php
return [
    'template_path' => storage_path('app/certificates/certificate_template.png'),
    'font_path' => storage_path('app/fonts/Cairo-Bold.ttf'),

    'student_name' => [
        'x' => 1000,
        'y' => 585,
        'size' => 56,
        'color' => '#1F2937',
        'align' => 'center',
    ],

    'level_name' => [
        'x' => 1000,
        'y' => 785,
        'size' => 40,
        'color' => '#1F2937',
        'align' => 'center',
    ],

    'certificate_number' => [
        'x' => 220,
        'y' => 1244,
        'size' => 22,
        'color' => '#6E6E69',
        'align' => 'left',
    ],

    'issued_at' => [
        'x' => 220,
        'y' => 1354,
        'size' => 22,
        'color' => '#6E6E69',
        'align' => 'left',
    ],
];
