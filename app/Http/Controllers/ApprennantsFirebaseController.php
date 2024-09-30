<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Services\ApprenantsFirebaseService;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Exports\UserFirebaseExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ApprenantsImport;

class ApprennantsFirebaseController extends Controller
{
    protected $apprenantsFirebaseService;

    public function __construct(ApprenantsFirebaseService $apprenantsFirebaseService)
    {
        $this->apprenantsFirebaseService = $apprenantsFirebaseService;

    }

    public function store(Request $request)
    {

        $firebaseKey = $this->apprenantsFirebaseService->createApprenant($request->all());
        return response()->json(['message' => 'Apprenant créé avec succès', 'id' => $firebaseKey]);
    }

    public function index()
    {
        $apprenants = $this->apprenantsFirebaseService->getAllApprenants();
        return response()->json(['apprenants' => $apprenants]);
    }

    public function importApprenants(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,csv',
        ]);

        $file = $request->file('file');

        Excel::import(new ApprenantsImport($this->apprenantsFirebaseService), $file);

        return response()->json(['message' => 'Apprenants imported successfully'], 200);
    }
public function storebis(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string',
            'prenom' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'telephone' => 'required|string|unique:users,telephone',
            'adresse' => 'required|string',
            'password' => 'required|string|min:6',
            'referentiel_id' => 'required|string',
            'photo' => 'nullable|image|max:2048', // 2MB Max
        ]);

        // Hasher le mot de passe
        $validated['password'] = Hash::make($validated['password']);

        // Gérer l'upload de la photo si elle est présente
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('photos', 'public');
            $validated['photo'] = $photoPath;
        }

        $result = $this->apprenantsFirebaseService->createApprenantAndUser($validated);

        if (isset($result['error'])) {
            return response()->json(['error' => $result['error']], 422);
        }

        return response()->json([
            'message' => 'Apprenant et utailisateur créés avec succès',
            'userIdFirebase' => $result['userIdFirebase'],
            'apprenantKey' => $result['apprenantKey']
        ], 201);
    }
public function addPresences(Request $request, $apprenantId)
{
    // Validation de la requête
    $request->validate([
        'presences' => 'required|array', // Validation que 'presences' est bien un tableau
        'presences.*.mois' => 'required|string', // Validation pour chaque présence
        'presences.*.date' => 'required|string',
        'presences.*.entree' => 'required|string',
        'presences.*.sortie' => 'required|string',
    ]);

    $presences = $request->input('presences');

    $results = [];
    foreach ($presences as $presenceData) {
        // Appel à la méthode addPresence pour chaque entrée dans le tableau de présences
        $result = $this->apprenantsFirebaseService->addPresence($apprenantId, $presenceData);

        if (isset($result['error'])) {
            return response()->json(['error' => $result['error']], $result['status']);
        }

        $results[] = $result;
    }

    return response()->json(['message' => 'Présences ajoutées avec succès.', 'results' => $results], 200);
}

public function addNotes(Request $request, $apprenantId)
    {
        $request->validate([
            'notes' => 'required|array',
            'notes.*.module' => 'required|string',
            'notes.*.note' => 'required|numeric|min:0|max:20',
        ]);

        $notes = $request->input('notes');


        // Appel au service pour ajouter les notes à l'apprenant
        $result = $this->apprenantsFirebaseService->addNotesToApprenant($apprenantId,$notes);
//dd($result);
        if (isset($result['error'])) {
            return response()->json(['error' => $result['error']], $result['status']);
        }

        return response()->json(['message' => $result['success']], $result['status']);
    }
}
