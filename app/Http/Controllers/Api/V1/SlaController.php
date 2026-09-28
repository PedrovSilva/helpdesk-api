<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\StoreSlaRequest;
use App\Http\Requests\UpdateSlaRequest;
use App\Http\Resources\SlaResource;
use App\Models\Sla;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class SlaController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $slas = Sla::query()
            ->orderBy('priority')
            ->paginate(15);

        return SlaResource::collection($slas);
    }

    public function store(StoreSlaRequest $request): SlaResource
    {
        $sla = Sla::create($request->validated());

        return new SlaResource($sla);
    }

    public function show(Sla $sla): SlaResource
    {
        return new SlaResource($sla);
    }

    public function update(
        UpdateSlaRequest $request,
        Sla $sla
    ): SlaResource {
        $sla->update($request->validated());

        return new SlaResource($sla->refresh());
    }

    public function destroy(Sla $sla): JsonResponse
    {
        $sla->delete();

        return response()->json(null, 204);
    }
}
