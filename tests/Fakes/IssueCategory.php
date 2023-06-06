<?php


namespace Tnapf\JsonMapper\Tests\Fakes;

enum IssueCategory: string
{
    case GENERAL = 'general';
    case ENHANCEMENT = 'enhancement';
    case BUG = 'bug';
    case INVALID = 'invalid';
}
