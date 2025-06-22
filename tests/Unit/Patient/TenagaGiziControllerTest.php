<?php

namespace Tests\Unit;

use App\Http\Controllers\TenagaGiziController;
use App\Models\FoodConsumption;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log; // Mock Log facade
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Mockery;

class TenagaGiziControllerTest extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Mockery::close();

        Log::shouldReceive('info')->andReturnSelf();
        Log::shouldReceive('warning')->andReturnSelf();
        Log::shouldReceive('error')->andReturnSelf();
    }

    /** @test */
    public function it_marks_food_consumption_as_delivered_correctly()
    {
        // WB-18: Tandai sebagai `delivered`.

        // Mock Request (tidak ada body yang spesifik untuk metode ini)
        $request = new Request();

        // Mock FoodConsumption instance sebagai PARTIAL MOCK
        $foodConsumptionMock = Mockery::mock(FoodConsumption::class)->makePartial(); // <--- KOREKSI INI
        $foodConsumptionMock->id = 1;
        $foodConsumptionMock->status = 'planned'; // Status awal
        
        // Expect save() method to be called after status change
        // Metode setAttribute tidak perlu di-mock secara eksplisit lagi
        $foodConsumptionMock->shouldReceive('save')->once()->andReturn(true); // Simulate successful save

        // Mock User dan Auth untuk Policy
        $user = new User();
        $user->role = 'tenaga-gizi';
        Auth::shouldReceive('user')->andReturn($user);
        Auth::shouldReceive('id')->andReturn(1);

        // Mock controller dan authorize method
        $controller = Mockery::mock(TenagaGiziController::class)->makePartial();
        $controller->shouldAllowMockingProtectedMethods();
        $controller->shouldReceive('authorize')->once()->with('markAsDelivered', $foodConsumptionMock)->andReturn(true);

        // Panggil metode yang diuji
        $response = $controller->markAsDelivered($request, $foodConsumptionMock);

        // Assertions
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString(
            json_encode(['message' => 'Makanan berhasil ditandai sebagai diantar.']),
            $response->getContent()
        );
    }
}