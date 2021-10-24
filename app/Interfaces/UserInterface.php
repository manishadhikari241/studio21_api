<?php


namespace App\Interfaces;


interface UserInterface
{
    public function all();

    public function store($request);

    public function get($request);

    public function update($request,$id);

    public function delete($request);

}
