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
        Schema::create('users', function (Blueprint $table) {
            $table->comment('table des utilisateurs de la plateforme');
            $table->bigIncrements('id');
            $table->string('nom');
            $table->string('prenom');
            $table->string('username')->unique();
            $table->unsignedBigInteger('role_id')->nullable()->index();
            $table->boolean('is_active')->default(false);
            $table->string('email')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
