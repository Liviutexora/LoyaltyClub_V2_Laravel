<?php

namespace App\Services;

/**
 * Transaction orchestration layer for QR financial validation.
 *
 * Responsibilities:
 * - Build transaction payload.
 * - Delegate transaction processing through EcosystemHub.
 * - Exclude MLM/cashback/business-rule calculations.
 */
class TransactionValidationService
{
    public function __construct(private EcosystemHubService $ecosystemHubService)
    {
    }

    /**
     * Validate a loyalty transaction payload.
     */
    public function validateTransaction(array $payload): array
    {
        $requiredFields = ['company_id', 'user_id', 'amount', 'discount'];

        foreach ($requiredFields as $field) {
            if (!array_key_exists($field, $payload)) {
                return [
                    'success' => false,
                    'message' => 'Invalid transaction payload',
                ];
            }
        }

        return $this->ecosystemHubService
            ->forwardTransactionValidation($payload);
    }
}
