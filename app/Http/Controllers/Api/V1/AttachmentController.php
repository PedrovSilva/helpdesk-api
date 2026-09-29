<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\StoreAttachmentRequest;
use App\Http\Requests\UpdateAttachmentRequest;
use App\Http\Resources\AttachmentResource;
use App\Models\Attachment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AttachmentController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $attachments = Attachment::query()
            ->latest()
            ->paginate(15);

        return AttachmentResource::collection($attachments);
    }

    public function store(StoreAttachmentRequest $request): AttachmentResource
    {
        $attachment = Attachment::create($request->validated());

        return new AttachmentResource($attachment);
    }

    public function show(Attachment $attachment): AttachmentResource
    {
        return new AttachmentResource($attachment);
    }

    public function update(
        UpdateAttachmentRequest $request,
        Attachment $attachment
    ): AttachmentResource {
        $attachment->update($request->validated());

        return new AttachmentResource($attachment->refresh());
    }

    public function destroy(Attachment $attachment): JsonResponse
    {
        $attachment->delete();

        return response()->json(null, 204);
    }
}
