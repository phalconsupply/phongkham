<?php

namespace App\Modules\Prescription\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Prescription\Models\Prescription;
use App\Modules\Prescription\Models\PrescriptionItem;
use App\Modules\Patient\Models\Patient;
use App\Modules\Encounter\Models\Encounter;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class PrescriptionController extends Controller
{
    public function index(Request $request)
    {
        $query = Prescription::with(['patient', 'doctor', 'encounter', 'icd10Code'])
            ->orderBy('prescription_date', 'desc');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('prescription_code', 'like', "%{$search}%")
                  ->orWhereHas('patient', function($q) use ($search) {
                      $q->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('patient_code', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $prescriptions = $query->paginate(15)->withQueryString();

        return Inertia::render('Prescription/Index', [
            'prescriptions' => $prescriptions,
            'filters' => $request->only(['search', 'status']),
        ]);
    }

    public function create()
    {
        $patients = Patient::orderBy('last_name')->orderBy('first_name')->get(['id', 'patient_code', 'first_name', 'last_name']);
        $doctors = User::role('doctor')->orderBy('name')->get(['id', 'name']);
        $encounters = Encounter::with('patient')
            ->whereIn('status', ['in-progress', 'completed'])
            ->orderBy('encounter_date', 'desc')
            ->limit(50)
            ->get();

        return Inertia::render('Prescription/Create', [
            'patients' => $patients,
            'doctors' => $doctors,
            'encounters' => $encounters,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'encounter_id' => 'nullable|exists:encounters,id',
            'doctor_id' => 'required|exists:users,id',
            'prescription_date' => 'required|date',
            'diagnosis' => 'nullable|string',
            'notes' => 'nullable|string',
            'icd10_code_id' => 'nullable|exists:icd10_codes,id',
            'items' => 'required|array|min:1',
            'items.*.medication_name' => 'required|string',
            'items.*.dosage' => 'nullable|string',
            'items.*.frequency' => 'nullable|string',
            'items.*.duration' => 'nullable|string',
            'items.*.route' => 'nullable|string',
            'items.*.quantity' => 'nullable|integer|min:1',
            'items.*.unit' => 'nullable|string',
            'items.*.instructions' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $prescription = Prescription::create([
                'patient_id' => $validated['patient_id'],
                'encounter_id' => $validated['encounter_id'] ?? null,
                'doctor_id' => $validated['doctor_id'],
                'prescription_date' => $validated['prescription_date'],
                'diagnosis' => $validated['diagnosis'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'icd10_code_id' => $validated['icd10_code_id'] ?? null,
            ]);

            foreach ($validated['items'] as $item) {
                $prescription->items()->create($item);
            }

            DB::commit();

            return redirect()->route('prescriptions.show', $prescription)->with('success', 'Tơ thuốc đã được tạo thành công.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Có lỗi xảy ra khi tạo tơ thuốc.'])->withInput();
        }
    }

    public function show(Prescription $prescription)
    {
        $prescription->load(['patient', 'doctor', 'encounter', 'items', 'icd10Code']);

        return Inertia::render('Prescription/Show', [
            'prescription' => $prescription,
        ]);
    }

    public function edit(Prescription $prescription)
    {
        $prescription->load(['patient', 'doctor', 'encounter', 'items', 'icd10Code']);
        $patients = Patient::orderBy('last_name')->orderBy('first_name')->get(['id', 'patient_code', 'first_name', 'last_name']);
        $doctors = User::role('doctor')->orderBy('name')->get(['id', 'name']);
        $encounters = Encounter::with('patient')
            ->whereIn('status', ['in-progress', 'completed'])
            ->orderBy('encounter_date', 'desc')
            ->limit(50)
            ->get();

        return Inertia::render('Prescription/Edit', [
            'prescription' => $prescription,
            'patients' => $patients,
            'doctors' => $doctors,
            'encounters' => $encounters,
        ]);
    }

    public function update(Request $request, Prescription $prescription)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'encounter_id' => 'nullable|exists:encounters,id',
            'doctor_id' => 'required|exists:users,id',
            'prescription_date' => 'required|date',
            'diagnosis' => 'nullable|string',
            'notes' => 'nullable|string',
            'status' => 'required|in:active,completed,cancelled',
            'icd10_code_id' => 'nullable|exists:icd10_codes,id',
            'items' => 'required|array|min:1',
            'items.*.medication_name' => 'required|string',
            'items.*.dosage' => 'nullable|string',
            'items.*.frequency' => 'nullable|string',
            'items.*.duration' => 'nullable|string',
            'items.*.route' => 'nullable|string',
            'items.*.quantity' => 'nullable|integer|min:1',
            'items.*.unit' => 'nullable|string',
            'items.*.instructions' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $prescription->update([
                'patient_id' => $validated['patient_id'],
                'encounter_id' => $validated['encounter_id'] ?? null,
                'doctor_id' => $validated['doctor_id'],
                'prescription_date' => $validated['prescription_date'],
                'diagnosis' => $validated['diagnosis'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'status' => $validated['status'],
                'icd10_code_id' => $validated['icd10_code_id'] ?? null,
            ]);

            // Delete old items and create new ones
            $prescription->items()->delete();
            foreach ($validated['items'] as $item) {
                $prescription->items()->create($item);
            }

            DB::commit();

            return redirect()->route('prescriptions.show', $prescription)->with('success', 'Tơ thuốc đã được cập nhật.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Có lỗi xảy ra khi cập nhật tơ thuốc.'])->withInput();
        }
    }

    public function destroy(Prescription $prescription)
    {
        $prescription->delete();

        return redirect()->route('prescriptions.index')->with('success', 'Tơ thuốc đã được xóa.');
    }
}
