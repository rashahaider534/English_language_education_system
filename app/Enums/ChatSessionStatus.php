<?php

namespace App\Enums;

enum ChatSessionStatus: string
{
    case ACTIVE = 'active';
    case ENDED = 'ended';
}
