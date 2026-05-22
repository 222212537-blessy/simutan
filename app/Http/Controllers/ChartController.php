<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use ConsoleTVs\Charts\Classes\Chartjs\Chart;

class ChartController extends Controller
{
    public function index()
    {
        $chart = new Chart;
        
        $chart->labels(['January', 'February', 'March', 'April', 'May', 'June', 'July'])
              ->dataset('Monthly Sales', 'bar', [65, 59, 80, 81, 56, 55, 40])
              ->backgroundColor('#FF6384')
              ->borderColor('#FF6384')
              ->color('#FF6384');
        
        return view('chart', ['chart' => $chart]);
    }
}
