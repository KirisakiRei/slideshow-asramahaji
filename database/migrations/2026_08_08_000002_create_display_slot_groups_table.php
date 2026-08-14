<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('display_slot_groups', function (Blueprint $table) {
            $table->id();
            $table->string('slot', 30);
            $table->foreignId('photo_group_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['slot', 'photo_group_id']);
            $table->index(['slot', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('display_slot_groups');
    }
};
