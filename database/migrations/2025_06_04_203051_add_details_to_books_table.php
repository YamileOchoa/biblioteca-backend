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
        Schema::table('books', function (Blueprint $table) {
            $table->text('synopsis')->nullable();
            $table->integer('pages')->nullable();
            $table->string('publisher')->nullable();
            $table->integer('stock')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
             $table->dropColumn(['synopsis', 'pages', 'publisher', 'stock']);
        });
    }
};
