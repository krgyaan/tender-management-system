<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    public function index()
    {
        $acItems = Item::where('team', 'AC')->orderBy('status')->get();
        $dcItems = Item::where('team', 'DC')->orderBy('status')->get();
        return view('master.items', compact('acItems', 'dcItems'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'team' => 'required',
            'heading' => 'required',
            'name' => "required|max:255|unique:items,name,NULL,id,team,{$request->team}",
        ]);

        Item::create($request->all());
        return redirect()->back()->with('success', 'Item added successfully.');
    }

    public function edit(Item $item)
    {
        return view('items.edit', compact('item'));
    }

    public function update(Request $request, Item $item)
    {
        $request->validate([
            'team' => 'required',
            'heading' => 'required',
            'name' => "required|max:255|unique:items,name,NULL,id,team,{$request->team}",
        ]);

        $item->update($request->all());
        return redirect()->back()->with('success', 'Item updated successfully.');
    }

    public function delete($id)
    {
        $item = Item::find($id);
        if ($item === null) {
            return redirect()->back()->with('error', 'Item not found.');
        }

        try {
            $item->status = 0;
            $item->save();
            return redirect()->back()->with('success', 'Item inactivated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred while inactivating the item.');
        }
    }

    public function approve($id)
    {
        $item = Item::find($id);
        if ($item === null) {
            return redirect()->back()->with('error', 'Item not found.');
        }

        try {
            $item->status = 1;
            $item->save();
            return redirect()->back()->with('success', 'Item approved successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred while approving the item.');
        }
    }
}
