<?php

namespace Modules\HEALTHAI\Services\Agent;

/**
 * Contract for all data resolvers used by the AI Agent.
 *
 * Adding a new intent requires only:
 *  1. A new AgentIntent enum case.
 *  2. A new class implementing this interface.
 *  3. Registration in AgentDataResolverFactory.
 */
interface DataResolverInterface
{
    /**
     * @param  string $keyword  Cleaned keyword extracted by the router.
     * @return array<int, array<string, mixed>>  Structured context data for the LLM.
     */
    public function resolve(string $keyword): array;
}
