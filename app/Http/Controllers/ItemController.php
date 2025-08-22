<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\ItemHeading;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;

class ItemController extends Controller
{
    public function index()
    {
        $acItems = Item::where('team', 'AC')->orderBy('status')->get();
        $dcItems = Item::where('team', 'DC')->orderBy('status')->get();
        $acHeadings = ItemHeading::where('team', 'AC')->where('status', '1')->pluck('name');
        $dcHeadings = ItemHeading::where('team', 'DC')->where('status', '1')->pluck('name');

        return view('master.items', compact('acItems', 'dcItems', 'acHeadings', 'dcHeadings'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'team' => 'required',
            'heading' => 'required',
            'name' => [
                'required',
                'max:255',
                Rule::unique('items')->where(function ($query) use ($request) {
                    return $query->where('team', $request->team);
                }),
            ],
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
            'name' => [
                'required',
                'max:255',
                Rule::unique('items')->ignore($item->id)->where(function ($query) use ($request) {
                    return $query->where('team', $request->team);
                }),
            ],
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
            $item->status = '0';
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
    
    public function addHeading(Request $request)
    {
        if ($request->isMethod('get')) {
            $acHeadings = ItemHeading::where('team', 'AC')->orderBy('status')->get();
            $dcHeadings = ItemHeading::where('team', 'DC')->orderBy('status')->get();
            return view('master.add-heading', compact('acHeadings', 'dcHeadings'));
        }

        if ($request->isMethod('post')) {
            try {
                $data = $request->validate([
                    'name' => 'required',
                    'team' => 'required',
                ]);

                $heading = ItemHeading::create($data);
                Log::info("Item Heading added", ['heading' => $heading]);

                return redirect()->back()->with('success', 'Item heading added successfully.');
            } catch (\Exception $e) {
                return redirect()->back()->with('error', 'An error occurred while adding the item heading.');
            }
        }
    }
    
    public function editHeading($id)
    {
        try {
            $editHeading = ItemHeading::findOrFail($id);
            $acHeadings = ItemHeading::where('team', 'AC')->orderBy('status')->get();
            $dcHeadings = ItemHeading::where('team', 'DC')->orderBy('status')->get();
            
            return view('master.add-heading', compact('editHeading', 'acHeadings', 'dcHeadings'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Heading not found.');
        }
    }
    
    public function updateHeading(Request $request, $id)
    {
        try {
            $heading = ItemHeading::findOrFail($id);
            
            $data = $request->validate([
                'name' => 'required',
                'team' => 'required'
            ]);
    
            $heading->update($data);
            Log::info("Item Heading updated", ['heading' => $heading]);
    
            return redirect()->route('items.add-heading')->with('success', 'Heading updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred while updating the heading.');
        }
    }
    
    public function deleteHeading($id)
    {
        try {
            $heading = ItemHeading::findOrFail($id);

            // Check if heading is used in any items
            $itemsCount = Item::where('heading', $heading->name)->count();
            if ($itemsCount > 0) {
                return redirect()->back()->with('error', 'Cannot delete heading. It is being used by items.');
            }

            $heading->status = '0';
            $heading->save();
            Log::info("Item Heading inactivated", ['heading' => $heading]);

            return redirect()->back()->with('success', 'Heading inactivated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred while inactivating the heading.');
        }
    }
    
    public function getHeadings(Request $request)
    {
        Log::info("Retrieving headings for team", ['team' => $request->input('team')]);
        try {
            $team = $request->input('team');

            if (!$team) {
                Log::error("Team parameter is missing.");
                return response()->json(['error' => 'Team parameter is missing.'], 400);
            }

            $headings = ItemHeading::where('team', $team)
                ->where('status', '1')
                ->pluck('name');

            Log::info("Retrieved headings", ['headings' => json_encode($headings)]);
            return response()->json($headings);
        } catch (\Exception $e) {
            Log::error("Error retrieving headings", ['error' => $e->getMessage()]);
            return response()->json(['error' => 'An error occurred while retrieving headings.'], 500);
        }
    }
}
