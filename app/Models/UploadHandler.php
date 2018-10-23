<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use AWS;
use DB;
use Imagick;

define('STATIC_IMAGE_URL',"http://www.babychakra.com/article/");
ini_set('max_execution_time', '2000');

//Model to handle all the uploads
class UploadHandler extends Model {

     public $folder;
    public $subfolder;
    public $medium;
    public $crop = false;
    private $bucket='babychakraserviceproviders'; 

    //Initialize the model for any type of image upload(user,review,service provider etc.)
    public function initializeModel($options,$params){
        if($options=='admin_service_provider_image'){
            $this->folder = 'serviceproviders/';
            $this->subfolder = date('Ymd').'/';
            $this->medium = 500;
        }
        elseif($options=='contest_images'){
            $this->folder = 'contest_images/';
            if(isset($params) && $params != ''){
                $this->subfolder = $params.'/';
            }
            else{
                $this->subfolder = date('Ymd').'/';
            }
            $this->medium = 300;
        }
        elseif($options=='news_feed_images'){
            $this->folder = 'news_feed/';

            $this->subfolder = date('Ymd').'/';

            $this->medium = 500;
        }
        elseif($options=='review_image'){
            $this->folder = 'serviceproviders/';
            $this->subfolder = 'reviews/';
            $this->medium = 300;
        }
        elseif($options=='summercamp_images'){
            $this->folder = 'summercamp_images/';
            $this->subfolder = date('Ymd').'/';
            $this->medium = 500;
        }
        elseif($options=='user_image'){
            $this->folder = '/users';
            $this->subfolder = '';
            $this->medium = 300;
            $this->crop = true;
        }
        else{
            $this->folder = 'uploads/';
        }

    }

    //get upload path
    public function get_upload_path(){
        $path=$this->folder.$this->subfolder;
        return $path;
    }

    //get file name(a random string)
    public function get_file_name(){
        $timestamp=time();
        $unique_id=uniqid();
        $file_name=$unique_id.'_'.$timestamp;
        return $file_name.'.jpg';
    }

    //upload the original file
    public function uploadOriginal($image_path, $image_save_path){
        $s3 = AWS::createClient('s3');
        $image = new Imagick($image_path);
        $image->setimageformat("jpeg");
        $new_img = $image->getimageblob();

        return  $s3->putObject(array(
            'Bucket'     => $this->bucket,
            'Key'        => $image_save_path,
            'Body'       =>  $new_img ,
            'ContentType'  => 'image/jpeg',
            'ACL'          => 'public-read'
            ));
    }

    //upload after optimization and cropping to create a square image
    public function square_image($image_path, $image_save_path){
        $image = new Imagick($image_path); //open image here

        $d = $image->getImageGeometry();
        $w = $d['width'];
        $h = $d['height'];

        if($w<$h){

            // $topcornerx = 0;
            // $topcornery = 0+($h-$w)/2;
            $width = $w;

        }else{
            // $topcornerx = 0+($w-$h)/2;
            // $topcornery = 0;
            $width = $h;

        }

        // $image->cropImage($width, $width, $topcornerx, $topcornery);
        $image->cropThumbnailImage($width, $width);
        $image->setImageCompressionQuality(60);
        $image = $image->flattenImages();
        $image->setimageformat("jpeg");

        $new_img = $image->getimageblob();

        $s3 = AWS::createClient('s3');
        $context = stream_context_create(array(
            's3' => array(
                'ACL' => 'public-read',
                'ContentType' => 'image/jpeg'
                )
            ));

        return  $s3->putObject(array(
            'Bucket'     => $this->bucket,
            'Key'        => $image_save_path,
            'Body'       =>  $new_img ,
            'ContentType'  => 'image/jpeg',
            'ACL'          => 'public-read'
            ));

        // $image->writeImage($image_save_path);
    }

    //optimise, crop and create a square image of specified size
    public function createThumbnail($image_path, $image_save_path, $width, $height){
        $image = new Imagick($image_path); //open image here

        $image->cropThumbnailImage($width, $height);
        $image->setImageCompressionQuality(60);
        $image = $image->flattenImages();;
        // $image->writeImage($image_save_path);
        $new_img = $image->getimageblob();
        
        $context = stream_context_create(array(
            's3' => array(
                'ACL' => 'public-read',
                'ContentType' => 'image/jpeg'
                )
            ));
        $s3 = AWS::createClient('s3');
        $context = stream_context_create(array(
            's3' => array(
                'ACL' => 'public-read',
                'ContentType' => 'image/jpeg'
                )
            ));

        return  $s3->putObject(array(
            'Bucket'     => $this->bucket,
            'Key'        => $image_save_path,
            'Body'       =>  $new_img ,
            'ContentType'  => 'image/jpeg',
            'ACL'          => 'public-read'
            ));
    }

    //to upload optimised image of a given aspect ratio
    public function createImageWithAspectRatio($image_path, $image_save_path, $aspectRatio){
        $image = new Imagick($image_path); //open image here

        $d = $image->getImageGeometry();
        $w = $d['width'];
        $h = $d['height'];
        $error = 'file uploaded successfully';
        $return =true;

        if($w>=$h ){

            if($w >1.5*$h){
                $width = 1.5*$h;
                $height = $h;
            }
            else{
                $width = $w;
                $height = $width/1.5;
            }

        }elseif($w < $h)
        {

            if($w < 1.5*$h){
                $height = $w*1.5;
                $width = $w;
            }
            else{
                $height = $h;
                $width = $height/1.5;
            }


        }
        else{
            $error = "image cannot be scaled as expected";
            $return = false;
            return json_encode(array("error"=>$error,"response"=>$return));
        }


        $image->cropThumbnailImage($width, $height);
        $image->setImageCompressionQuality(60);
        $image = $image->flattenImages();;
        // $image->writeImage($image_save_path);
        $new_img = $image->getimageblob();

        $s3 = AWS::createClient('s3');
        $context = stream_context_create(array(
            's3' => array(
                'ACL' => 'public-read',
                'ContentType' => 'image/jpeg'
                )
            ));

        if(!$s3->putObject(array(
            'Bucket'     => $this->bucket,
            'Key'        => $image_save_path,
            'Body'       =>  $new_img ,
            'ContentType'  => 'image/jpeg',
            'ACL'          => 'public-read'
            )))
        {
            $error = "image could not be uploaded";
            $return = false;
        }

        return json_encode(array("error"=>$error,"response"=>$return));
    }

    //to upload thumbnail of optimised image of a given aspect ratio
     public function createThumbnailImageWithAspectRatio($image_path, $image_save_path, $aspectRatio){
        $image = new Imagick($image_path); //open image here

        $d = $image->getImageGeometry();
        $w = $d['width'];
        $h = $d['height'];
        $error = 'file uploaded successfully';
        $return =true;

        if($w>=$h ){

            if($w >1.5*$h){
                $width = 1.5*$h;
                $height = $h;
            }
            else{
                $width = $w;
                $height = $width/1.5;
            }

        }elseif($w < $h)
        {

            if($w < 1.5*$h){
                $height = $w*1.5;
                $width = $w;
            }
            else{
                $height = $h;
                $width = $height/1.5;
            }


        }
        else{
            $error = "image cannot be scaled as expected";
            $return = false;
            return json_encode(array("error"=>$error,"response"=>$return));
        }

        if($width>$height){
            $height=150;
            $width=1.5*150;
        }
        else{
            $width=150;
            $height=1.5*150;
        }

        $image->cropThumbnailImage($width, $height);
        $image->setImageCompressionQuality(60);
        $image = $image->flattenImages();;
        // $image->writeImage($image_save_path);
        $new_img = $image->getimageblob();

        $s3 = AWS::createClient('s3');
        $context = stream_context_create(array(
            's3' => array(
                'ACL' => 'public-read',
                'ContentType' => 'image/jpeg'
                )
            ));

        if(!$s3->putObject(array(
            'Bucket'     => $this->bucket,
            'Key'        => $image_save_path,
            'Body'       =>  $new_img ,
            'ContentType'  => 'image/jpeg',
            'ACL'          => 'public-read'
            )))
        {
            $error = "image could not be uploaded";
            $return = false;
        }

        return json_encode(array("error"=>$error,"response"=>$return));
    }

    //
    //The commented functions below were used to run scripts to optimise and store article and service images
    //Do not put them on any routes
    // 



    /*private function createImageWithAspectRatio1($image_path, $image_save_path, $s3){
         $image_path =  str_replace(' ', '%20', $image_path);
        try{

            $image = new Imagick($image_path); //open image here
        }
        catch(Exception $e){
            echo "Image not found\n";
            return;
        }

        $path_parts = pathinfo($image_path);
        $extension =  strtolower(explode('?',$path_parts['extension'])[0]);
        if($extension == 'jpg'  || $extension == 'jpeg'){
            $image_type = 'image/jpeg';
        }
        elseif($extension == 'png'){
            $image_type = 'image/png';
        }
        else{
                    echo "index image found\n";
                    return;
                    
                    // $url = parse_url($src);
                    // $img_src = explode('=',explode('&',$url['query'])[1])[1];
                    // echo $img_src."\n";
                    // // echo $src." ".$article['id'] ;
                    // // echo "\n";
        }

        $d = $image->getImageGeometry();
        $w = $d['width'];
        $h = $d['height'];
        $error = 'file uploaded successfully';
        $return =true;

        if($w>=$h ){

            if($w >1.5*$h){
                $width = 1.5*$h;
                $height = $h;
            }
            else{
                $width = $w;
                $height = $width/1.5;
            }

        }elseif($w < $h)
        {

            if($w < 1.5*$h){
                $height = $w*1.5;
                $width = $w;
            }
            else{
                $height = $h;
                $width = $height/1.5;
            }


        }
        else{
            $error = "image cannot be scaled as expected";
            $return = false;
            return json_encode(array("error"=>$error,"response"=>$return));
        }


        $image->cropThumbnailImage($width, $height);
        $image->setImageCompressionQuality(60);
        $image = $image->flattenImages();;
        // $image->writeImage($image_save_path);
        $new_img = $image->getimageblob();


        $s3->putObject(array(
            'Bucket'     => 'babychakraserviceproviders',
            'Key'        => $image_save_path,
            'Body'       =>  $new_img ,
            'ContentType'  => $image_type,
            'ACL'          => 'public-read'
            ));
        echo "file written at ".$image_save_path." \n";
        // return json_encode(array("error"=>$error,"response"=>$return));
    }

    public function checkVertical($image_path, $id,$myfile){
      $image_path =  str_replace(' ', '%20', $image_path);
        try{

            $im = new Imagick($image_path); //open image here
        }
        catch(Exception $e){
            fwrite($myfile, $id."   image not found\n");
            echo "Image not found\n";
            return;
        }


            $d = $im->getImageGeometry();
            $w = $d['width'];
            $h = $d['height'];
            if($h > $w)
                fwrite($myfile, $id." \n");

           
    }
   
     public function script(){
        
        $s3 = AWS::createClient('s3');
        $sql = "SELECT bc_content.id as id, bc_content.introtext, bc_content.title, bc_content.images FROM bc_content INNER JOIN `bc_categories` ON `bc_categories`.`id` = `bc_content`.`catid` WHERE `bc_categories`.`published` = 1 and `bc_categories`.`path` like 'learn%' and `bc_categories`.`level` NOT IN (0,1) and `bc_content`.`state`=1 ORDER BY `id` ASC";

        $articles=DB::select(DB::raw($sql));
        $ids = [];
        $i=0;
        $file = fopen('article_vertical.txt','w');
        foreach ($articles as $article) {
            // $i++;
            // echo $i.' '.$article->id."\n";
            // continue;
            $introtext = $article->introtext;
            $title=$article->title;
            $images=$article->images;
            $images = json_decode($images,true);
            $image = $images['image_intro'];
                        if($image == ""){
                            $image = 'images/no-image.png';
                        }

            // $article_image = 'http://s3-ap-southeast-1.amazonaws.com/babychakraserviceproviders'.$image;
            $image_save_path = 'arpit/'.$image;
            $image_save_path = str_replace('%20',' ', $image_save_path);
            $article_image = STATIC_IMAGE_URL.$image;
            // echo "file written at ".$image_save_path." \n";
            // $this->createImageWithAspectRatio1($article_image, $image_save_path, $s3);
            $this->checkVertical($article_image, $article->id, $file);
            $i++;
            // $text= $text.'<div class="blog-detail-img"><img  style="width:100vw;" src="'.$article_image.'" /></div>';
            // $replace_string = 'img src="http://s3-ap-southeast-1.amazonaws.com/babychakraserviceproviders/';
            // $replace_string = 'img src="http://www.babychakra.com/article/';
            // $introtext = str_replace('img src="',$replace_string,$introtext);
            // libxml_use_internal_errors(true);

            $dom = new DOMDocument();
            echo $article->id.'<br />';
              if($article->id=='306') $introtext=str_replace('width="100%"', '', $introtext);
            $dom->loadHTML($introtext);
            foreach($dom->getElementsByTagName('img') as $img){
                $i++;
                $src = $img->getAttribute('src');
                if(strpos($src,'data:image')!==false){
                    echo "True data:image found\n";
                    continue;
                }
                else{
                    $path_parts = pathinfo($src);
                    $extension =  strtolower(explode('?',$path_parts['extension'])[0]);
                    // echo $path_parts['extension'];
                    // echo "\n";
                    if($extension == 'jpg'  || $extension == 'jpeg'){
                        $image_type = 'image/jpeg';

                    }
                    elseif($extension == 'png'){
                        $image_type = 'image/png';

                    }
                    else{
                        echo "index image found\n";
                        continue;
                        // $url = parse_url($src);
                        // $img_src = explode('=',explode('&',$url['query'])[1])[1];
                        // echo $img_src."\n";
                        // // echo $src." ".$article['id'] ;
                        // // echo "\n";
                    }
                    if(strpos($src, 'http')!==false){
                        $img_src = $src;
                        $src = "images/".$path_parts['filename'];
                    }
                    else
                        $img_src = STATIC_IMAGE_URL.$src;

                    $image_save_path = "arpit/".$src;
                    $image_save_path = str_replace('%20',' ', $image_save_path);
                    try{
                        $url =  str_replace(' ', '%20', $img_src);
                        $im = new Imagick($url);
                        $im->setImageCompressionQuality(60);
                        $new_img = $im->getimageblob();
                        $s3->putObject(array(
                            'Bucket'     => 'babychakraserviceproviders',
                            'Key'        => $image_save_path,
                            'Body'       =>  $new_img ,
                            'ContentType'  => $image_type,
                            'ACL'          => 'public-read'
                            ));
                        echo "file written at ".$image_save_path." \n";
                    }
                    catch(Exception $e){
                        echo "Image not found: \n";
                        // continue;
                    }
                }
            }
        }
        fclose($file);
    }
     

    public function servicescript(){

        $prefix = "http://s3-ap-southeast-1.amazonaws.com/babychakraserviceproviders/images/serviceproviders/";

        $s3 = AWS::createClient('s3');

        $sql = "SELECT id FROM bc_services_providers_new ";
        $sp=DB::select(DB::raw($sql));
        $ids = [];
        foreach ($sp as $service) {

            $sqlImg = "SELECT id image_id, image_url image_path FROM `service_providers_images_new` WHERE service_provider_id = ".$service->id;
            
            $rowImg=DB::select(DB::raw($sqlImg));

            if(empty($rowImg)) $rowImg=array();

            foreach ($rowImg as $image) {

                $url =  str_replace(' ', '%20', $prefix.$image->image_path);
                $url_dest = str_replace('%20', ' ',     "/serviceproviders/".$image->image_path);
                try{
                    $path_parts = pathinfo($image->image_path);
                    $extension =  strtolower(explode('?',$path_parts['extension'])[0]);
                    // echo $path_parts['extension'];
                    // echo "\n";
                    if($extension == 'jpg'  || $extension == 'jpeg'){
                        $image_type = 'image/jpeg';

                    }
                    elseif($extension == 'png'){
                        $image_type = 'image/png';

                    }
                    else{
                        echo "index image found\n";
                        continue;
                        // $url = parse_url($src);
                        // $img_src = explode('=',explode('&',$url['query'])[1])[1];
                        // echo $img_src."\n";
                        // // echo $src." ".$article['id'] ;
                        // // echo "\n";
                    }

                    $im = new Imagick($url);
                    $d = $im->getImageGeometry();
                    $w = $d['width'];
                    $h = $d['height'];

                    if($w>=$h ){
                        if($w >1.5*$h){
                            $width = 1.5*$h;
                            $height = $h;
                        }
                        else{
                            $width = $w;
                            $height = $width/1.5;
                        }
                    }elseif($w < $h)
                    {
                        if($w < 1.5*$h){
                            $height = $w*1.5;
                            $width = $w;
                        }
                        else{
                            $height = $h;
                            $width = $height/1.5;
                        }
                    }
                    $im->cropThumbnailImage($width, $height);
                    $im->setImageCompressionQuality(60);
                    $new_img = $im->getimageblob();
                    $status  = $s3->putObject(array(
                        'Bucket'     => 'babychakraserviceproviders',
                        'Key'        => $url_dest,
                        'Body'       =>  $new_img ,
                        'ContentType'  => $image_type,
                        'ACL'          => 'public-read'
                        ));
                    // var_dump($status);
                    echo $url_dest."\n";

                }
                catch(Exception $e){
                    echo "Image not found: ".$image->image_id." \n";
                    continue;
                }
            }
        }
    }*/

    //To submit article images in the cropper api
    public function submitArticle() {
        $s3 = AWS::createClient('s3');

        $left = $_REQUEST['left'];
        $top = $_REQUEST['top'];
        $width = $_REQUEST['width'];
        $height = $_REQUEST['height'];
        $article_id = $_REQUEST['article_id'];
        $offset = $_REQUEST['offset'];
        $nextOffset = $offset+1;

        // print_r($_REQUEST)
        $sql = "SELECT images FROM bc_content WHERE id =".$article_id;
        // echo $sql;
        // die();
        $articles=DB::select(DB::raw($sql));

        foreach ($articles as $article) {
        $images=$article->images;
            $images = json_decode($images,true);
            $image = $images['image_intro'];
                        if($image == ""){
                            $image = 'images/no-image.png';
                        }
            $image_dest = str_replace('%20', ' ', '/articles/'.$image);
            $image_path = str_replace(' ', '%20', 'http://www.babychakra.com/article/'.$image);

        }
        $path_parts = pathinfo($image_path);
        $extension =  strtolower(explode('?',$path_parts['extension'])[0]);
        // echo $path_parts['extension'];
        // echo "\n";
        if($extension == 'jpg'  || $extension == 'jpeg'){
            $image_type = 'image/jpeg';

        }
        elseif($extension == 'png'){
            $image_type = 'image/png';

        }
         elseif($extension == 'gif'){
            $image_type = 'image/gif';

        }


        $image = new Imagick($image_path);
        $image->cropImage($width, $height, $left, $top);
        $image->setImageCompressionQuality(60);
        $new_img = $image->getimageblob();
        $s3->putObject(array(
            'Bucket'     => 'babychakraserviceproviders',
            'Key'        => $image_dest,
            'Body'       =>  $new_img ,
            'ContentType'  => $image_type,
            'ACL'          => 'public-read'
            ));
         return $nextOffset;


    }

    //To submit service images in the cropper api
    public function submitService(){
        $s3 = AWS::createClient('s3');

        $left = $_REQUEST['left'];
        $top = $_REQUEST['top'];
        $width = $_REQUEST['width'];
        $height = $_REQUEST['height'];
        $offset = $_REQUEST['offset'];

        // print_r($_REQUEST)
        $sql = "SELECT id image_id, image_url image_path FROM `service_providers_images_new` ORDER BY id LIMIT 1 OFFSET ".$offset;
        $images=DB::select(DB::raw($sql));
        $nextOffset = $offset+1;
        foreach ($images as $image) {
            $image_path = $image->image_path;
            $image_dest = '/serviceproviders/'.$image_path;
            $service_image = str_replace(' ', '%20', "http://s3-ap-southeast-1.amazonaws.com/babychakraserviceproviders/images/serviceproviders/".$image->image_path);
        }
        $path_parts = pathinfo($image_path);
        $extension =  strtolower(explode('?',$path_parts['extension'])[0]);
        // echo $path_parts['extension'];
        // echo "\n";
        if($extension == 'jpg'  || $extension == 'jpeg'){
            $image_type = 'image/jpeg';

        }
        elseif($extension == 'png'){
            $image_type = 'image/png';

        }
        elseif($extension == 'gif'){
            $image_type = 'image/gif';

        }

        $image = new Imagick($service_image);
        $image->cropImage($width, $height, $left, $top);
        $image->setImageCompressionQuality(60);
        $new_img = $image->getimageblob();
        $s3->putObject(array(
            'Bucket'     => 'babychakraserviceproviders',
            'Key'        => $image_dest,
            'Body'       =>  $new_img ,
            'ContentType'  => $image_type,
            'ACL'          => 'public-read'
            ));

        return $nextOffset;

    }

}

?>