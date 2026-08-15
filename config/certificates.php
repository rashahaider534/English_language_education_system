<?php

return [
    'template_path' => storage_path('app/certificates/certificate_template.png'),
    'font_path' => storage_path('app/fonts/Cairo-Bold.ttf'),

    // اسم الطالب — y هنا هو المركز العمودي للنص (وليس baseline)
    // الخط الفاصل عند y≈333، فتركنا فجوة تنفّس مريحة فوقه
    'student_name' => [
        'x' => 512,
        'y' => 316,
        'size' => 30,
        'color' => '#1F2937',
        'align' => 'center',
        'max_chars_width' => 380, // للتصغير التلقائي إذا الاسم طويل (شرح تحت)
    ],

    // المستوى — مركز الفراغ الداخلي للإكليل الذهبي فعليًا (قِسته من الصورة)
    'level_name' => [
        'x' => 512,
        'y' => 472,
        'size' => 22,
        'color' => '#1F2937',
        'align' => 'center',
        'max_chars_width' => 130, // مهم جدًا هون، شوف الملاحظة تحت
    ],

    'certificate_number' => [
        'x' => 118,
        'y' => 523,
        'size' => 16,
        'color' => '#1F2937',
        'align' => 'left',
    ],

    'issued_at' => [
        'x' => 118,
        'y' => 589,
        'size' => 15,
        'color' => '#1F2937',
        'align' => 'left',
    ],
];
