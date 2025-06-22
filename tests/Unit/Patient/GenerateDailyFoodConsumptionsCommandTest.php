<?php

namespace Tests\Unit;

use App\Console\Commands\GenerateDailyFoodConsumptions;
use App\Models\Patient;
use App\Models\Menu;
use App\Models\FoodConsumption;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase; // Pastikan ini base class Anda
use Mockery; // Penting untuk Mockery
use Symfony\Component\Console\Tester\CommandTester;
use Carbon\Carbon; // Pastikan Carbon diimpor

/**
 * Pastikan Anda meng-extend Tests\TestCase jika Anda memiliki file tersebut di root tests/
 * Contoh: class GenerateDailyFoodConsumptionsCommandTest extends \Tests\TestCase
 * Jika tidak, tetap extends BaseTestCase seperti ini dan pastikan trait CreatesApplication ada
 */
class GenerateDailyFoodConsumptionsCommandTest extends BaseTestCase
{
    // Ini penting untuk mengintegrasikan Mockery dengan PHPUnit dan memastikan pembersihan mock
    use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

    /**
     * Metode ini berjalan sebelum setiap test.
     */
    protected function setUp(): void
    {
        parent::setUp();

        // **PENTING:** Atur Mockery agar melemparkan exception untuk ekspektasi yang tidak terpenuhi
        // ini default behaviour, tapi bagus untuk memastikan
        // Mockery::shouldAllowMockingMethodsUnnecessarily(false);
        // Mockery::shouldIgnoreMissingExceptions(false);

        // Mock Facades yang digunakan dalam command
        Log::shouldReceive('info')->andReturnSelf();
        Log::shouldReceive('warning')->andReturnSelf();
        Log::shouldReceive('error')->andReturnSelf();

        // Ini akan membersihkan semua mock di akhir test.
        // Tidak perlu Mockery::close() manual di sini jika menggunakan MockeryPHPUnitIntegration
    }

    /**
     * Metode ini berjalan setelah setiap test.
     */
    protected function tearDown(): void
    {
        // Pastikan waktu Carbon di-reset setelah setiap test
        Carbon::setTestNow(null);
        parent::tearDown();

        // Mockery::close() dipanggil secara otomatis oleh MockeryPHPUnitIntegration
    }

    /** @test */
    public function it_generates_food_consumptions_for_active_patients_with_valid_schedule()
    {
        // WB-31: Generate konsumsi untuk pasien aktif dengan jadwal valid.

        // Tanggal yang diuji
        $testDate = '2025-06-14';
        Carbon::setTestNow(Carbon::parse($testDate)); // Mock waktu saat ini untuk konsistensi

        // ====================================================================
        // MOCK STATIC CALLS PADA ELOQUENT MODELS (Penting: gunakan 'overload')
        // Ini adalah cara Mockery mengambil alih metode statis (::where, ::find)
        // ====================================================================
        $patientStaticMock = Mockery::mock('overload:' . Patient::class);
        $menuStaticMock = Mockery::mock('overload:' . Menu::class);
        $foodConsumptionStaticMock = Mockery::mock('overload:' . FoodConsumption::class);

        // 1. Mock Patient::where()->get()
        $patientStaticMock->shouldReceive('where->get')
                          ->once()
                          ->andReturn(collect([
                              (object)['id' => 1, 'nama_pasien' => 'Pasien Test', 'tipe_pasien' => 'Normal', 'status_pasien' => 'aktif']
                          ]));

        // 2. Mock Menu::find()
        $menuInstanceMockPagi = Mockery::mock(Menu::class)->makePartial(); // Instance mock untuk Menu pagi
        $menuInstanceMockPagi->id = 10;
        $menuInstanceMockPagi->nama = 'Menu Test Pagi';
        $menuInstanceMockPagi->kalori = 300;
        $menuInstanceMockPagi->total_protein = 20;
        $menuInstanceMockPagi->total_karbohidrat = 30;
        $menuInstanceMockPagi->total_lemak = 10;

        $menuInstanceMockSiang = Mockery::mock(Menu::class)->makePartial(); // Instance mock untuk Menu siang
        $menuInstanceMockSiang->id = 11;
        $menuInstanceMockSiang->nama = 'Menu Test Siang';
        $menuInstanceMockSiang->kalori = 400;
        $menuInstanceMockSiang->total_protein = 25;
        $menuInstanceMockSiang->total_karbohidrat = 35;
        $menuInstanceMockSiang->total_lemak = 15;
        
        $menuStaticMock->shouldReceive('find')
                       ->with(10)
                       ->andReturn($menuInstanceMockPagi)
                       ->ordered(); // Penting untuk ekspektasi berurutan
        $menuStaticMock->shouldReceive('find')
                       ->with(11)
                       ->andReturn($menuInstanceMockSiang)
                       ->ordered(); // Penting untuk ekspektasi berurutan


        // 3. Mock DB::table()->join()->where()->...->get()
        DB::shouldReceive('table->join->where->where->where->where->select->get')
          ->once()
          ->andReturn(collect([
              (object)['menu_id' => 10, 'waktu_makan' => 'pagi'],
              (object)['menu_id' => 11, 'waktu_makan' => 'siang']
          ]));

        // 4. Mock FoodConsumption::updateOrCreate()
        // Ini akan dipanggil dua kali, untuk menu pagi dan menu siang
        $foodConsumptionStaticMock->shouldReceive('updateOrCreate')
                                  ->times(2) // Total 2 panggilan
                                  ->andReturnUsing(function ($attributes, $values) use ($testDate) {
                                      // Logika verifikasi untuk setiap panggilan updateOrCreate
                                      // Ini akan dipanggil untuk setiap item dari jadwalMenus
                                      $this->assertArrayHasKey('patient_id', $attributes);
                                      $this->assertEquals(1, $attributes['patient_id']);
                                      $this->assertArrayHasKey('menu_id', $attributes);
                                      $this->assertContains($attributes['menu_id'], [10, 11]); // Pastikan menu_id adalah salah satu dari yang diharapkan
                                      $this->assertEquals($testDate, $attributes['tanggal']);
                                      
                                      $this->assertArrayHasKey('status', $values);
                                      $this->assertEquals('planned', $values['status']);
                                      $this->assertArrayHasKey('nama_makanan', $values);
                                      $this->assertArrayHasKey('kalori', $values);
                                      // Assert nilai spesifik berdasarkan menu_id
                                      if ($attributes['menu_id'] === 10) {
                                          $this->assertEquals('pagi', $attributes['waktu_makan']);
                                          $this->assertEquals('Menu Test Pagi', $values['nama_makanan']);
                                          $this->assertEquals(300, $values['kalori']);
                                      } elseif ($attributes['menu_id'] === 11) {
                                          $this->assertEquals('siang', $attributes['waktu_makan']);
                                          $this->assertEquals('Menu Test Siang', $values['nama_makanan']);
                                          $this->assertEquals(400, $values['kalori']);
                                      }

                                      // Simulasikan pembuatan instance baru
                                      $instance = new FoodConsumption();
                                      $instance->fill($attributes);
                                      $instance->fill($values);
                                      $instance->wasRecentlyCreated = true;
                                      return $instance;
                                  });


        // Inisialisasi Command
        $command = new GenerateDailyFoodConsumptions();
        $tester = new CommandTester($command);

        // Eksekusi Command
        $tester->execute(['date' => $testDate]);

        // Assertions pada output konsol
        $output = $tester->getDisplay();
        $this->assertStringContainsString('Generating daily food consumptions for ' . $testDate, $output);
        $this->assertStringContainsString('Generated 2 new food consumption entries for ' . $testDate, $output);

        // Verifikasi Log (opsional, jika Anda ingin pastikan log dipanggil)
        Log::shouldHaveReceived('info')->with('Starting daily food consumption generation for ' . $testDate);
        Log::shouldHaveReceived('info')->with('Finished daily food consumption generation. Total new entries: 2.');
    }
}