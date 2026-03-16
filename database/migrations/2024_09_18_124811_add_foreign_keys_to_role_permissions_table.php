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
        Schema::table('roles_permissions', function (Blueprint $table) {
            $table->foreign(['id_role'], 'roles_permissions_ibfk_1')->references(['id'])->on('roles')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['id_permission'], 'roles_permissions_ibfk_2')->references(['id'])->on('permissions')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('roles_permissions', function (Blueprint $table) {
            $table->dropForeign('roles_permissions_ibfk_1');
            $table->dropForeign('roles_permissions_ibfk_2');
        });
    }
};
