<?php
namespace Tests\Unit\Patient;

use Tests\TestCase;
use Illuminate\Http\Request; // Asumsi Anda menggunakan Laravel Request object

class CalculateBMRTest extends TestCase
{
    /**
     * Helper function to create a mock Request object.
     * @param float $berat_badan
     * @param float $tinggi_badan
     * @param int $usia
     * @param string $jenis_kelamin
     * @return Request
     */
    private function createRequest(
        float $berat_badan,
        float $tinggi_badan,
        int $usia,
        string $jenis_kelamin
    ): Request {
        return new Request([
            'berat_badan' => $berat_badan,
            'tinggi_badan' => $tinggi_badan,
            'usia' => $usia,
            'jenis_kelamin' => $jenis_kelamin,
        ]);
    }

    /**
     * @dataProvider provideMaleBMRData
     */
    public function testCalculateBMR_Pria($berat, $tinggi, $usia, $expectedBMR)
    {
        $request = $this->createRequest($berat, $tinggi, $usia, 'pria');
        $bmr = $this->callPrivateMethod('calculateBMR', [$request]);
        $this->assertEquals($expectedBMR, $bmr);
    }

    /**
     * Data provider for male BMR calculations.
     */
    public static function provideMaleBMRData(): array
    {
        return [
            'Pria Dewasa Normal' => [70, 175, 30, 1649], // (10*70) + (6.25*175) - (5*30) + 5 = 700 + 1093.75 - 150 + 5 = 1648.75 -> 1649 (rounded)
            'Pria Muda Aktif' => [75, 180, 25, 1810],   // (10*75) + (6.25*180) - (5*25) + 5 = 750 + 1125 - 125 + 5 = 1755 -> 1755 (rounded)
            'Pria Tua' => [65, 170, 60, 1373],        // (10*65) + (6.25*170) - (5*60) + 5 = 650 + 1062.5 - 300 + 5 = 1417.5 -> 1418 (rounded)
            'Pria Berat Ringan' => [55, 160, 28, 1425], // (10*55) + (6.25*160) - (5*28) + 5 = 550 + 1000 - 140 + 5 = 1415 -> 1415 (rounded)
        ];
    }

    /**
     * @dataProvider provideFemaleBMRData
     */
    public function testCalculateBMR_Wanita($berat, $tinggi, $usia, $expectedBMR)
    {
        $request = $this->createRequest($berat, $tinggi, $usia, 'wanita');
        $bmr = $this->callPrivateMethod('calculateBMR', [$request]);
        $this->assertEquals($expectedBMR, $bmr);
    }

    /**
     * Data provider for female BMR calculations.
     */
    public static function provideFemaleBMRData(): array
    {
        return [
            'Wanita Dewasa Normal' => [60, 165, 28, 1342], // (10*60) + (6.25*165) - (5*28) - 161 = 600 + 1031.25 - 140 - 161 = 1330.25 -> 1330 (rounded)
            'Wanita Muda Aktif' => [55, 160, 22, 1264],   // (10*55) + (6.25*160) - (5*22) - 161 = 550 + 1000 - 110 - 161 = 1279 -> 1279 (rounded)
            'Wanita Tua' => [50, 155, 55, 966],         // (10*50) + (6.25*155) - (5*55) - 161 = 500 + 968.75 - 275 - 161 = 1032.75 -> 1033 (rounded)
            'Wanita Tinggi' => [68, 170, 35, 1391],     // (10*68) + (6.25*170) - (5*35) - 161 = 680 + 1062.5 - 175 - 161 = 1406.5 -> 1407 (rounded)
        ];
    }

    /**
     * Test case for edge values (e.g., minimum valid age, weight, height).
     */
    public function testCalculateBMR_EdgeCases()
    {
        // Pria dengan nilai minimum (misal: usia 1 tahun, berat dan tinggi realistis untuk balita)
        $requestMaleEdge = $this->createRequest(10, 80, 1, 'pria');
        $bmrMaleEdge = $this->callPrivateMethod('calculateBMR', [$requestMaleEdge]);
        // Expected: (10*10) + (6.25*80) - (5*1) + 5 = 100 + 500 - 5 + 5 = 600
        $this->assertEquals(600, $bmrMaleEdge);

        // Wanita dengan nilai minimum
        $requestFemaleEdge = $this->createRequest(10, 80, 1, 'wanita');
        $bmrFemaleEdge = $this->callPrivateMethod('calculateBMR', [$requestFemaleEdge]);
        // Expected: (10*10) + (6.25*80) - (5*1) - 161 = 100 + 500 - 5 - 161 = 434
        $this->assertEquals(434, $bmrFemaleEdge);
    }

    /**
     * Test case with zero values (though input validation should ideally handle this).
     */
    public function testCalculateBMR_ZeroValues()
    {
        // Zero values (not realistic, but good for robustness)
        $requestZero = $this->createRequest(0, 0, 0, 'pria');
        $bmrZero = $this->callPrivateMethod('calculateBMR', [$requestZero]);
        // Expected: (10*0) + (6.25*0) - (5*0) + 5 = 5
        $this->assertEquals(5, $bmrZero);

        $requestZeroFemale = $this->createRequest(0, 0, 0, 'wanita');
        $bmrZeroFemale = $this->callPrivateMethod('calculateBMR', [$requestZeroFemale]);
        // Expected: (10*0) + (6.25*0) - (5*0) - 161 = -161
        $this->assertEquals(-161, $bmrZeroFemale);
    }

    /**
     * Test case for non-numeric input (assuming Request handles this gracefully or validation is done prior).
     * For demonstration, we'll assume the request object might pass non-numeric data
     * if not strictly type-hinted or validated at a higher level.
     * In a real scenario, you'd likely test input validation separately.
     */
    public function testCalculateBMR_InvalidJenisKelamin()
    {
        $request = $this->createRequest(70, 170, 30, 'invalid_gender');
        // Since the function has an 'else' for 'wanita', it will default to the female formula
        // for any non-'pria' input. This highlights a potential area for improvement:
        // explicit handling of invalid gender input.
        $bmr = $this->callPrivateMethod('calculateBMR', [$request]);
        // Expected for 'wanita': (10*70) + (6.25*170) - (5*30) - 161 = 700 + 1062.5 - 150 - 161 = 1451.5 -> 1452
        $this->assertEquals(1452, $bmr);
    }

    /**
     * Helper method to call private or protected methods.
     * Useful for testing private functions that cannot be directly accessed.
     * In a real application, consider refactoring private methods if they become too complex
     * to test easily, making them protected or public (or extracting them into separate classes).
     *
     * @param string $methodName
     * @param array $args
     * @return mixed
     */
    private function callPrivateMethod(string $methodName, array $args = [])
    {
        // Create an instance of the class containing the private function.
        // Replace 'YourClassName' with the actual class name where calculateBMR resides.
        // For example, if it's in a Controller named 'BMRController':
        $object = new class {
            // Paste your original calculateBMR function here for the anonymous class to use
            private function calculateBMR(Request $request)
            {
                $berat = $request->berat_badan; // dalam kg
                $tinggi = $request->tinggi_badan; // dalam cm
                $usia = $request->usia; // dalam tahun
                $jenis_kelamin = $request->jenis_kelamin; // 'pria' atau 'wanita'

                if ($jenis_kelamin === 'pria') {
                    // Rumus Mifflin-St Jeor untuk pria
                    $bmr = (10 * $berat) + (6.25 * $tinggi) - (5 * $usia) + 5;
                } else {
                    // Rumus Mifflin-St Jeor untuk wanita
                    $bmr = (10 * $berat) + (6.25 * $tinggi) - (5 * $usia) - 161;
                }

                return round($bmr);
            }
        };

        $reflection = new \ReflectionClass($object);
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true); // Make the private method accessible

        return $method->invokeArgs($object, $args);
    }
}