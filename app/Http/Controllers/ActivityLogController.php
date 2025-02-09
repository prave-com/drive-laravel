<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');

        $logs = Activity::query()
            ->when($search, function ($query) use ($search) {
                $query->where('description', 'like', '%'.$search.'%')
                    ->orWhereHas('causer', function ($query) use ($search) {
                        $query->where('email', 'like', '%'.$search.'%');
                    })
                    // Optionally, search by date as well
                    ->orWhereDate('created_at', 'like', '%'.$search.'%')
                    ->orWhereTime('created_at', 'like', '%'.$search.'%');
            })
            ->latest()
            ->paginate(50);

        return view('admin.activity-logs.index', compact('logs', 'search'));
    }
}
