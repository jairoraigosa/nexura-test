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
        Schema::create('empleados', function (Blueprint $table) {
            $table->id()->comment('Identificador del empleado');
            $table->string('nombre', 255)->comment('Nombre del empleado. Campo tipo Text, solo debe permitir letras con o sin tilde y espacios, no se admiten caracteres especiales ni números. Obligatorio');
            $table->string('email', 255)->unique()->comment('Correo electrónico del empleado. Campo de tipo Text|Email. Solo permite una estructura de correo. Obligatorio');
            $table->char('sexo', 1)->comment('Campo de tipo Radio Button. M para masculino y F para femenino. Obligatorio');
            $table->foreignId('area_id')->comment('Área de la empresa a la que pertenece el empleado. Campo de tipo select. Obligatorio')->constrained('areas')->onDelete('cascade');
            $table->integer('boletin')->default(0)->comment('1 para recibir boletín, 0 para no recibir boletín. Campo de tipo checkbox. Opcional');
            $table->text('descripcion')->comment('Se describe la experiencia del empleado. Campo de tipo textarea. Obligatorio');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('empleados');
    }
};
