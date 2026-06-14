<?php

namespace Modules\HEALTHAI\Services\Agent;

use Modules\HEALTHAI\Enums\AgentIntent;
use Modules\HEALTHAI\Services\Agent\Resolvers\AppointmentResolver;
use Modules\HEALTHAI\Services\Agent\Resolvers\BedResolver;
use Modules\HEALTHAI\Services\Agent\Resolvers\COEResolver;
use Modules\HEALTHAI\Services\Agent\Resolvers\DepartmentResolver;
use Modules\HEALTHAI\Services\Agent\Resolvers\DoctorResolver;
use Modules\HEALTHAI\Services\Agent\Resolvers\ExcelResolver;
use Modules\HEALTHAI\Services\Agent\Resolvers\GeneralResolver;
use Modules\HEALTHAI\Services\Agent\Resolvers\PharmacyResolver;

final class AgentDataResolverFactory
{
    private static array $map = [
        AgentIntent::EXCEL->value               => ExcelResolver::class,
        AgentIntent::DATABASE_DOCTOR->value     => DoctorResolver::class,
        AgentIntent::DATABASE_DEPARTMENT->value => DepartmentResolver::class,
        AgentIntent::DATABASE_COE->value        => COEResolver::class,
        AgentIntent::API_BEDS->value            => BedResolver::class,
        AgentIntent::API_PHARMACY->value        => PharmacyResolver::class,
        AgentIntent::BOOK_APPOINTMENT->value    => AppointmentResolver::class,
        AgentIntent::GENERAL->value             => GeneralResolver::class,
    ];

    public function make(AgentIntent $intent): DataResolverInterface
    {
        $class = self::$map[$intent->value] ?? GeneralResolver::class;
        return app($class);
    }
}
