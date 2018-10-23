<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use DB;

class RedeemableItem extends Model
{
    protected $table = 'redeemable_items';


    public function claimItems($number=1){

        DB::transaction(function() use ($number){

            $old_items = RedeemableItem::where('id',$this->id)->lists('stock')->get(0);
            
            if($old_items >= $number){

                $new_items = $old_items - $number;
                $updated = RedeemableItem::where('id',$this->id)->update(['stock' => $new_items ]);
                $this->stock = $new_items;
            
            } else {
                throw new \Exception('Item Out of Stock');
            }
        });

    }

    public function hasStock(){
        if($this->stock > 0){
            return true;
        } 
        return false; 
    }

    public function getUniqueImagePath($original_filepath=null)
    {

        $pInfo = pathinfo($original_filepath);
        if(isset($pInfo) && isset($pInfo['extension'])){
            $extension = $pInfo['extension'];
        }else{
            $extension = 'jpg';
        }
        $unique_id = uniqid('redeemableitem_');
        $path = join('/',[ $this->id, $unique_id.'.'.$extension]);
        return $path;
    }
}
