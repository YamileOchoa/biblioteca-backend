<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('books', function (Blueprint $table) {
            $table->string('cover_image')->nullable();
        });
    }

    public function down()
    {
        if (Schema::hasColumn('books', 'cover_image')) {
            Schema::table('books', function (Blueprint $table) {
                $table->dropColumn('cover_image');
            });
        }
    }
};
