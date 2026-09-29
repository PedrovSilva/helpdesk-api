<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\StoreTicketRequest;
use App\Http\Requests\UpdateTicketRequest;
use App\Http\Resources\TicketResource;
use App\Models\Ticket;
use App\Services\TicketService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TicketController extends Controller
{
    public function __construct(public TicketService $ticketService) {}

    public function index(): AnonymousResourceCollection
    {
        $tickets = Ticket::query()
            ->latest()
            ->paginate(15);

        return TicketResource::collection($tickets);
    }

    public function store(StoreTicketRequest $request): TicketResource
    {
        $ticket = $this->ticketService->create($request->validated());

        return new TicketResource($ticket);
    }

    public function show(Ticket $ticket): TicketResource
    {
        return new TicketResource($ticket);
    }

    public function update(
        UpdateTicketRequest $request,
        Ticket $ticket
    ): TicketResource {
        $ticket = $this->ticketService->update(
            $ticket,
            $request->validated()
        );

        return new TicketResource($ticket);
    }

    public function destroy(Ticket $ticket): JsonResponse
    {
        $ticket->delete();

        return response()->json(null, 204);
    }
}
