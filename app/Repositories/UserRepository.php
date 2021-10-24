<?php


namespace App\Repositories;


use App\Constants\ErrorCodes;
use App\Constants\Roles;
use App\Interfaces\UserInterface;
use App\Models\RepresentativeDiscountHistory;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserRepository extends MainRepository implements UserInterface
{
    public function all()
    {
        return User::with(['roles', 'representativeDiscountHistories' => function ($query) {
            $query->orderBy('id', 'Desc');
        }])->orderBy('id', 'desc')->get();

    }

    public function store($request)
    {
        try {
            $user = new User();
            $user->first_name = $request->first_name;
            $user->last_name = $request->last_name;
            $user->email = $request->email;
            $user->password = bcrypt($request->password);
            $user->phone = $request->phone;
            $user->role_id = $request->role_id;
            $user->lang_pref = $request->lang_pref ? $request->lang_pref : 'en';
            $user->otp_verified = true;
            $user->save();
            if ($request->role_id == Roles::REPRESENTATIVE)
                $this->saveRepresentativeDiscountHistory($user->id, $request->discount_percentage);
            return $this->success('User has been created successfully', $user, ErrorCodes::SUCCESS);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function get($request)
    {

    }

    public function update($request, $id)
    {
        try {
            $user = User::findorfail($id);
            if ($user->id != 1) {
                $user->first_name = $request->first_name;
                $user->last_name = $request->last_name;
                $user->email = $request->email;
                $user->phone = $request->phone;
                $user->role_id = $request->role_id;
                if ($request->password != '')
                    $user->password = bcrypt($request->password);
                $user->save();
                if ($request->role_id == Roles::REPRESENTATIVE)
                    $this->saveRepresentativeDiscountHistory($user->id, $request->discount_percentage);

            }
            return $this->success('User has been updated successfully', $user, ErrorCodes::SUCCESS);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    private function saveRepresentativeDiscountHistory($userId, $discount)
    {
        $prev = RepresentativeDiscountHistory::where('user_id', $userId)->latest()->first();
        if ($prev && $prev->discount_percent == $discount) {
            return false;
        }
        $discountHistory = new RepresentativeDiscountHistory();
        $discountHistory->discount_percent = $discount;
        $discountHistory->user_id = $userId;
        $discountHistory->save();
        return true;
    }

    public function delete($request)
    {
        // TODO: Implement delete() method.
    }
}
