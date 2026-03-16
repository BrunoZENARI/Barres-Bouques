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
        Schema::create('roles_permissions', function (Blueprint $table) {
            $table->comment('table de jointure entre les permissions et les roles ');
            $table->bigIncrements('id');
            $table->unsignedBigInteger('id_permission')->nullable()->index('id_permission');
            $table->unsignedBigInteger('id_role')->nullable()->index('id_role');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles_permissions');
    }
};
