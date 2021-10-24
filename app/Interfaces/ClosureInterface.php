<?php


namespace App\Interfaces;


interface ClosureInterface
{
    public function store($request);

    public function all();

    public function delete($id);

    public function customClosure($request);

    public function weeklyClosure($request);

    public function resetCustomClosure();

    public function resetWeeklyClosure();

}
