<?php
//                          ~~Vegeta~~
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Models\Feed;

/**
 *      FeedElement Class 
 *  Instance creation of this class will add the Feed Element 
 *  to the user's feed who creates it.
 */

class FeedElement extends Eloquent {
    use GetStream\StreamLaravel\Eloquent\ActivityTrait;

    protected $table = 'pins';
    protected $fillable = array('user_id', 'item_id', 'influencer_id');
    protected $dates = ['deleted_at'];
    
    public function item()
    {
        return $this->belongsTo('Item');
    }
    public function user()
    {
        return $this->belongsTo('User');
    }



}
