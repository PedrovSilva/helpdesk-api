<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\StoreCommentRequest;
use App\Http\Requests\UpdateCommentRequest;
use App\Http\Resources\CommentResource;
use App\Models\Comment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CommentController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $comments = Comment::query()
            ->latest()
            ->paginate(15);

        return CommentResource::collection($comments);
    }

    public function store(StoreCommentRequest $request): CommentResource
    {
        $comment = Comment::create($request->validated());

        return new CommentResource($comment);
    }

    public function show(Comment $comment): CommentResource
    {
        return new CommentResource($comment);
    }

    public function update(
        UpdateCommentRequest $request,
        Comment $comment
    ): CommentResource {
        $comment->update($request->validated());

        return new CommentResource($comment->refresh());
    }

    public function destroy(Comment $comment): JsonResponse
    {
        $comment->delete();

        return response()->json(null, 204);
    }
}
