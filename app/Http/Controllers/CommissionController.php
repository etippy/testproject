<?php

namespace App\Http\Controllers;

use App\Services\CommissionService;
use App\Helpers\ExchangeRateHelper;
use Illuminate\Http\Request;

class CommissionController extends Controller
{
    public function calculateCommissions(Request $request)
    {
        // Validate the uploaded CSV file.
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt',
        ]);

        // Retrieve the uploaded CSV file.
        $csvFile = $request->file('csv_file');

        // Read the CSV file content.
        $csvContent = file_get_contents($csvFile->getRealPath());

        // Process the CSV file and calculate commission fees.
        $results = $this->processCSVContent($csvContent);

        // Return the results as a view or JSON response, depending on the request type.
        if ($request->expectsJson()) {
            return response()->json($results);
        }

        return view('commissions', ['results' => $results]);
    }
    

    private function processCSVContent(string $csvContent): array
    {
        $commissionService = new CommissionService();
        
        // Split the CSV content into lines.
        $csvLines = explode("\n", trim($csvContent));

        $results = [];

        foreach ($csvLines as $index => $line) {
            $data = str_getcsv($line);
            
            $operationDate = $data[0];
            $userId = $data[1];
            $userType = $data[2];
            $operationType = $data[3];
            $amount = floatval($data[4]);
            $currency = $data[5];

            // Calculate the commission fee.
            $commissionFee = $commissionService->calculateCommissionFee(
                $userType,
                $operationType,
                $amount,
                $currency,
                $exchangeRates[$currency] ?? 1,
                $operationDate,
                (int) $userId
            );

            $results[] = $commissionFee;
        }

        // echo '<pre>';
        //     print_r($results);
        // echo '</pre>';

        // foreach ($results as $fee) {
        //     echo $fee . "<br>";
        // }
        return $results;    
    }




    public function calculateCommissionsTest(Request $request)
    {
        // Validate the uploaded CSV file.
        $request->validate([
            'csv_file_test' => 'required|file|mimes:csv,txt',
        ]);

        // Retrieve the uploaded CSV file.
        $csvFile = $request->file('csv_file_test');

        // Read the CSV file content.
        $csvContent = file_get_contents($csvFile->getRealPath());

        // Process the CSV file and calculate commission fees.
        $results = $this->processCSVContentTest($csvContent);

        // Return the results as a view or JSON response, depending on the request type.
        if ($request->expectsJson()) {
            return response()->json($results);
        }

        return view('commissions', ['testResults' => $results]);
    }
    

    private function processCSVContentTest(string $csvContent): array
    {
        $commissionService = new CommissionService();
        
        // Split the CSV content into lines.
        $csvLines = explode("\n", trim($csvContent));

        $results = [];

        foreach ($csvLines as $index => $line) {
            $data = str_getcsv($line);
            
            $operationDate = $data[0];
            $userId = $data[1];
            $userType = $data[2];
            $operationType = $data[3];
            $amount = floatval($data[4]);
            $currency = $data[5];

            // Calculate the commission fee.
            $commissionFee = $commissionService->calculateCommissionFeeTest(
                $userType,
                $operationType,
                $amount,
                $currency,
                $exchangeRates[$currency] ?? 1,
                $operationDate,
                (int) $userId
            );

            $results[] = $commissionFee;
        }

        return $results;    
    }

}
