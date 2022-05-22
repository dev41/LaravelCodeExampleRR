<?php

namespace App\Models;

/**
 * Class OnboardingAgreements
 * @package App\Models
 *
 * @property int $id
 * @property int $creator_id
 * @property int $searcher_id
 * @property int $chat_id
 * @property int $status
 * @property string $full_name
 * @property string $emergency_repair_name
 * @property string $emergency_repair_phone
 * @property int $su_details
 * @property int $searcher_details
 * @property int $rent
 * @property int $rent_period
 * @property int $bond
 * @property int $bills_amount
 * @property int $lease
 * @property int $bills_included
 * @property int $expire_in
 * @property int $move_date
 * @property int $created_at
 * @property int $updated_at
 *
 */
class OnboardingAgreements extends BaseModel
{
    protected $table = 'onboarding_agreements';

    const STATUS_CREATED = 0;
    const STATUS_SEND = 1;
    const STATUS_SU_SIGNED = 2;
    const STATUS_SU_SIGNED_AND_UPDATED = 3;
    const STATUS_RR_SIGNED = 4;
    const STATUS_DECLINED = 5;
    const STATUS_EXPIRED = 9;
    const STATUS_CLOSED = 10;

    const BILLS_INCLUDED_NO = 0;
    const BILLS_INCLUDED_YES = 1;

    protected $fillable = [
        'emergency_repair_name',
        'emergency_repair_phone',
        'full_name',
        'rent',
        'bond',
        'bills_amount',
        'bills_included',
        'move_date',
        'expire_in',
        'lease',
    ];

    /**
     * @param int|null $type
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function getAgreementFiles(int $type = null)
    {
        $query = $this->hasMany(AgreementFiles::class, 'parent_entity_id', 'id')->orderBy('created_at', 'desc');

        if ($type) {
            $query->where(['type' => $type]);
        }

        return $query;
    }
}
