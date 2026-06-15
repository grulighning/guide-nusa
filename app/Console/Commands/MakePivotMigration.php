<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakePivotMigration extends Command
{
    protected $signature = 'make:migration:pivot {table : The pivot table name (e.g. destination_guide)} {--foreign1= : First foreign column name} {--foreign2= : Second foreign column name}';

    protected $description = 'Create a migration for a pivot table with two constrained foreign ID columns';

    public function handle(): int
    {
        $table = $this->argument('table');

        // Derive foreign column names from the pivot table name
        // destination_guide → [destination, guide]
        $segments = explode('_', $table);

        $foreign1 = $this->option('foreign1') ?? ($segments[0] ?? 'first') . '_id';
        $foreign2 = $this->option('foreign2') ?? ($segments[1] ?? 'second') . '_id';

        // Derive table names for constrained()
        $table1 = $segments[0] ?? 'first';
        $table2 = $segments[1] ?? 'second';

        // Pluralise for the constrained table reference (Laravel convention)
        $table1Plural = Str::plural($table1);
        $table2Plural = Str::plural($table2);

        // Build timestamp-based filename
        $timestamp = now()->format('Y_m_d_His');
        $filename = "{$timestamp}_create_{$table}_table.php";
        $path = database_path("migrations/{$filename}");

        $stub = <<<PHP
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('{$table}', function (Blueprint \$table) {
            \$table->foreignId('{$foreign1}')->constrained('{$table1Plural}')->cascadeOnDelete();
            \$table->foreignId('{$foreign2}')->constrained('{$table2Plural}')->cascadeOnDelete();
            \$table->primary(['{$foreign1}', '{$foreign2}']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('{$table}');
    }
};

PHP;

        File::put($path, $stub);

        $this->info("✅ Migration created: {$filename}");

        return Command::SUCCESS;
    }
}
