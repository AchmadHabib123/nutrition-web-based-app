<?php

namespace Tests\Unit;

use App\Models\BahanMakanan;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

class BahanMakananTest extends BaseTestCase
{
    /** @test */
    public function it_calculates_calories_per_100g_correctly()
    {
        // WB-23: Hitung kalori per 100g bahan makanan.

        $bahan = new BahanMakanan();
        $bahan->protein = 10;
        $bahan->karbohidrat = 20;
        $bahan->total_lemak = 5;

        // Expected: (10*4) + (20*4) + (5*9) = 40 + 80 + 45 = 165
        $this->assertEquals(165, $bahan->kalori_per_100g);
    }

    /** @test */
    public function it_calculates_calories_per_100g_correctly_with_zero_macros()
    {
        // WB-24: Hitung kalori per 100g bahan makanan dengan makro nol.

        $bahan = new BahanMakanan();
        $bahan->protein = 0;
        $bahan->karbohidrat = 0;
        $bahan->total_lemak = 0;

        $this->assertEquals(0, $bahan->kalori_per_100g);
    }
}