<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Agregar la columna title si no existe
        if (!Schema::hasColumn('posts', 'title')) {
            Schema::table('posts', function (Blueprint $table) {
                $table->string('title')->nullable()->after('user_id');
            });
            
            // Asignar títulos por defecto a posts existentes
            DB::table('posts')->whereNull('title')->update([
                'title' => DB::raw("CONCAT('Publicación ', id)")
            ]);
            
            // Hacer la columna NOT NULL después de asignar valores
            Schema::table('posts', function (Blueprint $table) {
                $table->string('title')->nullable(false)->change();
            });
        }
    }

    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn('title');
        });
    }
};