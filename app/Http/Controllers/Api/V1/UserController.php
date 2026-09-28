<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResouce;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): AnonymousResourceCollection
    {
    //    $this->authorize('viewAny', User::class);

        $users = User::query()
            ->orderBy('name')
            ->paginate(15);

        return UserResouce::collection($users);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request) : UserResouce
    {
 //       $this->authorize('create', User::class);
        $user = User::create($request->validate());

        return new UserResouce($user);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): UserResouce
    {
   //     $this->authorize('view', User::class);
        return new UserResouce(User::query()->findOrFail($id));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, string $id): UserResouce
    {
   //     $this->authorize('update', User::class);
        $user = User::query()->findOrFail($id);
        $user = $user->update($request->validate());

        return new UserResouce($user->refresh());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): jsonResponse
    {
       // $this->authorize('delete', User::class);
        $user = User::query()->findOrFail($id);
        $user->delete();
        return response()->json(null, 204);
    }
}
