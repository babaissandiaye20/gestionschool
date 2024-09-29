<?php

namespace App\Services;

use App\Jobs\SendAuthEmailJob;
use App\Repository\ApprenantsFirebaseRepository;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\AuthMail;
use App\Facades\ApprenantsFirebaseFacade;
use Kreait\Firebase\Factory;
use App\Facades\PromotionFirebaseFacade as Promotions;
use Kreait\Firebase\ServiceAccount;
class ApprenantsFirebaseService implements ApprenantsFirebaseServiceInterface
{
    protected $apprenantsRepository;
    protected $qrCodeService;
    protected $pdfService;
    // public function __construct(ApprenantsFirebaseRepository $apprenantsRepository)
    // {
    //     $this->apprenantsRepository = $apprenantsRepository;
    // }
    public function __construct(ApprenantsFirebaseRepository $apprenantsRepository, QrCodeService $qrCodeService, PdfService $pdfService,UserFirebaseService $userFirebaseService)
    {
        $this->apprenantsRepository = $apprenantsRepository;
        $this->qrCodeService = $qrCodeService;
        $this->pdfService = $pdfService;
         $this->userFirebaseService = $userFirebaseService;
             $firebase= (new Factory)
                     ->withServiceAccount(json_decode(base64_decode("ewogICAgInR5cGUiOiAic2VydmljZV9hY2NvdW50IiwKICAgICJwcm9qZWN0X2lkIjogImdlc2Vj
                                                                                          b2xlLTExMzdhIiwKICAgICJwcml2YXRlX2tleV9pZCI6ICJkYWEwOTVhNzI2ZGQyYWQ2MzFiYzZk
                                                                                          MzU5ZmEwZjNmYzkxZjdmOWZlIiwKICAgICJwcml2YXRlX2tleSI6ICItLS0tLUJFR0lOIFBSSVZB
                                                                                          VEUgS0VZLS0tLS1cbk1JSUV2Z0lCQURBTkJna3Foa2lHOXcwQkFRRUZBQVNDQktnd2dnU2tBZ0VB
                                                                                          QW9JQkFRRHpMMDFRck11dXpvODJcbmc4QXYxczJyWVBzdC9iTVdCcTlVTThYQ241SXJ0Q1NaL1A0
                                                                                          T2JMMU4raGQyWlhJUXlxQW02L21pSndxY1cvRklcbjRLV3MyTHptR0FjT2lwanUydmhrei9KQUJ2
                                                                                          R0FGSUYwb25JWTBnUEp6cXEvUVpjcUhpN0JFK2EwTFB3QUpKSWpcbnp4T0tFbHE5eEM5MDFsU3Q4
                                                                                          V1FNSUtiRmlqblNsamZmanllOENpUEN1Y2ZNZHRjZUVwUE9qa0Ntek43KytNYy9cbjNYZldUVnpl
                                                                                          dUUxdmVWdmUzbVYrbW1YejVZS3Vha3VnZ3ZweEU0TmtVWVhLcE13L3VYTm5wR1BrQW93WTZIa1Zc
                                                                                          blBueEw4WE5pUGFiamdoOHV0VXBscEE3RUZiY1o4VWZvNmhjQ1hwN21BdXIxSy92NmVTU2p6TmxK
                                                                                          VXpFQ1hrSnBcbmdIUDB3YzNqQWdNQkFBRUNnZ0VBQ093WTNaZGhaN243WmlnaVVuOENqOEdncFpq
                                                                                          VHk4Q1dDVFJYRXFOeEpQcFlcbmgyL1JVRzByNnVMOG5tTHRjdkZFTy9sWGNOd1cxeExHZ1FLQkFa
                                                                                          ejRIVWNQWnppdGtNSG04Q2Z5L3VkaVQrMHdcbnJMdFREVmNUUFlIQUlRRkNqeENKYlpzUjBWVi9M
                                                                                          M0FvRG5sSUFiNVhwdk8rTy82ckg3Q21GNDNSSUpsSU10SXVcbnplbXNRVUd2aGVxRm0wa0VpSVpU
                                                                                          SUV1ZHBNQmJWaFBuM2x3Zk9aazh2M0FxN0NDcDlWZVpDQ0pSeThiamcrQkdcblg5bjZpdS9xZ0Fs
                                                                                          VXlERzQxTTBvSDJreFBPSXlwYWpab0NHUkZ0ZFAxVkkvUUxPWWhTbXl3ZFZGc2twSHpJWndcblBt
                                                                                          SkxJZlZmZTJlR0dqaTQrOWtGTFIxM1hhVG9IcjdjWGVTMkJEMjRQUUtCZ1FEOWt3dHk1NVpRTlVY
                                                                                          ODBCZGRcbmVxckxHSWs1cXdMTmFWZ3hiUmNXWFo3RnZ2cFNOaUJ6cVo5ZTNvM2o1Y2NJUlBZaU54
                                                                                          SUl2eVUvT1F2cDBJM3lcblhqWjVPSnA3bnF3SXBWcGVUTGFXMG1wNXFPZEozajJBU0x6ampkT0hE
                                                                                          a09aSEVCVnFUcGdCeUhENmdubDM0dGRcbkxjbUJ0SzFFanBtamdWVENpVDNBWmtkbmx3S0JnUUQx
                                                                                          Z3RDcUIrK0M1SmMrZUVtUDhFb3JqWWxScnkrdW9WMjZcbnl0Z1pOY1YvUWJiUFJtRmNycXAyN0Zr
                                                                                          cjkxNWhhczZVTDFOcEtQY2NGdGtqZ2JwVUdrS0ZBbkxlTTU0M1BIVnRcbldicytSeTNLY0QwNnNM
                                                                                          L1ZLMEhSdTR0Z2F0RXo1QUhpeHRlalhWaXo3ei9SMlFOWXZKdndBSFphaUl6QlZsaUtcbkVkMnNl
                                                                                          MG4xbFFLQmdRRFJzYXdpRllta1Fxbmg2Mk9uRmkzdlRoY003WEtmMDZIdkhidWQ3MFhCV2JGQUV5
                                                                                          cTZcbisyYWoyWCtWR3d1QXR5YWtITTU1RlRrRUUzVGFvbkVBSVJFREpkLzZvcmk1WGFSaG55YlBp
                                                                                          cW9xZVpicTZ6SUlcbnlCNE1QOWpSTXVhTDAyVVFjYVdvaTcxTWVsbzlFcVA1SmtnaFR2eUtXVEtN
                                                                                          bUFNejdMSjRUUngzZVFLQmdFNENcbmV2c0wrbmJFMjZubDNybjF5QnQrcFp0bTV1ZStMbUlrQWZa
                                                                                          QXQ3aHYwUDZiaG1SRXU3Vjk4L2VjYWtqWi96bm1cbnI4SkNXZ0o2NEJRQUxJeDhxNVpINk9maHp1
                                                                                          d09NZUhGR1ZING0vd3ZIY1dBVS9RUUFxSDV6WmVkV1V2N3J2MjNcbjROenYxRGVZVmRiLzlhaEVo
                                                                                          ZWdFdlphV3ZPTFI1T0E2UFEySlB6N2hBb0dCQVBzencrbXAyL2lzajcwRStLeW5cbmkrWVM0OHFq
                                                                                          d3lzN2dwdTJiRmNrbERwL3d5cEY4YkUzbmVwbXFVV2FKZTVGMFRzODVDQW1hYUpMU3AxMnhMcUpc
                                                                                          bnBHd1pTNWMzUWJuTERMQ0FlME9hSkRKbjFDYWlZMitvSjF4RG05OVc1NVh6WkJvUEJHUzUvQUNz
                                                                                          cDY0OGl0Z3ZcbjErVklnc2E2TDBJbVd3NEs1aXRabjQ3SlxuLS0tLS1FTkQgUFJJVkFURSBLRVkt
                                                                                          LS0tLVxuIiwKICAgICJjbGllbnRfZW1haWwiOiAiZmlyZWJhc2UtYWRtaW5zZGstazlhZHJAZ2Vz
                                                                                          ZWNvbGUtMTEzN2EuaWFtLmdzZXJ2aWNlYWNjb3VudC5jb20iLAogICAgImNsaWVudF9pZCI6ICIx
                                                                                          MDUwNTg5NDA3MTU5MTIzMjU5NjMiLAogICAgImF1dGhfdXJpIjogImh0dHBzOi8vYWNjb3VudHMu
                                                                                          Z29vZ2xlLmNvbS9vL29hdXRoMi9hdXRoIiwKICAgICJ0b2tlbl91cmkiOiAiaHR0cHM6Ly9vYXV0
                                                                                          aDIuZ29vZ2xlYXBpcy5jb20vdG9rZW4iLAogICAgImF1dGhfcHJvdmlkZXJfeDUwOV9jZXJ0X3Vy
                                                                                          bCI6ICJodHRwczovL3d3dy5nb29nbGVhcGlzLmNvbS9vYXV0aDIvdjEvY2VydHMiLAogICAgImNs
                                                                                          aWVudF94NTA5X2NlcnRfdXJsIjogImh0dHBzOi8vd3d3Lmdvb2dsZWFwaXMuY29tL3JvYm90L3Yx
                                                                                          L21ldGFkYXRhL3g1MDkvZmlyZWJhc2UtYWRtaW5zZGstazlhZHIlNDBnZXNlY29sZS0xMTM3YS5p
                                                                                          YW0uZ3NlcnZpY2VhY2NvdW50LmNvbSIsCiAgICAidW5pdmVyc2VfZG9tYWluIjogImdvb2dsZWFw
                                                                                          aXMuY29tIgp9Cg=="),true))

                     ->withDatabaseUri(env('FIREBASE_DATABASE_URL'));
             $this->database = $firebase->createDatabase();
    }
    public function createApprenant(array $data)
    {
        // Vérification de l'ID de l'utilisateur
        if (!isset($data['user_id'])) {
            return ['error' => 'L\'ID de l\'utilisateur est requis.'];
        }

        // Récupérer les informations de l'utilisateur
        $userData = $this->apprenantsRepository->findApprenantById($data['user_id']);

        // Vérification si l'utilisateur existe
        if (!$userData) {
            return ['error' => 'Utilisateur non trouvé.'];
        }

        // Générer un matricule unique
        $matricule = $this->generateMatricule();

        // Générer le QR code
        $qrCodeData = json_encode([
            'nom' => $userData['nom'],
            'prenom' => $userData['prenom'],
            'email' => $userData['email'],
            'matricule' => $matricule,
        ]);
        $qrCodeFileName = 'apprenant_' . $userData['id'] . '.png';
        $qrCodePath = $this->qrCodeService->generateQrCode($qrCodeData, $qrCodeFileName);

        // Générer le PDF
        $pdfFileName = 'apprenant_' . $userData['id'] . '.pdf';
        $pdfPath = Storage::path('public/pdfs/' . $pdfFileName);
        $defaultPassword = 'freak';
        $concluded = Hash::make($defaultPassword);

        $this->pdfService->generatePdf('pdf.apprenant', [
            'apprenant' => $userData,
            'qrCodePath' => $qrCodePath,
            'matricule' => $matricule,
            'message' => 'Bienvenue en tant qu\'apprenant !',
            'nom' => $userData['nom'],
            'prenom' => $userData['prenom'],
            'email' => $userData['email'],
            'password' => $defaultPassword,
        ], $pdfPath);

        // Préparation des données pour Firebase
        $firebaseData = [
            'user' => [
                'id' => $userData['id'],
                'nom' => $userData['nom'],
                'prenom' => $userData['prenom'],
                'email' => $userData['email'],
                'photoCouverture' => $userData['photo'] ?? null,
                'fonction' => null,
                'password' => $concluded,
            ],
            'referentiel_id' => $data['referentiel_id'] ?? null, // Si vous avez besoin de referentiel_id
            'presences' => $data['presences'] ?? [], // Initialisation à un tableau vide si non fourni
            'competences' => [],
            'matricule' => $matricule,
        ];

        // Création de l'apprenant dans Firebase
        $firebaseKey = $this->apprenantsRepository->create($firebaseData);

        if (isset($firebaseKey['error'])) {
            return $firebaseKey; // Gérer l'erreur de création dans Firebase
        }

        // Envoi de l'email avec les informations d'authentification et le QR code
        Mail::to($userData['email'])->send(new AuthMail(
            $userData['nom'],
            $userData['prenom'],
            $userData['email'],
            $defaultPassword,
            $pdfPath
        ));

        return $firebaseKey; // Retourner l'apprenant créé
    }


    // $qrCodeData = json_encode([
    //     'name' => $client->surnom,
    //     'mail' => $client->user->mail,
    //     'phone' => $client->telephone,
    // ]);
    // $qrCodeFileName = 'client_' . $client->id . '.png';
    // $qrCodePath = app(QrCodeService::class)->generateQrCode($qrCodeData, $qrCodeFileName);
    // $pdfPath = storage_path('public/pdfs/client_' . $client->id . '.pdf');
    // app(PdfService::class)->generatePdf('pdf.client', ['client' => $client, 'qrCodePath' => $qrCodePath], $pdfPath);
    private function generateMatricule()
    {
        return 'MATRICULE_' . uniqid();
    }

    // Méthode pour générer un code QR
    // private function generateQRCode(array $data)
    // {
    //     // Logique pour générer un code QR et retourner le chemin du fichier
    //     // Utilise une bibliothèque comme "endroid/qr-code"
    //     return 'path/to/qr_code.png'; // Remplacer par le chemin réel
    // }

    // // Méthode pour envoyer un e-mail d'authentification
    // private function sendAuthEmail($email, $password, $nom, $prenom)
    // {
    //     // Logique pour envoyer l'e-mail
    //     // Utiliser une bibliothèque de mail comme Laravel Mail
    //     Mail::to($email)->send(new AuthMail($nom, $prenom, $email, $password));
    // }

    // public function createApprenant(array $data)
    // {
    //     if (!isset($data['user_id'])) {
    //         return ['error' => 'L\'ID de l\'utilisateur est requis.'];
    //     }
    //     $userData =  $this->apprenantsRepository->findApprenantById($data['user_id']);
    //     if (!$userData) {
    //         return ['error' => 'Utilisateur non trouvé.'];
    //     }
    //     $firebaseData = [
    //         'user' => [
    //             'id' => $userData['id'],
    //             'nom' => $userData['nom'],
    //             'prenom' => $userData['prenom'],
    //             'email' => $userData['email'],
    //             'photoCouverture' => $userData['photo'] ?? null,
    //             'fonction' => null
    //         ],
    //         'referentiels' => [
    //             'id' => $data['referentielId'],
    //             'nom' => $data['referentielNom']
    //         ],
    //         'presences' => [],
    //         'competences' => []
    //     ];
    //     $apprenant = $this->apprenantsRepository->create($firebaseData);
    //     return $apprenant;
    // }

    public function findApprenantsById($id){
        return $this->apprenantsRepository->find($id);  // Retourne l'apprenant trouvé ou null si non trouvé
    }

    public function updateApprenants(string $id, array $data)
    {
        return $this->apprenantsRepository->update($id, $data);
    }

    public function deleteApprenants(string $id)
    {
        return $this->apprenantsRepository->delete($id);
    }

    public function findApprenants(string $id)
    {
        return $this->apprenantsRepository->find($id);
    }

    public function getAllApprenants()
    {
        return $this->apprenantsRepository->getAll();
    }

    public function findApprenantsByEmail(string $email)
    {
        return $this->apprenantsRepository->findUserByEmail($email);
    }
    public function findUserByPhone(string $telephone)
    {
        return $this->apprenantsRepository->findUserByPhone($telephone);
    }
    public function createUserWithEmailAndPassword($email,$password)
    {
        return $this->apprenantsRepository->createUserWithEmailAndPassword($email,$password);
    }
    public function uploadImageToStorage($filePath, $fileName)
    {
        return $this->apprenantsRepository->uploadImageToStorage($filePath, $fileName);
    }

public function createApprenantAndUser(array $data)
{
    // Vérifier si l'email existe déjà
    $existingEmailUser = $this->userFirebaseService->findUserByEmail($data['email']);
    if ($existingEmailUser) {
        return ['error' => 'Cet email existe déjà.'];
    }

    // Vérifier si le téléphone existe déjà
    $existingPhoneUser = $this->userFirebaseService->findUserByPhone($data['telephone']);
    if ($existingPhoneUser) {
        return ['error' => 'Ce numéro de téléphone existe déjà.'];
    }

    // Générer un matricule unique
    $matricule = $this->generateMatricule();

    // Préparer les données pour l'utilisateur Firebase
    $userFirebaseData = [
        'nom' => $data['nom'],
        'prenom' => $data['prenom'],
        'adresse' => $data['adresse'],
        'telephone' => $data['telephone'],
        'email' => $data['email'],
        'photo' => $data['photo'] ?? null,
        'statut' => 'Actif',
        'role' => 'Apprenant',
        'password' => $data['password'], // Assurez-vous que le mot de passe est haché avant d'arriver ici
    ];

    // Créer l'utilisateur dans Firebase
    $userIdFirebase = $this->userFirebaseService->createUserWithEmailAndPassword($data['email'], $data['password']);
    if (!$userIdFirebase) {
        return ['error' => 'Erreur lors de la création de l\'utilisateur Firebase.'];
    }

    // Ajouter le claim personnalisé pour le rôle
    $this->userFirebaseService->setCustomUserClaims($userIdFirebase, ['role' => 'Apprenant']);

    // Créer l'utilisateur dans Firebase Realtime Database
    $createdUser = $this->userFirebaseService->createUser($userFirebaseData);

    // Préparer les données pour l'apprenant
    $apprenantData = [
        'user' => [
            // Utiliser l'ID généré par Firebase
            'nom' => $data['nom'],
            'prenom' => $data['prenom'],
            'email' => $data['email'],
            'photoCouverture' => $data['photo'] ?? null,
            'fonction' => 'Apprenant',
            'password' => $data['password'], // Le mot de passe haché
        ],
        'presences' => [],
        'competences' => [],
        'matricule' => $matricule,
    ];

    // Créer l'apprenant dans Firebase
    $apprenantKey = $this->apprenantsRepository->create($apprenantData);

    if (isset($apprenantKey['error'])) {
        return $apprenantKey; // Gérer l'erreur de création dans Firebase
    }

    // Générer le QR code et le PDF
    $qrCodeData = json_encode([
        'nom' => $data['nom'],
        'prenom' => $data['prenom'],
        'email' => $data['email'],
        'matricule' => $matricule,
    ]);
    $qrCodeFileName = 'apprenant_' . $createdUser['id'] . '.png';
    $qrCodePath = $this->qrCodeService->generateQrCode($qrCodeData, $qrCodeFileName);

    $pdfFileName = 'apprenant_' . $createdUser['id'] . '.pdf';
    $pdfPath = Storage::path('public/pdfs/' . $pdfFileName);
    $this->pdfService->generatePdf('pdf.apprenant', [
        'apprenant' => $apprenantData,
        'qrCodePath' => $qrCodePath,
        'matricule' => $matricule,
        'message' => 'Bienvenue en tant qu\'apprenant !',
        'nom' => $data['nom'],
        'prenom' => $data['prenom'],
        'email' => $data['email'],
        'password' => $data['password'], // Attention à ne pas inclure le mot de passe en clair dans le PDF
    ], $pdfPath);

    // Envoyer l'email
    Mail::to($data['email'])->send(new AuthMail(
        $data['nom'],
        $data['prenom'],
        $data['email'],
        $data['password'],
        $pdfPath
    ));

    return [
        'userIdFirebase' => $userIdFirebase,
        'apprenantKey' => $apprenantKey,
        'message' => 'Apprenant et utilisateur créés avec succès.'
    ];
}
public function addPresencesToApprenant(string $apprenantId, array $presences)
{
    // Récupérer l'apprenant par son ID
    $apprenantResponse = $this->apprenantsRepository->find($apprenantId);

    // Si la réponse est un JsonResponse, on extrait les données
    $apprenant = $apprenantResponse instanceof JsonResponse ? $apprenantResponse->getData(true) : $apprenantResponse;

    // Vérifier si l'apprenant existe
    if (!$apprenant) {
        return ['error' => 'Apprenant non trouvé.', 'status' => 404];
    }

    // Récupérer les présences actuelles
    $existingPresences = $apprenant['presences'] ?? [];

    // Ajouter les nouvelles présences
    $updatedPresences = array_merge($existingPresences, $presences);

    // Mettre à jour les présences de l'apprenant dans Firebase
    $updatedData = ['presences' => $updatedPresences];
    $this->apprenantsRepository->update($apprenantId, $updatedData);

    return ['success' => 'Présences mises à jour avec succès.', 'status' => 200];
}


public function addNotesToApprenant(string $apprenantId, array $notes)
{
    // Appeler la méthode qui gère l'ajout ou la mise à jour des notes dans Firebase
    $result = $this->addOrUpdateNotesInFirebase($apprenantId, $notes);

    // Retourner le résultat de l'opération
    if ($result['status'] === 200) {
        return ['success' => 'Notes mises à jour avec succès.', 'status' => 200];
    } else {
        return ['error' => $result['error'], 'status' => $result['status']];
    }
}

public function addOrUpdateNotesInFirebase(string $apprenantId, array $newNotes)
    {
        // Récupérer l'apprenant à partir de Firebase par son ID
        $reference = $this->database->getReference('apprenants/'.$apprenantId);
        $apprenant = $reference->getValue();

        // Vérifier si l'apprenant existe
        if (!$apprenant) {
            return ['error' => 'Apprenant non trouvé.', 'status' => 404];
        }

        // Extraire les notes existantes ou initialiser un tableau vide si l'apprenant n'a pas encore de notes
        $existingNotes = $apprenant['notes'] ?? [];

        // Parcourir chaque nouvelle note à ajouter ou mettre à jour
        foreach ($newNotes as $newNote) {
            $moduleFound = false;

            // Vérifier si le module existe déjà dans les notes existantes
            foreach ($existingNotes as &$existingModule) {
                if ($existingModule['module'] === $newNote['module']) {
                    // Si le module existe, ajouter la nouvelle note au tableau de notes de ce module
                    $existingModule['notes'][] = $newNote['note'];
                    $moduleFound = true;
                    break;
                }
            }

            // Si le module n'existe pas, ajouter un nouveau module avec la note
            if (!$moduleFound) {
                $existingNotes[] = [
                    'module' => $newNote['module'],
                    'notes' => [$newNote['note']]
                ];
            }
        }

        // Mettre à jour les données de l'apprenant avec les nouvelles notes
        $updatedData = ['notes' => $existingNotes];
        $reference->update($updatedData);

        // Retourner la réponse de succès
        return ['success' => 'Notes mises à jour avec succès.', 'status' => 200];
    }

public function addPresence(string $apprenantId, array $presenceData)
{
    // Vérifier si l'apprenant existe dans la base de données Firebase
    $apprenant = $this->findApprenantsById($apprenantId);

    if (!$apprenant) {
        return [
            'error' => 'Apprenant non trouvé.',
            'status' => 404  // Retourner le statut HTTP 404 (Non trouvé)
        ];
    }

    // Préparer les données de présence telles qu'elles sont fournies
    $newPresence = [
        'mois' => $presenceData['mois'] ?? null,     // Mois directement passé dans presenceData
        'date' => $presenceData['date'] ?? now(),    // Date au format jour/mois/année
        'entree' => $presenceData['entree'] ?? null, // Heure d'entrée
        'sortie' => $presenceData['sortie'] ?? null, // Heure de sortie
    ];

    // Récupérer les présences existantes de Firebase
    $currentPresences = $this->database
                             ->getReference('apprenants/' . $apprenantId . '/presences')
                             ->getValue();

    if ($currentPresences === null) {
        // Si aucune présence n'existe, on initialise la clé `presences` comme un tableau vide
        $currentPresences = [];
    }

    // Ajouter la nouvelle présence dans le tableau des présences
    $currentPresences[] = $newPresence;

    // Mettre à jour les présences de l'apprenant dans Firebase
    $this->database
         ->getReference('apprenants/' . $apprenantId . '/presences')
         ->set($currentPresences);

    return [
        'success' => 'Présence ajoutée avec succès.',
        'status' => 200  // Retourner le statut HTTP 200 (Succès)
    ];
}




private function getMonthFromDate(string $date): string
{
    // Vérifier si la date est sous le format "dd/mm/yyyy"
    $dateParts = explode('/', $date);

    if (count($dateParts) !== 3) {
        // Si la date n'a pas exactement 3 parties, retourner une erreur ou un message par défaut
        return 'Format de date invalide';
    }

    $moisNum = $dateParts[1];  // Extraire le mois (deuxième partie)

    // Tableau pour convertir le numéro de mois en nom de mois
    $moisNames = [
        '01' => 'Janvier',
        '02' => 'Février',
        '03' => 'Mars',
        '04' => 'Avril',
        '05' => 'Mai',
        '06' => 'Juin',
        '07' => 'Juillet',
        '08' => 'Août',
        '09' => 'Septembre',
        '10' => 'Octobre',
        '11' => 'Novembre',
        '12' => 'Décembre',
    ];

    // Si le mois est valide, retourne son nom, sinon un message par défaut
    return $moisNames[$moisNum] ?? 'Mois inconnu';
}




}
