<?php

namespace Tests\Unit;

use App\Models\Menu;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

class MenuTest extends BaseTestCase
{
    /** @test */
    public function it_calculates_menu_calories_correctly()
    {
        // WB-21: Hitung kalori menu dengan makro positif.

        $menu = new Menu();
        $menu->total_protein = 10;
        $menu->total_karbohidrat = 20;
        $menu->total_lemak = 5;

        $menu->calculateCalories();

        // Expected: (10*4) + (20*4) + (5*9) = 40 + 80 + 45 = 165
        $this->assertEquals(165, $menu->kalori);
    }

    /** @test */
    public function it_calculates_menu_calories_correctly_with_zero_macros()
    {
        // WB-22: Hitung kalori menu dengan makro nol.

        $menu = new Menu();
        $menu->total_protein = 0;
        $menu->total_karbohidrat = 0;
        $menu->total_lemak = 0;

        $menu->calculateCalories();

        $this->assertEquals(0, $menu->kalori);
    }
}