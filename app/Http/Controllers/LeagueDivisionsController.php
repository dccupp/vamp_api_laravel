<?php

namespace App\Http\Controllers;

use App\Models\LeagueDivision;
use Illuminate\Http\Request;

class LeagueDivisionsController extends Controller
{
    public function create(Request $request)
    {
        $request->validate([
            'league_id' => 'required|integer|exists:leagues,league_id',
            'name'      => 'required|string',
        ]);

        $division = LeagueDivision::create([
            'league_id' => $request->league_id,
            'name'      => $request->name,
        ]);

        return response()->json([
            'status'      => 'success',
            'message'     => 'Division created successfully',
            'division_id' => $division->division_id,
        ], 201);
    }

    public function getDivisionsByLeagueId($league_id)
    {
        $divisions = LeagueDivision::where('league_id', $league_id)->get();

        if ($divisions->isEmpty()) {
            return response()->json(['status' => 'error', 'message' => 'No divisions found for this league'], 404);
        }

        return response()->json($divisions);
    }

    public function getDivisionById($id)
    {
        $division = LeagueDivision::find($id);

        if (!$division) {
            return response()->json(['status' => 'error', 'message' => 'Division not found'], 404);
        }

        return response()->json($division);
    }

    public function updateDivision(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string',
        ]);

        $division = LeagueDivision::find($id);

        if (!$division) {
            return response()->json(['status' => 'error', 'message' => 'Division not found'], 404);
        }

        $division->update(['name' => $request->name]);

        return response()->json(['status' => 'success', 'message' => 'Division updated successfully']);
    }

    public function deleteDivision($id)
    {
        $division = LeagueDivision::find($id);

        if (!$division) {
            return response()->json(['status' => 'error', 'message' => 'Division not found'], 404);
        }

        $division->delete();

        return response()->json(['status' => 'success', 'message' => 'Division deleted successfully']);
    }
}
