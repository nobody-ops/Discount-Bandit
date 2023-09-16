<?php


use App\Models\Store;
use Illuminate\Support\Facades\DB;



function prepare_multiple_prices_in_table($record){

    $currencies=get_currencies();
    $prices= $record->stores->map(function ($model) use ($currencies) {
        if ($model['pivot']['price'] <= $model->pivot->notify_price)
            $color_string="green";
        else
            $color_string="red";

        return  "<span  style='color:$color_string'>" . $currencies[$model->currency_id] . $model->pivot->price/100 ." </span>";}
    )->implode("<br>");

    return  \Illuminate\Support\Str::of($prices)->toHtmlString();
}


function prepare_multiple_notify_prices_in_table($record){

    $currencies=get_currencies();
    $notify_prices= $record->stores->map(function ($model) use ($currencies) {
        return  $currencies[$model->currency_id]  . $model->pivot->notify_price / 100 . " " ;
    })->implode("<br>");


    return  \Illuminate\Support\Str::of($notify_prices)->toHtmlString();
}


function prepare_multiple_update_in_table($record){
    $dates= $record->stores->map(function ($model) {
        return  "<span>" .  $model->pivot->updated_at."</span>";}
    )->implode("<br>");

    return  \Illuminate\Support\Str::of($dates)->toHtmlString();
}


function get_currencies($currency_id=0){
    $currencies=Cache::get("currencies");
    if (!$currencies)
    {
        $currencies=\App\Models\Currency::all()->pluck('code','id');
    }
    Cache::put("currencies",$currencies , 3600);

    if ($currency_id)
        return $currencies[$currency_id];

    return $currencies;
}

function clear_job($slug=""){
    if ($slug)
        DB::table('jobs')->whereIn('queue', [$slug])->delete();
    else
    {
        $slugs=Store::all()->pluck('slug');
        DB::table('jobs')->whereIn('queue', $slugs)->delete();
    }
}


function get_numbers_only_with_dot($string){
    return preg_replace('/[^0-9.]/', '', $string);
}

