<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Security\SecureFileUpload;
use App\Security\UrlValidator;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $query = Project::query();
        if ($request->filled('search')) {
            $s = UrlValidator::escapeLike((string) $request->input('search'));
            $query->where(function ($q) use ($s) {
                $q->where('title', 'like', "%{$s}%")->orWhere('category', 'like', "%{$s}%");
            });
        }
        $projects = $query->orderBy('order', 'asc')->paginate(10)->withQueryString();

        return view('admin.projects.index', compact('projects'));
    }

    public function create()
    {
        return view('admin.projects.form', ['project' => new Project()]);
    }

    public function show(Project $project)
    {
        return redirect()->route('admin.projects.edit', $project->id);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:200',
            'slug' => 'nullable|string|max:200|unique:projects,slug',
            'category' => 'required|string|max:100',
            'client_name' => 'nullable|string|max:150',
            'image_file' => 'nullable|file|max:8192|mimes:jpg,jpeg,png,webp',
            'image' => 'nullable|string|max:500',
            'summary' => 'required|string|max:500',
            'description' => 'nullable|string',
            'challenge' => 'nullable|string',
            'solution' => 'nullable|string',
            'results' => 'nullable|string',
            'technologies' => 'nullable|string',
            'project_url' => 'nullable|string|max:255',
            'order' => 'nullable|integer',
            'is_featured' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ], [
            'image_file.uploaded' => 'The image failed to upload. The file may exceed server upload limits (upload_max_filesize / post_max_size in php.ini) or directory write permissions.',
            'image_file.max' => 'The image file must not exceed 8MB.',
        ]);

        if (! empty($validated['image']) && ! UrlValidator::isSafeMediaPath($validated['image'])) {
            return back()->withInput()->withErrors(['image' => 'Image URL must be a valid http/https URL or relative media path.']);
        }

        if (! empty($validated['project_url']) && ! UrlValidator::isSafeHttpUrl($validated['project_url'], false)) {
            return back()->withInput()->withErrors(['project_url' => 'Live Project URL must start with http:// or https://.']);
        }

        if ($request->hasFile('image_file')) {
            try {
                $validated['image'] = SecureFileUpload::storeImage($request->file('image_file'), 'projects', 8192);
            } catch (\Illuminate\Validation\ValidationException $e) {
                return back()->withInput()->withErrors($e->errors());
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error('Project image upload failed: '.$e->getMessage());

                return back()->withInput()->withErrors(['image_file' => 'Failed to save uploaded image: '.$e->getMessage()]);
            }
        } elseif (! empty($request->input('image'))) {
            $validated['image'] = trim($request->input('image'));
        } elseif (empty($validated['image'])) {
            $validated['image'] = '/assets/images/projects/project-analytics.jpg';
        }

        $validated['slug'] = ! empty($validated['slug']) ? Str::slug($validated['slug']) : Str::slug($validated['title']);
        $validated['technologies'] = ! empty($request->input('technologies')) ? array_filter(array_map('trim', explode(',', $request->input('technologies')))) : [];
        $validated['is_featured'] = $request->boolean('is_featured');
        $validated['is_active'] = $request->boolean('is_active');
        $validated['order'] = $validated['order'] ?? 0;

        Project::create($validated);

        return redirect()->route('admin.projects.index')->with('success', 'Project created successfully.');
    }

    public function edit(Project $project)
    {
        return view('admin.projects.form', compact('project'));
    }

    public function update(Request $request, Project $project)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:200',
            'slug' => 'nullable|string|max:200|unique:projects,slug,'.$project->id,
            'category' => 'required|string|max:100',
            'client_name' => 'nullable|string|max:150',
            'image_file' => 'nullable|file|max:8192|mimes:jpg,jpeg,png,webp',
            'image' => 'nullable|string|max:500',
            'summary' => 'required|string|max:500',
            'description' => 'nullable|string',
            'challenge' => 'nullable|string',
            'solution' => 'nullable|string',
            'results' => 'nullable|string',
            'technologies' => 'nullable|string',
            'project_url' => 'nullable|string|max:255',
            'order' => 'nullable|integer',
            'is_featured' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ], [
            'image_file.uploaded' => 'The image failed to upload. The file may exceed server upload limits (upload_max_filesize / post_max_size in php.ini) or directory write permissions.',
            'image_file.max' => 'The image file must not exceed 8MB.',
        ]);

        if (! empty($validated['image']) && ! UrlValidator::isSafeMediaPath($validated['image'])) {
            return back()->withInput()->withErrors(['image' => 'Image URL must be a valid http/https URL or relative media path.']);
        }

        if (! empty($validated['project_url']) && ! UrlValidator::isSafeHttpUrl($validated['project_url'], false)) {
            return back()->withInput()->withErrors(['project_url' => 'Live Project URL must start with http:// or https://.']);
        }

        if ($request->hasFile('image_file')) {
            try {
                $previous = $project->image;
                $validated['image'] = SecureFileUpload::storeImage($request->file('image_file'), 'projects', 8192);
                SecureFileUpload::deleteManagedFile($previous);
            } catch (\Illuminate\Validation\ValidationException $e) {
                return back()->withInput()->withErrors($e->errors());
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error('Project image upload failed: '.$e->getMessage());

                return back()->withInput()->withErrors(['image_file' => 'Failed to save uploaded image: '.$e->getMessage()]);
            }
        } elseif (! empty($request->input('image'))) {
            $validated['image'] = trim($request->input('image'));
        }

        $validated['slug'] = ! empty($validated['slug']) ? Str::slug($validated['slug']) : Str::slug($validated['title']);
        $validated['technologies'] = ! empty($request->input('technologies')) ? array_filter(array_map('trim', explode(',', $request->input('technologies')))) : [];
        $validated['is_featured'] = $request->boolean('is_featured');
        $validated['is_active'] = $request->boolean('is_active');
        $validated['order'] = $validated['order'] ?? 0;

        $project->update($validated);

        return redirect()->route('admin.projects.index')->with('success', 'Project updated successfully.');
    }

    public function destroy(Project $project)
    {
        SecureFileUpload::deleteManagedFile($project->image);
        $project->delete();

        return redirect()->route('admin.projects.index')->with('success', 'Project removed.');
    }
}
