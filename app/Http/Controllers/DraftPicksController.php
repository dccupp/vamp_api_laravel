<?php

namespace App\Http\Controllers;

use App\Models\DraftPick;
use Illuminate\Http\Request;

class DraftPicksController extends Controller
{
    public function create(Request $request)
    {
        $request->validate([
            'temp_player_id' => 'required|string',
            'player_name'    => 'required|string',
            'position'       => 'sometimes|string',
            'team'           => 'sometimes|string',
            'draft_year'     => 'sometimes|integer',
            'draft_round'    => 'sometimes|integer',
            'draft_pick'     => 'sometimes|integer',
            'gsis_id'        => 'sometimes|string',
            'pfr_player_id'  => 'sometimes|string',
        ]);

        $draftPick = DraftPick::create($request->only([
            'temp_player_id',
            'player_name',
            'position',
            'team',
            'draft_year',
            'draft_round',
            'draft_pick',
            'gsis_id',
            'pfr_player_id',
        ]));

        return response()->json([
            'status'  => 'success',
            'message' => 'Draft pick created successfully',
            'id'      => $draftPick->id,
        ], 201);
    }

    public function getDraftPicks()
    {
        $draftPicks = DraftPick::all();

        return response()->json($draftPicks);
    }

    public function getDraftPickById($id)
    {
        $draftPick = DraftPick::find($id);

        if (!$draftPick) {
            return response()->json(['status' => 'error', 'message' => 'Draft pick not found'], 404);
        }

        return response()->json($draftPick);
    }

    public function updateDraftPick(Request $request, $id)
    {
        $request->validate([
            'temp_player_id' => 'required|string',
            'player_name'    => 'required|string',
            'position'       => 'sometimes|string',
            'team'           => 'sometimes|string',
            'draft_year'     => 'sometimes|integer',
            'draft_round'    => 'sometimes|integer',
            'draft_pick'     => 'sometimes|integer',
            'gsis_id'        => 'sometimes|string',
            'pfr_player_id'  => 'sometimes|string',
        ]);

        $draftPick = DraftPick::find($id);

        if (!$draftPick) {
            return response()->json(['status' => 'error', 'message' => 'Draft pick not found'], 404);
        }

        $draftPick->update($request->only([
            'temp_player_id',
            'player_name',
            'position',
            'team',
            'draft_year',
            'draft_round',
            'draft_pick',
            'gsis_id',
            'pfr_player_id',
        ]));

        return response()->json(['status' => 'success', 'message' => 'Draft pick updated successfully']);
    }

    public function deleteDraftPick($id)
    {
        $draftPick = DraftPick::find($id);

        if (!$draftPick) {
            return response()->json(['status' => 'error', 'message' => 'Draft pick not found'], 404);
        }

        $draftPick->delete();

        return response()->json(['status' => 'success', 'message' => 'Draft pick deleted successfully']);
    }
}
