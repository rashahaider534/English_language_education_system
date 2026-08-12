<?php

namespace App\Enums;

enum ChatMessageRole: string
{
    case USER = 'user';
    case ASSISTANT = 'assistant';
}
