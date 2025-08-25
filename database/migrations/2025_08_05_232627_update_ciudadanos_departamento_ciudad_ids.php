<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ciudadanos', function (Blueprint $table) {
            // Agregar nuevas columnas
            $table->unsignedBigInteger('departamento_nacimiento_id')->nullable()->after('fecha_nacimiento');
            $table->unsignedBigInteger('ciudad_nacimiento_id')->nullable()->after('departamento_nacimiento_id');
            
            // Agregar índices
            $table->index('departamento_nacimiento_id');
            $table->index('ciudad_nacimiento_id');
        });
        
        // Migrar datos existentes si los hay
        DB::statement('
            UPDATE ciudadanos c
            INNER JOIN departamentos d ON UPPER(TRIM(c.departamento_nacimiento)) = UPPER(TRIM(d.nombre))
            SET c.departamento_nacimiento_id = d.id
            WHERE c.departamento_nacimiento IS NOT NULL
        ');
        
        DB::statement('
            UPDATE ciudadanos c
            INNER JOIN ciudades ci ON UPPER(TRIM(c.ciudad_nacimiento)) = UPPER(TRIM(ci.nombre))
            SET c.ciudad_nacimiento_id = ci.id
            WHERE c.ciudad_nacimiento IS NOT NULL
        ');
        
        // Eliminar columnas antiguas
        Schema::table('ciudadanos', function (Blueprint $table) {
            $table->dropColumn(['departamento_nacimiento', 'ciudad_nacimiento']);
        });
    }

    public function down(): void
    {
        Schema::table('ciudadanos', function (Blueprint $table) {
            $table->string('departamento_nacimiento')->nullable();
            $table->string('ciudad_nacimiento')->nullable();
            $table->dropColumn(['departamento_nacimiento_id', 'ciudad_nacimiento_id']);
        });
    }
};