<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('adresse');

            $table->string('adresse_numero')->nullable()->after('telephone');
            $table->string('adresse_rue')->nullable()->after('adresse_numero');
            $table->string('adresse_complement1')->nullable()->after('adresse_rue');
            $table->string('adresse_complement2')->nullable()->after('adresse_complement1');
            $table->string('adresse_code_postal')->nullable()->after('adresse_complement2');
            $table->string('adresse_ville')->nullable()->after('adresse_code_postal');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'adresse_numero',
                'adresse_rue',
                'adresse_complement1',
                'adresse_complement2',
                'adresse_code_postal',
                'adresse_ville',
            ]);

            $table->string('adresse')->nullable()->after('telephone');
        });
    }
};
