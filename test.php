<?php

require_once("Home.php"); // including home controller

/**
 * class config
 * @category controller
 */
class Dashboard extends Home {

    public $user_id;

    /**
     * load constructor method
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
        if ($this->session->userdata('logged_in') != 1)
            redirect('home/login_page', 'location');
        $this->user_id = $this->session->userdata('user_id');

        set_time_limit(0);
        $this->important_feature();
        $this->member_validity();
    }

    /**
     * load index method. redirect to config
     * @access public
     * @return void
     */
    public function index($default_value = '0') {
        $this->is_broadcaster_exist = $this->broadcaster_exist();
        if ($this->session->userdata('user_type') != 'Admin')
            $default_value = '0';
        if ($default_value == '0') {
            $user_id = $this->user_id;
            $data['other_dashboard'] = '0';
        } else {
            $user_id = $default_value;
            if ($default_value == 'system')
                $data['system_dashboard'] = 'yes';
            else {
                $user_info = $this->basic->get_data('users', array('where' => array('id' => $user_id)));
                $data['user_name'] = isset($user_info[0]['name']) ? $user_info[0]['name'] : '';
                $data['user_email'] = isset($user_info[0]['email']) ? $user_info[0]['email'] : '';
                $data['system_dashboard'] = 'no';
            }

            $data['other_dashboard'] = '1';
        }

        if ($this->is_demo === '1' && $data['other_dashboard'] === '1' && isset($data['system_dashboard']) && $data['system_dashboard'] === 'no') {
            echo "<h2 style='text-align:center;color:red;border:1px solid red; padding: 10px'>This feature is disabled in this demo.</h2>";
            exit();
        }


        $current_year = date("Y");
        $lastyear = $current_year - 1;
        $current_month = date("Y-m");
        $current_date = date("Y-m-d");
        $data['month_number'] = date('m');

        // first item section
        $total_subscribers = 0;
        $where = array(
            'where' => array(
                // 'user_id' => $user_id,
                'date_format(subscribed_at,"%Y-%m")' => $current_month,
                'permission' => '1',
                'social_media' => 'fb'
            )
        );
        if ($default_value != 'system')
           $where['where']['user_id'] = $user_id;
        $select = array('count(id) as subscribers');
        $subscriber_info = $this->basic->get_data('messenger_bot_subscriber', $where, $select);
        $subscribed = isset($subscriber_info[0]['subscribers']) ? $subscriber_info[0]['subscribers'] : 0;
        $data['subscribed'] = $subscribed;
        $where = array(
            'where' => array(
                // 'user_id' => $user_id,
                'date_format(unsubscribed_at,"%Y-%m")' => $current_month,
                'permission' => '0',
                'social_media' => 'fb'
            )
        );
        if ($default_value != 'system')
            $where['where']['user_id'] = $user_id;
        $select = array('count(id) as unsubscribers');
        $unsubscriber_info = $this->basic->get_data('messenger_bot_subscriber', $where, $select);
        $unsubscribed = isset($unsubscriber_info[0]['unsubscribers']) ? $unsubscriber_info[0]['unsubscribers'] : 0;
        $data['unsubscribed'] = $unsubscribed;
        $total_subscribers = $subscribed + $unsubscribed;
        $data['total_subscribers'] = $total_subscribers;

        $where = array(
            'where' => array(
                // 'user_id' => $user_id,
                'date_format(completed_at,"%Y-%m")' => $current_month
            )
        );
        if ($default_value != 'system')
            $where['where']['user_id'] = $user_id;
        $select = array('sum(successfully_sent) as total_message_sent');
        $conversation_message_sent_info = $this->basic->get_data("facebook_ex_conversation_campaign", $where, $select);
        $total_conversion_message_sent = isset($conversation_message_sent_info[0]['total_message_sent']) ? $conversation_message_sent_info[0]['total_message_sent'] : 0;

        $where = array(
            'where' => array(
                // 'user_id' => $user_id,
                'date_format(completed_at,"%Y-%m")' => $current_month
            )
        );
        if ($default_value != 'system')
            $where['where']['user_id'] = $user_id;
        if ($this->is_broadcaster_exist) {
            $select = array('sum(successfully_sent) as total_message_sent');
            $broadcast_message_sent_info = $this->basic->get_data("messenger_bot_broadcast_serial", $where, $select);
        }
        $total_broadcast_message_sent = isset($broadcast_message_sent_info[0]['total_message_sent']) ? $broadcast_message_sent_info[0]['total_message_sent'] : 0;
        $data['total_message_sent'] = $total_conversion_message_sent + $total_broadcast_message_sent;

        // end of first item section
        // second item section [last 7 days subscribers]
        $last_seven_day = date("Y-m-d", strtotime("$current_date - 7 days"));
        $where = array(
            'where' => array(
                // 'user_id' => $user_id,
                'date_format(subscribed_at,"%Y-%m-%d") >=' => $last_seven_day,
                'social_media' => 'fb'
            )
        );
        if ($default_value != 'system')
            $where['where']['user_id'] = $user_id;
        $select = array('count(id) as subscribers', 'date_format(subscribed_at,"%Y-%m-%d") as subscribed_at');
        $seven_days_subscriber_info = $this->basic->get_data('messenger_bot_subscriber', $where, $select, '', '', '', 'date_format(subscribed_at,"%Y-%m-%d") asc', 'date_format(subscribed_at,"%Y-%m-%d")');
        $seven_days_subscriber_chart_label = array();
        $seven_days_subscriber_chart_data = array();
        $seven_days_subscriber_gain = 0;
        if (!empty($seven_days_subscriber_info)) {
            foreach ($seven_days_subscriber_info as $value) {
                array_push($seven_days_subscriber_chart_label, date("jS M y", strtotime($value['subscribed_at'])));
                array_push($seven_days_subscriber_chart_data, $value['subscribers']);
                $seven_days_subscriber_gain = $seven_days_subscriber_gain + $value['subscribers'];
            }
        }
        $data['seven_days_subscriber_chart_label'] = $seven_days_subscriber_chart_label;
        $data['seven_days_subscriber_chart_data'] = $seven_days_subscriber_chart_data;
        $data['seven_days_subscriber_gain'] = $seven_days_subscriber_gain;
        // end of second item section
        // third item section [24 hour interaction]
        $current_time = date("Y-m-d H:i:s");
        $yesterday = date("Y-m-d H:i:s", strtotime($current_time . " -1 day"));
        $where = array(
            'where' => array(
                // 'user_id' => $user_id,
                'last_subscriber_interaction_time >=' => $yesterday,
                'social_media' => 'fb'
            )
        );
        if ($default_value != 'system')
            $where['where']['user_id'] = $user_id;
        $select = array('count(id) as subscribers', 'date_format(last_subscriber_interaction_time,"%Y-%m-%d %H:%i") as subscribed_at');
        $hourly_subscriber_info = $this->basic->get_data('messenger_bot_subscriber', $where, $select, '', '', '', 'date_format(last_subscriber_interaction_time,"%Y-%m-%d %H") asc', 'date_format(last_subscriber_interaction_time,"%Y-%m-%d %H")');
        $hourly_subscriber_chart_label = array();
        $hourly_subscriber_chart_data = array();
        $hourly_subscriber_gain = 0;
        if (!empty($hourly_subscriber_info)) {
            foreach ($hourly_subscriber_info as $value) {
                array_push($hourly_subscriber_chart_label, date("h A", strtotime($value['subscribed_at'])));
                array_push($hourly_subscriber_chart_data, $value['subscribers']);
                $hourly_subscriber_gain = $hourly_subscriber_gain + $value['subscribers'];
            }
        }
        $data['hourly_subscriber_chart_label'] = $hourly_subscriber_chart_label;
        $data['hourly_subscriber_chart_data'] = $hourly_subscriber_chart_data;
        $data['hourly_subscriber_gain'] = $hourly_subscriber_gain;
        // end of third item section
        // forth item section [male vs female subscriber]
        $male_list = array();
        $female_list = array();
        $male_female_date_list = array();
        $past_thirty_day = date("Y-m-d", strtotime($current_date . " -30 days"));
        $where = array(
            'where' => array(
                // 'user_id' => $user_id,
                'date_format(subscribed_at,"%Y-%m-%d") >=' => $past_thirty_day,
                'is_bot_subscriber' => '1',
                'social_media' => 'fb'
            )
        );
        if ($default_value != 'system')
            $where['where']['user_id'] = $user_id;
        $select = array('count(id) as subscribers', 'gender', 'date_format(subscribed_at,"%Y-%m-%d") as subscribed_at');
        $subscriber_info = $this->basic->get_data('messenger_bot_subscriber', $where, $select, '', '', '', 'date_format(subscribed_at,"%Y-%m-%d") asc', 'date_format(subscribed_at,"%Y-%m-%d"),gender');
        foreach ($subscriber_info as $value) {
            if ($value['gender'] == 'male')
                $male_list[$value['subscribed_at']] = $value['subscribers'];
            else
                $female_list[$value['subscribed_at']] = $value['subscribers'];

            if (!isset($male_list[$value['subscribed_at']]))
                $male_list[$value['subscribed_at']] = 0;
            if (!isset($female_list[$value['subscribed_at']]))
                $female_list[$value['subscribed_at']] = 0;

            $formated_date = date("jS M", strtotime($value['subscribed_at']));
            $male_female_date_list[$value['subscribed_at']] = $formated_date;
        }

        $largest_values = array();
        $max_value = 1;
        if (!empty($male_list))
            array_push($largest_values, max($male_list));
        if (!empty($female_list))
            array_push($largest_values, max($female_list));
        if (!empty($largest_values))
            $max_value = max($largest_values);
        if ($max_value > 10)
            $data['step_size'] = floor($max_value / 10);
        else
            $data['step_size'] = 1;

        $data['male_subscribers'] = $male_list;
        $data['female_subscribers'] = $female_list;
        $data['male_female_date_list'] = $male_female_date_list;
        // end of forth item section [male vs female subscriber]
        // fifth item section [email,phone,birthdate,locale gain]
        // email section
        $where = array(
            'where' => array(
                // 'user_id' => $user_id,
                'date_format(entry_time,"%Y-%m")' => $current_month,
                'permission' => '1',
                'email !=' => '',
                'social_media' => 'fb'
            )
        );
        if ($default_value != 'system')
            $where['where']['user_id'] = $user_id;
        $select = array('count(id) as subscribers', 'gender');
        $subscriber_info = $this->basic->get_data('messenger_bot_subscriber', $where, $select, '', '', '', '', 'gender');
        $combined_info['email']['male'] = 0;
        $combined_info['email']['female'] = 0;
        foreach ($subscriber_info as $value) {
            if ($value['gender'] == 'male')
                $combined_info['email']['male'] = $value['subscribers'];
            else
                $combined_info['email']['female'] = $value['subscribers'];
        }
        $combined_info['email']['total_email_gain'] = $combined_info['email']['male'] + $combined_info['email']['female'];
        $percentage_info = $this->get_percentage($combined_info['email']['male'], $combined_info['email']['female']);
        $combined_info['email']['male_percentage'] = $percentage_info[0];
        $combined_info['email']['female_percentage'] = $percentage_info[1];
        // end of email section
        // phone section
        $where = array(
            'where' => array(
                // 'user_id' => $user_id,
                'date_format(phone_number_entry_time,"%Y-%m")' => $current_month,
                'permission' => '1',
                'phone_number !=' => '',
                'social_media' => 'fb'
            )
        );
        if ($default_value != 'system')
            $where['where']['user_id'] = $user_id;
        $select = array('count(id) as subscribers', 'gender');
        $subscriber_info = $this->basic->get_data('messenger_bot_subscriber', $where, $select, '', '', '', '', 'gender');
        $combined_info['phone']['male'] = 0;
        $combined_info['phone']['female'] = 0;
        foreach ($subscriber_info as $value) {
            if ($value['gender'] == 'male')
                $combined_info['phone']['male'] = $value['subscribers'];
            else
                $combined_info['phone']['female'] = $value['subscribers'];
        }
        $combined_info['phone']['total_phone_gain'] = $combined_info['phone']['male'] + $combined_info['phone']['female'];
        $percentage_info = $this->get_percentage($combined_info['phone']['male'], $combined_info['phone']['female']);
        $combined_info['phone']['male_percentage'] = $percentage_info[0];
        $combined_info['phone']['female_percentage'] = $percentage_info[1];
        // end of phone section
        // birthdate section
        $where = array(
            'where' => array(
                // 'user_id' => $user_id,
                'date_format(birthdate_entry_time,"%Y-%m")' => $current_month,
                'permission' => '1',
                'social_media' => 'fb'
            )
        );
        if ($default_value != 'system')
            $where['where']['user_id'] = $user_id;
        $select = array('count(id) as subscribers', 'gender');
        $subscriber_info = $this->basic->get_data('messenger_bot_subscriber', $where, $select, '', '', '', '', 'gender');
        $combined_info['birthdate']['male'] = 0;
        $combined_info['birthdate']['female'] = 0;
        foreach ($subscriber_info as $value) {
            if ($value['gender'] == 'male')
                $combined_info['birthdate']['male'] = $value['subscribers'];
            else
                $combined_info['birthdate']['female'] = $value['subscribers'];
        }
        $combined_info['birthdate']['total_birthdate_gain'] = $combined_info['birthdate']['male'] + $combined_info['birthdate']['female'];
        $percentage_info = $this->get_percentage($combined_info['birthdate']['male'], $combined_info['birthdate']['female']);
        $combined_info['birthdate']['male_percentage'] = $percentage_info[0];
        $combined_info['birthdate']['female_percentage'] = $percentage_info[1];
        // end of birthdate section


        $data['combined_info'] = $combined_info;
        // end of fifth item section [email,phone,birthdate,locale gain]
        // sixth item section [latest subscribers]
        $page_list = array();
        $latest_subscriber_list = array();
        $where = array(
            'where' => array(
                // 'user_id' => $user_id,
                'permission' => '1',
                'is_bot_subscriber' => '1',
                'social_media' => 'fb'
            )
        );
        if ($default_value != 'system')
            $where['where']['user_id'] = $user_id;
        $latest_subscriber_info = $this->basic->get_data('messenger_bot_subscriber', $where, '', '', 6, '', 'subscribed_at desc');

        $where = array(
            'where' => array()
        );
        if ($default_value != 'system')
            $where['where']['user_id'] = $user_id;
        $page_info = $this->basic->get_data('facebook_rx_fb_page_info', $where, array('id', 'page_name', 'page_id'));
        foreach ($page_info as $value) {
            $page_list[$value['id']]['page_name'] = $value['page_name'];
            $page_list[$value['id']]['page_id'] = $value['page_id'];
        }
        $i = 0;
        foreach ($latest_subscriber_info as $value) {
            $latest_subscriber_list[$i]['first_name'] = $value['first_name'];
            $latest_subscriber_list[$i]['last_name'] = $value['last_name'];
            $latest_subscriber_list[$i]['full_name'] = $value['full_name'];
            if ($value['link'] == '')
                $latest_subscriber_list[$i]['link'] = 'disabled';
            else
                $latest_subscriber_list[$i]['link'] = $value['link'];

            $latest_subscriber_list[$i]['subscribed_at'] = date_time_calculator($value['subscribed_at'], true);
            $latest_subscriber_list[$i]['subscribe_id'] = $value['subscribe_id'];
            $latest_subscriber_list[$i]['page_name'] = $page_list[$value['page_table_id']]['page_name'];
            $latest_subscriber_list[$i]['page_id'] = $page_list[$value['page_table_id']]['page_id'];

            $profile_pic = ($value['profile_pic'] != "") ? $value["profile_pic"] : base_url('assets/img/avatar/avatar-1.png');
            $latest_subscriber_list[$i]['image_path'] = ($value["image_path"] != "") ? base_url($value["image_path"]) : $profile_pic;

            $i++;
        }
        $data['latest_subscriber_list'] = $latest_subscriber_list;
        // end sixth item section [latest subscribers]
        // item section [latest 24h subscribers]
        $current_time = date("Y-m-d H:i:s");
        $yesterday = date("Y-m-d H:i:s", strtotime($current_time . " -1 day"));
        $where = array(
            'where' => array(
                // 'user_id' => $user_id,
                'last_subscriber_interaction_time >=' => $yesterday,
                'social_media' => 'fb'
            )
        );
        if ($default_value != 'system')
            $where['where']['user_id'] = $user_id;
        $latest_24hsubscriber_info = $this->basic->get_data('messenger_bot_subscriber', $where, '', '', 6, '', 'last_subscriber_interaction_time desc');

        $i = 0;
        $latest_24hsubscriber_list = array();
        foreach ($latest_24hsubscriber_info as $value) {
            $latest_24hsubscriber_list[$i]['first_name'] = $value['first_name'];
            $latest_24hsubscriber_list[$i]['last_name'] = $value['last_name'];
            $latest_24hsubscriber_list[$i]['full_name'] = $value['full_name'];
            $latest_24hsubscriber_list[$i]['link'] = $value['link'];
            $latest_24hsubscriber_list[$i]['last_subscriber_interaction_time'] = date_time_calculator($value['last_subscriber_interaction_time'], true);
            $latest_24hsubscriber_list[$i]['subscribe_id'] = $value['subscribe_id'];
            $latest_24hsubscriber_list[$i]['page_name'] = $page_list[$value['page_table_id']]['page_name'];
            $latest_24hsubscriber_list[$i]['page_id'] = $page_list[$value['page_table_id']]['page_id'];

            $profile_pic = ($value['profile_pic'] != "") ? $value["profile_pic"] : base_url('assets/img/avatar/avatar-1.png');
            $latest_24hsubscriber_list[$i]['image_path'] = ($value["image_path"] != "") ? base_url($value["image_path"]) : $profile_pic;

            $i++;
        }
        $data['latest_24hsubscriber_list'] = $latest_24hsubscriber_list;
        // end item section [latest 24h subscribers]
        // seventh item section [top sources of subscribers]
        $refferer_source_info = array();
        $refferer_source_info['checkbox_plugin']['title'] = $this->lang->line("Checkbox Plugin");
        $refferer_source_info['customer_chat_plugin']['title'] = $this->lang->line("Customer Chat Plugin");
        $refferer_source_info['sent_to_messenger']['title'] = $this->lang->line("Sent to Messenger Plugin");
        $refferer_source_info['me_link']['title'] = $this->lang->line("m.me Link");
        $refferer_source_info['direct']['title'] = $this->lang->line("Direct From Facebook");
        $refferer_source_info['direct']['subscribers'] = 0;
        $refferer_source_info['comment_private_reply']['title'] = $this->lang->line("Comment Private Reply");
        $where = array(
            'where' => array(
                // 'user_id' => $user_id,
                // 'date_format(subscribed_at,"%Y-%m")' => $current_month,
                'permission' => '1',
                'social_media' => 'fb'
            )
        );
        if ($default_value != 'system')
            $where['where']['user_id'] = $user_id;
        $select = array('count(id) as subscribers', 'refferer_source');
        $subscriber_refferer_info = $this->basic->get_data('messenger_bot_subscriber', $where, $select, '', '', '', '', 'refferer_source');

        foreach ($subscriber_refferer_info as $value) {
            if ($value['refferer_source'] == 'checkbox_plugin')
                $refferer_source_info['checkbox_plugin']['subscribers'] = $value['subscribers'];
            else if ($value['refferer_source'] == 'CUSTOMER_CHAT_PLUGIN')
                $refferer_source_info['customer_chat_plugin']['subscribers'] = $value['subscribers'];
            else if ($value['refferer_source'] == 'SEND-TO-MESSENGER-PLUGIN')
                $refferer_source_info['sent_to_messenger']['subscribers'] = $value['subscribers'];
            else if ($value['refferer_source'] == 'SHORTLINK')
                $refferer_source_info['me_link']['subscribers'] = $value['subscribers'];
            else if ($value['refferer_source'] == 'FB PAGE' || $value['refferer_source'] == '')
                $refferer_source_info['direct']['subscribers'] += $value['subscribers'];
            else if ($value['refferer_source'] == 'COMMENT PRIVATE REPLY')
                $refferer_source_info['comment_private_reply']['subscribers'] = $value['subscribers'];
        }
        $data['refferer_source_info'] = $refferer_source_info;
        // end of seventh item section [top sources of subscribers]
        // last auto reply report section
        $where = array(
            'where' => array()
        );
        if ($default_value != 'system')
            $where['where']['user_id'] = $user_id;
        $last_auto_reply_post_info = $this->basic->get_data('facebook_ex_autoreply_report', $where, $select = '', $join = '', $limit = '6', $start = NULL, 'reply_time DESC');

        $data['my_last_auto_reply_data'] = $last_auto_reply_post_info;
        // end of last auto reply report section
        // upcoming facebook poster campaign section
        $where = array(
            'where' => array(
                'posting_status' => '0'
            )
        );
        if ($default_value != 'system')
            $where['where']['user_id'] = $user_id;
        $scheduled_auto_post_campaign = $this->basic->get_data('facebook_rx_auto_post', $where, $select = '', $join = '', $limit = 5, $start = NULL, 'schedule_time ASC');
        $where = array(
            'where' => array(
                'posting_status' => '0'
            )
        );
        if ($default_value != 'system')
            $where['where']['user_id'] = $user_id;
        $scheduled_cta_post_campaign = $this->basic->get_data('facebook_rx_cta_post', $where, $select = '', $join = '', $limit = 5, $start = NULL, 'schedule_time ASC');
        $where = array(
            'where' => array(
                'posting_status' => '0'
            )
        );
        if ($default_value != 'system')
            $where['where']['user_id'] = $user_id;
        $scheduled_carousel_slider_campaign = $this->basic->get_data('facebook_rx_slider_post', $where, $select = '', $join = '', $limit = 5, $start = NULL, 'schedule_time ASC');

        $upcoming_post_campaign_array = array();

        foreach ($scheduled_auto_post_campaign as $value)
            $upcoming_post_campaign_array[] = $value;
        foreach ($scheduled_cta_post_campaign as $value)
            $upcoming_post_campaign_array[] = $value;
        foreach ($scheduled_carousel_slider_campaign as $value)
            $upcoming_post_campaign_array[] = $value;

        usort($upcoming_post_campaign_array, function($a, $b) {
            if ($a['schedule_time'] == $b['schedule_time'])
                return 0;
            else if ($a['schedule_time'] > $b['schedule_time'])
                return 1;
            else
                return -1;
        });
        $data['upcoming_post_campaign_array'] = $upcoming_post_campaign_array;
        // end of upcoming facebook poster campaign section
        // recently completed facebook poster campaign
        $where = array(
            'where' => array(
                'posting_status' => '2'
            )
        );
        if ($default_value != 'system')
            $where['where']['user_id'] = $user_id;
        $all_time_auto_post = $this->basic->get_data('facebook_rx_auto_post', $where, $select = '', $join = '', $limit = 5, $start = NULL, 'last_updated_at DESC');
        $where = array(
            'where' => array(
                'posting_status' => '2'
            )
        );
        if ($default_value != 'system')
            $where['where']['user_id'] = $user_id;
        $all_time_cta_post = $this->basic->get_data('facebook_rx_cta_post', $where, $select = '', $join = '', $limit = 5, $start = NULL, 'last_updated_at DESC');
        $where = array(
            'where' => array(
                'posting_status' => '2'
            )
        );
        if ($default_value != 'system')
            $where['where']['user_id'] = $user_id;
        $all_time_slider_post = $this->basic->get_data('facebook_rx_slider_post', $where, $select = '', $join = '', $limit = 5, $start = NULL, 'last_updated_at DESC');

        $recently_completed_post_array = array();
        foreach ($all_time_auto_post as $value)
            $recently_completed_post_array[] = $value;
        foreach ($all_time_cta_post as $value)
            $recently_completed_post_array[] = $value;
        foreach ($all_time_slider_post as $value)
            $recently_completed_post_array[] = $value;
        usort($recently_completed_post_array, function($a, $b) {
            if ($a['last_updated_at'] == $b['last_updated_at'])
                return 0;
            else if ($a['last_updated_at'] < $b['last_updated_at'])
                return 1;
            else
                return -1;
        });
        $data['recently_completed_post_array'] = $recently_completed_post_array;
        // end of recently completed facebook poster campaign

        $where = array(
            'where' => array(
                'posting_status' => '2'
            )
        );
        if ($default_value != 'system')
            $where['where']['user_id'] = $user_id;
        $recently_message_sent_completed_campaing_info = $this->basic->get_data('facebook_ex_conversation_campaign', $where, $select = '', $join = '', $limit = '5', $start = NULL, 'added_at DESC');

        $where = array(
            'where' => array(
                'posting_status' => '0'
            )
        );
        if ($default_value != 'system')
            $where['where']['user_id'] = $user_id;
        // $upcoming_message_sent_campaign_info = $this->basic->get_data('facebook_ex_conversation_campaign',$where,$select='',$join='',$limit='5',$start=NULL,'added_at DESC');

        $data['recently_message_sent_completed_campaing_info'] = $recently_message_sent_completed_campaing_info;
        // $data['upcoming_message_sent_campaign_info'] = $upcoming_message_sent_campaign_info;
//         $where = array(
//            'where' => array(
//                // 'user_id' => $user_id,
//                'date_format(updated_at,"%Y-%m")' => $current_month,
//                'action_type' => 'checkout',
//                'status' => 'rejected'
//            )
//        );
$a_jan=0;$a_feb=0;$a_mar=0;$a_apr=0;$a_may=0;$a_jun=0;$a_jul=0;$a_aug=0;$a_sep=0;$a_oct=0;$a_nov=0;$a_dec=0;
$w_jan=0;$w_feb=0;$w_mar=0;$w_apr=0;$w_may=0;$w_jun=0;$w_jul=0;$w_aug=0;$w_sep=0;$w_oct=0;$w_nov=0;$w_dec=0;
$web=0;$app=0;
        $trial=0;$starter=0;$basic=0;$advance=0;$lite=0;
        $data['total_store'] = $this->db->query("select * from ecommerce_store")->num_rows();
        $data['total_product'] = $this->db->query("select * from ecommerce_product")->num_rows();
        $data['total_merchant'] = $this->db->query("select * from users")->num_rows();
        $data['total_app_merchant'] = $this->db->query("select * from users where fcm_token!=''")->num_rows();
        $data['total_orders'] = $this->db->query("select * from ecommerce_cart where action_type='checkout'")->num_rows();
        $data['total_rejected'] = $this->db->query("select * from ecommerce_cart where status='rejected' and action_type='checkout'")->num_rows();
        $data['total_approved'] = $this->db->query("select * from ecommerce_cart where status='approved' and action_type='checkout'")->num_rows();
        $data['total_incomes'] = $this->db->query("select sum(payment_amount) as total_income from ecommerce_cart where action_type='checkout'")->result();
        $graphData=$this->db->query("select * from users where user_type='Member'")->result_array();
        foreach($graphData as $gdata){
          if ($gdata['package_id']=='1') {$trial++;}if ($gdata['package_id']=='2') {$lite++;}if ($gdata['package_id']=='3') {$starter++;}if ($gdata['package_id']=='4') {$basic++;}
          if ($gdata['package_id']=='5') {$advance++;}
          $udate=explode('-',$gdata['add_date']);
          $uYear=$udate[0];$uMonth=$udate[1];$curYear=date('Y');
          if (($uYear==$curYear)&&($uMonth=='01')) {if ($gdata['fcm_token']!='') {$a_jan++;}else{$w_jan++;}}
          if (($uYear==$curYear)&&($uMonth=='02')) {if ($gdata['fcm_token']!='') {$a_feb++;}else{$w_feb++;}}
          if (($uYear==$curYear)&&($uMonth=='03')) {if ($gdata['fcm_token']!='') {$a_mar++;}else{$w_mar++;}}
          if (($uYear==$curYear)&&($uMonth=='04')) {if ($gdata['fcm_token']!='') {$a_apr++;}else{$w_apr++;}}
            if (($uYear==$curYear)&&($uMonth=='05')) {if ($gdata['fcm_token']!='') {$a_may++;}else{$w_may++;}}
              if (($uYear==$curYear)&&($uMonth=='06')) {if ($gdata['fcm_token']!='') {$a_jun++;}else{$w_jun++;}}
                if (($uYear==$curYear)&&($uMonth=='07')) {if ($gdata['fcm_token']!='') {$a_jul++;}else{$w_jul++;}}
                  if (($uYear==$curYear)&&($uMonth=='08')) {if ($gdata['fcm_token']!='') {$a_aug++;}else{$w_aug++;}}
                    if (($uYear==$curYear)&&($uMonth=='09')) {if ($gdata['fcm_token']!='') {$a_sep++;}else{$w_sep++;}}
                      if (($uYear==$curYear)&&($uMonth=='10')) {if ($gdata['fcm_token']!='') {$a_oct++;}else{$w_oct++;}}
                        if (($uYear==$curYear)&&($uMonth=='11')) {if ($gdata['fcm_token']!='') {$a_nov++;}else{$w_nov++;}}
                          if (($uYear==$curYear)&&($uMonth=='12')) {if ($gdata['fcm_token']!='') {$a_dec++;}else{$w_dec++;}}

        }
        $data['package_data']=array($trial,$lite,$starter,$basic,$advance);
        $data['graph_web_data']=array($w_jan,$w_feb,$w_mar,$w_apr,$w_may,$w_jun,$w_jul,$w_aug,$w_sep,$w_oct,$w_nov,$w_dec);
          $data['graph_app_data']=array($a_jan,$a_feb,$a_mar,$a_apr,$a_may,$a_jun,$a_jul,$a_aug,$a_sep,$a_oct,$a_nov,$a_dec);
        if ($this->session->userdata('user_type') == 'Admin') {
            $data['body'] = 'dashboard/dashboard2';
        } else {
            $data['body'] = 'dashboard/dashboard';
        }
        $data['page_title'] = $this->lang->line('Dashboard');
        $this->_viewcontroller($data);
    }

    public function get_first_div_content($system_dashboard = 'no') {
        $this->ajax_check();
        $this->is_broadcaster_exist = $this->broadcaster_exist();
        $month_no = $this->input->post('month_no', true);
        if ($month_no == 'year')
            $search_year = date("Y");
        else
            $search_month = date("Y-{$month_no}");

        // first item section
        $total_subscribers = 0;
        $where_simple = array();
        if ($system_dashboard == 'no')
            $where_simple['user_id'] = $this->user_id;
        $where_simple['permission'] = '1';
        $where_simple['social_media'] = 'fb';
        if ($month_no == 'year')
            $where_simple['date_format(subscribed_at,"%Y")'] = $search_year;
        else
            $where_simple['date_format(subscribed_at,"%Y-%m")'] = $search_month;

        $where = array(
            'where' => $where_simple
        );
        $select = array('count(id) as subscribers');
        $subscriber_info = $this->basic->get_data('messenger_bot_subscriber', $where, $select);
        $subscribed = isset($subscriber_info[0]['subscribers']) ? $subscriber_info[0]['subscribers'] : 0;
        $data['subscribed'] = custom_number_format($subscribed);

        $where_simple = array();
        if ($system_dashboard == 'no')
            $where_simple['user_id'] = $this->user_id;
        $where_simple['permission'] = '0';
        $where_simple['social_media'] = 'fb';
        if ($month_no == 'year')
            $where_simple['date_format(unsubscribed_at,"%Y")'] = $search_year;
        else
            $where_simple['date_format(unsubscribed_at,"%Y-%m")'] = $search_month;
        $where = array(
            'where' => $where_simple
        );
        $select = array('count(id) as unsubscribers');
        $unsubscriber_info = $this->basic->get_data('messenger_bot_subscriber', $where, $select);
        $unsubscribed = isset($unsubscriber_info[0]['unsubscribers']) ? $unsubscriber_info[0]['unsubscribers'] : 0;
        $data['unsubscribed'] = custom_number_format($unsubscribed);
        $total_subscribers = $subscribed + $unsubscribed;
        $data['total_subscribers'] = custom_number_format($total_subscribers);

        $where_simple = array();
        if ($system_dashboard == 'no')
            $where_simple['user_id'] = $this->user_id;
        if ($month_no == 'year')
            $where_simple['date_format(completed_at,"%Y")'] = $search_year;
        else
            $where_simple['date_format(completed_at,"%Y-%m")'] = $search_month;
        $where = array(
            'where' => $where_simple
        );
        $select = array('sum(successfully_sent) as total_message_sent');
        $conversation_message_sent_info = $this->basic->get_data("facebook_ex_conversation_campaign", $where, $select);
        $total_conversion_message_sent = isset($conversation_message_sent_info[0]['total_message_sent']) ? $conversation_message_sent_info[0]['total_message_sent'] : 0;

        $where_simple = array();
        if ($system_dashboard == 'no')
            $where_simple['user_id'] = $this->user_id;
        if ($month_no == 'year')
            $where_simple['date_format(completed_at,"%Y")'] = $search_year;
        else
            $where_simple['date_format(completed_at,"%Y-%m")'] = $search_month;
        $where = array(
            'where' => $where_simple
        );
        $select = array('sum(successfully_sent) as total_message_sent');
        if ($this->is_broadcaster_exist) {
            $broadcast_message_sent_info = $this->basic->get_data("messenger_bot_broadcast_serial", $where, $select);
        }
        $total_broadcast_message_sent = isset($broadcast_message_sent_info[0]['total_message_sent']) ? $broadcast_message_sent_info[0]['total_message_sent'] : 0;
        $total_message_sent = $total_conversion_message_sent + $total_broadcast_message_sent;
        $data['total_message_sent'] = custom_number_format($total_message_sent);
        // end of first item section
        echo json_encode($data, true);
    }

    public function get_subscriber_data_div($system_dashboard = 'no') {
        $this->ajax_check();
        $period = $this->input->post('period', true);
        $today = date("Y-m-d");
        $last_seven_day = date("Y-m-d", strtotime("$today - 7 days"));
        $this_month = date("Y-m");
        $this_year = date("Y");

        // fifth item section [email,phone,birthdate,locale gain]
        // email section
        $where_simple = array();
        if ($system_dashboard == 'no')
            $where_simple['user_id'] = $this->user_id;

        if ($period == 'today')
            $where_simple['date_format(entry_time,"%Y-%m-%d")'] = $today;
        else if ($period == 'week')
            $where_simple['date_format(entry_time,"%Y-%m-%d") >='] = $last_seven_day;
        else if ($period == 'month')
            $where_simple['date_format(entry_time,"%Y-%m")'] = $this_month;
        else if ($period == 'year')
            $where_simple['date_format(entry_time,"%Y")'] = $this_year;

        $where_simple['permission'] = '1';
        $where_simple['email !='] = '';
        $where_simple['social_media'] = 'fb';
        $where = array(
            'where' => $where_simple
        );
        $select = array('count(id) as subscribers', 'gender');
        $subscriber_info = $this->basic->get_data('messenger_bot_subscriber', $where, $select, '', '', '', '', 'gender');
        $combined_info['email']['male'] = 0;
        $combined_info['email']['female'] = 0;
        foreach ($subscriber_info as $value) {
            if ($value['gender'] == 'male')
                $combined_info['email']['male'] = number_format($value['subscribers']);
            else
                $combined_info['email']['female'] = number_format($value['subscribers']);
        }
        $combined_info['email']['total_email_gain'] = number_format($combined_info['email']['male'] + $combined_info['email']['female']);
        $percentage_info = $this->get_percentage($combined_info['email']['male'], $combined_info['email']['female']);
        $combined_info['email']['male_percentage'] = $percentage_info[0] . '%';
        $combined_info['email']['female_percentage'] = $percentage_info[1] . '%';
        // end of email section
        // phone section
        $where_simple = array();
        if ($system_dashboard == 'no')
            $where_simple['user_id'] = $this->user_id;

        if ($period == 'today')
            $where_simple['date_format(phone_number_entry_time,"%Y-%m-%d")'] = $today;
        else if ($period == 'week')
            $where_simple['date_format(phone_number_entry_time,"%Y-%m-%d") >='] = $last_seven_day;
        else if ($period == 'month')
            $where_simple['date_format(phone_number_entry_time,"%Y-%m")'] = $this_month;
        else if ($period == 'year')
            $where_simple['date_format(phone_number_entry_time,"%Y")'] = $this_year;

        $where_simple['permission'] = '1';
        $where_simple['phone_number !='] = '';
        $where_simple['social_media'] = 'fb';
        $where = array(
            'where' => $where_simple
        );
        $select = array('count(id) as subscribers', 'gender');
        $subscriber_info = $this->basic->get_data('messenger_bot_subscriber', $where, $select, '', '', '', '', 'gender');
        $combined_info['phone']['male'] = 0;
        $combined_info['phone']['female'] = 0;
        foreach ($subscriber_info as $value) {
            if ($value['gender'] == 'male')
                $combined_info['phone']['male'] = number_format($value['subscribers']);
            else
                $combined_info['phone']['female'] = number_format($value['subscribers']);
        }
        $combined_info['phone']['total_phone_gain'] = number_format($combined_info['phone']['male'] + $combined_info['phone']['female']);
        $percentage_info = $this->get_percentage($combined_info['phone']['male'], $combined_info['phone']['female']);
        $combined_info['phone']['male_percentage'] = $percentage_info[0] . '%';
        $combined_info['phone']['female_percentage'] = $percentage_info[1] . '%';
        // end of phone section
        // birthdate section
        $where_simple = array();
        if ($system_dashboard == 'no')
            $where_simple['user_id'] = $this->user_id;
        if ($period == 'today')
            $where_simple['date_format(birthdate_entry_time,"%Y-%m-%d")'] = $today;
        else if ($period == 'week')
            $where_simple['date_format(birthdate_entry_time,"%Y-%m-%d") >='] = $last_seven_day;
        else if ($period == 'month')
            $where_simple['date_format(birthdate_entry_time,"%Y-%m")'] = $this_month;
        else if ($period == 'year')
            $where_simple['date_format(birthdate_entry_time,"%Y")'] = $this_year;
        $where_simple['permission'] = '1';
        $where_simple['social_media'] = 'fb';
        $where = array(
            'where' => $where_simple
        );
        $select = array('count(id) as subscribers', 'gender');
        $subscriber_info = $this->basic->get_data('messenger_bot_subscriber', $where, $select, '', '', '', '', 'gender');
        $combined_info['birthdate']['male'] = 0;
        $combined_info['birthdate']['female'] = 0;
        foreach ($subscriber_info as $value) {
            if ($value['gender'] == 'male')
                $combined_info['birthdate']['male'] = number_format($value['subscribers']);
            else
                $combined_info['birthdate']['female'] = number_format($value['subscribers']);
        }
        $combined_info['birthdate']['total_birthdate_gain'] = number_format($combined_info['birthdate']['male'] + $combined_info['birthdate']['female']);
        $percentage_info = $this->get_percentage($combined_info['birthdate']['male'], $combined_info['birthdate']['female']);
        $combined_info['birthdate']['male_percentage'] = $percentage_info[0] . '%';
        $combined_info['birthdate']['female_percentage'] = $percentage_info[1] . '%';
        // end of birthdate section
        // end of fifth item section [email,phone,birthdate,locale gain]

        echo json_encode($combined_info, true);
    }

    public function get_percentage($first_number, $second_number) {
        if ($first_number == 0 && $second_number == 0)
            return [(float) 0, (float) 0];

        $total = (int) $first_number + (int) $second_number;

        $first_percent = ($first_number / $total) * 100;
        $second_percent = ($second_number / $total) * 100;

        return [(float) $first_percent, (float) $second_percent];
    }

    public function all_store() {
        $data['body'] = 'dashboard/all_store';
        $data['page_title'] = $this->lang->line('All Store');
        $data['store'] = $this->basic->get_data("ecommerce_store");
        $this->_viewcontroller($data);
    }
  public function orderList($store_id='') {
       $store = $this->db->query("SELECT * FROM `ecommerce_store` where id=$store_id")->result();
        $data['body'] = 'dashboard/orderList';
        $data['page_title'] = $this->lang->line('Order List');
        $data['store_name']=$store[0]->store_name;
        $data['cart'] = $this->db->query("SELECT * FROM `ecommerce_cart` where store_id=$store_id")->result();
        $this->_viewcontroller($data);
    }
  public function cartDel($cart_id='',$store_id='') {
        $del=$this->db->query("delete from ecommerce_cart where id=$cart_id");
        $store = $this->db->query("SELECT * FROM `ecommerce_store` where id=$store_id")->result();
        $data['body'] = 'dashboard/orderList';
        $data['page_title'] = $this->lang->line('Order List');
        $data['store_name']=$store[0]->store_name;
        $data['cart'] = $this->db->query("SELECT * FROM `ecommerce_cart` where store_id=$store_id")->result();
        $this->_viewcontroller($data);
    }
    public function download($param = '') {
        error_reporting(0);
        $this->load->library('excel');
        $data = $this->db->query("SELECT store_id, product_name, product_description, original_price,sell_price,stock_item,stock_display,thumbnail,featured_images,category_id,status,is_featured FROM `ecommerce_product` WHERE store_id=$param")->result_array();
        $file = 'Product' . '.xls';
        header('Content-Type: application/vnd.ms-excel'); //mime type
        header('Content-Disposition: attachment;filename="' . $file . '"'); //tell browser what's the file name
        header('Cache-Control: max-age=0'); //no cache
//        $this->excel->setActiveSheetIndex(0);
//        $this->excel->getActiveSheet()->setTitle($filename);
//        $this->excel->getActiveSheet()->setCellValue('A1', $title);
//        $this->excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(20);
//        $this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
//        $this->excel->getActiveSheet()->mergeCells('A1:' . $cell . '1');
//        $this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        //$this->excel->getActiveSheet()->setCellValue('A2', 'store_id');
        $this->excel->getActiveSheet()->setCellValue('B2', 'product_name');
        $this->excel->getActiveSheet()->setCellValue('C2', 'product_description');
        $this->excel->getActiveSheet()->setCellValue('D2', 'original_price');
        $this->excel->getActiveSheet()->setCellValue('E2', 'sell_price');
        $this->excel->getActiveSheet()->setCellValue('F2', 'taxable');
        $this->excel->getActiveSheet()->setCellValue('G2', 'stock_item');
        $this->excel->getActiveSheet()->setCellValue('H2', 'stock_display');
        $this->excel->getActiveSheet()->setCellValue('I2', 'purchase_note');
        $this->excel->getActiveSheet()->setCellValue('J2', 'thumbnail');
        $this->excel->getActiveSheet()->setCellValue('K2', 'featured_images');
        $this->excel->getActiveSheet()->setCellValue('L2', 'category_id');
        $this->excel->getActiveSheet()->setCellValue('M2', 'status');
        $this->excel->getActiveSheet()->setCellValue('N2', 'is_featured');
        $this->excel->getActiveSheet()->getStyle('A2:E2')->getFont()->getColor()->setRGB('555555');
        $this->excel->getActiveSheet()->getStyle('A2:E2')->getFont()->setSize(10);
        $this->excel->getActiveSheet()->getStyle('A2:E2')->getFont()->setBold(true);
        $i = 3;
        foreach ($data as $item) {
            //print_r($item);
            //$this->excel->getActiveSheet()->setCellValue('A' . $i, $item['store_id']);
            $this->excel->getActiveSheet()->setCellValue('B' . $i, $item['product_name']);
            $this->excel->getActiveSheet()->setCellValue('C' . $i, $item['product_description']);
            $this->excel->getActiveSheet()->setCellValue('D' . $i, number_format($item['original_price'], 0, ',', '.'));
            $this->excel->getActiveSheet()->setCellValue('E' . $i, number_format($item['sell_price'], 0, ',', '.'));
            $this->excel->getActiveSheet()->setCellValue('F' . $i, $item['taxable']);
            $this->excel->getActiveSheet()->setCellValue('G' . $i, $item['stock_item']);
            $this->excel->getActiveSheet()->setCellValue('H' . $i, $item['stock_display']);
            $this->excel->getActiveSheet()->setCellValue('I' . $i, $item['purchase_note']);
            $this->excel->getActiveSheet()->setCellValue('J' . $i, $item['thumbnail']);
            $this->excel->getActiveSheet()->setCellValue('K' . $i, $item['featured_images']);
            $this->excel->getActiveSheet()->setCellValue('L' . $i, $item['category_id']);
            $this->excel->getActiveSheet()->setCellValue('M' . $i, $item['status']);
            $this->excel->getActiveSheet()->setCellValue('N' . $i, number_format($item['is_featured'], 0, ',', '.'));
            //$this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
            $this->excel->getActiveSheet()->getColumnDimension('A')->setAutoSize(TRUE);
            $this->excel->getActiveSheet()->getColumnDimension('B')->setAutoSize(TRUE);
            $this->excel->getActiveSheet()->getColumnDimension('C')->setAutoSize(TRUE);
            $this->excel->getActiveSheet()->getColumnDimension('D')->setAutoSize(TRUE);
            $i++;
        }

        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
        $objWriter->save('php://output');
    }
 public function download($param = '') {
        error_reporting(0);
        $this->load->library('excel');
        $data = $this->db->query("SELECT store_id, product_name, product_description, original_price,sell_price,stock_item,stock_display,thumbnail,featured_images,category_id,status,is_featured FROM `ecommerce_product` WHERE store_id=$param")->result_array();
        $file = 'Product' . '.xls';
        header('Content-Type: application/vnd.ms-excel'); //mime type
        header('Content-Disposition: attachment;filename="' . $file . '"'); //tell browser what's the file name
        header('Cache-Control: max-age=0'); //no cache
//        $this->excel->setActiveSheetIndex(0);
//        $this->excel->getActiveSheet()->setTitle($filename);
//        $this->excel->getActiveSheet()->setCellValue('A1', $title);
//        $this->excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(20);
//        $this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
//        $this->excel->getActiveSheet()->mergeCells('A1:' . $cell . '1');
//        $this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        //$this->excel->getActiveSheet()->setCellValue('A2', 'store_id');
        $this->excel->getActiveSheet()->setCellValue('B2', 'product_name');
        $this->excel->getActiveSheet()->setCellValue('C2', 'product_description');
        $this->excel->getActiveSheet()->setCellValue('D2', 'original_price');
        $this->excel->getActiveSheet()->setCellValue('E2', 'sell_price');
        $this->excel->getActiveSheet()->setCellValue('F2', 'taxable');
        $this->excel->getActiveSheet()->setCellValue('G2', 'stock_item');
        $this->excel->getActiveSheet()->setCellValue('H2', 'stock_display');
        $this->excel->getActiveSheet()->setCellValue('I2', 'purchase_note');
        $this->excel->getActiveSheet()->setCellValue('J2', 'thumbnail');
        $this->excel->getActiveSheet()->setCellValue('K2', 'featured_images');
        $this->excel->getActiveSheet()->setCellValue('L2', 'category_id');
        $this->excel->getActiveSheet()->setCellValue('M2', 'status');
        $this->excel->getActiveSheet()->setCellValue('N2', 'is_featured');
        $this->excel->getActiveSheet()->getStyle('A2:E2')->getFont()->getColor()->setRGB('555555');
        $this->excel->getActiveSheet()->getStyle('A2:E2')->getFont()->setSize(10);
        $this->excel->getActiveSheet()->getStyle('A2:E2')->getFont()->setBold(true);
        $i = 3;
        foreach ($data as $item) {
            //print_r($item);
            //$this->excel->getActiveSheet()->setCellValue('A' . $i, $item['store_id']);
            $this->excel->getActiveSheet()->setCellValue('B' . $i, $item['product_name']);
            $this->excel->getActiveSheet()->setCellValue('C' . $i, $item['product_description']);
            $this->excel->getActiveSheet()->setCellValue('D' . $i, number_format($item['original_price'], 0, ',', '.'));
            $this->excel->getActiveSheet()->setCellValue('E' . $i, number_format($item['sell_price'], 0, ',', '.'));
            $this->excel->getActiveSheet()->setCellValue('F' . $i, $item['taxable']);
            $this->excel->getActiveSheet()->setCellValue('G' . $i, $item['stock_item']);
            $this->excel->getActiveSheet()->setCellValue('H' . $i, $item['stock_display']);
            $this->excel->getActiveSheet()->setCellValue('I' . $i, $item['purchase_note']);
            $this->excel->getActiveSheet()->setCellValue('J' . $i, $item['thumbnail']);
            $this->excel->getActiveSheet()->setCellValue('K' . $i, $item['featured_images']);
            $this->excel->getActiveSheet()->setCellValue('L' . $i, $item['category_id']);
            $this->excel->getActiveSheet()->setCellValue('M' . $i, $item['status']);
            $this->excel->getActiveSheet()->setCellValue('N' . $i, number_format($item['is_featured'], 0, ',', '.'));
            //$this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
            $this->excel->getActiveSheet()->getColumnDimension('A')->setAutoSize(TRUE);
            $this->excel->getActiveSheet()->getColumnDimension('B')->setAutoSize(TRUE);
            $this->excel->getActiveSheet()->getColumnDimension('C')->setAutoSize(TRUE);
            $this->excel->getActiveSheet()->getColumnDimension('D')->setAutoSize(TRUE);
            $i++;
        }

        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
        $objWriter->save('php://output');
    }
    private function get_store_list() {
        $store_list = $this->basic->get_data("ecommerce_store", array("where" => array("user_id" => $this->user_id, "status" => "1")), $select = '', $join = '', $limit = '', $start = NULL, $order_by = 'store_name ASC');
        $store_info = array();
        foreach ($store_list as $value) {
            $store_info[$value['id']] = $value['store_name'];
        }
        return $store_info;
    }

    public function store_details() {
        $data['body'] = 'dashboard/store_list';
        $data['page_title'] = $this->lang->line('Store Details');
        ;
        $this->_viewcontroller($data);
    }

    public function store_list_data() {
        $search_value = $_POST['search']['value'];
        $display_columns = array("#", 'CHECKBOX', 'serial', 'store_logo', 'store_name', 'total_order', 'total_earning', 'status', 'store_address', 'created_at',);
        $search_columns = array('store_name');
        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
        $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
        $limit = isset($_POST['length']) ? intval($_POST['length']) : 10;
        $sort_index = isset($_POST['order'][0]['column']) ? strval($_POST['order'][0]['column']) : 2;
        $sort = isset($display_columns[$sort_index]) ? $display_columns[$sort_index] : 'serial';
        $order = isset($_POST['order'][0]['dir']) ? strval($_POST['order'][0]['dir']) : 'asc';
        $order_by = $sort . " " . $order;
        $where_custom = '';
        if ($search_value != '') {
            foreach ($search_columns as $key => $value)
                $temp[] = $value . " LIKE " . "'%$search_value%'";
            $imp = implode(" OR ", $temp);
            $where_custom .= " where (" . $imp . ") ";
            $info = $this->basic->db->query("select * from ecommerce_store $where_custom limit $start,$limit")->result_array();
        } else {
            $info = $this->basic->db->query("select * from ecommerce_store order by id desc limit $start,$limit")->result_array();
        }
        $total_result = $this->db->query("select * from ecommerce_store")->num_rows();
        foreach ($info as $key => $value) {
            $store_id = $info[$key]['id'];
            $total_order = $this->db->query("SELECT count(id) AS total_order FROM `ecommerce_cart` WHERE store_id='" . $store_id . "' AND action_type='checkout'")->result_array();
            $info[$key]['total_order'] = $total_order[0]['total_order'];
            $total_earning = $this->db->query("SELECT SUM(payment_amount) AS total_earning FROM `ecommerce_cart` WHERE store_id='" . $store_id . "' AND action_type='checkout'")->result_array();
            $info[$key]['total_earning'] = round($total_earning[0]['total_earning'],2);

            if ($info[$key]['status'] == 1)
                $info[$key]['status'] = "<span class='badge badge-status text-success'><i class='fa fa-check-circle green'></i> " . $this->lang->line('Active') . "</span>";
            else
                $info[$key]['status'] = "<span class='badge badge-status text-danger'><i class='fa fa-times-circle red'></i> " . $this->lang->line('Inactive') . "</span>";

            $info[$key]['created_at'] = date("jS M y H:i", strtotime($info[$key]['created_at']));

            if ($info[$key]['store_logo'] == '')
                $url = base_url('assets/img/icon/rocket.png');
            else
                $url = base_url('upload/ecommerce/' . $info[$key]['store_logo']);
            $info[$key]['store_logo'] = "<a  target='_BLANK' href='" . $url . "'><img class='rounded-circle' style='height:50px;width:50px;border-radius:4px;border:1px solid #eee;padding:2px;' src='" . $url . "'></a>";
        }

        $data['draw'] = (int) $_POST['draw'] + 1;
        $data['recordsTotal'] = $total_result;
        $data['recordsFiltered'] = $total_result;
        $data['data'] = convertDataTableResult($info, $display_columns, $start, $primary_key = "id");
        echo json_encode($data);
    }

    public function marchant_list_data() {
        $search_value = $_POST['search']['value'];
        $display_columns = array("#", 'CHECKBOX', 'serial', 'name', 'email', 'mobile', 'source', 'package_name', 'store_name', 'status', 'merchant_order_id','total_product', 'add_date',);
        $search_columns = array('name','email','mobile');
        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
        $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
        $limit = isset($_POST['length']) ? intval($_POST['length']) : 10;
        $sort_index = isset($_POST['order'][0]['column']) ? strval($_POST['order'][0]['column']) : 2;
        $sort = isset($display_columns[$sort_index]) ? $display_columns[$sort_index] : 'serial';
        $order = isset($_POST['order'][0]['dir']) ? strval($_POST['order'][0]['dir']) : 'asc';
        $order_by = $sort . " " . $order;
        $where_custom = '';
        if ($search_value != '') {
            foreach ($search_columns as $key => $value)
                $temp[] = $value . " LIKE " . "'%$search_value%'";
            $imp = implode(" OR ", $temp);
            $where_custom .= " AND (" . $imp . ") ";
            $info = $this->basic->db->query("select * from users where user_type='Member' $where_custom limit $start,$limit")->result_array();
        } else {
            $info = $this->basic->db->query("select * from users where user_type='Member' limit $start,$limit")->result_array();
        }

        $total_result = $this->db->query("select * from users where user_type='Member'")->num_rows();

        foreach ($info as $key => $value) {
            $user_id = $info[$key]['id'];
            $package_id = $info[$key]['package_id'];
            $total_product = $this->db->query("SELECT count(id) AS total_product FROM `ecommerce_product` WHERE user_id='" . $user_id . "'")->result_array();
            $info[$key]['total_product'] = $total_product[0]['total_product'];
            $package_name = @$this->db->query("SELECT package_name FROM `package` WHERE id='" . $package_id . "'")->row()->package_name;
            $info[$key]['package_name'] = $package_name;
            $store_name = @$this->db->query("SELECT store_name FROM `ecommerce_store` WHERE user_id='" . $user_id . "'")->row()->store_name;
            $info[$key]['store_name'] = $store_name;
            $merchant_order_id=$info[$key]['merchant_order_id'];


            if ($info[$key]['status'] == 1)
                $info[$key]['status'] = "<span class='badge badge-status text-success'><i class='fa fa-check-circle green'></i> " . $this->lang->line('Active') . "</span>";
            else
                $info[$key]['status'] = "<span class='badge badge-status text-danger'><i class='fa fa-times-circle red'></i> " . $this->lang->line('Inactive') . "</span>";

            $info[$key]['add_date'] = date("jS M y H:i", strtotime($info[$key]['add_date']));
            if (!empty($info[$key]['fcm_token'])) {
                $info[$key]['source'] = 'App';
            } else {
                $info[$key]['source'] = 'Web';
            }

            if (!empty($info[$key]['merchant_order_id'])) {
                $info[$key]['merchant_order_id'] = "<a class='btn btn-success' href='https://qpe.co.in/home/download/$merchant_order_id'>Invoice</a>";
            } else {
                $info[$key]['merchant_order_id'] = 'N/A';
            }


//            if ($info[$key]['store_logo'] == '')
//                $url = base_url('assets/img/icon/rocket.png');
//            else
//                $url = base_url('upload/ecommerce/' . $info[$key]['store_logo']);
//            $info[$key]['store_logo'] = "<a  target='_BLANK' href='" . $url . "'><img class='rounded-circle' style='height:50px;width:50px;border-radius:4px;border:1px solid #eee;padding:2px;' src='" . $url . "'></a>";
        }

        $data['draw'] = (int) $_POST['draw'] + 1;
        $data['recordsTotal'] = $total_result;
        $data['recordsFiltered'] = $total_result;
        $data['data'] = convertDataTableResult($info, $display_columns, $start, $primary_key = "id");
        echo json_encode($data);
    }

    public function marchant_details() {
        $data['body'] = 'dashboard/marchant_list';
        $data['page_title'] = $this->lang->line('Marchant Details');
        $this->_viewcontroller($data);
    }
    public function downloadMerchantDetails() {
        error_reporting(0);
        $sl=0;
        $this->load->library('excel');
        //$data = $this->db->query("SELECT store_id, product_name, product_description, original_price,sell_price,stock_item,stock_display,thumbnail,featured_images,category_id,status,is_featured FROM `ecommerce_product` WHERE store_id=$param")->result_array();
        $data= $this->basic->db->query("select * from users where user_type='Member' order by id desc")->result_array();
        $file = 'Users' . '.xls';
        header('Content-Type: application/vnd.ms-excel'); //mime type
        header('Content-Disposition: attachment;filename="' . $file . '"'); //tell browser what's the file name
        header('Cache-Control: max-age=0'); //no cache
//        $this->excel->setActiveSheetIndex(0);
//        $this->excel->getActiveSheet()->setTitle($filename);
//        $this->excel->getActiveSheet()->setCellValue('A1', $title);
//        $this->excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(20);
//        $this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
//        $this->excel->getActiveSheet()->mergeCells('A1:' . $cell . '1');
//        $this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $this->excel->getActiveSheet()->setCellValue('A2', 'Sl.No');
        $this->excel->getActiveSheet()->setCellValue('B2', 'Name');
        $this->excel->getActiveSheet()->setCellValue('C2', 'Mobile');
        $this->excel->getActiveSheet()->setCellValue('D2', 'Email');
        $this->excel->getActiveSheet()->setCellValue('E2', 'Date');
        $this->excel->getActiveSheet()->setCellValue('F2', 'Package');
        $this->excel->getActiveSheet()->setCellValue('G2', 'Expiry');
        $this->excel->getActiveSheet()->setCellValue('H2', 'Source');
        $this->excel->getActiveSheet()->setCellValue('I2', 'Store');
        $this->excel->getActiveSheet()->setCellValue('J2', 'Product');
        $this->excel->getActiveSheet()->setCellValue('K2', 'Order');
        $this->excel->getActiveSheet()->setCellValue('L2', 'Earning');
        $this->excel->getActiveSheet()->setCellValue('M2', 'status');
        $this->excel->getActiveSheet()->getStyle('A2:E2')->getFont()->getColor()->setRGB('555555');
        $this->excel->getActiveSheet()->getStyle('A2:E2')->getFont()->setSize(10);
        $this->excel->getActiveSheet()->getStyle('A2:E2')->getFont()->setBold(true);
        $i = 3;
        foreach ($data as $item) {
          $sl++;
          $user_id=$item['id'];
          $package_id=$item['package_id'];
          if ($item['status'] == 1)
              $status = "Active";
          else
              $status = "Inactive";
          if (!empty($item['fcm_token'])) {
              $source = 'App';
          } else {
              $source = 'Web';
          }
          $total_product = $this->db->query("SELECT count(id) AS total_product FROM `ecommerce_product` WHERE user_id='" . $user_id . "'")->result_array();
          $product = $total_product[0]['total_product'];
          $package_name = @$this->db->query("SELECT package_name FROM `package` WHERE id='" . $package_id . "'")->row()->package_name;
          $package = $package_name;
          $store_name = @$this->db->query("SELECT store_name FROM `ecommerce_store` WHERE user_id='" . $user_id . "'")->row()->store_name;
          $store = $store_name;
          $cart = @$this->db->query("SELECT count(id) as total,sum(payment_amount) as earning FROM `ecommerce_cart` where user_id='" . $user_id . "'")->result_array();
          $order = $cart[0]['total'];
          $earning=$cart[1]['earning'];
          $add_date=date('M d, Y', strtotime($item['add_date']));
            $exp_date=date('M d, Y', strtotime($item['expired_date']));
            $this->excel->getActiveSheet()->setCellValue('A' . $i, $sl);
            $this->excel->getActiveSheet()->setCellValue('B' . $i, $item['name']);
            $this->excel->getActiveSheet()->setCellValue('C' . $i, $item['mobile']);
            $this->excel->getActiveSheet()->setCellValue('D' . $i, $item['email']);
            $this->excel->getActiveSheet()->setCellValue('E' . $i, $add_date);
            $this->excel->getActiveSheet()->setCellValue('F' . $i, $package);
            $this->excel->getActiveSheet()->setCellValue('G' . $i, $item['expired_date']);
            $this->excel->getActiveSheet()->setCellValue('H' . $i, $source);
            $this->excel->getActiveSheet()->setCellValue('I' . $i, $store);
            $this->excel->getActiveSheet()->setCellValue('J' . $i, $product);
            $this->excel->getActiveSheet()->setCellValue('K' . $i, $order);
            $this->excel->getActiveSheet()->setCellValue('L' . $i, $earning);
            $this->excel->getActiveSheet()->setCellValue('M' . $i, $status);

            //$this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
            $this->excel->getActiveSheet()->getColumnDimension('A')->setAutoSize(TRUE);
            $this->excel->getActiveSheet()->getColumnDimension('B')->setAutoSize(TRUE);
            $this->excel->getActiveSheet()->getColumnDimension('C')->setAutoSize(TRUE);
            $this->excel->getActiveSheet()->getColumnDimension('D')->setAutoSize(TRUE);
            $i++;
        }

        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
        $objWriter->save('php://output');
    }

}
