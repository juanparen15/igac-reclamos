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
            // $table->enum('tipo_documento', ['CC', 'CE', 'TI', 'PAS', 'NIT']);
            // $table->string('numero_documento')->unique();
            $table->string('primer_nombre');
            $table->string('segundo_nombre')->nullable();
            $table->string('primer_apellido');
            $table->string('segundo_apellido')->nullable();
            $table->string('numero_celular');
            $table->text('direccion_notificacion');
            $table->date('fecha_nacimiento');
            $table->string('departamento_id')->nullable();
            $table->string('ciudad_id')->nullable();
            $table->enum('genero', ['M', 'F', 'O']);
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