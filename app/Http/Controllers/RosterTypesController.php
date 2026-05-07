<?php

namespace App\Http\Controllers;

use App\Models\RosterType;

class RosterTypesController extends Controller
{
    public function getRosterTypes()
    {
        $rosterTypes = RosterType::all();

        if ($rosterTypes->isEmpty()) {
            return response()->json(['status' => 'error', 'message' => 'No roster types found'], 404);
        }

        return response()->json($rosterTypes);
    }

    public function getRosterTypeById($roster_type_id)
    {
        $rosterType = RosterType::find($roster_type_id);

        if (!$rosterType) {
            return response()->json(['status' => 'error', 'message' => 'Roster type not found'], 404);
        }

        return response()->json($rosterType);
    }
}
