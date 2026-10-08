<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ImportDjango extends Command
{
    protected $signature = 'legion:import-django {file} {--dry-run}';

    protected $description = 'Import a private Django export into an EMPTY Laravel MySQL database.';

    public function handle(): int
    {
        $file = $this->argument('file');
        if (! is_file($file)) {
            $this->error('Snapshot missing.');

            return 1;
        }
        $data = json_decode(file_get_contents($file), true, 512, JSON_THROW_ON_ERROR);
        $defs = json_decode(file_get_contents(resource_path('data/cms-schema.json')), true);
        $allowed = array_column($defs, 'table');
        foreach ($data['tables'] as $t => $rows) {
            if (! in_array($t, $allowed, true)) {
                $this->error('Unknown table: '.$t);

                return 1;
            } if (DB::table($t)->exists()) {
                $this->error('Refusing to overwrite non-empty table: '.$t);

                return 1;
            }
        }
        $this->table(['Table', 'Records'], collect($data['tables'])->map(fn ($rows, $t) => [$t, count($rows)])->values()->all());
        if ($this->option('dry-run')) {
            return 0;
        }
        Schema::disableForeignKeyConstraints();
        try {
            DB::transaction(function () use ($data, $defs) {
                foreach ($defs as $def) {
                    $t = $def['table'];
                    $jsons = array_column(array_filter($def['fields'], fn ($f) => $f['type'] === 'JSONField'), 'column');
                    $dates = array_column(array_filter($def['fields'], fn ($f) => $f['type'] === 'DateTimeField'), 'column');
                    foreach (array_chunk($data['tables'][$t] ?? [], 100) as $chunk) {
                        foreach ($chunk as &$r) {
                            foreach ($jsons as $j) {
                                if (array_key_exists($j, $r)) {
                                    $r[$j] = json_encode($r[$j], JSON_UNESCAPED_UNICODE);
                                }
                            }foreach ($dates as $d) {
                                if (! empty($r[$d])) {
                                    $r[$d] = Carbon::parse($r[$d])->utc()->format('Y-m-d H:i:s.u');
                                }
                            }
                        }unset($r);
                        if ($chunk) {
                            DB::table($t)->insert($chunk);
                        }
                    }
                }
            });
        } finally {
            Schema::enableForeignKeyConstraints();
        }
        $this->info('Import committed. Existing passwords and IDs preserved.');

        return 0;
    }
}
