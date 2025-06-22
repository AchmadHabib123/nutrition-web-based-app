<?php

use App\Models\User;
use App\Models\FoodConsumption;
use App\Policies\FoodConsumptionPolicy;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

class FoodConsumptionPolicyTest extends BaseTestCase
{
    protected $policy;

    protected function setUp(): void
    {
        parent::setUp();
        $this->policy = new FoodConsumptionPolicy();
    }

    /** @test */
    public function ahli_gizi_can_mark_delivered_food_as_consumed()
    {
        // WB-28: Diizinkan tandai consumed.

        $user = new User();
        $user->role = 'ahli-gizi';

        $foodConsumption = new FoodConsumption();
        $foodConsumption->status = 'delivered';

        $this->assertTrue($this->policy->markAsConsumed($user, $foodConsumption));
    }

    /** @test */
    public function tenaga_gizi_cannot_mark_delivered_food_as_consumed()
    {
        // WB-29: Tidak diizinkan tandai consumed (role salah).

        $user = new User();
        $user->role = 'tenaga-gizi'; // Role salah

        $foodConsumption = new FoodConsumption();
        $foodConsumption->status = 'delivered';

        $this->assertFalse($this->policy->markAsConsumed($user, $foodConsumption));
    }

    /** @test */
    public function ahli_gizi_cannot_mark_planned_food_as_consumed()
    {
        // WB-30: Tidak diizinkan tandai consumed (status bukan delivered).

        $user = new User();
        $user->role = 'ahli-gizi';

        $foodConsumption = new FoodConsumption();
        $foodConsumption->status = 'planned'; // Status salah

        $this->assertFalse($this->policy->markAsConsumed($user, $foodConsumption));
    }
}