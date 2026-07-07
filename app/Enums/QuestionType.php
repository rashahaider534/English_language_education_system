<?php

namespace App\Enums;

enum QuestionType: string
{
    case MCQ = 'MCQ';
    case ARRANGE = 'ARRANGE';
    case FILL = 'FILL';
    case PAIR = 'PAIR';
}
