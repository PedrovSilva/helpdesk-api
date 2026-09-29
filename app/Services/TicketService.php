<?php

namespace App\Services;

use App\Enums\Priority;
use App\Enums\TicketStatus;
use App\Models\Sla;
use App\Models\Ticket;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

class TicketService
{
    public function __construct(public SlaService $slaService) {}

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function create(array $attributes): Ticket
    {
        $priority = $this->priorityFrom(
            $attributes['priority'] ?? Priority::MEDIUM
        );
        $sla = $this->slaForPriority($priority);

        return Ticket::create([
            ...$attributes,
            'priority' => $priority,
            'status' => TicketStatus::OPEN,
            'sla_id' => $sla->id,
            'sla_due_date' => $this->slaService->calculateResolutionDeadline(
                $sla,
                now()
            ),
        ]);
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function update(Ticket $ticket, array $attributes): Ticket
    {
        if (array_key_exists('priority', $attributes)) {
            $priority = $this->priorityFrom($attributes['priority']);

            if ($priority !== $ticket->priority) {
                $sla = $this->slaForPriority($priority);
                $attributes['priority'] = $priority;
                $attributes['sla_id'] = $sla->id;
                $attributes['sla_due_date'] = $this->slaService->calculateResolutionDeadline(
                    $sla,
                    now()
                );
            }
        }

        if (array_key_exists('status', $attributes)) {
            $status = $this->statusFrom($attributes['status']);
            $attributes['status'] = $status;

            if ($status === TicketStatus::RESOLVED && $ticket->resolved_at === null) {
                $attributes['resolved_at'] = now();
            }

            if ($status === TicketStatus::CLOSED && $ticket->closed_at === null) {
                $attributes['closed_at'] = now();
            }
        }

        $ticket->update($attributes);

        return $ticket->refresh();
    }

    protected function slaForPriority(Priority $priority): Sla
    {
        try {
            return $this->slaService->findForPriority($priority);
        } catch (ModelNotFoundException) {
            throw ValidationException::withMessages([
                'priority' => 'No active SLA is configured for this priority.',
            ]);
        }
    }

    protected function priorityFrom(Priority|string $priority): Priority
    {
        return $priority instanceof Priority
            ? $priority
            : Priority::from($priority);
    }

    protected function statusFrom(TicketStatus|string $status): TicketStatus
    {
        return $status instanceof TicketStatus
            ? $status
            : TicketStatus::from($status);
    }
}
