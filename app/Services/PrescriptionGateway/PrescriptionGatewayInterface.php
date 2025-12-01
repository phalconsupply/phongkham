<?php

namespace App\Services\PrescriptionGateway;

interface PrescriptionGatewayInterface
{
    /**
     * Send prescription to external pharmacy system
     *
     * @param array $prescriptionData
     * @return array Response from gateway
     */
    public function sendPrescription(array $prescriptionData): array;

    /**
     * Check prescription status
     *
     * @param string $prescriptionId
     * @return array Status information
     */
    public function checkStatus(string $prescriptionId): array;

    /**
     * Cancel prescription
     *
     * @param string $prescriptionId
     * @return bool Success status
     */
    public function cancelPrescription(string $prescriptionId): bool;
}
