<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('empleado_rol', function (Blueprint $table) {
            $table->foreignId('empleado_id')->comment('Identificador del empleado')->constrained('empleados')->onDelete('cascade');
            $table->foreignId('rol_id')->comment('Identificador del rol')->constrained('roles')->onDelete('cascade');
            $table->timestamps();

            // Primary key compuesta
            $table->primary(['empleado_id', 'rol_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('empleado_rol');
    }
};
