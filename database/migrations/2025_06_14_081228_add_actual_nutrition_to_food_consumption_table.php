<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('food_consumptions', function (Blueprint $table) {
            // Kolom untuk menyimpan nilai aktual yang dikonsumsi setelah validasi
            // Default 0.00 dan tidak nullable agar tidak perlu null check di sum
            $table->decimal('actual_kalori', 8, 2)->default(0.00)->after('kalori');
            $table->decimal('actual_protein', 8, 2)->default(0.00)->after('actual_kalori');
            $table->decimal('actual_karbohidrat', 8, 2)->default(0.00)->after('actual_protein');
            $table->decimal('actual_lemak', 8, 2)->default(0.00)->after('actual_karbohidrat');

            // Persentase konsumsi menu keseluruhan (derived from actual/original totals)
            $table->decimal('consumption_percentage', 5, 2)->nullable()->after('actual_lemak');
            // Catatan Ahli Gizi
            $table->text('notes')->nullable()->after('consumption_percentage');

            // Optional: Tambahkan status 'skipped' jika belum ada di ENUM atau VARCHAR
            // Pastikan Anda memilih salah satu dari ini jika status belum support 'skipped'
            // Jika status sudah VARCHAR, tidak perlu modifikasi kolom.
            // Jika status ENUM, Anda perlu statement SQL langsung:
            // DB::statement("ALTER TABLE food_consumption CHANGE COLUMN status status ENUM('planned', 'delivered', 'consumed', 'skipped') NOT NULL DEFAULT 'planned'");
        });
    }

    public function down(): void
    {
        Schema::table('food_consumption', function (Blueprint $table) {
            $table->dropColumn([
                'actual_kalori',
                'actual_protein',
                'actual_karbohidrat',
                'actual_lemak',
                'consumption_percentage',
                'notes',
            ]);
            // Jika Anda mengubah ENUM, perlu rollback ENUM juga
        });
    }
};