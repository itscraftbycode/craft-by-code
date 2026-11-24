@extends('layouts.admin')

@section('title', $post->exists ? 'Edit Article: ' . $post->title : 'Write New Article')
@section('header_title', $post->exists ? 'Edit Article' : 'New Blog Article')

@section('content')
    <div class="max-w-4xl space-y-6">
        <div class="flex items-center justify-between">
            <a href="{{ route('admin.blog.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800 transition flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="m15 18-6-6 6-6"/></svg>
                <span>Back to Articles</span>
            </a>
            @if($post->exists)
                <a href="{{ route('blog.show', $post->slug) }}" target="_blank" class="text-xs font-semibold text-blue-600 hover:underline">
                    View on Website &rarr;
                </a>
            @endif
        </div>

        <form action="{{ $post->exists ? route('admin.blog.update', $post->id) : route('admin.blog.store') }}" 
              method="POST" 
              enctype="multipart/form-data"
              class="bg-white p-8 sm:p-10 rounded-3xl border border-slate-200/80 shadow-sm space-y-6">
            @csrf
            @if($post->exists)
                @method('PUT')
            @endif

            <h2 class="text-xl font-bold text-slate-900 border-b border-slate-100 pb-4">
                {{ $post->exists ? 'Edit Article' : 'Draft New Article' }}
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Article Title *</label>
                    <input type="text" name="title" required value="{{ old('title', $post->title) }}" placeholder="10 Best Practices for Clean Code..."
                           class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-600">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">URL Slug</label>
                    <input type="text" name="slug" value="{{ old('slug', $post->slug) }}" placeholder="clean-code-best-practices"
                           class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-600">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Category *</label>
                    <input type="text" name="category" required value="{{ old('category', $post->category) }}" placeholder="Development, Design, AI, DevOps..."
                           class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-600">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Author Name</label>
                    <input type="text" name="author_name" value="{{ old('author_name', $post->author_name ?? 'CraftByCode Team') }}"
                           class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-600">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Read Time</label>
                    <input type="text" name="read_time" value="{{ old('read_time', $post->read_time ?? '5 min read') }}" placeholder="5 min read"
                           class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-600">
                </div>
            </div>

            <!-- Blog Image Upload & URL -->
            <div class="space-y-3" x-data="{
                filePreview: '',
                directImageUrl: '{{ old('image', $post->image ? (str_starts_with($post->image, 'http') || str_starts_with($post->image, '/') ? $post->image : asset($post->image)) : '') }}',
                updateFilePreview(e) {
                    const file = e.target.files[0];
                    if (file) {
                        if (file.size > 8 * 1024 * 1024) {
                            alert('File is too large! Maximum allowed image size is 8MB.');
                            e.target.value = '';
                            return;
                        }
                        this.filePreview = URL.createObjectURL(file);
                    } else {
                        this.filePreview = '';
                    }
                }
            }">
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Featured Cover Image</label>
                
                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-6 p-4 rounded-2xl bg-slate-50 border border-slate-200">
                    <template x-if="filePreview || directImageUrl">
                        <img :src="filePreview || directImageUrl" alt="Cover preview" class="w-28 h-20 object-cover rounded-xl border border-slate-300 shadow-sm bg-slate-900">
                    </template>
                    <template x-if="!filePreview && !directImageUrl">
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
                            <input type="text" name="image" x-model="directImageUrl" value="{{ old('image', $post->image) }}" placeholder="/assets/images/blog/blog-code.jpg or https://..."
                                   class="w-full px-3 py-2 bg-white border border-slate-200 rounded-xl text-xs font-mono text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-600">
                            @error('image')
                                <p class="text-xs text-rose-600 font-semibold mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Excerpt (Short Summary) *</label>
                <textarea name="excerpt" required rows="2" placeholder="Brief summary displayed on cards..."
                          class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-600">{{ old('excerpt', $post->excerpt) }}</textarea>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Full Article Body (HTML / Markdown supported) *</label>
                <textarea name="content" required rows="10" placeholder="<p>Detailed article content...</p>"
                          class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-sans focus:outline-none focus:ring-2 focus:ring-blue-600">{{ old('content', $post->content) }}</textarea>
            </div>

            <div class="flex items-center gap-6 pt-2">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $post->is_featured) ? 'checked' : '' }}
                           class="w-4 h-4 rounded text-blue-600 focus:ring-blue-500">
                    <span class="text-xs font-bold text-slate-700">Featured Article</span>
                </label>

                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_published" value="1" {{ old('is_published', $post->is_published ?? true) ? 'checked' : '' }}
                           class="w-4 h-4 rounded text-blue-600 focus:ring-blue-500">
                    <span class="text-xs font-bold text-slate-700">Published (Visible to readers)</span>
                </label>
            </div>

            <div class="pt-6 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('admin.blog.index') }}" class="px-5 py-2.5 rounded-xl bg-slate-100 text-slate-700 text-xs font-bold">
                    Cancel
                </a>
                <button type="submit" class="px-7 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold shadow">
                    {{ $post->exists ? 'Update Article' : 'Publish Article' }}
                </button>
            </div>
        </form>
    </div>
@endsection

