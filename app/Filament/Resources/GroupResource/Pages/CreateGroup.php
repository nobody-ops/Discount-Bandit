<?php

namespace App\Filament\Resources\GroupResource\Pages;

use App\Classes\GroupHelper;
use App\Classes\MainStore;
use App\Classes\Stores\Amazon;
use App\Classes\Stores\Argos;
use App\Classes\Stores\Ebay;
use App\Classes\Stores\Walmart;
use App\Classes\URLHelper;
use App\Filament\Resources\GroupResource;
use App\Models\Product;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use PhpParser\Node\Arg;

class CreateGroup extends CreateRecord
{
    protected static string $resource = GroupResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {

        if ($this->data["url_products"]){
            $urls=$this->data["url_products"];
            //make sure all urls are correct
            foreach ($urls as $single_url){
                if ($single_url["url"]){
                    $url=new URLHelper($single_url['url']);
                    if (!MainStore::validate_url($url))
                        $this->halt();
                }
            }
        }

        return parent::mutateFormDataBeforeCreate($data); // TODO: Change the autogenerated stub
    }


    protected function afterCreate()
    {
        if (Arr::exists($this->data , "products"))
            GroupHelper::update_products_records($this->record->id , $this->data["products"]);

        if ($this->data["url_products"]){
            $urls=$this->data["url_products"];
            foreach ($urls as $single_url){
                if ($single_url["url"]){
                    $url=new URLHelper($single_url['url']);
                    if (!MainStore::validate_url($url))
                        $this->halt();
                    else{
                        $product_id=MainStore::create_product($url);
                        GroupHelper::update_group_product_record($this->record->id, $product_id, $single_url["key"]);
                    }
                }

            }

        }
    }
}