<?php

namespace App\Http\Controllers;

use App\Models\Player;
use Illuminate\Http\Request;

class PlayersController extends Controller
{
    public function create(Request $request)
    {
        $request->validate([
            'id'                 => 'required|string|unique:players,id',
            'external_player_id' => 'required|string',
            'player_name'        => 'required|string',
            'position'           => 'sometimes|string',
            'team'               => 'sometimes|string',
        ]);

        $player = Player::create([
            'id'                 => $request->id,
            'external_player_id' => $request->external_player_id,
            'player_name'        => $request->player_name,
            'position'           => $request->input('position', ''),
            'team'               => $request->input('team', ''),
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Player created successfully',
            'id'      => $player->id,
        ], 201);
    }

    public function getPlayers()
    {
        $players = Player::all();

        if ($players->isEmpty()) {
            return response()->json(['status' => 'error', 'message' => 'No players found'], 404);
        }

        return response()->json($players);
    }

    public function getPlayerById($id)
    {
        $player = Player::find($id);

        if (!$player) {
            return response()->json(['status' => 'error', 'message' => 'Player not found'], 404);
        }

        return response()->json($player);
    }

    public function getPlayerByExternalPlayerId($external_player_id)
    {
        $player = Player::where('external_player_id', $external_player_id)->first();

        if (!$player) {
            return response()->json(['status' => 'error', 'message' => 'Player not found'], 404);
        }

        return response()->json($player);
    }

    public function updatePlayer(Request $request, $id)
    {
        $request->validate([
            'external_player_id' => 'required|string',
            'player_name'        => 'required|string',
            'position'           => 'sometimes|string',
            'team'               => 'sometimes|string',
        ]);

        $player = Player::find($id);

        if (!$player) {
            return response()->json(['status' => 'error', 'message' => 'Player not found'], 404);
        }

        $player->update($request->only(['external_player_id', 'player_name', 'position', 'team']));

        return response()->json(['status' => 'success', 'message' => 'Player updated successfully']);
    }

    public function deletePlayer($id)
    {
        $player = Player::find($id);

        if (!$player) {
            return response()->json(['status' => 'error', 'message' => 'Player not found'], 404);
        }

        $player->delete();

        return response()->json(['status' => 'success', 'message' => 'Player deleted successfully']);
    }
}
