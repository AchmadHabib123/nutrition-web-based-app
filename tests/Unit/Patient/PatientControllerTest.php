<?php

namespace Tests\Unit;

use App\Http\Controllers\PatientController;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Support\Facades\Auth; // Mock Auth facade
use Illuminate\Auth\Access\AuthorizationException; 
use App\Models\FoodConsumption;
use Illuminate\Http\Request;
use Illuminate\Support\Collection; // Penting untuk mock Collection
// use PHPUnit\Framework\TestCase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Mockery; // Menggunakan Mockery untuk mock facade jika diperlukan
use Illuminate\Support\Facades\Log; // Mock Log facade

class PatientControllerTest extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        // Reset Mockery setelah setiap test
        Mockery::close();

        // Mock the Log facade if it's used directly in the controller and we don't want real logging
        Log::shouldReceive('info')->andReturnSelf();
        Log::shouldReceive('warning')->andReturnSelf();
        Log::shouldReceive('error')->andReturnSelf();
    }

    /** @test */
    // public function it_aggregates_consumed_calories_and_macros_correctly()
    // {
    //     // WB-08: Agregasi kalori/makro terkonsumsi (semua item consumed).

    //     // Mock Request
    //     $request = Request::create('/ahli-gizi/patients/filter', 'GET', ['date' => '2025-06-14']);

    //     // Mock data FoodConsumption yang sudah dikonsumsi
    //     $consumedFood1 = (object)[
    //         'patient_id' => 1, 'actual_kalori' => 100, 'actual_protein' => 10,
    //         'actual_karbohidrat' => 20, 'actual_lemak' => 5, 'status' => 'consumed'
    //     ];
    //     $consumedFood2 = (object)[
    //         'patient_id' => 1, 'actual_kalori' => 150, 'actual_protein' => 15,
    //         'actual_karbohidrat' => 25, 'actual_lemak' => 8, 'status' => 'consumed'
    //     ];
    //     $consumedFood3 = (object)[
    //         'patient_id' => 2, 'actual_kalori' => 200, 'actual_protein' => 20,
    //         'actual_karbohidrat' => 30, 'actual_lemak' => 10, 'status' => 'consumed'
    //     ];

    //     // Mock FoodConsumption model
    //     // Kita perlu mock metode where, whereDate, where, get, dan sum
    //     $foodConsumptionMock = Mockery::mock('overload:' . FoodConsumption::class);
    //     $foodConsumptionMock->shouldReceive('where->whereDate->where->get')
    //                         ->andReturnUsing(function ($patientId, $date, $status) use ($consumedFood1, $consumedFood2, $consumedFood3) {
    //                             // Simulate filtering by patient_id and status
    //                             $filtered = collect([$consumedFood1, $consumedFood2, $consumedFood3])->filter(function($food) use ($patientId, $status) {
    //                                 return $food->patient_id === $patientId && $food->status === $status;
    //                             });
    //                             return $filtered;
    //                         });

    //     // Mock Patient model
    //     $patient1 = (object)['id' => 1, 'nama_pasien' => 'Patient A', 'status_pasien' => 'aktif', 'kalori_harian' => 2000];
    //     $patient2 = (object)['id' => 2, 'nama_pasien' => 'Patient B', 'status_pasien' => 'aktif', 'kalori_harian' => 2500];

    //     $patientMock = Mockery::mock('overload:' . Patient::class);
    //     $patientMock->shouldReceive('where->get')->andReturn(collect([$patient1, $patient2]));

    //     // Inisialisasi Controller
    //     $controller = new PatientController();

    //     // Panggil metode yang diuji
    //     $response = $controller->filterByDate($request);

    //     // Assertions
    //     $responseData = $response->getData(true); // true untuk associative array

    //     $this->assertEquals(4500, $responseData['summary']['total_target_calories']); // 2000 + 2500
    //     $this->assertEquals(450, $responseData['summary']['total_consumed_calories']); // 100 + 150 + 200
    //     $this->assertEquals(45, $responseData['summary']['total_consumed_protein']); // 10 + 15 + 20
    //     $this->assertEquals(75, $responseData['summary']['total_consumed_carbs']); // 20 + 25 + 30
    //     $this->assertEquals(23, $responseData['summary']['total_consumed_fat']); // 5 + 8 + 10

    //     // Pastikan patientData juga memiliki properti kalori_makanan_hari_ini yang benar
    //     $this->assertEquals(250, $responseData['patients'][0]['kalori_makanan_hari_ini']); // Pasien A: 100 + 150
    //     $this->assertEquals(200, $responseData['patients'][1]['kalori_makanan_hari_ini']); // Pasien B: 200
    // }
    public function it_validates_food_consumption_as_consumed_full_correctly()
    {
        // WB-09: Validasi: Habis Semua.

        // Mock Request
        $request = Request::create('/validate', 'POST', [
            'final_status' => 'consumed',
            'actual_kalori' => 200,
            'actual_protein' => 20,
            'actual_karbohidrat' => 30,
            'actual_lemak' => 10,
            'notes' => 'Habis semua test',
            'bahan_consumptions' => [ // <--- BERIKAN DATA DUMMY YANG VALID DI SINI
                [
                    'bahan_id' => 1, // ID dummy bahan makanan
                    'status' => 'consumed_full', // Contoh status
                    'sisa_gram' => 0 // Contoh sisa gram
                ]
            ]
        ]);

        // Mock Menu (jika FoodConsumption.menu digunakan dalam perhitungan atau validasi)
        // Jika FoodConsumption memiliki kolom kalori langsung, ini tidak terlalu penting
        $menuMock = Mockery::mock(Menu::class)->makePartial();
        $menuMock->kalori = 200;
        $menuMock->total_protein = 20;
        $menuMock->total_karbohidrat = 30;
        $menuMock->total_lemak = 10;

        // Mock FoodConsumption instance sebagai PARTIAL MOCK
        $foodConsumptionMock = Mockery::mock(FoodConsumption::class)->makePartial(); // <--- KOREKSI INI
        $foodConsumptionMock->id = 1;
        $foodConsumptionMock->status = 'delivered'; // Status awal
        $foodConsumptionMock->menu_id = 1; // Penting jika ada relasi ke menu
        $foodConsumptionMock->menu = $menuMock; // Set relasi menu pada mock

        // Expect update method to be called with correct data
        $foodConsumptionMock->shouldReceive('update')
                            ->once()
                            ->with([
                                'status' => 'consumed',
                                'actual_kalori' => 200.00,
                                'actual_protein' => 20.00,
                                'actual_karbohidrat' => 30.00,
                                'actual_lemak' => 10.00,
                                'notes' => 'Habis semua test',
                                // 'consumption_percentage' => null, // Jika ada di fillable
                                // 'updated_at' => Mockery::type(\Carbon\Carbon::class),
                            ])
                            ->andReturn(true); // Simulate successful update

        // Mock User dan Auth untuk Policy
        $user = new User();
        $user->role = 'ahli-gizi';
        Auth::shouldReceive('user')->andReturn($user);
        Auth::shouldReceive('id')->andReturn(1);

        // Mock controller dan authorize method
        $controller = Mockery::mock(PatientController::class)->makePartial();
        $controller->shouldAllowMockingProtectedMethods();
        $controller->shouldReceive('authorize')->once()->with('markAsConsumed', $foodConsumptionMock)->andReturn(true);


        // Panggil metode yang diuji
        $response = $controller->validateConsumption($request, $foodConsumptionMock);

        // Assertions
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString(
            json_encode(['message' => 'Validasi konsumsi berhasil disimpan.', 'food_consumption' => $foodConsumptionMock->toArray()]),
            $response->getContent()
        );
    }
}