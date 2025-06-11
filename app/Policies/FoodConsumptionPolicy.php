<?php

namespace App\Policies;

use App\Models\FoodConsumption;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class FoodConsumptionPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function markAsDelivered(User $user, FoodConsumption $foodConsumption): bool
    {
        // Hanya tenaga-gizi yang bisa mengubah status ke 'delivered'
        // dan hanya jika statusnya masih 'planned'
        \Log::info('FoodConsumptionPolicy@markAsDelivered: User Role=' . $user->role . ', Food Status=' . $foodConsumption->status);
        return ($user->role === 'tenaga-gizi' && $foodConsumption->status === 'planned');
    }

    // Anda bisa menambahkan metode lain untuk 'markAsConsumed', 'view', dll.
    public function markAsConsumed(User $user, FoodConsumption $foodConsumption): bool
    {
        // Hanya ahli-gizi yang bisa mengubah status ke 'consumed'
        // dan hanya jika statusnya 'delivered'
        return ($user->role === 'ahli-gizi' && $foodConsumption->status === 'delivered');
    }
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, FoodConsumption $foodConsumption): bool
    {
        return in_array($user->role, ['ahli-gizi', 'tenaga-gizi']);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, FoodConsumption $foodConsumption): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, FoodConsumption $foodConsumption): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, FoodConsumption $foodConsumption): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, FoodConsumption $foodConsumption): bool
    {
        return false;
    }
}
