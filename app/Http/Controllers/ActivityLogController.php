<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $query = ActivityLog::latest('created_at');

        if ($request->filled('event')) {
            $query->where('event', $request->event);
        }

        if ($request->filled('source_app')) {
            $sourceApp = $request->source_app;
            $query->where('properties', 'like', '%"source_app":"' . $sourceApp . '"%');
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($builder) use ($search) {
                $builder->where('description', 'like', '%' . $search . '%')
                    ->orWhere('causer_name', 'like', '%' . $search . '%');
            });
        }

        $activities = $query->paginate(30)->withQueryString();
        $eventTypes = ActivityLog::distinct()->orderBy('event')->pluck('event');
        $apps = config('cvs.apps', []);

        return view('activity-log.index', compact('activities', 'eventTypes', 'apps'));
    }
}
