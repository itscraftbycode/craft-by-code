@extends('layouts.admin')

@section('title', $project->exists ? 'Edit Project: ' . $project->title : 'Create New Project')
@section('header_title', $project->exists ? 'Edit Project' : 'New Project Case Study')

@section('content')
    <div class="max-w-4xl space-y-6">
        <div class="flex items-center justify-between">
            <a href="{{ route('admin.projects.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800 transition flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="m15 18-6-6 6-6"/></svg>
                <span>Back to Projects</span>
            </a>
            @if($project->exists)
                <a href="{{ route('portfolio.show', $project->slug) }}" target="_blank" class="text-xs font-semibold text-blue-600 hover:underline">
                    View on Website &rarr;
                </a>
            @endif
        </div>

        <form action="{{ $project->exists ? route('admin.projects.update', $project->id) : route('admin.projects.store') }}" 
              method="POST" 
              enctype="multipart/form-data"
              class="bg-white p-8 sm:p-10 rounded-3xl border border-slate-200/80 shadow-sm space-y-6">
            @csrf
            @if($project->exists)
                @method('PUT')
            @endif

            <h2 class="text-xl font-bold text-slate-900 border-b border-slate-100 pb-4">
                {{ $project->exists ? 'Edit Project Details' : 'Add New Portfolio Project' }}
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Project Title *</label>
                    <input type="text" name="title" required value="{{ old('title', $project->title) }}" placeholder="e.g. Analytics Dashboard"
                           class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-600">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">URL Slug</label>
                    <input type="text" name="slug" value="{{ old('slug', $project->slug) }}" placeholder="analytics-dashboard"
                           class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-600">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Category *</label>
                    <input type="text" name="category" required value="{{ old('category', $project->category) }}" placeholder="Web Application, Mobile App, SaaS..."
                           class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-600">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Client Name</label>
                    <input type="text" name="client_name" value="{{ old('client_name', $project->client_name) }}" placeholder="e.g. DataFlow Tech"
                           class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-600">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Live Demo URL</label>
                    <input type="url" name="project_url" value="{{ old('project_url', $project->project_url) }}" placeholder="https://example.com"
                           class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-600">
                </div>
            </div>

            <!-- Project Image Upload & URL -->
            <div class="space-y-3" x-data="{
                previewUrl: '{{ $project->image ? (str_starts_with($project->image, 'http') || str_starts_with($project->image, '/') ? $project->image : asset($project->image)) : '' }}',
                updateFilePreview(e) {
                    const file = e.target.files[0];
                    if (file) {
                        if (file.size > 8 * 1024 * 1024) {
                            alert('File is too large! Maximum allowed image size is 8MB.');
                            e.target.value = '';
                            return;
                        }
                        this.previewUrl = URL.createObjectURL(file);
                    }
                }
            }">
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Project Preview Image</label>
                
                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-6 p-4 rounded-2xl bg-slate-50 border border-slate-200">
                    <template x-if="previewUrl">
                        <img :src="previewUrl" alt="Project preview" class="w-28 h-20 object-cover rounded-xl border border-slate-300 shadow-sm bg-slate-900">
                    </template>
                    <template x-if="!previewUrl">
                        <div class="w-28 h-20 rounded-xl bg-slate-200 border border-slate-300 flex items-center justify-center text-slate-400 text-xs font-semibold">
                            No image
                        </div>
                    </template>

                    <div class="flex-1 space-y-3 w-full">
                        <div>
                            <span class="block text-[11px] font-bold text-slate-600 mb-1">Upload Image File (JPG, PNG, WebP up to 8MB)</span>
                            <input type="file" name="image_file" @change="updateFilePreview" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" 
                                   class="text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-blue-600 file:text-white hover:file:bg-blue-500 cursor-pointer">
                            @error('image_file')
                                <p class="text-xs text-rose-600 font-semibold mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <span class="block text-[11px] font-bold text-slate-600 mb-1">Or Direct Image URL / Path (Optional)</span>
                            <input type="text" name="image" x-model="previewUrl" value="{{ old('image', $project->image) }}" placeholder="/assets/images/projects/project-analytics.jpg or https://..."
                                   class="w-full px-3 py-2 bg-white border border-slate-200 rounded-xl text-xs font-mono text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-600">
                            @error('image')
                                <p class="text-xs text-rose-600 font-semibold mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Summary -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Project Summary (Card headline) *</label>
                <input type="text" name="summary" required value="{{ old('summary', $project->summary) }}" placeholder="Real-time analytics dashboard for data-driven decisions."
                       class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-600">
            </div>

            <!-- Full Description -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Full Description</label>
                <textarea name="description" rows="3" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-600">{{ old('description', $project->description) }}</textarea>
            </div>

            <!-- Challenge & Solution -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">The Challenge</label>
                    <textarea name="challenge" rows="3" placeholder="What technical or operational hurdle did the client face?"
                              class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-blue-600">{{ old('challenge', $project->challenge) }}</textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Our Solution</label>
                    <textarea name="solution" rows="3" placeholder="How did we design and build the platform?"
                              class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-blue-600">{{ old('solution', $project->solution) }}</textarea>
                </div>
            </div>

            <!-- Measurable Results -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Measurable Results</label>
                <input type="text" name="results" value="{{ old('results', $project->results) }}" placeholder="e.g. Dashboard load time reduced by 95%; conversion improved by 45%"
                       class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-600">
            </div>

            <!-- Technologies -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Technologies Used (comma separated)</label>
                <input type="text" name="technologies" value="{{ old('technologies', is_array($project->technologies) ? implode(", ", $project->technologies) : '') }}" placeholder="Laravel, Vue.js, Tailwind CSS, Redis, MySQL"
                       class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-600">
            </div>

            <!-- Checkboxes -->
            <div class="flex items-center gap-6 pt-2">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $project->is_featured ?? true) ? 'checked' : '' }}
                           class="w-4 h-4 rounded text-blue-600 focus:ring-blue-500">
                    <span class="text-xs font-bold text-slate-700">Display in Homepage Featured Projects</span>
                </label>

                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $project->is_active ?? true) ? 'checked' : '' }}
                           class="w-4 h-4 rounded text-blue-600 focus:ring-blue-500">
                    <span class="text-xs font-bold text-slate-700">Active (Publicly Visible)</span>
                </label>
            </div>

            <div class="pt-6 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('admin.projects.index') }}" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition">
                    Cancel
                </a>
                <button type="submit" class="px-7 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold transition shadow">
                    {{ $project->exists ? 'Update Project' : 'Save Project' }}
                </button>
            </div>
        </form>
    </div>
@endsection

