<?php


namespace App\Interfaces;


interface CouponsInterface
{
    public function all();

    public function store($request);

    public function delete($id);

    public function update($request, $id);

    public function attachRepresentative($request);

    public function allRepPayments();

    public function repPaymentsByUser();

    public function inviteCoupon($request);
}
