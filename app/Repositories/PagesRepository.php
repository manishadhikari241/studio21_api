<?php


namespace App\Repositories;


use App\Constants\ErrorCodes;
use App\Interfaces\PagesInterface;
use App\Models\Pages;
use App\Models\PageTranslations;

class PagesRepository extends MainRepository implements PagesInterface
{
    public function all()
    {
        try {
            $pages = Pages::orderBy('id', 'asc')->with('translations')->get();
            return $this->success('Pages Updated', $pages, ErrorCodes::SUCCESS);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function updatePages($request)
    {
        try {
            $pages = Pages::where('slug', $request->slug)->firstOrFail();
            $pagesTranslations = PageTranslations::updateOrCreate(['page_id' => $pages->id, 'lang' => 'en'],
                [
                    'title' => $request->title_en,
                    'meta_description' => $request->meta_description_en,
                    'meta_keywords' => $request->meta_keywords_en
                ]);
            $pagesTranslationsCh = PageTranslations::updateOrCreate(['page_id' => $pages->id, 'lang' => 'ch'],
                [
                    'title' => $request->title_ch,
                    'meta_description' => $request->meta_description_ch,
                    'meta_keywords' => $request->meta_keywords_ch
                ]);
            return $this->success('Pages Updated', $pages, ErrorCodes::SUCCESS);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function show($slug)
    {
        try {
            $pages = Pages::where('slug', $slug)->with('translations')->firstOrFail();
            return $this->success('Translations Fetched', $pages, ErrorCodes::SUCCESS);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }

    }
}
