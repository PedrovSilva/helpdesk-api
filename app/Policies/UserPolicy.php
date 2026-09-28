<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\User;

class UserPolicy
{
  public function viewAny(User $user): bool
  {
      return $user->userRole === UserRole::ADMIN;
  }

  public function view(User $user, User $model): bool
  {
      return $user->userRole === UserRole::ADMIN || $user->id === $model->id;
  }
  public function update(User $user, User $model): bool
  {
      return $user->userRole === UserRole::ADMIN || $user->id === $model->id;
  }
  public function delete(User $user, User $model): bool
  {
      return $user->userRole === UserRole::ADMIN && $user->id === $model->id;
  }

  public function create(User $user): bool
  {
      return $user->userRole === UserRole::ADMIN;
  }
}
