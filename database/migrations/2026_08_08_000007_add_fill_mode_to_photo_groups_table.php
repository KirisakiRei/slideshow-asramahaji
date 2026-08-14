<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * How photos of this group fill their display frame: cover (crop to fill)
     * or contain (show the whole photo, blurred backdrop fills the gaps).
     */
    public function up(): void
    {
        Schema::table('photo_groups', function (Blueprint $table) {
            $table->string('fill_mode', 10)->default('cover')->after('transition_type');
        });
    }

    public function down(): void
    {
        Schema::table('photo_groups', function (Blueprint $table) {
            $table->dropColumn('fill_mode');
        });
    }
};
