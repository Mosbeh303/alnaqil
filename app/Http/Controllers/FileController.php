<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\Store;
use Illuminate\Http\Request;
use Redirect;

class FileController extends Controller
{

    public function store(Request $request, Store $store)
    {

        $request->validate([
            'title' => 'required|string|max:255',
            'file' => 'required|mimes:png,jpeg,jpg,pdf|max:2048',        ],
        [

        ]);

        if ($request->file) {
            $fileName = time().'.'.$request->file->extension();

            $request->file->move(public_path('uploads/stores/'. $store->id .'/files'), $fileName);
        }

        $data = $store->files()->create([
            'title' => $request->title,
            'name' => $fileName,
        ]);


        return Redirect::back()->with('success', trans('file_added'));
    }

    public function destroy(File $file)
    {
        $file->delete();
        return redirect::back()->with('success', trans('file_deleted'));
    }
}
