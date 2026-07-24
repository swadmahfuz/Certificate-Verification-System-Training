<?php

namespace App\Http\Controllers;

use App\Models\Trainer;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class TrainerController extends Controller
{
    private $activityLog;

    /**
     * Only authenticated users may manage trainers.
     */
    public function __construct(ActivityLogService $activityLog)
    {
        $this->middleware('auth');
        $this->activityLog = $activityLog;
    }

    /**
     * Display all trainers.
     */
    public function index()
    {
        $trainers = Trainer::orderBy('is_active', 'desc')
            ->orderBy('name', 'asc')
            ->paginate(25);

        return view('trainers.index', compact('trainers'));
    }

    /**
     * Show the Add Trainer form.
     */
    public function create()
    {
        return view('trainers.create');
    }

    /**
     * Save a new trainer.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'required',
                'email',
                'max:255',
                'unique:certificates_training_trainers,email',
            ],

            'designation' => [
                'required',
                'string',
                'max:255',
            ],

            'signature' => [
                'required',
                'file',
                'mimes:png,jpg,jpeg,webp',
                'max:2048',
            ],

            'is_active' => [
                'nullable',
                'boolean',
            ],
        ]);

        /*
         * The default local Laravel disk stores this privately under:
         *
         * storage/app/trainer-signatures
         */
        $signaturePath = $request->file('signature')
            ->store('trainer-signatures');

        $trainer = Trainer::create([
            'name' => trim($validated['name']),
            'email' => strtolower(trim($validated['email'])),
            'designation' => trim($validated['designation']),
            'signature_path' => $signaturePath,
            'is_active' => $request->boolean('is_active'),
            'created_by_id' => Auth::id(),
        ]);

        $this->activityLog->record(
            'trainer.created',
            'trainer',
            $trainer->id,
            'Trainer ' . $trainer->name . ' was added.'
        );

        return redirect()
            ->route('trainers.index')
            ->with('success', 'Trainer added successfully.');
    }

    /**
     * Show the Edit Trainer form.
     */
    public function edit($id)
    {
        $trainer = Trainer::findOrFail($id);

        return view('trainers.edit', compact('trainer'));
    }

    /**
     * Update an existing trainer.
     */
    public function update(Request $request, $id)
    {
        $trainer = Trainer::findOrFail($id);

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique(
                    'certificates_training_trainers',
                    'email'
                )->ignore($trainer->id),
            ],

            'designation' => [
                'required',
                'string',
                'max:255',
            ],

            /*
             * A new signature is optional during editing.
             */
            'signature' => [
                'nullable',
                'file',
                'mimes:png,jpg,jpeg,webp',
                'max:2048',
            ],

            'is_active' => [
                'nullable',
                'boolean',
            ],
        ]);

        $trainer->name = trim($validated['name']);
        $trainer->email = strtolower(trim($validated['email']));
        $trainer->designation = trim($validated['designation']);
        $trainer->is_active = $request->boolean('is_active');

        /*
         * When a new signature is uploaded, preserve the old file.
         *
         * Older certificates may later reference the previous signature
         * path, so the previous image must not be deleted automatically.
         */
        if ($request->hasFile('signature')) {
            $trainer->signature_path = $request->file('signature')
                ->store('trainer-signatures');
        }

        $trainer->save();

        $this->activityLog->record(
            'trainer.updated',
            'trainer',
            $trainer->id,
            'Trainer ' . $trainer->name . ' was updated.'
        );

        return redirect()
            ->route('trainers.index')
            ->with('success', 'Trainer updated successfully.');
    }

    /**
     * Activate or deactivate a trainer.
     */
    public function toggleStatus($id)
    {
        $trainer = Trainer::findOrFail($id);

        $trainer->is_active = ! $trainer->is_active;
        $trainer->save();

        $status = $trainer->is_active ? 'activated' : 'deactivated';

        $this->activityLog->record(
            'trainer.status_changed',
            'trainer',
            $trainer->id,
            'Trainer ' . $trainer->name . ' was ' . $status . '.',
            ['is_active' => $trainer->is_active]
        );

        return redirect()
            ->route('trainers.index')
            ->with(
                'success',
                "Trainer {$status} successfully."
            );
    }

    /**
     * Securely display a trainer signature.
     *
     * Signature files are not directly publicly accessible.
     */
    public function signature($id)
    {
        $trainer = Trainer::findOrFail($id);

        if (
            empty($trainer->signature_path) ||
            ! Storage::exists($trainer->signature_path)
        ) {
            abort(404, 'Trainer signature was not found.');
        }

        return Storage::response($trainer->signature_path);
    }
}