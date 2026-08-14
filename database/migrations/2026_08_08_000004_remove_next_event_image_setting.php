<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('settings')->where('key', 'next_event_image')->delete();
    }

    public function down(): void
    {
        // No-op: value cannot be restored once removed.
    }
};
