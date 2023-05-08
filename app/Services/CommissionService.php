<?php

namespace App\Services;
use Illuminate\Support\Facades\Http;

use DateTime;

class CommissionService
{
    const PRIVATE_WITHDRAW_FEE_PERCENT = 0.003; // 0.3%
    const BUSINESS_WITHDRAW_FEE_PERCENT = 0.005; // 0.5%
    const DEPOSIT_FEE = 0.03;
    const WEEKLY_WITHDRAW_AMOUNT_LIMIT = 1000;
    const WEEKLY_WITHDRAW_FREE_OPERATIONS = 3;
    const API_URL = 'https://developers.paysera.com/tasks/api/currency-exchange-rates';

    private $userWeeklyWithdrawals = [];
    private $userWeeklyOperationCount = [];

    public function calculateCommissionFee(string $userType, string $operationType, float $amount, string $currency, float $exchangeRate, string $operationDate, int $userId): float
    {   
        $fee = 0;
        // Fetch the exchange rates from the API.
        $response = Http::get(self::API_URL);

        // If the API request fails, return an empty array.
        if ($response->failed()) {
            return [];
        }

        // Decode the JSON response to an array.
        $exchangeRates = $response->json();

        // Check the operation type and user type, and calculate the fee accordingly
        if($operationType === 'deposit'){
            $fee = ($amount * self::DEPOSIT_FEE) / 100;
        }else{
            if($userType === 'business'){
                $fee = $amount * self::BUSINESS_WITHDRAW_FEE_PERCENT;
            }else{
                $fee = $this->calculatePrivateWithdrawFee($amount, $operationDate, $userId, $currency, $exchangeRates);
            }
        }
        // Round up the fee to 2 decimal places
        $roundedFee = ceil($fee * pow(10, 2)) / pow(10, 2);

        return $fee;
    }


    //for testing
    public function calculateCommissionFeeTest(string $userType, string $operationType, float $amount, string $currency, float $exchangeRate, string $operationDate, int $userId): float
    {   
        $fee = 0;

        $exchangeRates = [
            'USD' => 1.1497,
            'JPY' => 129.53,
        ];

        if($operationType === 'deposit'){
            $fee = ($amount * self::DEPOSIT_FEE) / 100;
        }else{
            if($userType === 'business'){
                $fee = $amount * self::BUSINESS_WITHDRAW_FEE_PERCENT;
            }else{
                $fee = $this->calculatePrivateWithdrawFeeTest($amount, $operationDate, $userId, $currency, $exchangeRates);
            }
        }

        $roundedFee = ceil($fee * pow(10, 2)) / pow(10, 2);

        return $fee;
    }
    


    private function calculatePrivateWithdrawFee(float $amount, string $operationDate, int $userId, string $currency, array $exchangeRates): float
    {
        $fee = 0;
        $devider = 0;
        $weekStart = (new DateTime($operationDate))->modify('monday this week')->format('Y-m-d');
        
        $totalWithdrawnThisWeek = $this->userWeeklyWithdrawals[$userId][$weekStart] ?? 0;

        $freeAmountLimit = max(self::WEEKLY_WITHDRAW_AMOUNT_LIMIT - $totalWithdrawnThisWeek, 0);
        $currencyRate = $exchangeRates['rates'][$currency] ?? 1;
        $amountInEur = $amount / $currencyRate;
        
        //dd($currencyRate);
        $freeAmountLimitInCurrency = $freeAmountLimit * $currencyRate;
        $operationCountThisWeek = $this->userWeeklyOperationCount[$userId][$weekStart] ?? 0;
        if ($operationCountThisWeek < self::WEEKLY_WITHDRAW_FREE_OPERATIONS) {
            $excessAmount = max($amount - $freeAmountLimitInCurrency, 0);
            $feeInCurrency = $excessAmount * self::PRIVATE_WITHDRAW_FEE_PERCENT;
            //echo  $feeInCurrency . '<br>';
        } else {
            $feeInCurrency = $amount * self::PRIVATE_WITHDRAW_FEE_PERCENT;
        }
        
    
        //As JPY has no decimal it has to be rounded without decimales 
        if( $currency === 'JPY' ){ $devider = 1; }else{ $devider = 100; }

        $fee = ceil( $feeInCurrency * $devider ) / $devider;
        $this->userWeeklyWithdrawals[$userId][$weekStart] = $totalWithdrawnThisWeek + $amountInEur;
        $this->userWeeklyOperationCount[$userId][$weekStart] = $operationCountThisWeek + 1;

        return $fee;
    }


    //for testing
    private function calculatePrivateWithdrawFeeTest(float $amount, string $operationDate, int $userId, string $currency, array $exchangeRates): float
    {
        $fee = 0;
        $devider = 0;
        $weekStart = (new DateTime($operationDate))->modify('monday this week')->format('Y-m-d');
        
        $totalWithdrawnThisWeek = $this->userWeeklyWithdrawals[$userId][$weekStart] ?? 0;

        $freeAmountLimit = max(self::WEEKLY_WITHDRAW_AMOUNT_LIMIT - $totalWithdrawnThisWeek, 0);
        $currencyRate = $exchangeRates[$currency] ?? 1;
        $amountInEur = $amount / $currencyRate;
        
        $freeAmountLimitInCurrency = $freeAmountLimit * $currencyRate;
        $operationCountThisWeek = $this->userWeeklyOperationCount[$userId][$weekStart] ?? 0;
        if ($operationCountThisWeek < self::WEEKLY_WITHDRAW_FREE_OPERATIONS) {
            $excessAmount = max($amount - $freeAmountLimitInCurrency, 0);
            $feeInCurrency = $excessAmount * self::PRIVATE_WITHDRAW_FEE_PERCENT;
        } else {
            $feeInCurrency = $amount * self::PRIVATE_WITHDRAW_FEE_PERCENT;
        }
    
        //As JPY has no decimal it has to be rounded without decimales 
        if( $currency === 'JPY' ){ $devider = 1; }else{ $devider = 100; }

        $fee = ceil( $feeInCurrency * $devider ) / $devider;
        $this->userWeeklyWithdrawals[$userId][$weekStart] = $totalWithdrawnThisWeek + $amountInEur;
        $this->userWeeklyOperationCount[$userId][$weekStart] = $operationCountThisWeek + 1;

        return $fee;
    }

}
