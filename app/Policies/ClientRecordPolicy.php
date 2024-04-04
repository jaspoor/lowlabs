<?php

namespace App\Policies;

use App\Models\ClientRecord;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ClientRecordPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ClientRecord $record): bool
    {
        return $this->isOwner($user, $record);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ClientRecord $record): bool
    {
        return $this->isOwner($user, $record);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ClientRecord $record): bool
    {
        return $this->isOwner($user, $record);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, ClientRecord $record): bool
    {
        return $this->isOwner($user, $record);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, ClientRecord $record): bool
    {
        return $this->isOwner($user, $record);
    }

    private function isOwner(User $user, ClientRecord $record): bool 
    {
        return $user->id === $record->user_id;
    }
}
