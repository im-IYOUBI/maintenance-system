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
        Schema::table('tickets', function (Blueprint $table) {
            $table->date('preferred_date')->nullable();
            $table->integer('rating')->nullable();
            $table->text('rating_comment')->nullable();
            $table->string('specialty_required')->nullable();
            $table->timestamp('completed_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropColumn('preferred_date');
            $table->dropColumn('rating');
            $table->dropColumn('rating_comment');
            $table->dropColumn('specialty_required');
            $table->dropColumn('completed_at');
        });
    }
};
