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
        Schema::table('loans', function (Blueprint $table) {
            // Cambiamos los posibles valores de status
            $table->enum('status', ['pendiente', 'aprobado', 'rechazado', 'devuelto'])->default('pendiente')->change();

            // Permitimos que la fecha de préstamo sea nula (hasta que el admin apruebe)
            $table->date('loan_date')->nullable()->change();

            // Agregamos campo para registrar cuándo el admin respondió
            $table->timestamp('admin_response_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->enum('status', ['pendiente', 'devuelto'])->default('pendiente')->change();
            $table->date('loan_date')->nullable(false)->change();
            $table->dropColumn('admin_response_at');
        });
    }
};
