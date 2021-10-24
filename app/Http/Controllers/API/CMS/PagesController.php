<?php

namespace App\Http\Controllers\API\CMS;

use App\Http\Controllers\Controller;
use App\Http\Requests\CMS\PagesRequest;
use App\Interfaces\PagesInterface;
use Illuminate\Http\Request;

class PagesController extends Controller
{
    protected $pagesInterface;

    public function __construct(PagesInterface $pagesInterface)
    {
        $this->pagesInterface = $pagesInterface;
    }

    public function all()
    {
        return $this->pagesInterface->all();
    }

    public function updatePages(PagesRequest $request)
    {
        return $this->pagesInterface->updatePages($request);
    }

    public function show(Request $request)
    {
        return $this->pagesInterface->show($request->slug);
    }
}
