<?php

namespace App\Imports;

use App\Models\RestaurantMenu;
use App\Models\RestaurantMenuDate;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use DateTime;
use Exception;

class RestaurantMenuImport implements ToCollection
{
    /**
    * @param Collection $collection
    */
    public function collection (Collection $rows)
    {
        
        try {
          
            
            for ($j = 1; $j < 6; $j++) {
                $i = 5;
                $menu = "";
                
                for ($i; $i < 25; $i++) {
                    if (!isset($rows[$i][$j])) {
                        // 셀이 비어있는 경우 무시
                        continue;
                    }
                    if ($i === 5) {
                        $date = Carbon::createFromTimestamp(($rows[$i][$j] - 25569) * 86400);
                    }
                    if ($i === 6) {
                        $menu = "";
                        continue;
                    }
                    $menu = $menu . " " . $rows[$i][$j];

                    if ($i % 6 === 0) {
                        
                        switch ($i) {
                            case 12:
                                $meal_time = 'b';
                                
                                break;
                            case 18:
                                $meal_time = 'l';
                                break;
                            case 24:
                                $meal_time = 'd';
                                break;
                            default:
                                $meal_time = "error";
                        }
                        
                        //라라벨에서는 1주가 금요일부터 시작이라 1주치 전부 같은 주차로 나오게 처리
                        if ($j < 5) {
                            $dateEX = Carbon::parse($date);
                            $weekDate = $dateEX->weekOfMonth;
                        } else {
                            $dateEX = Carbon::parse($date);
                            $weekDate = $dateEX->weekOfMonth;
                            $weekDate = $weekDate - 1;
                        }

                        $dt = Carbon::createFromFormat('Y-m-d H:i:s', $date);
                        $dt->startOfWeek(CarbonInterface::MONDAY);
                        $weekOfMonth = $dt->copy()->weekOfMonth;

                        Log::info('주차 : ' . $weekOfMonth);
                        $year = date('Y', strtotime($date));
                        $month = date('m', strtotime($date));

                        $restaurantMenuDate = RestaurantMenuDate::where('month', $month)->where('year', $year)->where('week',$weekOfMonth)->first();
                        Log::info('날짜Id : ' . $restaurantMenuDate->id);
                        $menuData = new RestaurantMenu([
                            'date_id' => $restaurantMenuDate->id,
                            'date' => $date,
                            'menu' => $menu,
                            'meal_time' => $meal_time,
                        ]);
                        $menuData->save();
                        $menu = "";
                    }
                }
            }
        }catch(Exception $e){
            Log::error('Error during import: ' . $e->getMessage());
        }
        return;
    }
  
}
