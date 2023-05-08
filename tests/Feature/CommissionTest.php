<?php 

namespace Tests\Feature;

use App\Services\CommissionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommissionTest extends TestCase
{
    /**
     * Test commission fee calculation with sample data.
     *
     * @return void
     */
    public function testCommissionFeeCalculation()
	{
	    $commissionService = new CommissionService();    

	    // Sample data.
	    $csvLines = [
	        '2014-12-31,4,private,withdraw,1200.00,EUR',
	        '2015-01-01,4,private,withdraw,1000.00,EUR',
	        '2016-01-05,4,private,withdraw,1000.00,EUR',
	        '2016-01-05,1,private,deposit,200.00,EUR',
	        '2016-01-06,2,business,withdraw,300.00,EUR',
	        '2016-01-06,1,private,withdraw,30000,JPY',
	        '2016-01-07,1,private,withdraw,1000.00,EUR',
	        '2016-01-07,1,private,withdraw,100.00,USD',
	        '2016-01-10,1,private,withdraw,100.00,EUR',
	        '2016-01-10,2,business,deposit,10000.00,EUR',
	        '2016-01-10,3,private,withdraw,1000.00,EUR',
	        '2016-02-15,1,private,withdraw,300.00,EUR',
	    ];

	    // Expected results for each line.
	    $expectedResults = [
	        1.60,
	        3.00,
	        0.00,
	        0.06,
	        1.50,
	        0,
	        0.70,
	        0.30,
	        0.30,
	        3.00,
	        0.00,
	        0.00,
	    ];

	    // Hardcode the exchange rates.
	    $exchangeRates = [
	        'USD' => 1.1497,
	        'JPY' => 129.53,
	    ];

	    foreach ($csvLines as $index => $line) {
	        list($operationDate, $userId, $userType, $operationType, $amount, $currency) = explode(',', $line);

	        $amount = (float)$amount;
	        $userId = (int)$userId;
	        $exchangeRate = $exchangeRates[$currency] ?? 1;

	       

	        // Calculate the commission fee using the CommissionService.
	        $commissionFee = $commissionService->calculateCommissionFee(
	            $userType,
	            $operationType,
	            $amount,
	            $currency,
	            $exchangeRate,
	            $operationDate,
	            $userId
	        );

	        // Assert that the calculated commission fee matches the expected result.
	        $this->assertEqualsWithDelta($expectedResults[$index], $commissionFee, 2, "Line {$index}: Expected {$expectedResults[$index]}, got {$commissionFee}");

	    }
	}

}



?>