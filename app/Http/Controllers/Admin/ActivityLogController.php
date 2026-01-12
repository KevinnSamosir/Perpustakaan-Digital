<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $query = ActivityLog::with('user');

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('description', 'like', "%{$search}%");
        }

        // Filter by action
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        // Filter by model type
        if ($request->filled('model_type')) {
            $query->where('model_type', 'like', "%{$request->model_type}%");
        }

        // Filter by date
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $logs = $query->orderBy('created_at', 'desc')->paginate(20);

        // Stats
        $stats = [
            'total' => ActivityLog::count(),
            'create' => ActivityLog::where('action', 'create')->count(),
            'update' => ActivityLog::where('action', 'update')->count(),
            'delete' => ActivityLog::where('action', 'delete')->count(),
            'today' => ActivityLog::whereDate('created_at', today())->count(),
        ];

        return view('admin.logs.index', compact('logs', 'stats'));
    }

    public function clear(Request $request)
    {
        // Log the clearing action before deleting
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'delete',
            'description' => 'Menghapus semua activity logs',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        // Delete all logs except the one we just created
        ActivityLog::where('id', '<', ActivityLog::max('id'))->delete();

        return redirect()->back()->with('success', 'Semua log aktivitas berhasil dihapus');
    }
}
