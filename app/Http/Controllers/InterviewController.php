<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Interview;

class InterviewController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nbrPersonne' => 'required|integer',
            'depense' => 'required|numeric',
            'budget' => 'required|numeric',
        ]);

        $interview = Interview::create($validated);

        return response()->json([
            'message' => 'Interview enregistrée avec succès',
            'data' => $interview
        ], 201);
    }
}

