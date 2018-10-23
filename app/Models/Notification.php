<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{


	// NOTIFICATION TYPES
	const PUBLISHED           	= 'published';
    const PUBLISHED_ARTICLE     = 'published_article';
    const PUBLISHED_REVIEW      = 'published_review';
    const PUBLISHED_FEEDPOST    = 'published_feedpost';
    const LIKED            		= 'liked';
    const LIKED_REVIEW          = 'liked_review';
    const LIKED_ARTICLE         = 'liked_article';
    const LIKED_FEEDPOST        = 'liked_feedpost';
    const LIKED_QUESTION        = 'liked_question';
    const LIKED_COMMENT         = 'liked_comment';
    const LIKED_ANSWER          = 'liked_answer';
    const COMMENTED           	= 'commented';
    const COMMENTED_REVIEW      = 'commented_review';
    const COMMENTED_ARTICLE     = 'commented_article';
    const COMMENTED_FEEDPOST    = 'commented_feedpost';
    const ANSWERED           	= 'answered';
    const ANSWERED_QUESTION     = 'answered_question';
    const FOLLOWED           	= 'followed';
    const FB_FRND           	= 'fb_friend';
    const EVENT_NEARBY          = 'event_nearby';
    const EVENT_NEARBY_V2       = 'event_nearby_v2';
    const REVIEWED          	= 'reviewed';
    const RECOMMENDED          	= 'recommended';
    const NOTIFY_TEAM_UNANSWERED= 'question_unanswered_notify_team';
    const HELP_TO_ANSWER        = 'help_to_answer';
    const EXPERT_TO_ANSWER      = 'expert_to_answer';

    const REFFERED_USER         = 'reffered_user';
    const REFEREE_USER_B        = 'referee_user_b';

    const TAGGED_USER           = 'tagged_user';
    const TAGGED_USER_QUESTION  = 'tagged_user_question';
    const TAGGED_USER_ARTICLE   = 'tagged_user_article';
    const TAGGED_USER_SERVICE   = 'tagged_user_service';
    const TAGGED_USER_REVIEW    = 'tagged_user_review';
    const TAGGED_USER_FEEDPOST   = 'tagged_user_feedpost';

    const FOLLOWED_USER_POSTED           = 'followed_user_posted';
    const FOLLOWED_USER_POSTED_QUESTION  = 'followed_user_posted_question';
    const FOLLOWED_USER_POSTED_ARTICLE   = 'followed_user_posted_article';
    const FOLLOWED_USER_POSTED_SERVICE   = 'followed_user_posted_service';
    const FOLLOWED_USER_POSTED_REVIEW    = 'followed_user_posted_review';
    const FOLLOWED_USER_POSTED_FEEDPOST   = 'followed_user_posted_feedpost';
    
    const INTEREST_TAG_ARTICLE = 'interest_tag_article';

    const COMMENT_ON_COMMENTED  = 'comment_on_commented';
    const COMMENT_ON_COMMENTED_ARTICLE  = 'comment_on_commented_article';
    const COMMENT_ON_COMMENTED_SERVICE  = 'comment_on_commented_service';
    const COMMENT_ON_COMMENTED_FEEDPOST  = 'comment_on_commented_feedpost';
    const COMMENT_ON_COMMENTED_QUESTION  = 'comment_on_commented_question';
    const COMMENT_ON_COMMENTED_REVIEW  = 'comment_on_commented_review';

    const WELCOME               = 'welcome';
    const CUSTOM                = 'custom';
    const MARKETING_NOTIFICATION = 'marketing_notification';
    const INTEREST_TAG_NOTIFICATION = 'interest_tag_notification';

    //growth tracker notificatons
    const WELCOME_WEIGHT_HEIGHT_TITLE = 'welcome_weight_height_title';
    const WELCOME_WEIGHT_HEIGHT_DESCRIPTION = 'welcome_weight_height_description';
    const CALENDAR_WEIGHT_HEIGHT_TITLE = 'calendar_weight_height_title';
    const CALENDAR_WEIGHT_HEIGHT_DESCRIPTION = 'calendar_weight_height_description';
    const WELCOME_CALENDAR_WEIGHT_HEIGHT_TITLE = 'welcome_calendar_weight_height_title';
    const WELCOME_CALENDAR_WEIGHT_HEIGHT_DESCRIPTION = 'welcome_calendar_weight_height_description';
    const WELCOME_MILESTONE_ACHIEVED_TITLE = 'welcome_milestone_achieved_title';
    const WELCOME_MILESTONE_ACHIEVED_DESCRIPTION = 'welcome_milestone_achieved_description';
    const ACHIEVED_MILESTONE_TIP_TITLE = 'achieved_milestone_tip_title';
    const SKILL_NOTIFICATION_TITLE = 'skill_notification_title';
    const WEIGHT_GAIN_TIP_TITLE = 'weight_gain_tip_title';
    const HEIGHT_GAIN_TIP_TITLE = 'height_gain_tip_title';
    const VACCINATION_TITLE = 'vaccination_title';
    const VACCINATION_DESCRIPTION = 'vaccination_description';

    const GROWTH_TRACKER = 'growth-tracker';
    const METRIC = 'metric';
    const VACCINE = 'user-metrics/vaccine';


    // NOTIFICATION STATES
    const STATE_CREATED 	= 'CREATED';
    const STATE_SENT		= 'SENT';
    const STATE_DELIVERED 	= 'DELIVERED';
    const STATE_SEEN		= 'SEEN';
    const STATE_READ 		= 'READ';
    const STATE_EXPIRED 	= 'EXPIRED';
    const STATE_DELETED 	= 'DELETED';
    const STATE_UPGRADED	= 'UPGRADED';

    public static $unreadNotificationStates = [self::STATE_CREATED, self::STATE_SENT, self::STATE_DELIVERED, self::STATE_SEEN];
	
    protected $table = 'notifications';
}
