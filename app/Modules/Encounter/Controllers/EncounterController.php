<?php

namespace App\Modules\Encounter\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Encounter\Models\Encounter;
use App\Modules\Patient\Models\Patient;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EncounterController extends Controller
{
    public function index(Request $request)
    {
        $query = Encounter::with(['patient', 'doctor'])
            ->orderBy('encounter_date', 'desc')
            ->orderBy('encounter_time', 'desc');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('encounter_code', 'like', "%{$search}%")
                  ->orWhereHas('patient', function($q) use ($search) {
                      $q->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('patient_code', 'like', "%{$search}%");
                  })
                  ->orWhereHas('doctor', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $encounters = $query->paginate(15)->withQueryString();

        return Inertia::render('Encounter/Index', [
            'encounters' => $encounters,
            'filters' => $request->only(['search', 'status']),
        ]);
    }

    public function create()
    {
        $patients = Patient::orderBy('last_name')->orderBy('first_name')->get(['id', 'patient_code', 'first_name', 'last_name']);
        $doctors = User::role('doctor')->orderBy('name')->get(['id', 'name']);

        return Inertia::render('Encounter/Create', [
            'patients' => $patients,
            'doctors' => $doctors,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'required|exists:users,id',
            'encounter_date' => 'required|date',
            'encounter_time' => 'required',
            'type' => 'required|in:outpatient,inpatient,emergency,followup',
            'chief_complaint' => 'nullable|string',
            'history' => 'nullable|string',
            'examination' => 'nullable|string',
            'diagnosis' => 'nullable|string',
            'treatment_plan' => 'nullable|string',
            'notes' => 'nullable|string',
            'temperature' => 'nullable|numeric|min:30|max:45',
            'blood_pressure' => 'nullable|string',
            'heart_rate' => 'nullable|integer|min:30|max:250',
            'respiratory_rate' => 'nullable|integer|min:5|max:60',
            'weight' => 'nullable|numeric|min:0|max:500',
            'height' => 'nullable|numeric|min:0|max:300',
            'spo2' => 'nullable|integer|min:0|max:100',
        ]);

        // Calculate BMI if weight and height provided
        if (!empty($validated['weight']) && !empty($validated['height'])) {
            $heightInMeters = $validated['height'] / 100;
            $validated['bmi'] = round($validated['weight'] / ($heightInMeters * $heightInMeters), 2);
        }

        $encounter = Encounter::create($validated);

        return redirect()->route('encounters.show', $encounter)->with('success', 'Cuộc khám đã được tạo thành công.');
    }

    public function show(Encounter $encounter)
    {
        $encounter->load(['patient', 'doctor']);

        return Inertia::render('Encounter/Show', [
            'encounter' => $encounter,
        ]);
    }

    public function edit(Encounter $encounter)
    {
        $encounter->load(['patient', 'doctor']);
        $patients = Patient::orderBy('last_name')->orderBy('first_name')->get(['id', 'patient_code', 'first_name', 'last_name']);
        $doctors = User::role('doctor')->orderBy('name')->get(['id', 'name']);

        return Inertia::render('Encounter/Edit', [
            'encounter' => $encounter,
            'patients' => $patients,
            'doctors' => $doctors,
        ]);
    }

    public function update(Request $request, Encounter $encounter)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'required|exists:users,id',
            'encounter_date' => 'required|date',
            'encounter_time' => 'required',
            'type' => 'required|in:outpatient,inpatient,emergency,followup',
            'status' => 'required|in:scheduled,in-progress,completed,cancelled',
            'chief_complaint' => 'nullable|string',
            'history' => 'nullable|string',
            'examination' => 'nullable|string',
            'diagnosis' => 'nullable|string',
            'treatment_plan' => 'nullable|string',
            'notes' => 'nullable|string',
            'temperature' => 'nullable|numeric|min:30|max:45',
            'blood_pressure' => 'nullable|string',
            'heart_rate' => 'nullable|integer|min:30|max:250',
            'respiratory_rate' => 'nullable|integer|min:5|max:60',
            'weight' => 'nullable|numeric|min:0|max:500',
            'height' => 'nullable|numeric|min:0|max:300',
            'spo2' => 'nullable|integer|min:0|max:100',
        ]);

        // Calculate BMI if weight and height provided
        if (!empty($validated['weight']) && !empty($validated['height'])) {
            $heightInMeters = $validated['height'] / 100;
            $validated['bmi'] = round($validated['weight'] / ($heightInMeters * $heightInMeters), 2);
        }

        // Set timestamps based on status
        if ($validated['status'] === 'in-progress' && !$encounter->started_at) {
            $validated['started_at'] = now();
        }
        if ($validated['status'] === 'completed' && !$encounter->completed_at) {
            $validated['completed_at'] = now();
        }

        $encounter->update($validated);

        return redirect()->route('encounters.show', $encounter)->with('success', 'Cuộc khám đã được cập nhật.');
    }

    public function destroy(Encounter $encounter)
    {
        $encounter->delete();

        return redirect()->route('encounters.index')->with('success', 'Cuộc khám đã được xóa.');
    }
}
