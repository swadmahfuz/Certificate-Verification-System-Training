<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function __construct(private ActivityLogService $activityLog)
    {
    }

    public function index()
    {
        $departments = Department::orderBy('name')->get();

        return view('admin.departments.index', compact('departments'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:departments,name',
        ]);

        $department = Department::create([
            'name' => trim($validated['name']),
            'is_active' => true,
        ]);

        $this->activityLog->record(
            'department.created',
            'department',
            $department->id,
            'Department "' . $department->name . '" was created.'
        );

        return back()->with('success', 'Department added successfully.');
    }

    public function update(Request $request, Department $department)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:departments,name,' . $department->id,
        ]);

        $department->update([
            'name' => trim($validated['name']),
        ]);

        $this->activityLog->record(
            'department.updated',
            'department',
            $department->id,
            'Department "' . $department->name . '" was updated.'
        );

        return back()->with('success', 'Department updated successfully.');
    }

    public function toggleStatus(Department $department)
    {
        $department->is_active = !$department->is_active;
        $department->save();

        $this->activityLog->record(
            'department.updated',
            'department',
            $department->id,
            'Department "' . $department->name . '" was ' . ($department->is_active ? 'activated' : 'deactivated') . '.'
        );

        return back()->with('success', 'Department status updated.');
    }
}
