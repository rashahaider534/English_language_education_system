<?php

namespace App\Enums;

enum ChatErrorType: string
{
    case GRAMMAR = 'grammar';
    case VOCABULARY = 'vocabulary';
    case SPELLING = 'spelling';
    case WORD_ORDER = 'word_order';
    case PREPOSITION = 'preposition';
    case TENSE = 'tense';
    case OTHER = 'other';
}
