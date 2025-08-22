<?php

namespace App\Http\Controllers;

use App\Models\DocumentSubmitted;
use Illuminate\Http\Request;

class DocumentSubmittedController extends Controller
{
    public function index()
    {
        $docs = DocumentSubmitted::all();
        return view('master.document-submitted', compact('docs'));
    }

    public function create() {}

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:document_submitteds|max:255',
        ]);

        DocumentSubmitted::create($request->all());

        return redirect()->back()->with('success', 'Document submitted added successfully.');
    }

    public function show(DocumentSubmitted $doc) {}

    public function edit(DocumentSubmitted $doc) {}

    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:document_submitteds,id',
            'name' => 'required|max:255|unique:document_submitteds,name,' . $request->id,
        ]);

        $doc = DocumentSubmitted::find($request->id);
        $doc->update($request->all());

        return redirect()->back()->with('success', 'Document submitted updated successfully.');
    }

    public function destroy(Request $request)
    {
        try {
            $doc = DocumentSubmitted::find($request->id);
            $doc->delete();
            return redirect()->back()->with('success', 'Document submitted deleted successfully.');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
    }
}
