<?php

namespace Modules\HEALTHAI\Enums;

enum AgentIntent: string
{
    case EXCEL               = 'EXCEL';
    case DATABASE_DOCTOR     = 'DATABASE_DOCTOR';
    case DATABASE_DEPARTMENT = 'DATABASE_DEPARTMENT';
    case DATABASE_COE        = 'DATABASE_COE';
    case API_BEDS            = 'API_BEDS';
    case API_PHARMACY        = 'API_PHARMACY';
    case BOOK_APPOINTMENT    = 'BOOK_APPOINTMENT';
    case GENERAL             = 'GENERAL';

    public static function fromString(string $value): self
    {
        return self::tryFrom($value) ?? self::GENERAL;
    }
}
