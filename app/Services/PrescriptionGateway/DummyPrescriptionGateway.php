<?php

namespace App\Services\PrescriptionGateway;

class DummyPrescriptionGateway implements PrescriptionGatewayInterface
{
    /**
     * Send prescription to external pharmacy system (dummy implementation)
     *
     * @param array $prescriptionData
     * @return array Response from gateway
     */
    public function sendPrescription(array $prescriptionData): array
    {
        // Dummy implementation - replace with actual API integration
        return [
            'success' => true,
            'prescription_id' => 'DUMMY-' . uniqid(),
            'message' => 'Prescription sent successfully (dummy)',
            'timestamp' => now()->toIso8601String(),
        ];
    }

    /**
     * Check prescription status (dummy implementation)
     *
     * @param string $prescriptionId
     * @return array Status information
     */
    public function checkStatus(string $prescriptionId): array
    {
        // Dummy implementation
        return [
            'prescription_id' => $prescriptionId,
            'status' => 'pending',
            'last_updated' => now()->toIso8601String(),
        ];
    }

    /**
     * Cancel prescription (dummy implementation)
     *
     * @param string $prescriptionId
     * @return bool Success status
     */
    public function cancelPrescription(string $prescriptionId): bool
    {
        // Dummy implementation
        return true;
    }
}
