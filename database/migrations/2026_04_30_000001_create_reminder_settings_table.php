<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reminder_settings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('days_before_due')->default(3);
            $table->boolean('reminder_due_soon_enabled')->default(true);
            $table->boolean('reminder_overdue_enabled')->default(true);
            $table->string('librarian_email')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reminder_settings');
    }
};
