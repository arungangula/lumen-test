<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
use App\Models\Service;

class ListYourBusiness extends Model {
	protected $table = "bc_services_providers_form_info";

  public function city(){

    return $this->belongsTo('App\Models\City','city_id');
  }

  public function location(){

    return $this->belongsTo('App\Models\Location','location_id');
  }

  public function service(){

    return $this->belongsTo('App\Models\Service','service_provider_id');
  }

  public function user(){

    return $this->belongsTo('App\Models\User','user_id');
  }



  public function getVerifyAttribute(){

    $userId    = ( isset($this->user_id) ) ? $this->user_id : null;
    $managerId = ( isset($this->service->manager_id) ) ? $this->service->manager_id : null;
    return ( $userId == $managerId ) ? "Verified" : "Not Verified";
  }

  public function getServiceStatusAttribute() {

    return ($this->service_provider_id) ? "Claimed Service" : "New Service";
  }

  public function addImage($image_path){

    if($this->images_data){
      $images = json_decode($this->images_data);
    } else {
      $images = [];
    }

    $images[] = $image_path;
    $this->images_data = json_encode($images);

  }

   public function removeImage($image_path){

    if($this->images_data){
      $images = json_decode($this->images_data);
    } else {
      $images = [];
    }

    if(($key = array_search($image_path, $images)) !== false) {
          unset($images[$key]);
    }

    $this->images_data = json_encode($images);

  }



  public function populateFromService(Service $service){


        $this->name = $service->name;
        $this->email = $service->email;
        $this->contact_name = $service->name;
       //$this->Mobile = $service->mobile_number;
        $this->address1 = $service->address1;
        $this->city_id =  $service->city_id;
        $this->location_id = $service->location_id;
       
        $this->about = $service->about;
        $this->website = $service->website;
        //from service subcategories

        $subcategory_ids = [];
        foreach($service->subcategories as $subcategory) {
          $subcategory_ids[] = $subcategory->id;
        }
          
        $this->sub_cat_id = implode(',', $subcategory_ids);

        //images copy from service
        $this->images_data = '';

        //business hours copy from service
        $business_hours = [];
        foreach($service->officeHours as $officeHour) {

          $business_hours[] =  [ 'day' => $officeHour->day, 'start' => $officeHour->start_time, 'end' => $officeHour->end_time ];


        }
        $this->business_hours = json_encode($business_hours);
        
        $images = [];
        // foreach($service->images as $image){
        //   $images[] = [];
        // }

        $this->images_data = json_encode($images);


        $this->website = $service->website;

  }



	public function getFilterHtmlForCat($filter_arr,$id){
        $str='';

        if(array_key_exists($id,$filter_arr['category'])){
          foreach($filter_arr['category'][$id] AS $key=>$value){

              if($value=="age_group"){
                 $age_group_min_year=$this->createDropDown('age_group_min_year','year',0,18,$id);
                 $age_group_min_month=$this->createDropDown('age_group_min_month','month',0,11,$id);
                 $age_group_max_year=$this->createDropDown('age_group_max_year','year',0,18,$id);
                 $age_group_max_month=$this->createDropDown('age_group_max_month','month',0,11,$id);

                 $str.='<div  class="bc_margin_bottom_16px"><label >Age group (Minimum)*</label>'.$age_group_min_year.' '.$age_group_min_month;
                 $str.= '<label class="bc_label_width">Age group (Maximum)*</label>'.$age_group_max_year.' '.$age_group_max_month.'</div>';

              }
              if($value=="price_range" || $value=="consultation_charge"){
                  $price_range_minimum=$this->createInputbox($value.'_min',$id,true);
                  $price_range_maximum=$this->createInputbox($value.'_max',$id,true);
                  $label=ucfirst(str_replace("_"," ",$value));

                  if($filter_arr['unit'][$id]=='one_time'){
                      $unit='';
                  }else{
                      $unit=stristr($filter_arr['unit'][$id],'per') ? str_replace("_"," ",$filter_arr['unit'][$id]): 'per '.$filter_arr['unit'][$id];

                  }
                  if($value=="consultation_charge"){
                      $str.='<div class="bc_margin_bottom_16px price_range" ><label>'.$label.'s '.$unit.'</label>'.$price_range_minimum.'</div>';

                  }
                  else if($id=='134'){
                      $str.='<div class="bc_margin_bottom_16px price_range"><label>Average fees per month (Minimum)</label>'.$price_range_minimum;
                      $str.= ' <label class="bc_label_width">Average fees per month (Maximum)</label>'.$price_range_maximum.'</div>';
                  }else if($id=='168'){
                      $name='price_range_'.$id;

                      $str.='<div class="bc_margin_bottom_16px"><label>Price for pregnancy test</label>'.$price_range_minimum;
                      $str.='<input type="hidden" id="'.$name.'" name="'.$name.'"></div>';
                     /* $str.= ' <label class="bc_label_width">Maximum price for two</label>'.$price_range_maximum.'</div>';*/
                  }else if($id=='155'){
                      $name='price_range_'.$id;

                      $str.='<div class="bc_margin_bottom_16px"><label>Price for two</label>'.$price_range_minimum;
                      $str.='<input type="hidden" id="'.$name.'" name="'.$name.'"></div>';
                      /* $str.= ' <label class="bc_label_width">Maximum price for two</label>'.$price_range_maximum.'</div>';*/
                  }else if($id=='121'){
                      $name='price_range_'.$id;

                      $str.='<div class="bc_margin_bottom_16px"><label>Price (Entry fee)</label>'.$price_range_minimum;
                      $str.='<input type="hidden" id="'.$name.'" name="'.$name.'">';
                      /* $str.= ' <label class="bc_label_width">Maximum price for two</label>'.$price_range_maximum.'</div>';*/
                  }else if($id=='164'){
                      $str.='<div class="bc_margin_bottom_16px price_range" ><label>'.$label.' '.$unit.'  rental* (Minimum)</label>'.$price_range_minimum;
                      $str.= ' <label class="bc_label_width">'.$label.' '.$unit.'  rental* (Maximum)</label>'.$price_range_maximum.'</div>';
                  }else{
                      if($id=='156'){
                          $label='Average fees';
                      }
                    $str.='<div class="bc_margin_bottom_16px price_range" ><label>'.$label.' '.$unit.' (Minimum)</label>'.$price_range_minimum;
                    $str.= ' <label class="bc_label_width">'.$label.' '.$unit.' (Maximum)</label>'.$price_range_maximum.'</div>';
                  }

              }
           /*  if($value=="cord_blood_bank"){
                  $price_range_minimum=$this->createInputbox('price_range_min',$id,true);
                  $price_range_maximum=$this->createInputbox('price_range_max',$id,true);

                  $str.='<div class="bc_margin_bottom_16px"><label>Price Range for store cord Minimum/Year</label>'.$price_range_minimum;
                  $str.= ' <label>Price Range for store cord Maximum/Year</label>'.$price_range_maximum.'</div>';
              }*/
              if($value=="home_delivery" || $value=="day_care_facility"||$value=="transportation"||$value=="air_conditioner"||$value=="home_blood_tests" || $value=="cafe_reading_room"){
                  $home_delivery=$this->createRadio($value,$id);
                  if($value=="air_conditioner"){
                      $label="AC classrooms";
                  }
                  elseif($value=="cafe_reading_room"){
                      $label="Cafe room";
                  }else{
                    $label=ucfirst(str_replace("_"," ",$value));
                  }
                  $str.='<div class="bc_margin_bottom_16px"><label>'.$label.' *</label>'.$home_delivery.'</div>';
              }
              if($value=="specialization"){

                  if($id==145){
                      $label=ucfirst(str_replace("_"," ",$value));
                      $str.='<div class="bc_margin_bottom_16px"><label>'.$label.' *</label>';
                      $str.='<div  style="display:inline-block;"><select style="width: 100% !important;" name="specialization_145" id="specialization_145"><option value="">------Select Area of specialization------</option>';
                      foreach($this->getPediatricSpecializationArray() AS $pedatrician){
                          $str.='<option value="'.$pedatrician.'">'.$pedatrician.'</option>';
                      }
                      $str.='</select></div><div id="specialization" style="display:none;"><input type="text" name="specialization_other_145" style="width: 70%;margin-left:10px;" id="specialization_other_145"></div>';
                  }else{
                    $specialization=$this->createInputbox('specialization',$id);
                    $str.='<div class="bc_margin_bottom_16px"><label>Area of specialization*</label>'.$specialization.'</div>';
                  }
              }
              if($value=="covered_under_child_specialists"||$value=="kids_special_menu"||$value=="children_entertainment_option"||$value=="kiddie_cutlery"||$value=="kids_friendly_chairs_tools"||$value=="english_speaking_nanny"){
                  $radio=$this->createRadio($value,$id);
                  $lable_name=$value;
                  $lable_name=($value=="kids_friendly_chairs_tools")? "kid_friendly_chairs" : $value;
                  $label=ucfirst(str_replace("_"," ",$lable_name));
                  $str.='<div class="bc_margin_bottom_16px"><label>'.$label.'*</label>'.$radio.'</div>';
              }
              if($value=="indoor_outdoor_both"){
                  $value_arr=array('indoor','outdoor','both');
                  $radio=$this->createThreeRadiobutton($value,$id,$value_arr);
                  $label="Type of play area";
                  $str.='<div class="bc_margin_bottom_16px"><label>'.$label.'*</label>'.$radio.'</div>';
              }
          }
        }

        return $str;
    }

    public function getOptionalFilterArr(){

        $option_filter=array( 157=>array('Birthday party venue'=>array('birthday_party_venue','radio'),'Cafe available'=>array('Cafe_available','radio'),
            'Wi-Fi'=>array('Wi-Fi','radio'),'Changing station'=>array('Changing station','radio'),'CCTV'=>array('CCTV','radio')),
            134=>array('Teaching methodology'=>array('teaching_methodlogy','textarea'),
            'Schools that kids go to after completeing Playschool'=>array('student_go_after_school','textbox'),
            'Only female supervision at all times'=>array('only_female_supervision_at_all_times','radio'),'Surveillance/CCTV'=>array('cctv','radio'),
            'Live feed'=>array('live_feed','radio'),'Playground'=>array('playground','radio'),
            'Provision for meals'=>array('provision_for_meals','radio'),'First aid/Medical facilities'=>array('first_aid_facilities','radio'),
            'Doctor on emergency'=>array('doctor_on_emergency','radio'),'Separate washroom for boys and girls'=>array('separate_washroom_for_boys_and_girls','radio'),
            'Swimming pool'=>array('swimming_pool','radio'),'Parent counseling'=>array('parent_counseling','radio')),
            156=>array('Only female supervision at all times'=>array('only_female_supervision_at_all_times','radio'),'Surveillance/CCTV'=>array('cctv','radio'),
                'Live feed'=>array('live_feed','radio'),'Playground'=>array('playground','radio'),
                'Provision for meals'=>array('provision_for_meals','radio'),'First aid/Medical facilities'=>array('first_aid_facilities','radio'),
                'Doctor on emergency'=>array('doctor_on_emergency','radio'),'Separate washroom for boys and girls'=>array('separate_washroom_for_boys_and_girls','radio'),
                'Parent counseling'=>array('parent_counseling','radio')),

            155=>array('Kiddy cutlery'=>array('Kiddy_cutlery','radio'),'Kids entertainment options'=>array('kids_entertainment_options','radio'),
                'Special play areas for kids'=>array('special_play_areas_for_kids','radio')),
            135=>array('Name of expert'=>array('name_of_expert','textbox'),'Qualification'=>array('qualification','textbox'),
                'Group Classes'=>array('group_classes','radio'),'Price per session'=>array('group_price_per_session','textbox'),
                'Private classes'=>array('private_classes','radio'),'Private Price per session'=>array('private_price_per_session','textbox'),'Online classes'=>array('online_classes','radio'),
                'Online price per session'=>array('online_price_per_session','textbox')),
            124=>array('Education'=>array('Education','textbox')),
            131=>array('Education'=>array('Education','textbox')),
            132=>array('Education'=>array('Education','textbox')),
            145=>array('Education'=>array('Education','textbox')),
            146=>array('Education'=>array('Education','textbox')),

            118=>array('Description'=>array('description','textarea'),'Type of Store'=>array('type_of_store','radio'),
                'Shipping and delivery details'=>array('shipping_and_delivery_details','textarea'),'Payment options'=>array('payment_option','radio')),
            114=>array('F&B Catering'=>array('fb_catering','radio'),'Venue Management'=>array('venue_management','radio'),'Video-Audio(DJ, Projectors, Live Bands etc)'=>array('video_audio','radio'),'Theme Decorations'=>array('theme_decorations','radio'),'Theme Cakes'=>array('theme_cakes','radio'),'Entertainers (magicians, clowns, etc.)'=>array('entertainers','radio'),
                'Lighting'=>array('lighting','radio'),'Artists & Entertainers(Host, Game Host, Clown, Magician, Jugglers, Face-painters, Tattoo-artists, Tarot Card reader, Bangle-maker, Potter etc)
                '=>array('artists_entertainers','radio'),'Games & Activities'=>array('games_activities','radio'),'Videographer & Photographer'=>array('videographer_photographer','radio'),
                'Gifts & Return gifts'=>array('gifts_return_gifts','radio'),'Packages'=>array('packages','textarea'),'Booking'=>array('booking','textarea'),'Area and location covered'=>array('Areas_locations_covered','textarea')),



            );
        return $option_filter;

    }

    public function getProgramArrFilter(){
        $program_arr=array(134=>array("PARENT TODDLER"=>array('parent_toddler_yes_no','parent_toddler_age_group','parent_toddler_days_timing','parent_toddler_fees_month','parent_toddler_batch_size','parent_toddler_teacher_child_ratio'),
            "PLAYGROUP"=>array('play_group_yes_no','paly_group_age_group','paly_group_days_timing','paly_group_fees_month','paly_group_batch_size','paly_group_teacher_child_ratio'),
            "NURSERY"=> array('nursery_yes_no','nursery_age_group','nursery_days_timing','nursery_fees_month','nursery_batch_size','nursery_teacher_child_ratio'),
            "JR.KG"=>array('jr_kg_yes_no','jr_kg_age_group','jr_kg_days_timing','jr_kg_fees_month','jr_kg_batch_size','jr_kg_teacher_child_ratio'),
            "SR.KG"=>array('sr_kg_yes_no','sr_kg_age_group','sr_kg_days_timing','sr_kg_fees_month','sr_kg_batch_size','sr_kg_teacher_child_ratio'),
            ),
            156=>array("DAYCARE"=>array('day_care_age_group','day_care_days_timing','day_care_fees_month','day_care_batch_size','day_care_teacher_child_ratio','teacher_to_caregiver_ratio')),
            106=>array("ACTIVITIES"=>array('activities_0','age_group_0','activities_days_timing_0','fees_per_month_0','sessions_period_0','batch_size_0'))
        );
        return $program_arr;
    }

    public function getAdmissionFilterArr(){
        $admission_filter=array(134=>array('Start of admissions'=>'start_of_admissions_season',
            'Documents required for admission'=>'documents_required_for_admission',
            'Cost of Application form'=>'cost_of_application_form'));

        return $admission_filter;
    }

    public function getPediatricSpecializationArray(){
        $pediatric_specialization_array=array('Pediatric Oncologist','Pediatric Surgeon','Pediatric Urologist','Pediatric OT','Neonatologist','Pediatric Anesthesiologist','Pediatric Dentist','Pediatric Dermatologist','Pediatric Opthamologist','Pediatric Cardiologist','Pediatric Orthopedic','Pediatric ENT','Homeopathy','Other');
        return $pediatric_specialization_array;
    }

    public function getoptionalFilterHtmlForCat($catId){
        $str='';
        $str.='<div class="bc_margin_bottom_16px">';

        $program_arr=$this->getProgramArrFilter();
        $option_yes_no_filter=$this->getOptionalFilterArr();


        $admission_filter=$this->getAdmissionFilterArr();
        if(array_key_exists($catId,$program_arr)){
            if($catId=='106'){
                $str .='<table width="100%" style="width:100%" border="1" cellspacing="0"  cellpadding="0" id="activity_table"  cellspacing="0"><tr><th class="bc_width_15">Activities</th><th class="bc_width_15">Age Group</th><th class="bc_width_15">Days/Timings</th><th class="bc_width_15">Fees/Month</th><th class="bc_width_15">No Of Sessions</th><th class="bc_width_15">Batch Size</th></tr>';
            }elseif($catId=='156'){
                $str .='<table width="100%" style="width:100%" border="1" cellspacing="0"  cellpadding="0"  cellspacing="0"><tr><th class="bc_width_15"></th><th class="bc_width_15">Age Group</th><th class="bc_width_15">Days/Timings</th><th class="bc_width_15">Fees/Month</th><th class="bc_width_15">Batch Size</th><th class="bc_width_15">Teacher Child Ratio</th><th class="bc_width_15">Teacher to Caregiver Ratio</th></tr>';
            }else{
                $str .='<table width="100%" style="width:100%" border="1" cellspacing="0"  cellpadding="0"  cellspacing="0"><tr><th class="bc_width_15">Programs</th><th class="bc_width_15">Status</th><th class="bc_width_15">Age Group</th><th class="bc_width_15">Days/Timings</th><th class="bc_width_15">Fees</th><th class="bc_width_15">Batch Size</th><th class="bc_width_15">Teacher Child Ratio</th></tr>';
            }
            foreach($program_arr[$catId] AS $key=>$val){
                 $str.='<tr>';
                if($catId!='106'){
                    $str.='<td>'.$key.'</td>';
                }
                foreach($val  AS $field_key =>$field_val){
                    if(preg_match('/_days_timing/', $field_val)){
                        $str.='<td><textarea name="'.$field_val.'" id="'.$field_val.'" rows="3" cols="7"></textarea></td>';
                    }elseif(preg_match('/_yes_no/', $field_val)){
                        $str.='<td><select class="bc_select-bg" name="'.$field_val.'" id="'.$field_val.'"><option value="">Select</option><option value="yes">Yes</option><option value="no">No</option></select></td>';
                    }else
                    {
                        $str.='<td><input type="text" name="'.$field_val.'" id="'.$field_val.'"></td>';
                    }

                }
                $str.='</tr>';
            }



            $str.='</table>';
            if($catId=='106'){
                $str.='<div id="add_more_activity" class="bc_add_more bc_add_activity" ><a href="javascript:void(0)" onclick="add_more_activity()">Add More Activities</a>
                <input type="hidden" name="add_more_activity_count" id="add_more_activity_count" value="1"></div>';
            }

        }
        $str.='</div>';



        if(array_key_exists($catId,$option_yes_no_filter)){
            if($catId=='118'){
                $str.='<div class="bc_margin_bottom_16px"><h5 style="text-decoration: underline">About Us</h5></div>';
            }

            foreach($option_yes_no_filter[$catId] AS $key=>$val){


                    if($catId=='118'){

                    if($val[0]=='description' || $val[0]=='shipping_and_delivery_details' ){
                        if($val=='shipping_and_delivery_details' ){
                            $str.='<div class="bc_margin_bottom_16px"><h5>If online:</h5></div>';
                        }
                        $textarea_val=$val[0].'_'.$catId;
                        $str.='<div class="bc_margin_bottom_16px"><label>'.$key.'</label><textarea name="'.$textarea_val.'" id="'.$textarea_val.'" rows="5" cols="7"></textarea></div>';
                    }else{

                            if($val[0]=='type_of_store'){
                                $val_array=array('online','offline','both');
                            }else{
                                $val_array=array('COD','online','both');
                            }

                            $radio=$this->createThreeRadiobutton($key,$catId,$val_array);
                            $label=$key;
                            $str.='<div class="bc_margin_bottom_16px"><label>'.$label.'</label>'.$radio.'</div>';
                       }

                }else{

                    if($val[1]=='textarea'){
                        $textarea_val=$val[0].'_'.$catId;
                        $str.='<div class="bc_margin_bottom_16px"><label>'.$key.'</label><textarea name="'.$textarea_val.'" id="'.$textarea_val.'" rows="5" cols="7"></textarea></div>';
                    }elseif($val[1]=='textbox'){
                            if($val[0]=='group_price_per_session' || $val[0]=='private_price_per_session' || $val[0]=='online_price_per_session'){
                                $str.='<div class="bc_margin_bottom_16px" id="'.$val[0].'_div" style="display:none"><label>Price per session</label>'.$this->createInputbox($val[0],$catId).'</div>';
                            }else{
                                $str.='<div class="bc_margin_bottom_16px"><label>'.$key.'</label>'.$this->createInputbox($val[0],$catId).'</div>';
                            }
                    }else{
                             $radio=$this->createRadio($val[0],$catId);
                             $label=$key;
                            $str.='<div class="bc_margin_bottom_16px"><label>'.$label.'</label>'.$radio.'</div>';
                        }

                }

            }

        }
        if(array_key_exists($catId,$admission_filter)){
            $str.='<div class="bc_margin_bottom_16px"><h5 style="text-decoration: underline">Admission</h5></div>';
            foreach($admission_filter[$catId] AS $key=>$val){
                if($key=='Form collection – Online/at school'){
                    $name='form_collection';

                    $radio=$this->createThreeRadiobutton($name,$catId,$val);
                    $label=$key;
                    $str.='<div class="bc_margin_bottom_16px"><label>'.$label.'</label>'.$radio.'</div>';
                }elseif($val=='start_of_admissions_season'){
                    $str.='<div class="bc_margin_bottom_16px"><label class="bc_align_left">'.$key.' (MM/DD)</label>';
                    $str.='<div class="input-append date bc_align_left"  id="start_of_admissions_season_0" data-date-format="dd/mm">
                        <input  type="text" readonly="readonly" name="start_of_admissions_season_0" ><span class="add-on"><i class="icon-th"></i></span>
                        </div><div id="add_more_admission_div" style="width:10%;display:inline-block" class="bc_margin_bottom_16px"><a href="javascript:void(0)" onclick="add_more_date_admission()">Add More</a>
                        <input type="hidden" name="admission_count" id="admission_count" value="1"></div></div>';
                }
                else
                {
                    $label=$key;
                    $str.='<div class="bc_margin_bottom_16px"><label>'.$label.'</label>'.$this->createInputbox($val,$catId).'</div>';
                }
            }
        }
        return $str;
    }

    public function createThreeRadiobutton($name,$id,$val){
        $name=$name."_".$id;

        $radiobutton ='<span class="bc_width_20_percentage"><input  type="radio" style="display:inline-block;"  name="'.$name.'" id="'.$name.'" value="'.$val[0].'"> '.ucfirst($val[0]).'</span>';
        $radiobutton .='<span class="bc_width_20_percentage"><input type="radio"  style="display:inline-block;"  name="'.$name.'" id="'.$name.'" value="'.$val[1].'"> '.ucfirst($val[1]).'</span>';
        $radiobutton .='<span class="bc_width_20_percentage"><input type="radio" style="display:inline-block;"  name="'.$name.'" id="'.$name.'" value="'.$val[2].'"> '.ucfirst($val[2]).'</span>';
        return $radiobutton;
    }

    public function createDropDown($name,$append,$start,$end,$id){
       $name=$name."_".$id;
        $dropdown ='<select name="'.$name.'" id="'.$name.'"  class="bc_width_10">';
        for($i=$start;$i<=$end;$i++){
            if($i==0 || $i==1){
                $dropdown.='<option value="'.$i.'">'.$i.' '.$append.'</option>';
            }else{
                $dropdown.='<option value="'.$i.'">'.$i.' '.$append.'s</option>';
            }
        }
        $dropdown.='</select>';
        return $dropdown;
    }

    public function createInputbox($name,$id,$validate=false){
        $name=$name."_".$id;
        if($validate){
            $inputbox='<input type="text" id="'.$name.'" name="'.$name.'" onblur="if(isNaN(this.value)){$(this).css(\'border\', \'1px solid red\');}else{$(this).css(\'border\', \'1px solid #cccccc\');};">';
        }else{
            $inputbox='<input type="text" id="'.$name.'" name="'.$name.'">';
        }
        return $inputbox;

    }

    public function createRadio($name,$id){
        $name=$name."_".$id;
        $radiobutton='';
        $radiobutton .='<span class="bc_width_15"><input class="bc_width_20" type="radio" style="display:inline-block;" name="'.$name.'" id="'.$name.'" value="no"> No </span>';
        $radiobutton .='<span class="bc_width_15"><input class="bc_width_20" type="radio" style="display:inline-block"  name="'.$name.'" id="'.$name.'" value="yes"> Yes </span> ';
        return $radiobutton;
    }

    public function submitSpData($request){
        $error='';
        $filter_arr= config('list_your_business');
        $program_filter_arr=$this->getProgramArrFilter();
        $subcatid_arr=array($request->input('cat_id'));
        $subcatid_str='';

        if($request->input('catId')){

            $subcatid_arr = array_merge($subcatid_arr,$request->input('catId'));

        }
        $subcatid_str = implode(",",array_filter($subcatid_arr));
        //$subcatid_str = 
        if(trim($request->input('name')) == ''){
            $error .='Please enter service provider name<br>';
        }
        elseif(!$request->input('business_hours')){
            $error .='Please add business hours<br>';
        }/*elseif(in_array('134',$subcatid_arr)){
            foreach($program_filter_arr['134'] AS $key =>$program){

               if($_POST[$program[0]]==''){
                  $error .='Please select the programs are avalible or not<br>';
                   break;
               }
            }
        }this may be use in future*/
        else
        {
          //dd($subcatid_arr);
            foreach(array_filter($subcatid_arr) AS $subcatids){

                foreach($filter_arr['category'][$subcatids] AS $filter_val){

                   if($filter_val=='age_group'){
                        $min_value_age=$request->input($filter_val.'_min_year_'.$subcatids).".".$request->input($filter_val.'_min_month_'.$subcatids);
                        $max_value_age=$request->input($filter_val.'_max_year_'.$subcatids).".".$request->input($filter_val.'_max_month_'.$subcatids);
                       if($min_value_age=='0.0' || $max_value_age=='0.0'){
                            $error .='Please select age group<br>';
                       }

                    }
                    elseif($filter_val=='price_range' && $subcatids=='164' ){
                        $min_value=$request->input($filter_val.'_min_'.$subcatids);
                        $max_value=$request->input($filter_val.'_max_'.$subcatids);
                        if($min_value=='' || $max_value=='' || $min_value=='0' ||  $max_value=='0' ){
                            $error .='Please Enter Price range<br>';
                        }

                    }
                   else{
                       if($filter_val!='price_range' && $filter_val!='consultation_charge' && $filter_val!='cord_blood_bank'){

                            $filter_sub_val=$request->input($filter_val.'_'.$subcatids);
                            $filter_name=str_replace("_"," ",$filter_val);

                            if(!$filter_sub_val){
                                $error .='Please select or enter the compulsory field';
                                break;
                            }
                       }

                    }
                }
            }


        }

        if($error==''){
            $business_hours_post= $request->input('business_hours');
            //explode(",",rtrim($request->input('business_hours'),","));

            $getWeekDaysArray = config('list_your_business')['business_hours']['days'];

            $str='';
            $business_hours=array();
            foreach($business_hours_post AS $val){

                //$str = explode("_",$val);
                /*dd($val);
                $business_hours_new['day']    = $getWeekDaysArray[$str[0]];
                $business_hours_new['start']  = date("H:i", strtotime($str[1]));
                $business_hours_new['end']    = date("H:i", strtotime($str[2]));;*/

                $business_hours[] = $val;
                //$business_hours_new;
            }

            if(!empty($business_hours)){
                $business_hours=json_encode($business_hours);
            }

            $images_array=array();
            for($i=0;$i<5;$i++){
                if($request->input('images')!=''){
                    $images_array[]=$request->input('images');
                }
            }
            if(!empty($images_array)){
                $images_array=json_encode($images_array);
            }else{
                $images_array='';
            }


            $optional_filter_arr=$this->getOptionalFilterArr();

            $admission_filter_arr=$this->getAdmissionFilterArr();

            $detail_data=array();
            $sp_cat_detail=array();

            foreach($subcatid_arr AS $key=>$subcatids){
                $package_values_arr=array();
                $program_arr=array();
                $cat_map_array_filter=array();
                $option_arr=array();
                $admission_arr=array();
                if(array_key_exists($subcatids,$filter_arr['category'])){

                    foreach($filter_arr['category'][$subcatids] AS $filter_val){

                        if($filter_val=='price_range' || $filter_val=='consultation_charge' || $filter_val=='cord_blood_bank'){
                            $min_value=$request->input($filter_val.'_min_'.$subcatids);
                            $max_value=$request->input($filter_val.'_max_'.$subcatids);

                            if($min_value!='' && $max_value==''){
                                $max_value=$min_value;
                            }
                            if($min_value!='' && $max_value!=''){
                                $package_values_arr=array('package_name'=>$filter_val,'min_charges'=>$min_value,'max_charges'=>$max_value, 'unit'=>$filter_arr['unit'][$subcatids]);
                            }

                        }
                        elseif($filter_val=='age_group'){
                            $min_value_age=$request->input($filter_val.'_min_year_'.$subcatids).".".$request->input($filter_val.'_min_month_'.$subcatids);
                            $max_value_age=$request->input($filter_val.'_max_year_'.$subcatids).".".$request->input($filter_val.'_max_month_'.$subcatids);
                            $cat_map_array_filter['age_group_min']=$min_value_age;
                            $cat_map_array_filter['age_group_max']=$max_value_age;

                        }else{

                            if($filter_val=='specialization' && $subcatids==145){

                                if(trim($request->input($filter_val.'_'.$subcatids))=='Other'){
                                    $filter_sub_val=$request->input($filter_val.'_other_'.$subcatids);
                                    $cat_map_array_filter[$filter_val]=$filter_sub_val;
                                }else{
                                    $filter_sub_val=$request->input($filter_val.'_'.$subcatids);
                                    $cat_map_array_filter[$filter_val]=$filter_sub_val;
                                }
                            }else{
                                $filter_sub_val=$request->input($filter_val.'_'.$subcatids);
                                $cat_map_array_filter[$filter_val]=$filter_sub_val;
                            }

                        }
                    }


                 }


                if(array_key_exists($subcatids,$program_filter_arr)){
                    if($subcatids=='106'){
                        $activity_data = array();
                        for($i=0;$i<5;$i++){

                            if($request->input('activities_'.$i)){
                                foreach($program_filter_arr[$subcatids] AS $key=>$program_val_arr){

                                    foreach($program_val_arr AS $value ){
                                        $value=str_replace("_0","",$value);
                                        $text=str_replace("_"," ",$value);
                                        $activity_data[$request->input('activities_'.$i)][$text]=$request->input($value.'_'.$i);
                                    }
                                }
                            }

                        }

                        $program_arr['activity'] = $activity_data;

                    }else{
                        foreach($program_filter_arr[$subcatids] AS $key=>$program_val_arr){
                            foreach($program_val_arr AS $value ){

                                if(!empty($request->input($value))){
                                    $text=str_replace("_"," ",$value);
                                    $program_arr[$key][$text]=$request->input($value);
                                }
                            }
                        }
                    }
                }
                if(array_key_exists($subcatids,$optional_filter_arr)){
                    foreach($optional_filter_arr[$subcatids] AS $key=>$option_arr_val){
                            if( $request->input($option_arr_val[0]."_".$subcatids) && $request->input($option_arr_val[0]."_".$subcatids) != ''){
                                $option_arr[$key]=$request->input($option_arr_val[0]."_".$subcatids);
                             }
                    }
                }

                if(array_key_exists($subcatids,$admission_filter_arr)){
                    foreach($admission_filter_arr[$subcatids] AS $key=>$admission_arr_val){
                        if($admission_arr_val=='start_of_admissions_season'){
                            $date=array();
                             for($i=0;$i<3;$i++){
                                 $date[]=$request->input($admission_arr_val."_".$i);
                             }
                            $admission_arr['start_of_admissions_season']=$date;
                        }

                        if($request->input($admission_arr_val."_".$subcatids) && $request->input($admission_arr_val."_".$subcatids) != ''){
                            $admission_arr[$key]=$request->input($admission_arr_val."_".$subcatids);
                        }
                    }
                }

                $sp_cat_detail['category'][$subcatids]=array();


                if(!empty($package_values_arr)){
                    $sp_cat_detail['category'][$subcatids]['packages']=$package_values_arr;
                }
                if(!empty($cat_map_array_filter)){
                    $sp_cat_detail['category'][$subcatids]['category_map_data_filter']=$cat_map_array_filter;
                }
                if(!empty($program_arr)){
                    $sp_cat_detail['category'][$subcatids]['detail_data']['program']=$program_arr;
                }
                if(!empty($option_arr)){
                    $sp_cat_detail['category'][$subcatids]['detail_data']['optional_filter']=$option_arr;
                }
                if(!empty($admission_arr)){
                    $sp_cat_detail['category'][$subcatids]['detail_data']['admission_field_data']=$admission_arr;
                }


            }

            $city_name='';
            $location_name='';
            if(!empty($request->input('city_id'))){
                
                $city = City::where('id',$request->input('city_id'))->get();
                $city_name = $city[0]->getAttributes()['city_name'];

                /*$city=new City();
                $city_obj=$city->load($request->input('city_id'));
                $city_name=$city_obj->city_name;*/
            }
            if(!empty($request->input('location_id'))){

                /*$location_sql="SELECT location_name FROM location_master WHERE id='".$request->input('location_id')."'";
                $dbObj = DBConn::getInstance('READ');
                $sqlStmt = $dbObj->prepare($location_sql);

                $data = $sqlStmt->execute();
                $data = $sqlStmt->fetch();
                $location_name=$data['location_name'];*/

                $location = Location::where('id',$request->input('location_id'))->get();
                $location_name = $location[0]->getAttributes()['location_name'];

            }
            $sp_cat_detail_email=json_encode($sp_cat_detail,JSON_PRETTY_PRINT);
            $sp_cat_detail=json_encode($sp_cat_detail);
            $home_no='';
            $mobile_no='';
            if(trim($request->input('home')!='')){
                 $home_no=trim($request->input('landline_country_code')).'-'.trim($request->input('landline_city_code')).'-'.trim($request->input('home'));
            }
            if(trim($request->input('mobile')!='')){
                $mobile_arr=explode(",",$request->input('mobile'));

                foreach($mobile_arr As $value){
                    $mobile_arr1[]=$request->input('mobile_country_code').'-'.$value;
                }

                $mobile_no=implode(",",$mobile_arr1);

            }
            $momstar_offers='';
            if( $request->input('momstar_offers') && $request->input('momstar_offers') == 'yes'){
                $momstar_offers=addslashes($request->input('offers_detail'));
            }
              try {

                    $this->sub_cat_id = $subcatid_str;
                    $this->name = $request->input('name');
                    $this->email = $request->input('email');
                    $this->contact_name = $request->input('contact_person');
                    $this->contact = $home_no;
                    $this->Mobile = $mobile_no;
                    $this->address1 = $request->input('address');
                    $this->state_id = $request->input('state_id');
                    $this->country_id = 1;
                    $this->city_id = $request->input('city_id');
                    $this->city_name = $city_name;
                    $this->location_id = $request->input('location_id');
                    $this->location = $location_name;
                    $this->date_of_establishment = $request->input('date_estabilshment');
                    $this->business_hours = $business_hours;
                    $this->website = $request->input('website');
                    $this->facebook_url = $request->input('facebook_url');
                    $this->about = $request->input('about');
                    $this->category_mapping_filter_data = $sp_cat_detail;
                    $this->images_data = $images_array;
                    $this->momstar_offers = $momstar_offers;
                    
                    $this->save();

                  /*$sql_insert="INSERT INTO bc_services_providers_form_info (sub_cat_id,name,email,
                  contact_name,contact,Mobile,address1,state_id,country_id,city_id,city_name,location_id,
                  location,date_of_establishment,business_hours,website,facebook_url,about,
                  category_mapping_filter_data,images_data,momstar_offers,created_at)
                  VALUES ('".$subcatid_str."',
                  '".trim(addslashes($request->input('name')))."',
                  '".trim($_POST['email'])."',
                  '".trim($_POST['contact_person'])."',
                  '".trim($home_no)."',
                  '".trim($mobile_no)."',
                  '".trim($_POST['address'])."',
                  '".trim($_POST['state_id'])."',
                  1,
                  '".trim($request->input('city_id'))."',
                  '".$city_name."',
                  '".trim($request->input('location_id'))."',
                  '".$location_name."',
                  '".trim($_POST['date_estabilshment'])."',
                  '".$business_hours."',
                  '".trim($_POST['website'])."',
                  '".trim($_POST['facebook_url'])."',
                  '".trim(addslashes($_POST['about']))."',
                  '".$sp_cat_detail."',
                  '".$images_array."','".$momstar_offers."',now())";
                  $dbCon      = DBConn::getInstance("WRITE");

                  $sqlStmt = $dbCon->prepare ($sql_insert);
                  $sqlStmt->execute() ;
                  $spid=$dbCon->lastInsertId();*/



             /*$cat_name_str='';
              foreach($subcatid_arr AS $value){
                  $catObj=new Category();
                  $catData=$catObj->load($value);
                  $cat_name_str.=$catData->category_name.',';

              }
              $cat_name_str=rtrim($cat_name_str,",");*/
              /*$datawrite='Data of service provider<br>';
              //$datawrite='id,Category Name,sub_cat_id,name,email,contact_name,contact,Mobile,address1,state_id,country_id,city_id,city_name,location_id,
                 // location,date_of_establishment,business_hours,website,facebook_url,category_mapping_filter_data,images_data'.'/n';
               $datawrite.='Id:.'.$spid.'<br>Category Name:"'.$cat_name_str.'"<br>sub_cat_id:"'.$subcatid_str.'"<br>Email:'.trim($request->input('name')).'
               <br>Email:'.trim($_POST['email']).'<br>Contact person:'.trim($_POST['contact_person']).'<br>contact:'.trim($home_no)."<br>Mobile:".trim($mobile_no).
                  '<br>Address:'.trim($_POST['address']).'<br>StateId:'.trim($_POST['state_id']).'<br>Contry:India<br>City Id:'.trim($request->input('city_id')).
                  '<br>City Name:'.$city_name.'<br>Location:'.trim($request->input('location_id')).'<br> Location Name:'.$location_name.'
                  <br>Date of estabilshment:'.trim($_POST['date_estabilshment']).'<br>About:'.$_POST['about'].'<br>Business Hours:'.$business_hours.'
                  <br>website:'.trim($_POST['website']).'<br>Facebook Url:'.trim($_POST['facebook_url']).'<br>Category data:'.$sp_cat_detail_email.'<br>imageData:'.$images_array.'<br>momstar offers:'.$momstar_offers;
                  $subject='Service provider '.$request->input('name').' has submit form on BabyChakra';
              $bodyText = $datawrite;

              //$body = MailSender::basicReviewEmailTemplate($bodyText);
              $recepientEmail = 'miteshkaria@babychakra.com';
              $recepientName  = 'Mitesh Karia';
              $recepient2Email = 'rashi@babychakra.com';
              $recepient2Name  = 'Rashi Khadria';*/


                /*if(SEND_MAIL_THROUGH_SES == true){
                    $mail = new SimpleEmailServiceMessage();
                    $mail->addTo($recepientName.'<'.$recepientEmail.'>');
                    if($recepient2Email != ''){
                       $mail->addCC($recepient2Name.'<'.$recepient2Email.'>');
                    }
                    $mail->setFrom('BabyChakra<hello@babychakra.com>');
                    $mail->setSubject($subject);
                    $mail->setMessageFromHtml($bodyText);

                    $sendmail = new AwsMailSend();
                    $result = $sendmail->sendEmail($mail);
                    $response = array(  'status' => 'success','msg' => 'Thanks for submission we will get back to you shortly.');

                }
                else{
                    $mailSender = new MailSender();
                    $mailArray['subject']       = $subject;
                    $mailArray['from']          = 'hello@babychakra.com';
                    $mailArray['from_name']     = 'BabyChakra';
                    $mailArray['to_email']      = $recepient3Email;
                    $mailArray['to_name']       = $recepient3Name;
                    $mailArray['reply_email']   = 'hello@babychakra.com';
                    $mailArray['reply_name']    = 'BabyChakra';
                    $mailArray['body']          = $bodyText;

                    $mailSender->sendMail($mailArray);
                    $response = array(  'status' => 'success','msg' => 'Thanks for submission we will get back to you shortly.');
                }*/

                $response = array(  'status' => 'success','message' => 'Thanks for submission we will get back to you shortly.');

              }catch (Exception $e) {
                  $response = array(  'status' => 'error','message' => 'Sorry there is look like some problem in data');
              }

        }else{
            $response = array(  'status' => 'error','message' => $error);
        }
    return response()->json($response);
    }
}