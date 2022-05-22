<?php

namespace App\Repositories;

use App\Models\PaymentRequest;
use App\Models\PaymentRequestFiles;
use Firebase\JWT\ExpiredException;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Routing\Exception\InvalidParameterException;

class PaymentRequestRepository extends Repository
{
    const FILES_SUB_DIR = 'payment-request';

    public static function getForRs(PaymentRequest $paymentRequest)
    {
        $prData = DB::table('payment_request as pr')
            ->select(
                'pr.id as id',
                'pr.status as status',
                'pr.purpose as purpose',
                self::getSelectDateTimeColumn('pr.expired_at', 'expirationDate'),
                self::getSelectDateTimeColumn('pr.move_in_date', 'moveDate'),
                DB::raw('GROUP_CONCAT(DISTINCT prrp.rs_profile_id) AS userId'),
                DB::raw('GROUP_CONCAT(DISTINCT prf.name SEPARATOR "|") AS agreementUrl')
            )
            ->leftJoin('payment_request_rs_profile as prrp', 'pr.id', '=', 'prrp.payment_request_id')
            ->leftJoin('payment_request_files as prf', function (JoinClause $join) {
                $join->on('pr.id', '=', 'prf.payment_request_id')
                    ->where('prf.type', '=', PaymentRequestFiles::TYPE_SU_AGREEMENT);
            })
            ->where(['pr.id' => $paymentRequest->id])
            ->groupBy('pr.id')
            ->first();

        if ($paymentRequest->checkExpired()) {
            throw new ExpiredException('Payment request is expired.');
        }

        $prices = DB::table('payment_request_price as prp')
            ->select(
                'prp.type',
                'prp.price'
            )
            ->where(['prp.payment_request_id' => $paymentRequest->id])
            ->get()->toArray();

        $prData->deposit = null;
        $prData->rent = null;
        $prData->bond = null;

        foreach ($prices as $price) {
            switch ($price->type) {
                case PaymentRequest::TYPE_DEPOSIT:
                    $prData->deposit = $price->price;
                    break;
                case PaymentRequest::TYPE_INITIAL_RENT:
                    $prData->rent = $price->price;
                    break;
                case PaymentRequest::TYPE_BOND:
                    $prData->bond = $price->price;
                    break;
                default: throw new InvalidParameterException('Invalid price type.');
            }
        }

        if ($prData->agreementUrl) {

            $fName = explode('|', $prData->agreementUrl)[0];
            $storageType = env('FILES_STORAGE_DRIVER');
            $fPath = Storage::disk($storageType)->path(PaymentRequestRepository::getFilePath($paymentRequest->id) . '/' . $fName);
            $fUrl = url(PaymentRequestRepository::getFilePath($paymentRequest->id) . '/' . $fName);
            $fSize = file_exists($fPath) ? filesize($fPath) : 0;

            $prData->agreementUrl = [
                'name' => $fName,
                'url' => $fUrl,
                'size' => $fSize,
            ];
        }

        return $prData;
    }

    public static function getFilePath(int $paymentRequestId): string
    {
        return 'files' . DIRECTORY_SEPARATOR . self::FILES_SUB_DIR
            . DIRECTORY_SEPARATOR . $paymentRequestId;
    }

    public static function getAvailableUserList(int $roomId): array
    {
        $users = DB::table('inspection as i')
            ->join('users as u', 'u.id', '=', 'i.user_id')
            ->join('searcher_profiles as sp', 'sp.id', '=', 'u.id')
            ->where('i.room_id', '=', $roomId)
            ->select(
                'u.id',
                DB::raw('CONCAT(u.first_name, " ", u.last_name) as fullName'),
                'sp.avatar as avatar'
            )->get()->toArray();

        return $users ?? [];
    }
}
