<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')
                  ->constrained('events')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');

            $table->string('name');
            $table->string('email');
            $table->string('phone_number')->nullable();
            $table->string('organization')->nullable();
            $table->text('motivation')->nullable();

            $table->string('status')->default('pending');
            $table->text('notes')->nullable();
            $table->timestamp('registered_at')->useCurrent();

            $table->foreignId('user_id')
                  ->nullable()
                  ->constrained('users')
                  ->onUpdate('cascade')
                  ->onDelete('set null');

            $table->timestamps();

            $table->unique(['event_id', 'email']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_registrations');
    }
};
