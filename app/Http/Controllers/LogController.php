<?php

namespace App\Http\Controllers;

use App\Models\Log;
use Illuminate\Http\Request;

class LogController extends Controller
{
  public function create (Request $request)
  {
    $request->validate([
      'league_member_id' => 'required|integer|exists:league_members,id',
      'type'             => 'required|in:add_player,drop_player,vampire_transaction',
      'message'          => 'required|string',
    ]);

    $log = Log::create([
      'league_member_id' => $request->league_member_id,
      'type'             => $request->type,
      'message'          => $request->message,
    ]);

    return response()->json([
      'status'  => 'success',
      'message' => 'Logging record created successfully',
      'id'      => $log->id,
    ], 201);
  }

  public function getLogById($id)
  {
    $log = Log::find($id);

    if (!$log) {
      return response()->json(['status' => 'error', 'message' => 'Log entry could not be found'], 404);
    }

    return response()->json($log);
  }

  public function getLogsByLeagueId($league_id)
  {
    $logs = Log::join('league_members', 'logging.league_member_id', '=', 'league_members.id')
      ->where('league_members.league_id', $league_id)
      ->select('logging.id', 'logging.league_member_id', 'logging.type', 'logging.message', 'logging.created_at', 'league_members.team_name')
      ->orderBy('logging.created_at', 'desc')
      ->get();

    return response()->json($logs);
  }

  public function getLogsByLeagueMemberId($league_member_id)
  {
    $logs = Log::where('league_member_id', $league_member_id)->get();

    if ($logs->isEmpty()) {
      return response()->json(['status' => 'error', 'message' => 'No logs by league member found'], 404);
    }

    return response()->json($logs);
  }

  public function deleteLog($id)
  {
    $log = Log::find($id);

    if (!$log) {
      return response()->json(['status' => 'error', 'message' => 'Log entry could not be found'], 404);
    }

    $log->delete();

    return response()->json(['status' => 'success', 'message' => 'Log entry has been deleted successfully']);
  }
}