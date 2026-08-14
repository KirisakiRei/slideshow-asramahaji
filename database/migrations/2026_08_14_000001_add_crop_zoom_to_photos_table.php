<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Crop zoom (100-400 %) applied on top of object-fit cover when a photo
     * fills its display frame. 100 means the current default framing; higher
     * values zoom in toward the photo's focal point. Videos always render
     * whole (contain) so they never read this value.
     */
    public function up(): void
    {
        Schema::table('photos', function (Blueprint $table) {
            $table->unsignedSmallInteger('crop_zoom')->default(100)->after('focus_y');
        });
    }

    public function down(): void
    {
        Schema::table('photos', function (Blueprint $table) {
            $table->dropColumn('crop_zoom');
        });
    }
};