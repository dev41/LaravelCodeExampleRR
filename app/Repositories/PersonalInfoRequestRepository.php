<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class PersonalInfoRequestRepository extends Repository
{
    const FILES_SUB_DIR = 'personal-info-request';

    public static function getById(int $requestId)
    {
        $data = DB::table('personal_info_request as pir')
            ->select(
                'pir.id',
                'pir.address',
                self::getSelectDateTimeColumn('pir.birthday'),
                'pir.country',
                'pir.email',
                'pir.first_name',
                'pir.last_name',
                'pir.middle_name',
                'pir.id_number',
                'pir.id_type',
                'pir.phone'
            )
            ->where('id', '=', $requestId)
            ->first();

        return $data;
    }

    public static function getFilePath(int $paymentRequestId): string
    {
        return 'files' . DIRECTORY_SEPARATOR . self::FILES_SUB_DIR
            . DIRECTORY_SEPARATOR . $paymentRequestId;
    }
}
