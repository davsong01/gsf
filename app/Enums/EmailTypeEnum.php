<?php

namespace App\Enums;

enum EmailTypeEnum: string
{
    case REJECT_AWARD_ENTRY = 'reject_award_entry';
    case APPROVE_AWARD_ENTRY = 'approve_award_entry';
}
