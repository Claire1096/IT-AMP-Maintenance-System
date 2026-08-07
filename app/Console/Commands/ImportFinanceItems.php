<?php

namespace App\Console\Commands;

use App\Models\Department;
use App\Models\FinanceItem;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportFinanceItems extends Command
{
    protected $signature = 'finance-items:import {file : Path to the normalized CSV file}';
    protected $description = 'One-time import of finance items from a normalized CSV (name,asset_type,department,quantity,may_quantity,july_quantity)';

    public function handle(): int
    {
        $path = $this->argument('file');

        if (!file_exists($path)) {
            $this->error("File not found: {$path}");
            return self::FAILURE;
        }

        $departmentMap = Department::pluck('id', 'name')->mapWithKeys(
            fn ($id, $name) => [strtoupper(trim($name)) => $id]
        );

        $handle = fopen($path, 'r');
        fgetcsv($handle); // skip header row

        $created = 0;
        $skipped = [];
        $unmappedDepartments = [];
        $rowNum = 1;

        DB::beginTransaction();

        try {
            while (($row = fgetcsv($handle)) !== false) {
                $rowNum++;

                if (count($row) < 6) {
                    $skipped[] = "Row {$rowNum}: malformed row (wrong column count)";
                    continue;
                }

                [$name, $assetType, $department, $quantity, $mayQuantity, $julyQuantity] = $row;

                $name = trim($name);
                if ($name === '') {
                    $skipped[] = "Row {$rowNum}: missing asset name";
                    continue;
                }

                $quantity = (int) $quantity;
                $mayQuantity = $mayQuantity === '' ? null : (int) $mayQuantity;
                $julyQuantity = $julyQuantity === '' ? null : (int) $julyQuantity;

                $currentQuantity = $julyQuantity ?? $mayQuantity ?? $quantity;
                $currentQuantity = max(0, min($currentQuantity, $quantity));
                $missingQuantity = max(0, $quantity - $currentQuantity);

                $departmentId = null;
                $department = trim($department);
                if ($department !== '') {
                    $key = strtoupper($department);
                    if (isset($departmentMap[$key])) {
                        $departmentId = $departmentMap[$key];
                    } else {
                        $unmappedDepartments[$department] = ($unmappedDepartments[$department] ?? 0) + 1;
                    }
                }

                $status = $missingQuantity > 0 ? 'missing' : 'in_use';

                $item = FinanceItem::create([
                    'item_tag' => $this->generateItemTag(),
                    'name' => $name,
                    'asset_type' => $assetType !== '' ? $assetType : null,
                    'quantity' => $quantity,
                    'current_quantity' => $currentQuantity,
                    'missing_quantity' => $missingQuantity,
                    'department_id' => $departmentId,
                    'status' => $status,
                    'missing_since' => $missingQuantity > 0 ? now() : null,
                ]);

                if ($mayQuantity !== null) {
                    $mayMissing = max(0, $quantity - $mayQuantity);
                    $item->monthlyLogs()->create([
                        'month' => '2026-06-01', // May data recorded as June per business rule
                        'quantity_on_hand' => $mayQuantity,
                        'missing_quantity' => $mayMissing,
                    ]);
                }

                if ($julyQuantity !== null) {
                    $julyMissing = max(0, $quantity - $julyQuantity);
                    $item->monthlyLogs()->create([
                        'month' => '2026-07-01',
                        'quantity_on_hand' => $julyQuantity,
                        'missing_quantity' => $julyMissing,
                    ]);
                }

                if ($mayQuantity === null && $julyQuantity === null) {
                    $item->monthlyLogs()->create([
                        'month' => now()->startOfMonth(),
                        'quantity_on_hand' => $quantity,
                        'missing_quantity' => 0,
                    ]);
                }

                $created++;
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->error('Import failed and was rolled back: ' . $e->getMessage());
            return self::FAILURE;
        }

        fclose($handle);

        $this->info("Import complete.");
        $this->info("Created: {$created}");

        if (!empty($skipped)) {
            $this->warn('Skipped rows:');
            foreach ($skipped as $s) {
                $this->line("  - {$s}");
            }
        }

        if (!empty($unmappedDepartments)) {
            $this->warn('Department names that had no match (item created with no department):');
            foreach ($unmappedDepartments as $dept => $count) {
                $this->line("  - \"{$dept}\" ({$count} rows)");
            }
        }

        return self::SUCCESS;
    }

    private function generateItemTag(): string
    {
        $year = now()->year;
        $count = FinanceItem::withTrashed()->where('item_tag', 'like', "FIN-{$year}-%")->count() + 1;

        return sprintf('FIN-%d-%04d', $year, $count);
    }
}
