<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model {

  protected $table = 'bc_commentable';

  protected $hidden = [ 'dump' ];

  /**
   * The fields that are fillable
   *
   * @var array
   */
  protected $fillable = array(
    'commentable_type',
    'commentable_id',
    'comment',
    'user_id',
  );


  public function getUniqueImagePath($extension=null){

    if(!$extension){
      $extension = 'jpg';
    }
        $unique_id = uniqid('comment_');
        $path = $unique_id.'.'.$extension;
        return $path;

  }

  /**
   * Defines the belongsTo relationship
   *
   * @return mixed
   */
  public function user()
  {
    return $this->belongsTo('App\Models\User','user_id');
  }

  /**
   * Defines the belongsTo relationship
   *
   * @return mixed
   */
  public function question()
  {
    return $this->belongsTo('App\Models\Question','commentable_id');
  }

  /**
   * Defines the belongsTo relationship
   *
   * @return mixed
   */
  public function article()
  {
    return $this->belongsTo('App\Models\Article','commentable_id');
  }

  /**
   * Defines the belongsTo relationship
   *
   * @return mixed
   */
  public function feedpost()
  {
    return $this->belongsTo('App\Models\FeedPost','commentable_id');
  }

  /**
   * Defines the belongsTo relationship
   *
   * @return mixed
   */
  public function review()
  {
    return $this->belongsTo('App\Models\Review','commentable_id');
  }

  public function hashTags(){
      return $this->morphToMany('App\Models\HashTag', 'hash_taggable')->withTimestamps();
  }

  public function likes()
    {
        return $this->hasMany('App\Models\PostLike', 'element_id')->where('element_type', FeedPost::COMMENT);
    }

  public function likeCounts(){

        return $this->likes()->selectRaw('element_id, count(*) as aggregate')->groupBy('element_id');
    }

  // altering 1 as yes and 0 as no for adminpanel
  public function getExpertAnswerAttribute(){
    if(isset($this->user) && $this->user->expert == 1){
        return 'Yes';
    }

    return 'No';
  }

  // fetching question for adminpanel
  public function getQuestAttribute(){
    
    if(isset($this->question)){
        return $this->question->question;
    }

    return '--';
  }

  // fetching user name for adminpanel
  public function getUserNameAttribute(){
    
    if(isset($this->user)){
        return $this->user->name;
    }

    return '--';
  }



}