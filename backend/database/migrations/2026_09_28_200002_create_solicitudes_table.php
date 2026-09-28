<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('solicitudes', function (Blueprint $table) {
            $table->id();
            $table->string('codigo')->unique();
            $table->string('titulo');
            $table->text('descripcion');
            $table->string('tipo');
            $table->string('prioridad')->default('media');
            $table->string('estado')->default('pendiente');
            $table->string('ubicacion')->nullable();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('asignado_a')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('recurso_id')->nullable()->constrained('recursos')->nullOnDelete();
            $table->timestamp('cerrada_at')->nullable();
            $table->timestamps();

            $table->index(['estado', 'prioridad']);
            $table->index(['tipo', 'estado']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('solicitudes');
    }
};
