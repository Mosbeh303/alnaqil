<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Folder;

class FolderController extends Controller
{
    public function index (){


        return inertia('Accounts', [
            'folders' => Folder::where('parent_id', null)->get(),
        ]);
    }

    public function create()
    {
        return inertia('Accounts/Folders/Create');
    }

    public function Store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $folder = Folder::create([
            'name' => $request->name,
            'description' => $request->description,
            'code' => $request->code,
            'parent_id' => $request->parentId
        ]);

        if($folder)
            if ($request->parentId)
                return back()->with('success', trans('group_updated_successfully'));
            else
                return redirect()->route('accounts')->with('success', trans('group_updated_successfully'));
        else
        return back()->with('error');

    }

    public function show (Folder $folder){
        return inertia('Accounts/Folders/Show', [
            'folders' => Folder::where('parent_id', $folder->id)->get(),
            'fold'    => $folder,
            'wallets' => $folder->wallets
        ]);
    }
}
