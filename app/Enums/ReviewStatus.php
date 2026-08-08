<?php

namespace App\Enums;

enum ReviewStatus: string
{
    case IN_REVIEW = 'in_review';

    case APPROVED = 'approved';
    case CHANGES_REQUESTED = 'changes_requested';
    case RELEASED = 'released';
}
