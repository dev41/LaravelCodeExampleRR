<?php

namespace App\Policies;

use App\Models\AccountType;
use App\Models\Property;
use App\Models\User;

/**
 * Class PropertyPolicy
 * @package App\Policies
 */
class PropertyPolicy extends Policy
{
    /**
     * @param User $user
     * @return bool
     */
    public function before(User $user)
    {
        if (!$user->hasAccount(AccountType::ACCOUNT_TYPE_SUPERUSER)) {
            return false;
        }
    }

    public function create(User $user)
    {
        return true;
    }

    public function view(User $user, Property $property): bool
    {
        $roleId = $property->getUserRole($user->id);
        return !empty($roleId) && in_array('property_view', self::RULES[$roleId]);
    }

    public function update(User $user, Property $property): bool
    {
        $roleId = $property->getUserRole($user->id);
        return !empty($roleId) && in_array('property_update', self::RULES[$roleId]);
    }

    public function delete(User $user, Property $property): bool
    {
        $roleId = $property->getUserRole($user->id);
        return !empty($roleId) && in_array('property_delete', self::RULES[$roleId]);
    }
}
