<?php


namespace App\Repositories;


use App\Constants\ErrorCodes;
use App\Interfaces\SlideShowsInterface;
use App\Models\Configuration;
use App\Models\SlideShows;
use Illuminate\Support\Facades\Storage;

class SlideShowsRepository extends MainRepository implements SlideShowsInterface
{
    public function all()
    {
        $slideShow = SlideShows::where('status', 1)->orderBy('order', 'asc')->get();
        return $this->success('Slideshows fetched', $slideShow, ErrorCodes::SUCCESS);
    }

    public function store($request)
    {
        try {
            $slideshow = SlideShows::create([
                'name' => $request->name,
                'landscape' => $this->handleFile($request->landscape, 'landscape'),
                'portrait' => $this->handleFile($request->portrait, 'portrait'),
                'order' => $request->order,
                'status' => $request->status
            ]);
            return $this->success('SlidesShows Added', $slideshow, ErrorCodes::SUCCESS);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    private function handleFile($file, $type)
    {
//        if (null !== $configuration && $configuration) {
//            $getFileName = explode("/", $configuration->configuration_value);
//            $getFile = end($getFileName);
//            if (Storage::disk('public')->exists('/configuration/' . $getFile)) {
//                Storage::disk('public')->delete('/configuration/' . $getFile);
//            }
//        }
        $path = Storage::disk('public')->put('slideshows/' . $type, $file, 'public');
        $fileName = explode('/', $path);
        return url(Storage::url('slideshows/' . $type . '/' . $fileName[2]));
    }

    public function delete($id)
    {
        try {
            $slides = SlideShows::findorfail($id);
            $landscape = explode('/', $slides->landscape);
            $portrait = explode('/', $slides->portrait);
            Storage::disk('public')->delete('/slideshows/landscape/' . end($landscape));
            Storage::disk('public')->delete('/slideshows/portrait/' . end($portrait));
            $slides->delete();
            return $this->success('Slideshow has been deleted successfully', [], ErrorCodes::SUCCESS);

        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function update($request, $id)
    {
        try {
            $slides = SlideShows::findorfail($id);
            $slides->name = $request->name;
            $slides->order = $request->order;
            $slides->status = $request->status;
            if ($request->landscape) {
                $landscape = explode('/', $slides->landscape);
                Storage::disk('public')->delete('/slideshows/landscape/' . end($landscape));
                $slides->landscape = $this->handleFile($request->landscape, 'landscape');
            }
            if ($request->portrait) {
                $portrait = explode('/', $slides->portrait);
                Storage::disk('public')->delete('/slideshows/portrait/' . end($portrait));
                $slides->portrait = $this->handleFile($request->portrait, 'portrait');
            }
            $slides->save();
            return $this->success('Slideshow has been updated successfully', [], ErrorCodes::SUCCESS);

        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }
}
