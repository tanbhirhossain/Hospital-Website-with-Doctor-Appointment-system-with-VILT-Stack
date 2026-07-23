<?php

namespace Modules\HEALTHAI\Services\Agent;

use Modules\HEALTHAI\Enums\AgentIntent;

final class RoutingDecision
{
    public function __construct(
        public readonly AgentIntent $intent,
        public readonly string $keyword,
    ) {}

    public static function make(AgentIntent $intent, string $keyword = ''): self
    {
        return new self($intent, trim($keyword));
    }

    public static function general(): self
    {
        return new self(AgentIntent::GENERAL, '');
    }
}
