<?php

namespace App\Repositories;

use App\Models\AgreementFiles;
use App\Models\Chat;
use App\Models\Room;
use Illuminate\Support\Facades\DB;

class OnboardingAgreementsRepository extends Repository
{
    public static function getById(int $agreementsId)
    {
        $data = DB::table('onboarding_agreements as oa')
            ->select(
                'oa.id as id',
                'oa.creator_id as creator_id',
                'oa.chat_id as chat_id',
                'oa.status as status',
                'oa.emergency_repair_name as emergency_repair_name',
                'oa.emergency_repair_phone as emergency_repair_phone',
                'oa.rent as rent',
                'oa.rent_period as rent_period',
                'oa.bond as bond',
                'oa.bills_amount as bills_amount',
                'oa.bills_included as bills_included',
                self::getSelectDateTimeColumn('oa.move_date', 'move_date'),
                self::getSelectDateTimeColumn('oa.expire_in', 'expire_in'),
                'oa.lease as lease',
                'oa.full_name as full_name',
                'oa.su_details as su_details',
                'oa.searcher_details as searcher_details'
            )
            ->where(['id' => $agreementsId])
            ->first();

        /** @var AgreementFiles $agreements */
        $agreements = AgreementFiles::where([
            'scope_id' => $data->creator_id,
            'type' => AgreementFiles::TYPE_AGREEMENT_ORIGIN,
        ])->first();

        $data->agreement = [];

        if ($agreements) {
            $data->agreement = [[
                'name' => $agreements->name,
                'url' => $agreements->getUrlAttribute(),
            ]];
        }

        $data->houseRules = [];

        $chat = Chat::find($data->chat_id);
        $room = Room::find($chat->room_id);

        /** @var AgreementFiles $agreements */
        $houseRules = AgreementFiles::where([
            'scope_id' => $room->property_id,
            'type' => AgreementFiles::TYPE_HOUSE_RULES_ORIGIN,
        ])->first();

        if ($houseRules) {
            $data->houseRules = [[
                'name' => $houseRules->name,
                'url' => $houseRules->getUrlAttribute(),
            ]];
        }

        return $data;
    }
}
