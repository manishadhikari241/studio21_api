<?php


namespace App\Interfaces;


interface PagesInterface
{
    public function all();

    public function updatePages($request);

    public function show($slug);

}
