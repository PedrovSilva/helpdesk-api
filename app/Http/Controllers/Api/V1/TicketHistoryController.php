<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\StoreTicketHistoryRequest;
use App\Http\Requests\UpdateTicketHistoryRequest;
use App\Http\Resources\TicketHistoryResource;
use App\Models\TicketHistory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TicketHistoryController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $ticketHistories = TicketHistory::query()
            ->latest()
            ->paginate(15);

        return TicketHistoryResource::collection($ticketHistories);
    }

    public function store(StoreTicketHistoryRequest $request): TicketHistoryResource
    {
        $ticketHistory = TicketHistory::create($request->validated());

        return new TicketHistoryResource($ticketHistory);
    }

    public function show(TicketHistory $ticketHistory): TicketHistoryResource
    {
        return new TicketHistoryResource($ticketHistory);
    }

    public function update(
        UpdateTicketHistoryRequest $request,
        TicketHistory $ticketHistory
    ): TicketHistoryResource {
        $ticketHistory->update($request->validated());

        return new TicketHistoryResource($ticketHistory->refresh());
    }

    public function destroy(TicketHistory $ticketHistory): JsonResponse
    {
        $ticketHistory->delete();

        return response()->json(null, 204);
    }
}
