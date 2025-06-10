<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Imports\EmdsImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Log;

class CsvImportController extends Controller
{
    public function showUploadForm()
    {
        return view('upload-csv');
    }

    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,txt',
        ]);

        Log::info('Importing EMD data from CSV file.', ['file' => $request->file('file')]);

        Excel::import(new EmdsImport, $request->file('file'));

        Log::info('EMD data imported successfully from CSV file.');

        return back()->with('success', 'Data imported successfully.');
    }
}
