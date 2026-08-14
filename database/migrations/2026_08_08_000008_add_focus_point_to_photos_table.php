<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Focal point (0-100 %) used by object-position when the photo is cropped
     * to fill a frame: the part of the photo that must stay visible.
     */
    public function up(): void
    {
        Schema::table('photos', function (Blueprint $table) {
            $table->unsignedTinyInteger('focus_x')->default(50)->after('type');
            $table->unsignedTinyInteger('focus_y')->default(50)->after('focus_x');
        });
    }

    public function down(): void
    {
        Schema::table('photos', function (Blueprint $table) {
            $table->dropColumn(['focus_x', 'focus_y']);
        });
    }
};
