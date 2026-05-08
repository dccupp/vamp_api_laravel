<?php

namespace App\Http\Controllers;

use App\Models\RosteredPlayer;
use App\Models\LeagueMember;
use App\Models\Log;
use Illuminate\Http\Request;

class RosteredPlayersController extends Controller
{
    public function create(Request $request)
    {
        $request->validate([
            'league_member_id' => 'required|integer|exists:league_members,id',
            'player_id'        => 'required|string|exists:players,id',
            'roster_position'  => 'required|string',
            'is_rostered'      => 'sometimes|boolean',
        ]);

        $rosteredPlayer = RosteredPlayer::updateOrCreate(
            [
                'league_member_id' => $request->league_member_id,
                'player_id'        => $request->player_id,
            ],
            [
                'roster_position' => $request->roster_position,
                'is_rostered'     => $request->input('is_rostered', 1),
            ]
        );

        try {
            $player       = $rosteredPlayer->player;
            $leagueMember = LeagueMember::find($request->league_member_id);
            $teamName     = $leagueMember?->team_name ?? 'Unknown team';
            $playerName   = $player?->player_name ?? 'Unknown player';

            Log::create([
                'league_member_id' => $request->league_member_id,
                'type'             => 'add_player',
                'message'          => "{$teamName} added {$playerName} to their roster",
            ]);
        } catch (\Exception $e) {
            \Log::warning('Failed to write add_player log: ' . $e->getMessage());
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Rostered player added successfully',
            'id'      => $rosteredPlayer->id,
        ], 201);
    }

    public function getRosteredPlayerById($id)
    {
        $rosteredPlayer = RosteredPlayer::find($id);

        if (!$rosteredPlayer) {
            return response()->json(['status' => 'error', 'message' => 'Rostered player not found'], 404);
        }

        return response()->json($rosteredPlayer);
    }

    public function getRosteredPlayersByLeagueId($league_id)
    {
        $rosteredPlayers = RosteredPlayer::join('league_members', 'rostered_players.league_member_id', '=', 'league_members.id')
            ->where('league_members.league_id', $league_id)
            ->where('rostered_players.is_rostered', 1)
            ->select('rostered_players.id', 'rostered_players.league_member_id', 'rostered_players.player_id', 'rostered_players.roster_position', 'rostered_players.is_rostered', 'rostered_players.last_modified')
            ->get();

        return response()->json($rosteredPlayers);
    }

    public function getRosteredPlayersByLeagueMemberId($league_member_id)
    {
        $rosteredPlayers = RosteredPlayer::with('player')
            ->where('league_member_id', $league_member_id)
            ->where('is_rostered', 1)
            ->get()
            ->filter(fn($rp) => $rp->player && $rp->player->player_name && $rp->player->position && $rp->player->team)
            ->values()
            ->map(fn($rp) => [
                'id'                 => $rp->id,
                'league_member_id'   => $rp->league_member_id,
                'player_id'          => $rp->player_id,
                'roster_position'    => $rp->roster_position,
                'is_rostered'        => $rp->is_rostered,
                'last_modified'      => $rp->last_modified,
                'internal_player_id' => $rp->player->id,
                'player_name'        => $rp->player->player_name,
                'position'           => $rp->player->position,
                'team'               => $rp->player->team,
            ]);

        return response()->json($rosteredPlayers);
    }

    public function getRosteredPlayerByLeagueAndPlayerId($league_id, $player_id)
    {
        $rosteredPlayer = RosteredPlayer::join('league_members', 'rostered_players.league_member_id', '=', 'league_members.id')
            ->where('league_members.league_id', $league_id)
            ->where('rostered_players.player_id', $player_id)
            ->select('rostered_players.*')
            ->first();

        if (!$rosteredPlayer) {
            return response()->json((object)[]); // intentional: return empty object {} with 200
        }

        return response()->json($rosteredPlayer);
    }

    public function updateRosteredPlayer(Request $request, $id)
    {
        $request->validate([
            'league_member_id' => 'sometimes|integer|exists:league_members,id',
            'player_id'        => 'required|string|exists:players,id',
            'roster_position'  => 'required|string',
            'is_rostered'      => 'sometimes|boolean',
        ]);

        $rosteredPlayer = RosteredPlayer::find($id);

        if (!$rosteredPlayer) {
            return response()->json(['status' => 'error', 'message' => 'Rostered player not found'], 404);
        }

        $isBeingDropped = $request->input('is_rostered') == 0 && $rosteredPlayer->is_rostered == 1;
        $isBeingAdded   = $request->input('is_rostered') == 1 && $rosteredPlayer->is_rostered == 0;

        $rosteredPlayer->update([
            'league_member_id' => $request->input('league_member_id', $rosteredPlayer->league_member_id),
            'player_id'        => $request->player_id,
            'roster_position'  => $request->roster_position,
            'is_rostered'      => $request->input('is_rostered', 1),
        ]);

        if ($isBeingDropped) {
            try {
                $player       = $rosteredPlayer->player;
                $leagueMember = LeagueMember::find($rosteredPlayer->league_member_id);
                $teamName     = $leagueMember?->team_name ?? 'Unknown team';
                $playerName   = $player?->player_name ?? 'Unknown player';

                Log::create([
                    'league_member_id' => $rosteredPlayer->league_member_id,
                    'type'             => 'drop_player',
                    'message'          => "{$teamName} dropped {$playerName} from their roster and is now available on waivers",
                ]);
            } catch (\Exception $e) {
                \Log::warning('Failed to write drop_player log: ' . $e->getMessage());
            }
        }

        if ($isBeingAdded) {
            try {
                $player       = $rosteredPlayer->player;
                $leagueMember = LeagueMember::find($rosteredPlayer->league_member_id);
                $teamName     = $leagueMember?->team_name ?? 'Unknown team';
                $playerName   = $player?->player_name ?? 'Unknown player';

                Log::create([
                    'league_member_id' => $rosteredPlayer->league_member_id,
                    'type'             => 'add_player',
                    'message'          => "{$teamName} added {$playerName} to their roster from waivers",
                ]);
            } catch (\Exception $e) {
                \Log::warning('Failed to write add_player log: ' . $e->getMessage());
            }
        }

        return response()->json(['status' => 'success', 'message' => 'Rostered player updated successfully']);
    }

    public function deleteRosteredPlayer($id)
    {
        $rosteredPlayer = RosteredPlayer::find($id);

        if (!$rosteredPlayer) {
            return response()->json(['status' => 'error', 'message' => 'Rostered player not found'], 404);
        }

        $rosteredPlayer->delete();

        return response()->json(['status' => 'success', 'message' => 'Rostered player removed successfully']);
    }
}
