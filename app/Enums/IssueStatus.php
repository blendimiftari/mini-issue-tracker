<?php

namespace App\Enums;

enum IssueStatus: string
{
    case Open = 'open';
    case InProgress = 'in_progress';
    case Closed = 'closed';
}


