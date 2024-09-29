<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Facades\FirebaseFacade;
use Exception;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Storage;
use Kreait\Firebase\Auth;
class FirebaseModel
{
    protected $database;
    protected $auth;
    protected $storage;
    public function __construct()
    {

        $factory = (new Factory)
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

        $this->database = $factory->createDatabase();
        $this->auth = $factory->createAuth();
        $this->storage = $factory->createStorage();


    }

    public function getDatabase()
    {
        return $this->database;
    }

    // Méthode pour créer une nouvelle entrée dans Firebase
    public function create($path, $data)
    {
        try {
            $reference = $this->database->getReference($path);
            $key = $reference->push()->getKey();
            $reference->getChild($key)->set($data);
            return $key;
        } catch (\Exception $e) {
            Log::error('Erreur lors de la création dans Firebase : ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    public function setCustomUserClaims($uid, array $claims)
    {
        $this->auth->setCustomUserClaims($uid, $claims);
    }
    // Méthode pour rechercher une entrée spécifique dans Firebase
    public function find($path, $id)
    {
        try {
            $reference = $this->database->getReference($path);
            $allReferentiels = $reference->getValue();
            foreach ($allReferentiels as $key => $referentiel) {
                if (isset($referentiel['id']) && $referentiel['id'] == (int)$id) {
                    return $referentiel;
                }
            }
            // dd($referentiel);
            return response()->json(['error' => 'noeuds non trouvé'], 404);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la recherche dans Firebase : ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    public function findNoeudById(string $referentielId, string $path)
    {
        // Chemin vers le noeud des référentiels dans la Realtime Database
        $referentielsRef = $this->database->getReference($path);

        // Récupérer toutes les données de référentiels
        $referentiels = $referentielsRef->getValue();
        // dd($referentiels);
        // Parcourir les référentiels pour trouver celui correspondant à l'ID
        foreach ($referentiels as $key => $referentiel) {
            if ($key === $referentielId) {
                return $referentiel; // Retourner le référentiel correspondant
            }
        }

        // Si le référentiel n'est pas trouvé, retourner null ou une exception
        return null; // ou throw new NotFoundException("Référentiel non trouvé");
    }



    // Méthode pour mettre à jour une entrée spécifique dans Firebase
    public function update($path, $id, $data)
    {
        try {
            $reference = $this->database->getReference($path);
            $allReferentiels = $reference->getValue();

            // Vérifier si le référentiel existe
            $referentielFound = false;
            foreach ($allReferentiels as $key => $referentiel) {
                if (isset($referentiel['id']) && $referentiel['id'] == (int)$id) {
                    $referentielFound = true;
                    break; // Sortir de la boucle si trouvé
                }
            }

            if (!$referentielFound) {
                return response()->json(['error' => 'Référentiel non trouvé'], 404);
            }

            // Mise à jour du neoud en utilisant la bonne clé
            $reference->getChild($key)->update($data);
            return response()->json(['success' => 'Mise à jour réussie']);

        } catch (\Exception $e) {
            Log::error('Erreur lors de la mise à jour dans Firebase : ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // Méthode pour supprimer une entrée spécifique dans Firebase
    public function delete($path, $id)
    {
        try {
            $reference = $this->database->getReference($path);
            $allReferentiels = $reference->getValue();

            // Vérifier si le référentiel existe
            $referentielFound = false;
            $referentielKey = null; // Pour garder la clé du référentiel

            foreach ($allReferentiels as $key => $referentiel) {
                if ($referentiel['id'] == (int)$id) {
                    $referentielFound = true;
                    $referentielKey = $key; // Stocker la clé pour la mise à jour
                    break;
                }
            }

            if (!$referentielFound) {
                return response()->json(['error' => 'Référentiel non trouvé'], 404);
            }

            // Mettre à jour le champ 'actif' pour effectuer un soft delete
            $reference->getChild($referentielKey)->update(['actif' => false]); // Définir le champ actif à false

            return response()->json(['success' => 'Référentiel archivé avec succès']);

        } catch (\Exception $e) {
            Log::error('Erreur lors de la suppression dans Firebase : ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // Méthode pour tester la connexion Firebase (optionnelle)
    public function test()
    {
        Log::info('Testing Firebase connection');
        $reference = $this->database->getReference('test');
        $reference->set([
            'date' => now()->toDateTimeString(),
            'content' => 'Firebase connection test',
        ]);
        Log::info('Data pushed to Firebase');
    }

    // Exemple d'utilisation pour stocker des données via une requête
    public function store($request)
    {
        $reference = $this->database->getReference('test'); // Remplacez 'test' par votre chemin
        $newData = $reference->push($request);
        return response()->json($newData->getValue());
    }
    // Méthode pour obtenir tous les utilisateurs depuis Firebase
    public function getAll($path)
    {
        try {
            $reference = $this->database->getReference($path);
            $users = $reference->getValue();

            if ($users) {
                return $users;
            } else {
                return [];
            }
        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération des utilisateurs dans Firebase : ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function findUserByEmail(string $email)
    {
        $users = $this->getAll('users');
        foreach ($users as $user) {
            if ($user['email'] === $email) {
                return $user;
            }
        }
        return null;
    }
    public function findUserByPhone(string $telephone)
    {
        $users = $this->getAll('users');
        // dd($users, $telephone);
        foreach ($users as $user) {
            if (isset($userData['telephone'])) {
            if ($user['telephone'] === $telephone) {
                return $user;
            }
        }
        }
        return null;
    }
    public function createUserWithEmailAndPassword($email, $password)
    {
        Log::info('Création d\'un utilisateur Firebase avec email : ' . $email);
        try {
            $user = $this->auth->createUser([
                'email' => $email,
                'password' => $password,
            ]);
            return $user->uid;
            // Log::info('Utilisateur Firebase créé avec UID : ' . $user->uid);

        } catch (Exception $e) {
            Log::error('Firebase Auth Error: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur lors de la création de l\'utilisateur Firebase.'], 500);
        } catch (Exception $e) {
            Log::error('Firebase Exception: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur avec Firebase.'], 500);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la création de l\'utilisateur Firebase : ' . $e->getMessage());
            return response()->json(['error' => 'Erreur lors de la création de l\'utilisateur dans Firebase.'], 500);
        }
    }

    public function uploadImageToStorage($filePath, $fileName)
    {
        try {
            // Récupérer le bucket de Firebase Storage
            $bucket = $this->storage->getBucket();

            // Ouvrir le fichier et le télécharger
            $file = fopen($filePath, 'r');
            $bucket->upload($file, [
                'name' => $fileName // Nom du fichier dans le bucket
            ]);

            // Obtenez l'URL de téléchargement
            $object = $bucket->object($fileName);
            $url = $object->signedUrl(new \DateTime('tomorrow')); // URL temporaire d'un jour

            Log::info('Image téléchargée avec succès sur Firebase Storage : ' . $url);
            return $url;

        } catch (\Exception $e) {
            Log::error('Erreur lors du téléchargement de l\'image dans Firebase Storage : ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    public function getLastReferentiel()
    {
        // Récupérer tous les référentiels depuis Firebase
        $referentiels = $this->database->getReference('referentiels')->getValue();

        // Si aucun référentiel n'existe, renvoyer null
        if (!$referentiels) {
            return null; // Pas de référentiels existants
        }

        // Utiliser `collect` pour trier les référentiels par ID décroissant
        return collect($referentiels)->sortByDesc('id')->first();
    }
    public function deactivateOtherPromotions()
    {
        try {

            $promotions = $this->database->getReference('promotions')->getValue();

            if ($promotions) {
                foreach ($promotions as $key => $promotion) {
                    if (isset($promotion['etat']) && $promotion['etat'] === 'Actif') {
                        // Désactiver les autres promotions actives
                        $this->database->getReference("promotions/{$key}/etat")->set('Inactif');
                    }
                }
            }
        } catch (Exception $e) {
            throw new \Exception("Erreur lors de la désactivation des promotions: " . $e->getMessage());
        }
    }
    //get activePromotions
    public function getActivePromotion()
    {
        $promotions = $this->getAll('promotions');
        // dd($promotions);
        if ($promotions) {
            return collect($promotions)->where('etat', 'Actif')->all();
        }

        return [];
    }

public function findReferentielById( string $id) {
       return $this->database->getReference('referentiels')->getChild($id)->getValue();
       // Vérifiez que cette requête retourne bien les données attendues
   }

public function findApprenantById(string $id)
    {
        return $this->database->getReference('apprenants')->getChild($id)->getValue();
        // return Promotions::findNoeudById($referentielId,$this->firebaseNode);
    }

}
