<?php


namespace App\Repositories;


use App\Constants\ErrorCodes;
use App\Interfaces\AddressBookInterface;
use App\Models\BillingAddresses;
use Illuminate\Support\Facades\Auth;

class AddressBookRepository extends MainRepository implements AddressBookInterface
{
    public function all()
    {
        $addresses = BillingAddresses::where('user_id', Auth::guard('api')->id())->get();
        return $this->success('Data fetched', $addresses, ErrorCodes::SUCCESS);
    }

    public function store($request)
    {
        try {
            $address = BillingAddresses::create([
                'user_id' => Auth::guard('api')->id(),
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'company' => $request->company,
                'address_1' => $request->address1,
                'address_2' => $request->address2,
                'city' => $request->city,
                'country' => $request->country,
                'post_code' => $request->post_code,
            ]);
            return $this->success('Address Added Successfully', $address, ErrorCodes::SUCCESS);

        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function delete($id)
    {
        try {
            $address = BillingAddresses::where('user_id', Auth::guard('api')->id())->findOrFail($id);
            $address->delete();
            return $this->success('Billing Address Deleted Successfully', $address, ErrorCodes::SUCCESS);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

}
