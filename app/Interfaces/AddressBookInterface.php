<?php


namespace App\Interfaces;


interface AddressBookInterface
{
    public function all();

    public function store($request);

    public function delete($id);

}
