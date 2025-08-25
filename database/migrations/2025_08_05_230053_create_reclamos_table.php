<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reclamos', function (Blueprint $table) {
            $table->id();
            $table->string('numero_ticket')->unique();
            $table->foreignId('ciudadano_id')->constrained()->onDelete('cascade');
            $table->string('asunto');
            $table->text('mensaje');
            $table->json('tipos_reclamo_ids');
            $table->enum('estado', ['nuevo', 'en_proceso', 'resuelto', 'cerrado'])->default('nuevo');
            $table->string('archivo_oficio')->nullable();
            $table->json('archivos_adicionales')->nullable();
            $table->json('campos_adicionales')->nullable();
            $table->timestamp('fecha_resolucion')->nullable();
            $table->foreignId('asignado_a')->nullable()->constrained('users');
            $table->text('notas_internas')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reclamos');
    }
};