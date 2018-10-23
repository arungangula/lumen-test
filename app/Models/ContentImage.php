<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
use Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\File\UploadedFile;


class ContentImage extends Model {

    protected $table = 'bc_content_images';

    const ARTICLE_IMAGE = "Article";

    const CONTEST_IMAGE = "Contest";

    const EVENT_IMAGE = "Event";

    protected $uploadedFile;
    protected $type;
    protected $parentId;

    public function __construct(){

       
    }

    public function setImage(UploadedFile $uploadedFile, $content_type,$content_id){

        $this->uploadedFile = $uploadedFile;
        $this->type = $content_type;
        $this->parentId = $content_id;

    }


    private function getUniqueImageName(){
        $name = uniqid();
        $extension = $this->uploadedFile->getClientOriginalExtension();
        return $name.".".$extension;
    }

    public function saveImage(){
        

        switch($this->type){

            case self::ARTICLE_IMAGE:
                    $path = join('/', array(config('aws.articles_directory'), $this->parentId, $this->getUniqueImageName()));
                break;


            case self::CONTEST_IMAGE:
                    $path = join('/', array(config('aws.contests_directory'), $this->parentId, $this->getUniqueImageName()));
                break;


            case self::EVENT_IMAGE:
                    $path = join('/', array(config('aws.events_directory'), $this->parentId, $this->getUniqueImageName()));
                break;

            default:
                Log::error('Type does not match '.$this->type);
                return;
                break;
        }

        $fileResult = Storage::disk('s3')->put($path , file_get_contents($this->uploadedFile->getRealPath()));
        $this->filepath = $path;
        $this->content_type = $this->type;
        $this->content_id = $this->parentId;
        $this->save();

        return array('id' => $this->id,'path'=> $this->filepath);
    }

    


}
