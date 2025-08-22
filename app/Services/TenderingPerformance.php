<?php

namespace App\Services;

use Illuminate\Support\Collection;
use App\Models\TenderInfo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TenderingPerformance
{
    public function getStepsPerTender(array $tenderIds, array $taskStages): Collection
    {
        Log::info('Fetching steps per tender', ['tenderIds' => $tenderIds, 'taskStages' => $taskStages]);

        $result = DB::table('timer_trackers')
            ->select('tender_id', DB::raw('JSON_ARRAYAGG(stage) AS stages'))
            ->whereIn('tender_id', $tenderIds)
            ->whereIn('stage', $taskStages)
            ->groupBy('tender_id')
            ->get()
            ->keyBy('tender_id')
            ->map(function ($item) {
                return array_unique(json_decode($item->stages, true));
            });
        Log::info('Fetched steps per tender ', ['result' => $result->toArray()]);


        return $result;
    }

    public function getStepsPerTenderOnTime(array $tenderIds, array $taskStages): Collection
    {
        // Log::info('Fetching steps per tender on time', ['tenderIds' => $tenderIds, 'taskStages' => $taskStages]);

        $result = DB::table('timer_trackers')
            ->select('tender_id', 'stage', 'remaining_time')
            ->whereIn('tender_id', $tenderIds)
            ->whereIn('stage', $taskStages)
            ->where('status', 'completed')
            ->get()
            ->groupBy('tender_id')
            ->map(function ($items) {
                Log::info('Fetched steps per tender on time', ['items' => $items->toArray()]);

                return $items->mapWithKeys(fn($item) => [$item->stage => $item->remaining_time])->toArray();
            });

        // Log::info('Fetched steps per tender on time', ['result' => $result->toArray()]);

        return $result;
    }

    public function getPerformanceTasksByRole(string $role): array
    {
        return config("tenderingtasks.roles.$role", []);
    }

    public function getStepsSummary(array $tenderIds, array $tasks, string $usersRole): array
    {
        Log::info("Starting getStepsSummary", ['tenderIds' => $tenderIds, 'tasks' => $tasks, 'usersRole' => $usersRole]);

        if (empty($tenderIds)) {
            Log::warning("Tender IDs are empty.");

            return [];
        }

        if (empty($tasks)) {
            Log::warning("Tasks are empty.");

            return [];
        }

        $skippedStages = $this->getSkippedStages($tenderIds, $usersRole);
        // Log::info("Fetched skipped stages", ['skippedStages' => $skippedStages]);

        $stepsPerTender = $this->getStepsPerTender($tenderIds, $tasks);
        // Log::info("Fetched steps per tender", $stepsPerTender->toArray());

        $stepsPerTenderOnTime = $this->getStepsPerTenderOnTime($tenderIds, $tasks);
        // Log::info("Fetched steps per tender on time", $stepsPerTenderOnTime->toArray());

        if ($stepsPerTender->isEmpty()) {
            Log::warning("No steps per tender found.");

            return [];
        }

        if ($stepsPerTenderOnTime->isEmpty()) {
            Log::warning("No steps per tender on time found.");

            return [];
        }

        $mergedDoneStages = $this->mergeCompletedAndSkipped($stepsPerTender, $skippedStages, count($tasks));
        // Log::info("Merged done and skipped stages", ['mergedDoneStages' => $mergedDoneStages]);

        $overallSummary = $this->calculateSummary($mergedDoneStages, $stepsPerTenderOnTime);
        // Log::info("Calculated overall summary", ['overallSummary' => $overallSummary]);

        foreach ($mergedDoneStages as $tenderId => $stages) {
            // Log::info("Tender ID {$tenderId} - Done Stages", $stages);
        }

        Log::info("Completed getStepsSummary");

        return [
            'skippedStages' => $skippedStages,
            'stepsPerTender' => $stepsPerTender,
            'stepsPerTenderOnTime' => $stepsPerTenderOnTime,
            'mergedDoneStages' => $mergedDoneStages,
            'overallSummary' => $overallSummary,
        ];
    }

    private function getSkippedStages(array $tenderIds, string $role): array
    {
        if ($role !== 'tender-executive') {
            Log::info("Step 4 is Skipped because role is $role.");
            return [];
        }

        Log::info("Fetching skipped stages for tenders", ['tenderIds' => $tenderIds]);

        $tenders = TenderInfo::with([
            'info' => fn($query) => $query->select('id', 'tender_id', 'phyDocs')
        ])
            ->select('id', 'emd', 'tender_fees')
            ->whereIn('id', $tenderIds)
            ->get();

        Log::info("Fetched skipped stages for tenders", ['tenders' => $tenders->toArray()]);

        $skippedStages = [];

        foreach ($tenders as $tender) {
            $stages = [];

            if ($tender->rfq_to == '0') {
                $stages[] = 'rfq';
                // Log::info("Skipping RFQ for Tender #{$tender->id}.");
            }
            if ($tender->emd <= 0) {
                $stages[] = 'emd_request';
                // Log::info("Skipping EMD Request for Tender #{$tender->id}.");
            }
            if ($tender->tender_fees <= 0) {
                $stages[] = 'tender_fee';
                // Log::info("Skipping Tender Fee for Tender #{$tender->id}.");
            }
            if ($tender->info && $tender->info->phyDocs === 'No') {
                $stages[] = 'physical_docs';
                // Log::info("Skipping Physical Docs for Tender #{$tender->id}.");
            }

            if (empty($stages)) {
                // Log::info("No stages skipped for Tender #{$tender->id}.");
            }

            $skippedStages[$tender->id] = $stages;
        }

        Log::info("Step 4: Getting Not Applicable Stages on each Tender.");
        return $skippedStages;
    }

    private function mergeCompletedAndSkipped(Collection $executed, array $skipped, int $totalStageCount): array
    {
        // Log::info('Merging completed and skipped stages', ['executed' => $executed->toArray(), 'skipped' => $skipped, 'totalStageCount' => $totalStageCount]);

        $result = [];

        foreach ($executed as $tenderId => $stages) {
            if (empty($stages)) {
                Log::warning("No stages found for Tender ID $tenderId");
                continue;
            }

            // Log::info("Merging completed and skipped stages for Tender ID $tenderId", ['stages' => $stages]);

            $skip = $skipped[$tenderId] ?? [];

            if (!is_array($skip)) {
                Log::warning("Skipped stages for Tender ID $tenderId is not an array", ['skipped' => $skip]);
                continue;
            }

            // Log::info("Merged completed and skipped stages for Tender ID $tenderId", ['skipped' => $skip]);

            // Convert stages to array if it's not already
            if (!is_array($stages)) {
                $stages = [$stages];
            }

            // Remove duplicates from stages array
            $uniqueStages = array_unique($stages);

            $result[$tenderId] = [
                'skipped' => $skip,
                'stages' => array_values($uniqueStages),
                'done' => count($uniqueStages),
                'tenderhave' => $totalStageCount - count($skip),
            ];

            if (!is_array($result[$tenderId]['stages'])) {
                Log::warning("Executed stages for Tender ID $tenderId is not an array", ['stages' => $result[$tenderId]['stages']]);
                continue;
            }
            Log::info("Merged completed and skipped stages for Tender ID $tenderId", ['merged' => $result[$tenderId]]);
        }
        // Log::info('Merged completed and skipped stages', ['result' => $result]);

        return $result;
    }

    private function calculateSummary(array $mergedStages, Collection $onTime): array
    {
        Log::info('Calculating Summary', ['mergedStages' => $mergedStages, 'onTime' => $onTime->toArray()]);

        $summary = [
            'tenderhave' => 0,
            'done' => 0,
            'ontime' => 0,
        ];

        foreach ($mergedStages as $data) {
            if (is_array($data) && isset($data['tenderhave'], $data['done'])) {
                // Log::info('Summing done and tenderhave for Tender ID', ['data' => $data]);

                $summary['tenderhave'] += $data['tenderhave'];
                $summary['done'] += $data['done'];
            } else {
                Log::warning('Invalid data structure in mergedStages', ['data' => $data]);
            }
        }

        foreach ($onTime as $stages) {
            if (is_array($stages)) {
                foreach ($stages as $stage => $time) {
                    // Log::info("Checking if stage $stage is done on time", ['time' => $time]);

                    if (is_numeric($time) && $time > 0) {
                        $summary['ontime']++;
                    }
                }
            } else {
                Log::warning('Invalid data structure in onTime', ['stages' => $stages]);
            }
        }

        Log::info('Calculated Summary', ['summary' => $summary]);

        return $summary;
    }
}
