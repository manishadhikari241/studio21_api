<?php

namespace App\Http\Controllers\API\CMS;

use App\Constants\ErrorCodes;
use App\Http\Controllers\Controller;
use App\Models\Configuration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class ConfigurationController extends Controller
{
    public function index(Request $request)
    {
        if ($request->isMethod('post')) {
            $inputs = $request->all();
            foreach ($inputs as $key => $value) {
                if (is_file($value)) $value = $this->handleFile($key, $value);
                Configuration::updateOrCreate(['configuration_key' => $key], ['configuration_value' => $value]);
            }
            return $this->success('Successfully Saved', [], ErrorCodes::SUCCESS);
        } else {
            $configuration = Configuration::all();
            return $this->success('Success', Configuration::all(), ErrorCodes::SUCCESS);
        }
    }

    private function handleFile($key, $file)
    {
        $configuration = Configuration::where('configuration_key', '=', $key)->first();
        // Check if configuration exists
        if (null !== $configuration && $configuration) {
            $getFileName = explode("/", $configuration->configuration_value);
            $getFile = end($getFileName);
            if (Storage::disk('public')->exists('/configuration/' . $getFile)) {
                Storage::disk('public')->delete('/configuration/' . $getFile);
            }
        }
        $path = Storage::disk('public')->put('configuration', $file, 'public');
        $fileName = explode('/', $path);
        return url(Storage::url('configuration/' . $fileName[1]));
    }

}
