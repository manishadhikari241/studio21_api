<?php

namespace App\Http\Controllers\API\CMS;

use App\Http\Controllers\Controller;
use App\Http\Requests\CMS\AddUserRequest;
use App\Http\Requests\CMS\UpdateUserRequest;
use App\Interfaces\UserInterface;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    protected $userInterface;

    public function __construct(UserInterface $userInterface)
    {
        $this->userInterface = $userInterface;
    }

    public function index(Request $request)
    {
        return $this->userInterface->all();

    }

    public function store(AddUserRequest $request)
    {
        return $this->userInterface->store($request);
    }

    public function update(UpdateUserRequest $request, $id)
    {
        return $this->userInterface->update($request, $id);
    }

}
