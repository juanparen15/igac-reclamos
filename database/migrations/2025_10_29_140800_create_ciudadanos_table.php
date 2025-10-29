<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ciudadanos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            // ❌ ELIMINAR tipo_documento y numero_documento de aquí
            $table->string('primer_nombre')->nullable();
            $table->string('segundo_nombre')->nullable();
            $table->string('primer_apellido')->nullable();
            $table->string('segundo_apellido')->nullable();
            $table->string('numero_celular')->nullable();
            $table->text('direccion_notificacion')->nullable();
            $table->date('fecha_nacimiento')->nullable();
            $table->foreignId('departamento_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('ciudad_id')->nullable()->constrained('ciudades')->onDelete('set null');
            $table->enum('genero', ['M', 'F', 'O'])->nullable();
            $table->string('condicion_especial')->nullable();
            $table->string('foto_perfil')->nullable();
            $table->json('campos_adicionales')->nullable();
            $table->boolean('perfil_completo')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ciudadanos');
    }
};