<?php

namespace App\Http\Controllers;

use App\Models\FantasyWeek;
use App\Models\LeagueMember;
use App\Models\Player;
use App\Models\RosteredPlayer;
use App\Models\ScoringRule;
use App\Models\WaiverRule;

class WaiverPageController extends Controller
{
    public function getWaiverPageData($league_id, $user_id)
    {
        $season = request('season', (int) date('Y'));

        $leagueMember = LeagueMember::where('league_id', $league_id)
            ->where('user_id', $user_id)
            ->first();

        if (!$leagueMember) {
            return response()->json(['status' => 'error', 'message' => 'User is not a member of this league'], 404);
        }

        $userRoster = RosteredPlayer::with('player')
            ->where('league_member_id', $leagueMember->id)
            ->where('is_rostered', 1)
            ->get()
            ->filter(fn($rp) => $rp->player)
            ->values()
            ->map(fn($rp) => [
                'id'               => $rp->id,
                'league_member_id' => $rp->league_member_id,
                'player_id'        => $rp->player_id,
                'roster_position'  => $rp->roster_position,
                'is_rostered'      => $rp->is_rostered,
                'player_name'      => $rp->player->player_name,
                'position'         => $rp->player->position,
                'team'             => $rp->player->team,
            ]);

        $rosteredPlayerIds = RosteredPlayer::join('league_members', 'rostered_players.league_member_id', '=', 'league_members.id')
            ->where('league_members.league_id', $league_id)
            ->where('rostered_players.is_rostered', 1)
            ->pluck('rostered_players.player_id')
            ->toArray();

        $scoringRules = ScoringRule::find($league_id);

        $freeAgents = Player::whereNotIn('players.id', $rosteredPlayerIds)
            ->leftJoin('yearly_stats', function ($join) use ($season) {
                $join->on('players.id', '=', 'yearly_stats.player_id')
                    ->where('yearly_stats.season', '=', $season);
            })
            ->select(
                'players.id',
                'players.player_name',
                'players.position',
                'players.team',
                'players.external_player_id',
                'yearly_stats.passing_yards',
                'yearly_stats.passing_tds',
                'yearly_stats.interceptions',
                'yearly_stats.two_point_passes',
                'yearly_stats.rushing_yards',
                'yearly_stats.rushing_tds',
                'yearly_stats.two_point_rushes',
                'yearly_stats.receptions',
                'yearly_stats.receiving_yards',
                'yearly_stats.receiving_tds',
                'yearly_stats.two_point_receptions',
                'yearly_stats.special_teams_tds'
            )
            ->get()
            ->map(function ($player) use ($scoringRules) {
                $score = 0;

                if ($scoringRules) {
                    $passYds = $player->passing_yards ?? 0;
                    $rushYds = $player->rushing_yards ?? 0;
                    $recYds  = $player->receiving_yards ?? 0;

                    $score += $passYds * ($scoringRules->passing_yards ?? 0);
                    $score += ($player->passing_tds ?? 0) * ($scoringRules->passing_touchdowns ?? 0);
                    $score += ($player->interceptions ?? 0) * ($scoringRules->interceptions_thrown ?? 0);
                    $score += ($player->two_point_passes ?? 0) * ($scoringRules->two_point_pass ?? 0);
                    if ($passYds >= 400) $score += $scoringRules->passing_400_plus ?? 0;
                    elseif ($passYds >= 300) $score += $scoringRules->passing_300_399 ?? 0;

                    $score += $rushYds * ($scoringRules->rushing_yards ?? 0);
                    $score += ($player->rushing_tds ?? 0) * ($scoringRules->rushing_touchdowns ?? 0);
                    $score += ($player->two_point_rushes ?? 0) * ($scoringRules->two_point_rush ?? 0);
                    if ($rushYds >= 200) $score += $scoringRules->rushing_200_plus ?? 0;
                    elseif ($rushYds >= 100) $score += $scoringRules->rushing_100_199 ?? 0;

                    $score += ($player->receptions ?? 0) * ($scoringRules->receptions ?? 0);
                    $score += $recYds * ($scoringRules->receiving_yards ?? 0);
                    $score += ($player->receiving_tds ?? 0) * ($scoringRules->receiving_touchdowns ?? 0);
                    $score += ($player->two_point_receptions ?? 0) * ($scoringRules->two_point_reception ?? 0);
                    if ($recYds >= 200) $score += $scoringRules->receiving_200_plus ?? 0;
                    elseif ($recYds >= 100) $score += $scoringRules->receiving_100_199 ?? 0;

                    $score += ($player->special_teams_tds ?? 0) * ($scoringRules->kickoff_return_touchdown ?? 0);
                }

                return [
                    'id'                 => $player->id,
                    'player_name'        => $player->player_name ?? 'Unknown Player',
                    'position'           => $player->position ?? 'Unknown',
                    'team'               => $player->team ?? 'Unknown',
                    'external_player_id' => $player->external_player_id,
                    'stats' => [
                        'passing_yards'        => $player->passing_yards ?? 0,
                        'passing_tds'          => $player->passing_tds ?? 0,
                        'interceptions'        => $player->interceptions ?? 0,
                        'two_point_passes'     => $player->two_point_passes ?? 0,
                        'rushing_yards'        => $player->rushing_yards ?? 0,
                        'rushing_tds'          => $player->rushing_tds ?? 0,
                        'two_point_rushes'     => $player->two_point_rushes ?? 0,
                        'receptions'           => $player->receptions ?? 0,
                        'receiving_yards'      => $player->receiving_yards ?? 0,
                        'receiving_tds'        => $player->receiving_tds ?? 0,
                        'two_point_receptions' => $player->two_point_receptions ?? 0,
                        'special_teams_tds'    => $player->special_teams_tds ?? 0,
                    ],
                    'fantasy_score' => round($score, 2),
                ];
            });

        $isAfterWaiverDay = false;
        $waiverRule = WaiverRule::where('league_id', $league_id)->first();

        if ($waiverRule?->waiver_day) {
            $now = now();
            $currentWeek = FantasyWeek::where('begin_datetime', '<=', $now)
                ->where('end_datetime', '>=', $now)
                ->first();

            if (!$currentWeek) {
                $currentWeek = FantasyWeek::where('begin_datetime', '>', $now)
                    ->orderBy('begin_datetime')
                    ->first();
            }

            if ($currentWeek) {
                $iter = now()->parse($currentWeek->begin_datetime);
                $end  = now()->parse($currentWeek->end_datetime);
                $waiverDate = null;

                while ($iter->lte($end)) {
                    if ($iter->format('l') === $waiverRule->waiver_day) {
                        $waiverDate = $iter->copy();
                        break;
                    }
                    $iter->addDay();
                }

                $isAfterWaiverDay = $waiverDate ? now()->gte($waiverDate) : false;
            }
        }

        return response()->json([
            'league_member' => [
                'id'                    => $leagueMember->id,
                'league_id'             => $leagueMember->league_id,
                'user_id'               => $leagueMember->user_id,
                'team_name'             => $leagueMember->team_name,
                'remaining_faab_budget' => $leagueMember->remaining_faab_budget,
                'is_vamp'               => $leagueMember->is_vamp,
                'role'                  => $leagueMember->role,
                'is_active'             => $leagueMember->is_active,
            ],
            'user_roster'        => $userRoster,
            'free_agents'        => $freeAgents,
            'is_after_waiver_day' => $isAfterWaiverDay,
        ]);
    }
}