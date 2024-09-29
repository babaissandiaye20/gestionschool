<?php

namespace App\Imports;

use App\Services\ApprenantsFirebaseService;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class ApprenantsImport implements ToCollection, WithHeadingRow
{
    protected $apprenantsFirebaseService;

    public function __construct(ApprenantsFirebaseService $apprenantsFirebaseService)
    {
        $this->apprenantsFirebaseService = $apprenantsFirebaseService;
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $data = [
                'user_id' => $this->createOrGetUser($row),
                'referentielId' => $row['referentiel_id'] ?? null,
                'referentielNom' => $row['referentiel_nom'] ?? null,
            ];

            try {
                $result = $this->apprenantsFirebaseService->createApprenant($data);
                if (isset($result['error'])) {
                    Log::error("Failed to create apprenant for email {$row['email']}: " . $result['error']);
                } else {
                    Log::info("Successfully created apprenant for email {$row['email']}");
                }
            } catch (\Exception $e) {
                Log::error("Exception while creating apprenant for email {$row['email']}: " . $e->getMessage());
            }
        }
    }

    protected function createOrGetUser($rowData)
    {
        // Check if user already exists
        $existingUser = $this->apprenantsFirebaseService->findApprenantsByEmail($rowData['email']);

        if ($existingUser) {
            return $existingUser['id'];
        }

        // If user doesn't exist, create a new one
        $userData = [
            'nom' => $rowData['nom'],
            'prenom' => $rowData['prenom'],
            'email' => $rowData['email'],
            'photo' => $rowData['photo'] ?? null,
        ];

        // You might need to adjust this part based on how your service creates users
        $newUser = $this->apprenantsFirebaseService->createUserWithEmailAndPassword($rowData['email'], 'default_password');

        if (isset($newUser['error'])) {
            Log::error("Failed to create user for email {$rowData['email']}: " . $newUser['error']);
            return null;
        }

        return $newUser['id'] ?? null;
    }
}
