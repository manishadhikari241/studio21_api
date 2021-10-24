<?php


namespace App\Interfaces;


interface SlideShowsInterface
{
    public function all();

    public function store($request);

    public function delete($id);

    public function update($request,$id);
}
