<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CoilMachineTrack;
use App\Models\CoilManufacture;
use App\Models\CoilStock;
use App\Models\Machine;
use App\Models\ProductionReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard.
     *
     * @return View
     */
    public function index(): View
    {
        $todayProductionTotal = ProductionReport::query()
            ->whereDate('report_date', now()->toDateString())
            ->where('status', 1)
            ->where('is_deleted', false)
            ->sum('actual_set_shift');

        $activeMachinesCount = Machine::query()
            ->where('is_deleted', false)
            ->where('status', true)
            ->count();

        $notActiveMachinesCount = Machine::query()
            ->where('is_deleted', false)
            ->where('status', false)
            ->count();

        $machineInUseCount = Machine::query()
            ->where('is_deleted', false)
            ->whereNotNull('coil_id')
            ->count();

        $totalSuppliersCount = CoilManufacture::query()
            ->where('is_deleted', false)
            ->count();

        $inactiveSuppliersCount = CoilManufacture::query()
            ->where('is_deleted', false)
            ->where('status', false)
            ->count();

        $sf001TodayQuery = ProductionReport::query()
            ->whereDate('report_date', now()->toDateString())
            ->where('is_deleted', false)
            ->where('status', true);

        $totalWorkmanCount = (int) (clone $sf001TodayQuery)->sum('workman_count');
        $totalStaffCount = (int) (clone $sf001TodayQuery)->sum('staff_count');
        $totalManpowerWorking = $totalWorkmanCount + $totalStaffCount;

        $totalCoilsCount = CoilStock::query()
            ->where('is_deleted', false)
            ->count();

        $totalCoilWeightKg = (float) CoilStock::query()
            ->where('is_deleted', false)
            ->sum('net_weight_kg');

        $inUseCoilsCount = CoilStock::query()
            ->where('is_deleted', false)
            ->where('process', 'in_use')
            ->count();

        $loadedCoilWeightKg = (float) CoilMachineTrack::query()
            ->where('type', CoilMachineTrack::ACTION_LOAD)
            ->where('is_deleted', false)
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('coil_machine_track as unload_tracks')
                    ->whereColumn('unload_tracks.reference_track_id', 'coil_machine_track.id')
                    ->where('unload_tracks.type', CoilMachineTrack::ACTION_UNLOAD)
                    ->where('unload_tracks.is_deleted', 0);
            })
            ->sum('load_weight');

        return view('backend.dashboard', [
            'todayProductionTotal' => number_format((int) $todayProductionTotal),
            'activeMachinesCount' => $activeMachinesCount,
            'notActiveMachinesCount' => $notActiveMachinesCount,
            'machineInUseCount' => $machineInUseCount,
            'totalSuppliersCount' => $totalSuppliersCount,
            'inactiveSuppliersCount' => $inactiveSuppliersCount,
            'totalManpowerWorking' => $totalManpowerWorking,
            'totalStaffWorkingSf001' => $totalStaffCount,
            'totalWorkerWorkingSf001' => $totalWorkmanCount,
            'totalCoilsCount' => $totalCoilsCount,
            'totalCoilWeightKg' => number_format($totalCoilWeightKg, 0),
            'inUseCoilsCount' => $inUseCoilsCount,
            'loadedCoilWeightKg' => number_format($loadedCoilWeightKg, 0),
        ]);
    }
}
