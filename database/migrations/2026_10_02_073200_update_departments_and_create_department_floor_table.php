<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Add fields to existing departments table
        Schema::table('departments', function (Blueprint $table) {
            if (!Schema::hasColumn('departments', 'icon_key')) {
                $table->string('icon_key', 50)->nullable()->after('description');
            }
            if (!Schema::hasColumn('departments', 'created_at')) {
                $table->timestamps();
            }
            if (!Schema::hasColumn('departments', 'deleted_at')) {
                $table->softDeletes();
            }
        });

        // 2. Add foreign key to head_employee_id if employees table exists
        if (Schema::hasTable('employees')) {
            Schema::table('departments', function (Blueprint $table) {
                // Drop index if it exists (for safe re-running)
                // In earlier migration head_employee_id was a bigInteger
                // We just need to add the foreign key constraint
                $table->foreign('head_employee_id')
                    ->references('id')
                    ->on('employees')
                    ->nullOnDelete();
            });
        }

        // 3. Create Pivot Table
        if (!Schema::hasTable('department_floor')) {
            Schema::create('department_floor', function (Blueprint $table) {
                $table->id();
                
                $table->foreignId('department_id')
                    ->constrained('departments')
                    ->cascadeOnDelete();
                    
                $table->foreignId('floor_id')
                    ->constrained('floors')
                    ->cascadeOnDelete();
                    
                $table->boolean('is_primary')->default(false);
                
                $table->timestamps();
                
                $table->unique(['department_id', 'floor_id']);
                $table->index(['floor_id', 'department_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('department_floor');

        Schema::table('departments', function (Blueprint $table) {
            if (Schema::hasTable('employees')) {
                $table->dropForeign(['head_employee_id']);
            }
            if (Schema::hasColumn('departments', 'icon_key')) {
                $table->dropColumn('icon_key');
            }
            if (Schema::hasColumn('departments', 'created_at')) {
                $table->dropTimestamps();
            }
            if (Schema::hasColumn('departments', 'deleted_at')) {
                $table->dropSoftDeletes();
            }
        });
    }
};
