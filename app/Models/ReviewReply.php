<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReviewReply extends Model {

  protected $table = 'sp_replytoreview';

  /**
   * Defines the belongsTo relationship
   *
   * @return mixed
   */
  public function user()
  {
    return $this->belongsTo('App\Models\User','user_id');
  }

  public function serviceprovider()
  {
    return $this->belongsTo('App\Models\Service','service_provider_id');
  }

  public function review()
  {
     return $this->belongsTo('App\Models\Review','sp_review_id');
  }


}