<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

use App\Models\Service_category;
use App\Models\SubCategoryFieldType;
use App\Models\SubCategoryField;

class SubcategoryFieldsUpdate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        // adding behaviour to fields types
        Schema::table('sub_category_field_types', function(Blueprint $table) {
                $table->string('behaviour', 55)->default('');
        });

        foreach ($this->fields() as $val) {
            $fields = SubCategoryFieldType::where('name', $val[1])->first();
            if(!$fields){
                $fields = new SubCategoryFieldType;
            }

            $fields->title = $val[0];
            $fields->name = $val[1];
            $fields->type = $val[2];
            $fields->params = (isset($val[3])) ? $val[3] : '' ;
            if($val[2] == SubCategoryFieldType::TYPE_DROPDOWN){
                $fields->behaviour = (isset($val[4])) ? $val[4] : 'multi_select' ;
            } 
            else{
                $fields->behaviour = '';
            }
            
            $fields->save();
        }


        // add columns to 
        $serviceCategoryMapping = Service_category::first();
        $fieldTypes = SubCategoryFieldType::all();

        Schema::table('service_provider_category_mapping_new', function(Blueprint $table) use ($serviceCategoryMapping, $fieldTypes) {
            foreach ($fieldTypes as $field) {
                $fieldName = $field->name;
                if(!isset($serviceCategoryMapping->$fieldName)){
                    if($field->type == SubCategoryFieldType::TYPE_INTEGER){
                        $table->integer($field->name);
                    }
                    elseif($field->type == SubCategoryFieldType::TYPE_BOOLEAN){
                        $table->tinyInteger($field->name);
                    }
                    elseif($field->type == SubCategoryFieldType::TYPE_DROPDOWN){
                        $table->text($field->name);
                    }
                    elseif($field->type == SubCategoryFieldType::TYPE_TEXT){
                        $table->string($field->name, 255)->default('');
                    }
                }
            }     
        });



        foreach ($this->fieldsMapping() as $field_key => $category_id) {

            $fieldType = SubCategoryFieldType::where('name', $field_key)->first();

            if($fieldType && !SubCategoryField::where('sub_category_id', $category_id)->where('field_id', $fieldType->id)->exists()){
                $field = new SubCategoryField;
                $field->sub_category_id = $category_id;
                $field->field_id = $fieldType->id;
                $field->save();    
            }

        }


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }



    public function fields(){
        return [
            ['Price for Pregnancy Tests','preg_test_price', SubCategoryFieldType::TYPE_INTEGER],
            ['Home Blood Tests Available',  'home_blood_tests', SubCategoryFieldType::TYPE_BOOLEAN],
            ['Specialization','specialization',SubCategoryFieldType::TYPE_DROPDOWN, 'General pediatrician,Paediatric Dentistry,Paediatric Opthalmology,Paediatric Homeopathy,Alternate therapy,Speech Therapist,Pediatric Orthopaedics,Pediatric oncology,Pediatric neonatology,Pediatric cardiology,Paediatric diabetology,Pediatric Surgeon,Neonatologist'],
            ['Qualification',   'qualification', SubCategoryFieldType::TYPE_TEXT],
            ['Counselling Channels', 'counselling', SubCategoryFieldType::TYPE_DROPDOWN, 'at home,via Skype,on Home'],
            ['Transportation Facility', 'transportation',   SubCategoryFieldType::TYPE_BOOLEAN],
            ['Air Conditioner Classes', 'air_conditioner',  SubCategoryFieldType::TYPE_BOOLEAN],
            ['Class Size ','class_size', SubCategoryFieldType::TYPE_INTEGER],
            ['Child Teacher Ratio', 'child_teach_ratio',  SubCategoryFieldType::TYPE_INTEGER],
            ['Child Attendant Ratio', 'child_attendant_ratio',  SubCategoryFieldType::TYPE_INTEGER],
            ['Membership', 'membership', SubCategoryFieldType::TYPE_BOOLEAN],
            ['Party Packages', 'party_packages', SubCategoryFieldType::TYPE_BOOLEAN],
            ['Kid\'s Special Menu', 'kid_special_menu', SubCategoryFieldType::TYPE_BOOLEAN],
            ['Duration of Classes', 'class_duration', SubCategoryFieldType::TYPE_INTEGER],
            ['Price Min', 'price_range_min', SubCategoryFieldType::TYPE_INTEGER],
            ['Price Max', 'price_range_max', SubCategoryFieldType::TYPE_INTEGER],
            ['Age Min', 'age_group_min', SubCategoryFieldType::TYPE_INTEGER],
            ['Age Max', 'age_group_max', SubCategoryFieldType::TYPE_INTEGER],
            ['Doctors Experience', 'dr_experience', SubCategoryFieldType::TYPE_TEXT],
            ['Prior Appointment Required', 'prior_appointment_required', SubCategoryFieldType::TYPE_BOOLEAN],
            ['Nursing Room', 'nursing_room', SubCategoryFieldType::TYPE_BOOLEAN],
            ['Promotes Normal Delivery', 'normal_delivery', SubCategoryFieldType::TYPE_BOOLEAN],
            ['24/7 Support', '24_7_support', SubCategoryFieldType::TYPE_BOOLEAN],
            ['Post Delivery Support', 'post_delivery_support', SubCategoryFieldType::TYPE_BOOLEAN],
            ['Kid\'s Friendly Space', 'kids_frienldy_space', SubCategoryFieldType::TYPE_BOOLEAN],
            ['Home Visits',  'home_visits', SubCategoryFieldType::TYPE_BOOLEAN],
            ['After hours consultation',  'after_hours_consultation', SubCategoryFieldType::TYPE_BOOLEAN],
            ['Hospitalization Facility Available',  'hospitalization_facility', SubCategoryFieldType::TYPE_BOOLEAN],
            ['Hospital Associated With',  'hospitalization_associated_with', SubCategoryFieldType::TYPE_BOOLEAN],
            ['Playschool Types','playschool_types',SubCategoryFieldType::TYPE_DROPDOWN, 'Play group,Pre-nursery,Nursery,Junior Kg'],
            ['Teaching Methodology','teaching_methodology',SubCategoryFieldType::TYPE_DROPDOWN, 'Montessori education,Playway Method,Multiple Intelligence,Reggio Emilia method,Waldorf Education/ Steiner Education,Progressive Education,Other'],
            ['Playschool Facilities','playschool_facilities',SubCategoryFieldType::TYPE_DROPDOWN, 'Meals/snacks,Separate Pantry,Outdoor play area,Indoor play area,Pickup and drop facility available,Daycare facility/creche,Air conditioned classes'],
            ['Admission Cycle','admission_cycle',SubCategoryFieldType::TYPE_DROPDOWN, 'January,February,March,April,May,June,July,August,September,October,November,December,Throught the Year'],
            ['Multiple locations',  'multiple_locations', SubCategoryFieldType::TYPE_BOOLEAN],
            ['Sing along sessions with parents',  'sing_along_parents', SubCategoryFieldType::TYPE_BOOLEAN],
            ['Festival celebrations',  'festival_celebration', SubCategoryFieldType::TYPE_BOOLEAN],
            ['Field visits',  'field_visits', SubCategoryFieldType::TYPE_BOOLEAN],
            ['Bday celebration for every child',  'childs_birthday_celebration', SubCategoryFieldType::TYPE_BOOLEAN],
            ['Area of Premises in sq.ft',  'area_of_premises', SubCategoryFieldType::TYPE_TEXT],
            ['Location of Premises','premises_location',SubCategoryFieldType::TYPE_DROPDOWN, 'Residential,Commercial', 'single_select'],
            ['Mother toddler programs', 'mother_toddler_programs', SubCategoryFieldType::TYPE_BOOLEAN],
            ['Teacher Parent Interaction', 'teacher_parent_interaction', SubCategoryFieldType::TYPE_DROPDOWN, 'Weekly,Monthly,Anytime', 'single_select'],
            ['Safety', 'safety_features', SubCategoryFieldType::TYPE_DROPDOWN, '100% female supervision,Security Guard,CCTV/Live feed,Female attendant in transportation vehicle'],
            ['Conducts Summer Camps', 'summer_camps', SubCategoryFieldType::TYPE_BOOLEAN],
            ['Advance booking required', 'advance_booking_required', SubCategoryFieldType::TYPE_BOOLEAN],
            ['Indoor Games', 'indoor_games', SubCategoryFieldType::TYPE_BOOLEAN],
            ['Library', 'library', SubCategoryFieldType::TYPE_BOOLEAN],
            ['After School Learning Activities', 'after_school_learning_activities', SubCategoryFieldType::TYPE_TEXT],
            ['Day Care Type', 'day_care_types', SubCategoryFieldType::TYPE_DROPDOWN, 'Full Day Facility,Half Day Facility'],
            ['Activities available', 'activities_available',SubCategoryFieldType::TYPE_TEXT],
            ['100 % female supervision', 'female_supervision', SubCategoryFieldType::TYPE_BOOLEAN],
            ['CareTaker Child Ratio', 'child_caretaker_ratio',  SubCategoryFieldType::TYPE_INTEGER],
            ['Full day monthly pricing', 'day_care_fullday_price', SubCategoryFieldType::TYPE_INTEGER],
            ['Half day monthly pricing', 'day_care_halfday_price', SubCategoryFieldType::TYPE_INTEGER],
            ['Customized packages available', 'customized_packages', SubCategoryFieldType::TYPE_BOOLEAN],
            ['Medium of Communication', 'communication_medium', SubCategoryFieldType::TYPE_DROPDOWN, 'English,Hindi,Marathi,other'],
            ['Day Care Facilities', 'day_care_facilities', SubCategoryFieldType::TYPE_DROPDOWN, 'First aid kit available,Childcare Counsellor,AC classroom,Playground,Power backup,Live Feed,Surveillance,Boy/girl separate Restrooms,library,Babyproofed,Pickup and drop facility available,Optional meals,Only veg meals,Owner/Supervisor remains present herself,Meals for infants prepared on request,Breakfast,Brunch,Lunch,Snacks'],
            ['Child Friendliness Index', 'child_friendliness_index', SubCategoryFieldType::TYPE_INTEGER],
            ['Photography for Events Covered', 'photography_events_covered', SubCategoryFieldType::TYPE_DROPDOWN, 'Maternity Shoot,Newborn ,Baby Photography,Family shoots,Bday Party,Baby Showers,Naming ceremony,Cradle ceremony'],
            ['Which type of photography you specialise in?', 'photography_specialization', SubCategoryFieldType::TYPE_DROPDOWN, 'Candid,Traditional'],
            ['Where do you conduct the shoots?', 'photography_shoot_location', SubCategoryFieldType::TYPE_DROPDOWN, 'Indoor,Outdoor,Studio'],
            ['What about your shoot timings and days?', 'photography_shoot_timings', SubCategoryFieldType::TYPE_DROPDOWN, 'Flexible,Fixed'],
            ['What about your shoot timings and days?', 'photography_shoot_timings', SubCategoryFieldType::TYPE_DROPDOWN, 'Flexible,Fixed'],
            ['Is prior booking recommended?', 'photography_booking_recommended', SubCategoryFieldType::TYPE_BOOLEAN],
            ['After how many days of booking, shoot happens?', 'photography_days_after_booking', SubCategoryFieldType::TYPE_INTEGER],
            ['What will you give as the final product?', 'photography_final_product', SubCategoryFieldType::TYPE_DROPDOWN, 'Digital,Hard Copy'],
            ['Can visit entire city for shoot?', 'photography_entire_city_covered', SubCategoryFieldType::TYPE_BOOLEAN],
            ['Areas covered', 'areas_covered', SubCategoryFieldType::TYPE_TEXT],
        ];
    }

    public function fieldsMapping(){
        return ['dr_experience' => 128,'specialization' => 128,'prior_appointment_required' => 128,'nursing_room' => 128,'normal_delivery' => 128,'24_7_support' => 128,'post_delivery_support' => 128,'qualification' => 131,'dr_experience' => 131,'prior_appointment_required' => 131,'nursing_room' => 131,'kids_frienldy_space' => 131,'home_visits' => 131,'24_7_support' => 131,'backup_doctor' => 131,'hospitalization_facility' => 131,'hospitalization_associated_with' => 131,'playschool_types' => 134,'teaching_methodology' => 134,'child_teach_ratio' => 134,'admission_cycle' => 134,'playschool_facilities' => 134,'multiple_locations' => 134,'sing_along_parents' => 134,'festival_celebration' => 134,'field_visits' => 134,'childs_birthday_celebration' => 134,'area_of_premises' => 134,'premises_location' => 134,'mother_toddler_programs' => 134,'teacher_parent_interaction' => 134,'safety_features' => 134,'summer_camps' => 134,'advance_booking_required' => 134,'indoor_games' => 134,'library' => 134,'after_school_learning_activities' => 134,'day_care_types' => 156,'activities_available' => 156,'female_supervision' => 156,'premises_location' => 156,'child_caretaker_ratio' => 156,'day_care_fullday_price' => 156,'day_care_halfday_price' => 156,'customized_packages' => 156,'communication_medium' => 156,'day_care_facilities' => 156,'child_friendliness_index' => 156,'advance_booking_required' => 137,'photography_events_covered' => 137,'photography_specialization' => 137,'photography_shoot_location' => 137,'photography_shoot_timings' => 137,'photography_booking_recommended' => 137,'photography_days_after_booking' => 137,'photography_final_product' => 137,'photography_entire_city_covered' => 137,'areas_covered' => 137];
    }

}
