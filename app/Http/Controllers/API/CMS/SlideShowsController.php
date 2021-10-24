<?php

namespace App\Http\Controllers\API\CMS;

use App\Http\Controllers\Controller;
use App\Http\Requests\CMS\SlideShowUpdateRequest;
use App\Http\Requests\CMS\SlideShowRequest;
use App\Interfaces\SlideShowsInterface;
use Illuminate\Http\Request;

class SlideShowsController extends Controller
{
    protected $slideshows;

    public function __construct(SlideShowsInterface $slideShows)
    {
        $this->slideshows = $slideShows;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->slideshows->all();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(SlideShowRequest $request)
    {
        return $this->slideshows->store($request);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(SlideShowUpdateRequest $request, $id)
    {
        return $this->slideshows->update($request,$id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return $this->slideshows->delete($id);
    }
}
