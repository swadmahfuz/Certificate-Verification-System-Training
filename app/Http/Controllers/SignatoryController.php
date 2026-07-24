<?php

namespace App\Http\Controllers;

use App\Models\Signatory;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class SignatoryController extends Controller
{
    private $activityLog;

    public function __construct(ActivityLogService $activityLog)
    {
        $this->middleware('auth');
        $this->activityLog = $activityLog;
    }

    public function index()
    {
        $signatories = Signatory::orderBy('is_active', 'desc')->orderBy('name', 'asc')->paginate(25);

        return view('signatories.index', compact('signatories'));
    }

    public function create()
    {
        return view('signatories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:certificates_training_signatories,email',
            'designation' => 'required|string|max:255',
            'department' => 'nullable|string|max:255',
            'signature' => 'required|file|mimes:png,jpg,jpeg,webp|max:2048',
            'is_active' => 'nullable|boolean',
        ]);

        $signaturePath = $request->file('signature')->store('signatory-signatures');

        $signatory = Signatory::create([
            'name' => trim($validated['name']),
            'email' => strtolower(trim($validated['email'])),
            'designation' => trim($validated['designation']),
            'department' => !empty($validated['department']) ? trim($validated['department']) : null,
            'signature_path' => $signaturePath,
            'is_active' => $request->boolean('is_active'),
            'created_by_id' => Auth::id(),
        ]);

        $this->activityLog->record(
            'signatory.created',
            'signatory',
            $signatory->id,
            'Signatory ' . $signatory->name . ' was added.'
        );

        return redirect()->route('signatories.index')->with('success', 'Signatory added successfully.');
    }

    public function edit($id)
    {
        $signatory = Signatory::findOrFail($id);

        return view('signatories.edit', compact('signatory'));
    }

    public function update(Request $request, $id)
    {
        $signatory = Signatory::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('certificates_training_signatories', 'email')->ignore($signatory->id),
            ],
            'designation' => 'required|string|max:255',
            'department' => 'nullable|string|max:255',
            'signature' => 'nullable|file|mimes:png,jpg,jpeg,webp|max:2048',
            'is_active' => 'nullable|boolean',
        ]);

        $signatory->name = trim($validated['name']);
        $signatory->email = strtolower(trim($validated['email']));
        $signatory->designation = trim($validated['designation']);
        $signatory->department = !empty($validated['department']) ? trim($validated['department']) : null;
        $signatory->is_active = $request->boolean('is_active');

        /// Preserve the previous signature file because older certificates may reference it.
        if ($request->hasFile('signature')) {
            $signatory->signature_path = $request->file('signature')->store('signatory-signatures');
        }

        $signatory->save();

        $this->activityLog->record(
            'signatory.updated',
            'signatory',
            $signatory->id,
            'Signatory ' . $signatory->name . ' was updated.'
        );

        return redirect()->route('signatories.index')->with('success', 'Signatory updated successfully.');
    }

    public function toggleStatus($id)
    {
        $signatory = Signatory::findOrFail($id);
        $signatory->is_active = !$signatory->is_active;
        $signatory->save();

        $status = $signatory->is_active ? 'activated' : 'deactivated';

        $this->activityLog->record(
            'signatory.status_changed',
            'signatory',
            $signatory->id,
            'Signatory ' . $signatory->name . ' was ' . $status . '.',
            ['is_active' => $signatory->is_active]
        );

        return redirect()->route('signatories.index')->with('success', 'Signatory ' . $status . ' successfully.');
    }

    public function signature($id)
    {
        $signatory = Signatory::findOrFail($id);

        if (empty($signatory->signature_path) || !Storage::exists($signatory->signature_path)) {
            abort(404, 'Signatory signature was not found.');
        }

        return Storage::response($signatory->signature_path);
    }
}