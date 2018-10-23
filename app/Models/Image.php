<?php

namespace App\Models;

use Imagick;
use ImagickException;

//Model to handle all the uploads
class Image {

    protected $image;
    protected $type;
    protected $thumbsize;
    protected $normalsize;
    protected $cropParams;

    const GIF = "image/gif";

    const PROFILE_IMAGE = "profile_image";

    const SERVICE_IMAGE = "service_image";

    const ARTICLE_IMAGE = "article_image";

    const ARTICLE_TEXT_IMAGE = "article_text_image";

    const EVENT_IMAGE = "event_image";

    const REVIEW_IMAGE = "review_image";

    const CUSTOM_IMAGE = "custom_image";

    const COLLECTION_IMAGE = "collection_name";

    const PUSH_IMAGE = "push_image";

    const NEWSLETTER_IMAGE = "newsletter_image";

    const REDEEMABLEITEM_IMAGE = 'redeemableitem_image';

    const FEEDPOST_IMAGE = "feedpost_image";

    const QUESTION_IMAGE = "question_image";

    const COMMENT_IMAGE = "comment_image";

    const PACKAGE_IMAGE = "package_image";

    const BANK_DETAIL_IMAGE = "bank_detail_image";

    const ANNOUNCEMENT_IMAGE = "announcement_image";

    const USER_REFERRAL_IMAGE = "user_referral_image";

    const METRIC_IMAGE = "metric_image";

    const BRAND_STORY_IMAGE = "brand_story_image";

    const BRAND_ELEMENT_IMAGE = "brand_element_image";

    function __construct($image_path,$type,$params) {


        if(isset($params['url'])){
            
            try {
                $filehandle = fopen($image_path, 'rb');
                $this->image = new Imagick();
                $this->image->readImageFile($filehandle);  
                fclose($filehandle);    
            } catch (\Exception $e) {
                // above method fails trying different method
                $tmp_img = tempnam(sys_get_temp_dir(), str_replace('/', '', $image_path));
                file_put_contents($tmp_img, file_get_contents($image_path));
                $this->image = new Imagick($tmp_img);
            }
              
        } else {

            $this->image = new Imagick(realpath($image_path));

        }

        if(isset($params['cropParams'])){
            $this->cropParams = $params['cropParams'];
        }
        $this->type = $type;
        switch ($this->type) {

            case self::COLLECTION_IMAGE;
                $this->setCollectionImageSize();
            break;

            case self::SERVICE_IMAGE:
                $this->setServiceImageSize();
            break;

            case self::ARTICLE_IMAGE:
                $this->setArticleImageSize();
            break;

            case self::ARTICLE_TEXT_IMAGE:
                $this->setArticleTextImageSize();
            break;

            case self::EVENT_IMAGE:
                  $this->setEventImageSize();
                break;

            case self::REVIEW_IMAGE:
                  $this->setReviewImageSize();
                break;

            case self::PROFILE_IMAGE:
                $this->setProfileImageSize();
                break;

            case self::PUSH_IMAGE:
                $this->setPushImageSize();
                break;

            case self::FEEDPOST_IMAGE:
                $this->setFeedPostImageSize();
                break;

            case self::QUESTION_IMAGE:
                $this->setQuestionImageSize();
                break;

            case self::COMMENT_IMAGE:
                $this->setCommentImageSize();
                break;

            case self::PACKAGE_IMAGE:
                $this->setPackageImageSize();
                break;

            case self::BANK_DETAIL_IMAGE:
                $this->setBankDetailImageSize();
                break;
            case self::GIF:
                $this->setGifImageSize();
                break;
            case self::ANNOUNCEMENT_IMAGE:
                $this->setAnnouncementImageSize();
                break;
            default:

                break;
        }

    }

    //check if this is an image we can process- Security Issues
    public function isImageValid(){
        $mimeType = $this->image->getImageMimeType();

        if($mimeType == 'image/jpeg' || $mimeType == 'image/jpg' ||  $mimeType == 'image/png' || $mimeType == 'image/gif'){
            return true;
        } else {
            return false;
        }

    }

    public function setCustomImageSize(){

        // $this->image = new Imagick(realpath($file));
        $image_size = $this->image->getImageGeometry();
        $aspect_ratio = $image_size['width']/$image_size['height'];

        if($aspect_ratio <= 0.667)//or $aspect_ratio >= 0.4
        {
            $this->normalsize = [ 'width' =>  $image_size['width'],'height' => $image_size['height'], 'aspect_ratio' => $aspect_ratio];
        }
    }

    public function setPushImageSize(){

        // $this->image = new Imagick(realpath($file));
        $image_size = $this->image->getImageGeometry();
        $aspect_ratio = $image_size['width']/$image_size['height'];

        if($aspect_ratio >= 1.50)//or $aspect_ratio >= 0.4
        {
            $this->normalsize = [ 'width' =>  $image_size['width'],'height' => $image_size['height'], 'aspect_ratio' => $aspect_ratio];
        }
    }

    //SET PROFILE IMAGE SIZE
    public function setEventImageSize(){

        $max_width = config('image.event.normal.max_width');
        $image_size = $this->image->getImageGeometry();
        $aspect_ratio = $image_size['width']/$image_size['height'];
        if($aspect_ratio>=1){

            if($image_size['width'] > config('image.event.normal.max_width')){

                $height = config('image.event.normal.max_width')/$aspect_ratio;
                $this->normalsize = [ 'width' => config('image.event.normal.max_width') ,'height' => $height ];

            } else {

                $this->normalsize = [ 'width' => $image_size['width'] ,'height' => $image_size['height'] ];
            }

              $thumb_height = config('image.event.thumb.max_width')/$aspect_ratio;
              $this->thumbsize = [ 'width' => config('image.event.thumb.max_width') ,'height' => $thumb_height ];

        } else {

            if($image_size['height'] > config('image.event.normal.max_height')){

                $width = $aspect_ratio*config('image.event.normal.max_height');
                $this->normalsize = [ 'width' => $width  ,'height' => config('image.event.normal.max_height') ];

            } else {

                $this->normalsize = [ 'width' => $image_size['width'] ,'height' => $image_size['height'] ];
            }

              $thumb_width = $aspect_ratio*config('image.event.thumb.max_height');
              $this->thumbsize = [ 'width' =>  $thumb_width ,'height' => config('image.event.thumb.max_height') ];
        }

    }

    //SET SERVICE IMAGE SIZE
    public function setServiceImageSize(){


        $image_size = $this->image->getImageGeometry();

        if($image_size['width'] >= $image_size['height']) {
            $ratio = $image_size['width']/$image_size['height'];
            $width = config('image.service.normal.max_width');
            $height = $width/$ratio;
            $thumb_width = config('image.service.thumb.max_width');
            $thumb_height = $thumb_width/$ratio;

            $this->normalsize = [ 'width' => $width ,'height' => $height ];
            $this->thumbsize = [ 'width' => $thumb_width ,'height' => $thumb_height ];
        } else {
            $ratio = $image_size['height']/$image_size['width'];
            $height = config('image.service.normal.max_height');
            $width = $height / $ratio;
            $thumb_height = config('image.service.thumb.max_height');
            $thumb_width = $thumb_height  / $ratio;

            $this->normalsize = [ 'width' => $width ,'height' => $height ];
            $this->thumbsize = [ 'width' => $thumb_width ,'height' => $thumb_height ];
        }

    }

    //SET SERVICE IMAGE SIZE
    public function setGifImageSize(){

        $image_size = $this->image->getImageGeometry();
        $this->normalsize = [ 'width' => $image_size['width'] ,'height' => $image_size['height'] ];
        $this->thumbsize = [ 'width' => $image_size['width'] ,'height' => $image_size['height'] ];
    }


    //SET ARTICLE IMAGE SIZE
    public function setArticleTextImageSize(){

        $max_width = config('image.article_text.normal.max_width');
        $image_size = $this->image->getImageGeometry();
        $aspect_ratio = $image_size['width']/$image_size['height'];
        if($aspect_ratio>=1){

            if($image_size['width'] > config('image.article_text.normal.max_width')){

                $height = config('image.article_text.normal.max_width')/$aspect_ratio;
                $this->normalsize = [ 'width' => config('image.article_text.normal.max_width') ,'height' => $height ];

            } else {

                $this->normalsize = [ 'width' => $image_size['width'] ,'height' => $image_size['height'] ];
            }

        } else {

            if($image_size['height'] > config('image.article_text.normal.max_height')){

                $width = $aspect_ratio*config('image.article_text.normal.max_height');
                $this->normalsize = [ 'width' => $width  ,'height' => config('image.article_text.normal.max_height') ];

            } else {
                $this->normalsize = [ 'width' => $image_size['width'] ,'height' => $image_size['height'] ];
            }
        }
    }

    public function setPackageImageSize()
    {
        $this->thumbsize = [ 'width' => config('image.package_image.thumb.max_width') ,'height' => config('image.package_image.thumb.max_height')];
    }

    public function setBankDetailImageSize()
    {
        $this->thumbsize = [ 'width' => config('image.package_image.thumb.max_width') ,'height' => config('image.package_image.thumb.max_height')];
    }

    //SET ARTICLE IMAGE SIZE
    public function setArticleImageSize(){
        $ratio = config('image.article.ratio');
        $image_size = $this->image->getImageGeometry();
        $width = config('image.article.normal.max_width');
        $height = $width/$ratio;
        $thumb_width = config('image.article.thumb.max_width');
        $thumb_height = $thumb_width/$ratio;

        $this->normalsize = [ 'width' => $width ,'height' => $height ];
        $this->thumbsize = [ 'width' => $thumb_width ,'height' => $thumb_height ];
    }

    //SET COLLECTION IMAGE SIZE

    public function setCollectionImageSize(){

        $ratio = config('image.collection.ratio');
        $image_size = $this->image->getImageGeometry();
        $width = config('image.collection.normal.max_width');
        $height = $width/$ratio;
        $thumb_width = config('image.collection.thumb.max_width');
        $thumb_height = $thumb_width/$ratio;

        $this->normalsize = [ 'width' => $width ,'height' => $height ];
        $this->thumbsize = [ 'width' => $thumb_width ,'height' => $thumb_height ];
    }

    //SET PROFILE IMAGE SIZE - just scale down and crop from center
    public function setProfileImageSize(){

        $this->thumbsize = [ 'width' => config('image.profile.thumb.width') ,'height' => config('image.profile.thumb.height')];
        $this->normalsize = [ 'width' => config('image.profile.normal.width') ,'height' => config('image.profile.normal.height')];

    }

     //SET ARTICLE IMAGE SIZE
    public function setReviewImageSize(){
        $max_width = config('image.review.normal.max_width');
        $image_size = $this->image->getImageGeometry();
        $aspect_ratio = $image_size['width']/$image_size['height'];
        if($aspect_ratio>=1){

            if($image_size['width'] > config('image.review.normal.max_width')){

                $height = config('image.review.normal.max_width')/$aspect_ratio;
                $this->normalsize = [ 'width' => config('image.review.normal.max_width') ,'height' => $height ];

            } else {

                $this->normalsize = [ 'width' => $image_size['width'] ,'height' => $image_size['height'] ];
            }

              $thumb_height = config('image.review.thumb.max_width')/$aspect_ratio;
              $this->thumbsize = [ 'width' => config('image.review.thumb.max_width') ,'height' => $thumb_height ];

        } else {

            if($image_size['height'] > config('image.review.normal.max_height')){

                $width = $aspect_ratio*config('image.review.normal.max_height');
                $this->normalsize = [ 'width' => $width  ,'height' => config('image.review.normal.max_height') ];

            } else {

                $this->normalsize = [ 'width' => $image_size['width'] ,'height' => $image_size['height'] ];
            }

              $thumb_width = $aspect_ratio*config('image.review.thumb.max_height');
              $this->thumbsize = [ 'width' =>  $thumb_width ,'height' => config('image.review.thumb.max_height') ];
        }
    }

    //SET FEEDPOST IMAGE SIZE
    public function setFeedPostImageSize(){
        // $aspect_ratio = config('image.feedpost_image.ratio');
        $image_size = $this->image->getImageGeometry();
        $aspect_ratio = $image_size['width']/$image_size['height'];

        if($image_size['width'] > config('image.feedpost_image.normal.max_width')){
            $height = floor(config('image.feedpost_image.normal.max_width')/$aspect_ratio);
            $this->normalsize = [ 'width' => config('image.feedpost_image.normal.max_width') ,'height' => $height ];
        }
        // elseif($image_size['height'] > config('image.feedpost_image.normal.max_height')){
        //     $width = floor(config('image.feedpost_image.normal.max_width')/$aspect_ratio);
        //     $this->normalsize = [ 'width' => $width ,'height' => config('image.feedpost_image.normal.max_height') ];
        // }
        else{
            $this->normalsize = [ 'width' => $image_size['width'] ,'height' => $image_size['height'] ];
        }
        // dd($this->normalsize, $image_size);
        $width = config('image.feedpost_image.normal.max_width');
        $height = $width/$aspect_ratio;
        $thumb_width = config('image.feedpost_image.thumb.max_width');
        $thumb_height = $thumb_width/$aspect_ratio;

        //$this->normalsize = [ 'width' => $width ,'height' => $height ];
        $this->thumbsize = [ 'width' => $thumb_width ,'height' => $thumb_height ];
    }

    //SET FEEDPOST IMAGE SIZE
    public function setQuestionImageSize(){
        $aspect_ratio = config('image.question_image.ratio');
        $image_size = $this->image->getImageGeometry();
        //$aspect_ratio = $image_size['width']/$image_size['height'];
        if($image_size['width'] > config('image.question_image.normal.max_width')){
            $height = floor(config('image.question_image.normal.max_height')/$aspect_ratio);
            $this->normalsize = [ 'width' => config('image.question_image.normal.max_width') ,'height' => $height ];
        }
        elseif($image_size['height'] > config('image.question_image.normal.max_height')){
            $width = floor(config('image.question_image.normal.max_width')/$aspect_ratio);
            $this->normalsize = [ 'width' => $width ,'height' => config('image.question_image.normal.max_height') ];
        }
        else{
            $this->normalsize = [ 'width' => $image_size['width'] ,'height' => $image_size['height'] ];
        }

        $width = config('image.question_image.normal.max_width');
        $height = $width/$aspect_ratio;
        $thumb_width = config('image.question_image.thumb.max_width');
        $thumb_height = $thumb_width/$aspect_ratio;

        //$this->normalsize = [ 'width' => $width ,'height' => $height ];
        $this->thumbsize = [ 'width' => $thumb_width ,'height' => $thumb_height ];
    }

    public function setCommentImageSize(){
        $aspect_ratio = config('image.comment_image.ratio');
        $image_size = $this->image->getImageGeometry();
        if($image_size['width'] > config('image.comment_image.normal.max_width')){
            $height = floor(config('image.comment_image.normal.max_height')/$aspect_ratio);
            $this->normalsize = [ 'width' => config('image.review.normal.max_width') ,'height' => $height ];
        }
        elseif($image_size['height'] > config('image.comment_image.normal.max_height')){
            $width = floor(config('image.comment_image.normal.max_width')/$aspect_ratio);
            $this->normalsize = [ 'width' => $width ,'height' => config('image.comment_image.normal.max_height') ];
        }
        else{
            $this->normalsize = [ 'width' => $image_size['width'] ,'height' => $image_size['height'] ];
        }

        $width = config('image.comment_image.normal.max_width');
        $height = $width/$aspect_ratio;
        $thumb_width = config('image.comment_image.thumb.max_width');
        $thumb_height = $thumb_width/$aspect_ratio;

        //$this->normalsize = [ 'width' => $width ,'height' => $height ];
        $this->thumbsize = [ 'width' => $thumb_width ,'height' => $thumb_height ];
    }

    public function setAnnouncementImageSize() {
        $image_size = $this->image->getImageGeometry();
        $this->normalsize = [ 'width' => $image_size['width'] ,'height' => $image_size['height'] ];
    }

    public function getImageSignature(){

        return $this->image->getImageSignature();
    }

    public function getOriginalImageBlob(){
        try {
            return $this->image->getImageBlob();
        } catch (Exception $e) {
            $exception = $e->getMessage();
            $params = ['tags'  =>  ['ImageBlobError-Original'],
                        'error' =>  ['text'=>'Error During Original Image Blob',
                                     'more'=>['Error' => $exception],
                                    ],
                        ];
            logRequest($params);
            return true;
        }
    }

    public function getNormalImageBlob($quality = 70){

        if($this->cropParams){
            $image = $this->processImage($this->normalsize,$quality,false,'custom',$this->cropParams);
        } else {
            $image = $this->processImage($this->normalsize,$quality,true);
        }

        try {
            return $image->getImageBlob();
        } catch (Exception $e) {
            $exception = $e->getMessage();
            $params = ['tags'  =>  ['ImageBlobError-Normal'],
                        'error' =>  ['text'=>'Error During Normal Image Blob',
                                     'more'=>['Error' => $exception],
                                    ],
                        ];
            logRequest($params);
            return true;
        }

    }

     public function getCustomImageBlob(){//$params

        //$image = $this->processImage(['width' => $params['width'],'height' => $params['height'] ], 60, true);
        $image = $this->processImage($this->normalsize, 70, false);

        try {
            return $image->getImageBlob();
        } catch (Exception $e) {
            $exception = $e->getMessage();
            $params = ['tags'  =>  ['ImageBlobError-Custom'],
                        'error' =>  ['text'=>'Error During Custom Image Blob',
                                     'more'=>['Error' => $exception],
                                    ],
                        ];
            logRequest($params);
            return true;
        }
    }

    public function getThumbImageBlob(){

        if($this->cropParams){
            $image = $this->processImage($this->thumbsize,70,false,'custom',$this->cropParams);
        } else {
            $image = $this->processImage($this->thumbsize,70,true);
        }
        try {
            return $image->getImageBlob();
        } catch (Exception $e) {
            $exception = $e->getMessage();
            $params = ['tags'  =>  ['ImageBlobError-Thumb'],
                        'error' =>  ['text'=>'Error During Thumb Image Blob',
                                     'more'=>['Error' => $exception],
                                    ],
                        ];
            logRequest($params);
            return true;
        }

    }

    //Reads image from image path and returns the compressed image as a string
    public function processImage($req_size, $compress_quality=65,$resize=true, $cropStyle = "center",$cropParams=null){

        $image = clone $this->image;
        $initial_size = $image->getImageGeometry();

        //Calculating final Width and Height
        if($req_size['width'] && $initial_size['width'] >= $req_size['width']){
            $final_width = $req_size['width'];
        } else {
            $final_width = $initial_size['width'];
        }
        if($req_size['height'] && $initial_size['height'] >= $req_size['height']){
            $final_height = $req_size['height'];
        } else {
            $final_height = $initial_size['height'];
        }

        if($resize){

             $image->cropThumbnailImage($final_width, $final_height);


        } else {
                    //Calculating the crop topCornerX and topCornerY
            switch ($cropStyle) {
                case 'center':
                      $topCornerX = ($initial_size['width'] - $final_width)/2;
                      $topCornerY =  ($initial_size['height'] - $final_height)/2;
                    break;

                case 'center_horizontal':
                      $topCornerX = ($initial_size['width'] - $final_width)/2;
                      $topCornerY = 0;
                    break;

                case 'center_vertical':
                      $topCornerX = 0;
                      $topCornerY = ($initial_size['height'] - $final_height)/2;
                    break;

                case 'custom':
                      $topCornerX = $cropParams['left'];
                      $topCornerY = $cropParams['top'];
                      $crop_width = $cropParams['width'];
                      $crop_height = $cropParams['height'];

                default:
                    //SHOULD NEVER COME HERE
                    break;
            }
            if(isset($crop_width) && isset($crop_height)){

                $image->cropImage($crop_width, $crop_height, $topCornerX, $topCornerY);
                $image->cropThumbnailImage($final_width, $final_height);

            } else {

                $image->cropImage($final_width, $final_height, $topCornerX, $topCornerY);

            }

        }

        //Setting Image Compression Quality
        $image->setImageCompressionQuality($compress_quality);

        //Setting Image Format
        $image->setImageFormat("jpeg");

        //Flattening Layers etc and returning the new image
        if(config('app.env') == 'production') {
            return $image->flattenImages();
        } else {
            $image->setImageAlphaChannel(11);
            return $image->mergeImageLayers(imagick::LAYERMETHOD_FLATTEN);
        }
    }


}
