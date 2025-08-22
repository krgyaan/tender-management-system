<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Location;
use App\Models\Organization;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProjectController extends Controller
{
    public function index()
    {
        Log::info('Getting all projects');
        $organisations = Organization::where('status', '1')->get();
        $items = Item::where('status', '1')->get();
        $locations = Location::where('status', '1')->get();
        $projects = Project::with(['organisation', 'item', 'location'])->latest()->get();
        $acProjects = Project::with(['organisation', 'item', 'location'])->where('team_name', 'AC')->latest()->get();
        $dcProjects = Project::with(['organisation', 'item', 'location'])->where('team_name', 'DC')->latest()->get();
        $hoProjects = Project::with(['organisation', 'item', 'location'])->where('team_name', 'HO')->latest()->get();
        $bdProjects = Project::with(['organisation', 'item', 'location'])->where('team_name', 'BD')->latest()->get();
        return view('master.projects', compact('projects', 'acProjects', 'dcProjects', 'organisations', 'items', 'locations', 'hoProjects', 'bdProjects'));
    }

    public function create()
    {
        Log::info('Creating new project');
        $organisations = Organization::where('status', '1')->get();
        $items = Item::where('status', '1')->get();
        $locations = Location::where('status', '1')->get();
        return view('master.projects', compact('organisations', 'items', 'locations'));
    }

    public function store(Request $request)
    {
        Log::info('Creating new project: ' . json_encode($request->all()));
        try {
            // First validate everything except file
            $validated = $request->validate([
                'team_name' => 'required|string',
                'organisation' => 'required|exists:organizations,id',
                'item' => 'required|exists:items,id',
                'location' => 'required|exists:locations,id',
                'po_no' => 'required|string',
                'project_name' => 'required|string',
                'project_code' => 'required|string|unique:projects',
                'po_date' => 'required|date'
            ]);
    
            // Handle file upload separately
            if (!$request->hasFile('po_upload')) {
                Log::error('No file uploaded');
                throw new \Exception('PO document is required');
            }
    
            $file = $request->file('po_upload');
            
            if (!$file->isValid()) {
                Log::error('Invalid file upload');
                throw new \Exception('Invalid file upload');
            }
    
            // Log detailed file information
            Log::info('File details:', [
                'original_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType(),
                'size' => $file->getSize(),
                'error' => $file->getError()
            ]);
    
            // Validate file type
            $extension = strtolower($file->getClientOriginalExtension());
            $allowedTypes = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png'];
            
            if (!in_array($extension, $allowedTypes)) {
                Log::error('Invalid file type: ' . $extension);
                throw new \Exception('Invalid file type. Allowed types: PDF, DOC, DOCX, JPG, JPEG, PNG');
            }
    
            try {
                // Generate filename and move file
                $filename = time() . '_' . $file->getClientOriginalName();
                $path = public_path('uploads/projects');
    
                if (!file_exists($path)) {
                    mkdir($path, 0777, true);
                    Log::info('Created directory: ' . $path);
                }
    
                $file->move($path, $filename);
                Log::info('File uploaded successfully: ' . $filename);
                
                $validated['po_upload'] = $filename;
            } catch (\Exception $e) {
                Log::error('File upload failed: ' . $e->getMessage());
                throw new \Exception('Failed to upload file: ' . $e->getMessage());
            }
    
            // Create project
            $project = Project::create([
                'team_name' => $validated['team_name'],
                'organisation_id' => $validated['organisation'],
                'item_id' => $validated['item'],
                'location_id' => $validated['location'],
                'po_no' => $validated['po_no'],
                'project_name' => $validated['project_name'],
                'project_code' => $validated['project_code'],
                'po_date' => $validated['po_date'],
                'po_upload' => $validated['po_upload']
            ]);
    
            return redirect()->route('projects.index')->with('success', 'Project created successfully');
        } catch (\Exception $e) {
            Log::error('Project creation failed: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }

    public function show(Project $project)
    {
        Log::info('Showing project: ' . $project->id);
        $project->load(['organisation', 'item', 'location']);
        return view('master.projects', compact('project'));
    }

    public function edit(Project $project)
    {
        Log::info('Editing project: ' . $project->id);
        $organisations = Organization::where('status', '1')->get();
        $items = Item::where('status', '1')->get();
        $locations = Location::where('status', '1')->get();
        return view('master.projects', compact('project', 'organisations', 'items', 'locations'));
    }

    public function update(Request $request, Project $project)
    {
        try {
            Log::info('Validating project data for update');
            $validated = $request->validate([
                'team_name' => 'required|string',
                'organisation' => 'required|exists:organizations,id',
                'item' => 'required|exists:items,id',
                'location' => 'required|exists:locations,id',
                'po_no' => 'required|string',
                'project_name' => 'required|string',
                'project_code' => 'required|string|unique:projects,project_code,' . $project->id,
                'po_date' => 'required|date',
                'po_upload' => 'nullable|file|mimes:pdf,doc,docx'
            ]);

            // Handle file upload
            if ($request->hasFile('po_upload')) {
                // Delete old file if exists
                if ($project->po_upload && file_exists(public_path('uploads/projects/' . $project->po_upload))) {
                    Log::info('Deleting old PO file');
                    unlink(public_path('uploads/projects/' . $project->po_upload));
                }

                Log::info('Uploading new PO file');
                $file = $request->file('po_upload');
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move('uploads/projects/', $filename);
                $validated['po_upload'] = $filename;
            }

            Log::info('Updating project: ' . $project->id);
            $project->update([
                'team_name' => $validated['team_name'],
                'organisation_id' => $validated['organisation'],
                'item_id' => $validated['item'],
                'location_id' => $validated['location'],
                'po_no' => $validated['po_no'],
                'project_name' => $validated['project_name'],
                'project_code' => $validated['project_code'],
                'po_date' => $validated['po_date'],
                'po_upload' => $validated['po_upload'] ?? $project->po_upload
            ]);

            return redirect()->route('projects.index')->with('success', 'Project updated successfully');
        } catch (\Exception $e) {
            Log::error('Project update failed: ' . $e->getMessage());
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function destroy(Project $project)
    {
        try {
            Log::info('Deleting project: ' . $project->id);
            // Delete associated file
            if ($project->po_upload && file_exists(public_path('uploads/projects/' . $project->po_upload))) {
                Log::info('Deleting PO file');
                unlink(public_path('uploads/projects/' . $project->po_upload));
            }

            $project->delete();
            return redirect()->route('projects.index')->with('success', 'Project deleted successfully');
        } catch (\Exception $e) {
            Log::error('Project deletion failed: ' . $e->getMessage());
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
