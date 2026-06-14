<?php

namespace Modules\HEALTHAI\Services\Agent\Resolvers;

use Modules\HEALTHAI\Services\Agent\DataResolverInterface;

final class GeneralResolver implements DataResolverInterface
{
    public function resolve(string $keyword): array
    {
        return []; // Static hospital info is embedded in AgentPrompts::finalReply()
    }
}
