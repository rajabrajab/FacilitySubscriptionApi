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
        Schema::create('subscriptions', function (Blueprint $table) {
               $table->id();

            $table->foreignId('user_id')
                  ->constrained()
                  ->cascadeOnDelete();

            $table->foreignId('package_id')
                  ->constrained('packages')
                  ->cascadeOnDelete();

            $table->unsignedInteger('used_times')->default(0);
            $table->dateTime('expire_at')->nullable();

            $table->string('receipt_sham_cash')->nullable();

            $table->string('confirmation_file')->nullable();

            $table->enum('status',['pending','confirmed','cancelled','expired'])->default('pending');

            $table->softDeletes();
            $table->timestamps();

            $table->index(['user_id']);
            $table->index(['package_id']);
            $table->index(['status']);
            $table->index(['expire_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
