<?php

namespace App\Http\Controllers\API;

use App\Constants\ErrorCodes;
use App\Http\Controllers\Controller;
use App\Interfaces\AddressBookInterface;
use App\Interfaces\ReservationInterface;
use App\Interfaces\SlideShowsInterface;
use App\Models\Configuration;
use App\Models\RepCoupon;
use App\Models\TimeSlots;
use App\Repositories\AddressBookRepository;
use Carbon\Carbon;
use http\Client\Curl\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use function GuzzleHttp\Promise\all;

class AppController extends Controller
{
    protected $addressBook;
    protected $reservation;
    protected $slideShows;

    public function __construct(AddressBookInterface $addressBook, ReservationInterface $reservation, SlideShowsInterface $slideShows)
    {
        $this->addressBook = $addressBook;
        $this->reservation = $reservation;
        $this->slideShows = $slideShows;
    }

    public function init()
    {
        try {
            $data = [];
            $data['logo'] = $this->getConfig('logo');
            $data['phone'] = $this->getConfig('phone');
            $data['email'] = $this->getConfig('email');
            $data['location'] = $this->getConfig('location');
            $data['specification_file'] = $this->getConfig('specification_file');
            $data['legals']['terms_conditions'] = $this->getConfig('terms_conditions');
            $data['legals']['privacy_policy'] = $this->getConfig('privacy_policy');
            $data['legals']['cookie_policy'] = $this->getConfig('cookie_policy');
            $data['slideShows'] = $this->slideShows->all()->original['results'];

            if (Auth::guard('api')->check()) {
                $user = \App\Models\User::find(Auth::guard('api')->id());
                if ($user->isRepresentative()) {
                    $data['representative']['rep_discount'] = $user->representativeDiscountHistories->last() ? $user->representativeDiscountHistories->last()->discount_percent : 0;
                    $data['representative']['referral_code'] = $this->getRepCouponsCode($user->id);
                }
                $data['addresses'] = $this->addressBook->all()->original['results'];
            }
            return $this->success('Initialized Data', $data, ErrorCodes::SUCCESS);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    private function getRepCouponsCode($userId)
    {
        return RepCoupon::where('rep_id', $userId)->whereHas('coupons',function ($query){
            $query->whereDate('start_date' ,'<=', Carbon::now());
            $query->whereDate('end_date' ,'>=', Carbon::now());
        })->with('coupons')->get();
    }

    private function getConfig($key)
    {
        $value = Configuration::where('configuration_key', $key)->first();
        return $value ? $value->configuration_value : '';
    }
}
