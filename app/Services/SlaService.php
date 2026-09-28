<?php

namespace App\Services;

use App\Enums\Priority;
use App\Models\Sla;
use Carbon\CarbonInterface;

class SlaService
{
    public function findForPriority(Priority $priority): Sla
    {
        return Sla::query()
            ->where('priority', $priority)
            ->where('active', true)
            ->firstOrFail();
    }

    public function calculateResponseDeadline(
        Sla $sla,
        CarbonInterface $start
    ): CarbonInterface {
        return $start->copy()->addMinutes(
            $sla->response_time_minutes
        );
    }

    public function calculateResolutionDeadline(
        Sla $sla,
        CarbonInterface $start
    ): CarbonInterface {
        return $start->copy()->addMinutes(
            $sla->resolution_time_minutes
        );
    }
}
