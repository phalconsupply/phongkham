<?php

namespace App\Modules\Patient\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Patient\Models\Patient;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PatientController extends Controller
{
    public function index(Request $request)
    {
        $query = Patient::query();

        // Tìm kiếm
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('first_name', 'like', "%{$request->search}%")
                  ->orWhere('last_name', 'like', "%{$request->search}%")
                  ->orWhere('patient_code', 'like', "%{$request->search}%")
                  ->orWhere('phone', 'like', "%{$request->search}%");
            });
        }

        $patients = $query->latest()->paginate(20)->withQueryString();

        return Inertia::render('Patient/Index', [
            'patients' => $patients,
            'filters' => $request->only(['search'])
        ]);
    }

    public function create()
    {
        return Inertia::render('Patient/CreateSimple');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'date_of_birth' => 'required|date',
            'gender' => 'required|in:male,female,other',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'province' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:10',
            'id_number' => 'nullable|string|max:50',
            'insurance_number' => 'nullable|string|max:50',
            'emergency_contact_name' => 'nullable|string',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'notes' => 'nullable|string',
            'medical_history' => 'nullable|string',
            'allergies' => 'nullable|string',
            'blood_type' => 'nullable|string|max:10',
        ]);

        Patient::create($validated);

        return redirect()->route('patients.index')
            ->with('success', 'Đã thêm bệnh nhân thành công.');
    }

    public function show(Patient $patient)
    {
        return Inertia::render('Patient/Show', [
            'patient' => $patient
        ]);
    }

    public function edit(Patient $patient)
    {
        return Inertia::render('Patient/Edit', [
            'patient' => $patient
        ]);
    }

    public function update(Request $request, Patient $patient)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'date_of_birth' => 'required|date',
            'gender' => 'required|in:male,female,other',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'province' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:10',
            'id_number' => 'nullable|string|max:50',
            'insurance_number' => 'nullable|string|max:50',
            'emergency_contact_name' => 'nullable|string',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'notes' => 'nullable|string',
            'medical_history' => 'nullable|string',
            'allergies' => 'nullable|string',
            'blood_type' => 'nullable|string|max:10',
        ]);

        $patient->update($validated);

        return redirect()->route('patients.index')
            ->with('success', 'Đã cập nhật thông tin bệnh nhân.');
    }

    public function destroy(Patient $patient)
    {
        $patient->delete();

        return redirect()->route('patients.index')
            ->with('success', 'Đã xóa bệnh nhân.');
    }
}
