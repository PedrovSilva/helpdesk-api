<?php

use App\Enums\Priority;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('slas', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();

            $table->string('name', 100)->unique();

            $table->string('priority', 20)->unique();

            $table->unsignedInteger('response_time_minutes');
            $table->unsignedInteger('resolution_time_minutes');

            $table->boolean('active')->default(true);

            $table->timestamps();

            $table->index('priority');
            $table->index('active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('slas');
    }
};
