<?php

namespace App\Enums;

enum TopicStatus: string
{
    case PUBLISHED = 'published';
    case PENDING = 'pending';
}
