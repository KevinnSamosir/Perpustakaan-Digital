<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Publisher;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PublisherController extends Controller
{
    public function index()
    {
        $publishers = Publisher::withCount('books')
            ->orderBy('name')
            ->paginate(15);

        return view('admin.publishers.index', compact('publishers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:publishers,name',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'website' => 'nullable|url|max:255',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_active'] = $request->has('is_active');

        $publisher = Publisher::create($validated);

        // Log activity
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'create',
            'model_type' => Publisher::class,
            'model_id' => $publisher->id,
            'description' => 'Membuat penerbit baru: ' . $publisher->name,
            'new_values' => $publisher->toArray(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->back()->with('success', 'Penerbit berhasil ditambahkan');
    }

    public function update(Request $request, Publisher $publisher)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:publishers,name,' . $publisher->id,
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'website' => 'nullable|url|max:255',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'is_active' => 'nullable|boolean',
        ]);

        $oldValues = $publisher->toArray();
        
        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_active'] = $request->has('is_active');

        $publisher->update($validated);

        // Log activity
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'update',
            'model_type' => Publisher::class,
            'model_id' => $publisher->id,
            'description' => 'Mengubah penerbit: ' . $publisher->name,
            'old_values' => $oldValues,
            'new_values' => $publisher->toArray(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->back()->with('success', 'Penerbit berhasil diperbarui');
    }

    public function destroy(Publisher $publisher)
    {
        $publisherName = $publisher->name;
        
        // Log activity before deleting
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'delete',
            'model_type' => Publisher::class,
            'model_id' => $publisher->id,
            'description' => 'Menghapus penerbit: ' . $publisherName,
            'old_values' => $publisher->toArray(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        $publisher->delete();

        return redirect()->back()->with('success', 'Penerbit berhasil dihapus');
    }
}
