<?php

class Api extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->library('upload');
        $this->load->helper(array('url','form'));
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
        header("Access-Control-Allow-Methods:application/json; charset=utf-8");
    }
    public function webhookDunzo(){
    $request = file_get_contents('php://input');
    $req_dump = print_r( $request, true );
    $fp = file_put_contents( 'request.log', $req_dump );
    // Updated Answer
    if($json = json_decode(file_get_contents("php://input"), true)){
     $data = $json;
    }
    print_r($data);
    }
public function is_mobile_resgistred_old(){
        $postdata = json_decode(file_get_contents("php://input"), true);
        $mobile = $postdata['mobile'];
        $fcm_token = $postdata['fcm_token'];
		$q = $this->db->query("SELECT * FROM users WHERE mobile='" . $mobile . "'");
        if ($q->num_rows() > 0) {
            $query = $q->row();
            $user_id = $query->id;
            $data = array('fcm_token' =>$postdata['fcm_token']);
            $this->db->where('id',$user_id);
           // $this->db->set('fcm_token',$fcm_token);
            $this->db->update('users',$data);

            $temp_user_data=array();
            $temp_store_data=array();
            $store_data = $this->db->query("SELECT * FROM ecommerce_store WHERE user_id='" . $user_id . "' limit 1")->row();
            $temp_store_data['store_data']=$store_data;
            $temp_user_data['user_data']=$query;
            $final_data= array_merge($temp_store_data, $temp_user_data);
            echo $data['success'] = json_encode(array('status' => TRUE, 'register'=>'1' , 'message' => 'success', 'data' => $final_data));
        } else {
            echo $data['error'] = json_encode(array('status' => TRUE, 'register'=>'0' , 'message' => 'No record found', 'data' => NULL));
        }
}
public function is_mobile_resgistred(){
        $postdata = json_decode(file_get_contents("php://input"), true);
        $mobile = $postdata['mobile'];
        $fcm_token = $postdata['fcm_token'];
         $date=date('Y-m-d h:s:a');
	  $startDate=date('Y-m-d');
	  $futureDate=date('Y-m-d',strtotime(date("Y-m-d", time()) . " + 14 day"));
		$q = $this->db->query("SELECT * FROM users WHERE mobile='" . $mobile . "'");
        if ($q->num_rows() > 0) {
            $query = $q->row();
            $user_id = $query->id;
            $data = array('fcm_token' =>$postdata['fcm_token']);
            $this->db->where('id',$user_id);
           // $this->db->set('fcm_token',$fcm_token);
            $this->db->update('users',$data);
            $temp_user_data=array();
            $temp_store_data=array();
            $store_data = $this->db->query("SELECT * FROM ecommerce_store WHERE user_id='" . $user_id . "' limit 1")->row();
            $temp_store_data['store_data']=$store_data;
            $temp_user_data['user_data']=$query;
            $final_data= array_merge($temp_store_data, $temp_user_data);
            echo $data['success'] = json_encode(array('status' => TRUE, 'register'=>'1' , 'message' => 'success', 'data' => $final_data));
        } else {
        	$data=array(
				 'mobile'=>$mobile, 'user_type'=>'Member',
				'status'=>'1', 'add_date'=>$date,'fcm_token'=>$fcm_token  ,'package_id'=>'1', 'deleted'=>'0', 'currency'=>'INR','expired_date'=>$futureDate
				);
				if($this->db->insert('users',$data))
				{
 			     $user_id = $this->db->insert_id();
  			     $data['user_id']=$user_id;
					 echo $data['success'] = json_encode(array('status' => TRUE, 'register'=>'0' , 'message' => 'No New Registration', 'data' =>$data));
                  }else{
					 echo $data['error'] = json_encode(array('status' => TRUE, 'message' => 'somthing goes wrong', 'data' =>'NULL'));
				}
 			}

}
public function registration(){
      $postdata = json_decode(file_get_contents("php://input"), true);
      $name = $postdata['name'];
      $email =$postdata['email'];
      $password = md5($postdata['password']);
      $mobile = $postdata['mobile'];
       $fcm_token = $postdata['fcm_token'];
	  $date=date('Y-m-d h:s:a');
	  $startDate=date('Y-m-d');
	  $futureDate=date('Y-m-d',strtotime(date("Y-m-d", time()) . " + 14 day"));
 			$q = $this->db->query("SELECT * FROM users WHERE  email='".$email."'");
        if ($q->num_rows() > 0) {
         echo $data['success'] = json_encode(array('status' => FALSE, 'message' => 'Duplicate Email or Mobile', 'data' =>NULL));
        }else{
				$data=array(
				'name'=>$name, 'email'=>$email, 'mobile'=>$mobile, 'password'=>$password, 'user_type'=>'Member',
				'status'=>'1', 'add_date'=>$date,'fcm_token'=>$fcm_token  ,'package_id'=>'1', 'deleted'=>'0', 'currency'=>'INR','expired_date'=>$futureDate
				);
				if($this->db->insert('users',$data))
				{
 			     $user_id = $this->db->insert_id();
  			     $data['user_id']=$user_id;
					 echo $data['success'] = json_encode(array('status' => TRUE, 'message' => 'success', 'data' =>$data));
				}else{
					 echo $data['error'] = json_encode(array('status' => TRUE, 'message' => 'somthing goes wrong', 'data' =>'NULL'));
				}
 			}
}
public function payment_success_old(){
      $postdata = json_decode(file_get_contents("php://input"), true);
      $user_id = $postdata['user_id'];
      $package_id =$postdata['package_id'];
      $merchant_order_id =$postdata['merchant_order_id'];
      $razorpay_payment_id =$postdata['razorpay_payment_id'];
      $pdate =$postdata['purchase_date'];
      $exp_type = $postdata['exp_type'];
	  $date=date('Y-m-d h:s:a');
	  $startDate=date('Y-m-d');
 	 if($exp_type==1){
	  $expDate=date('Y-m-d',strtotime(date("Y-m-d", time()) . " + 1 month"));
 	 }else{
  	 $expDate=date('Y-m-d',strtotime(date("Y-m-d", time()) . " + 365 day"));
  	}
      $this->db->select_max('invoice_no');
$result = $this->db->get('users')->row();
$inv_no= $result->invoice_no;
$i_no=(int)$inv_no+1;
if($i_no<10){
$invoice_no='00'.$i_no;
}elseif($i_no<100){
$invoice_no='0'.$i_no;
}else{
$invoice_no=$i_no;
}

$data=array(
				'package_id'=>$package_id,
 			'merchant_order_id'=>$merchant_order_id,
  		 'razorpay_payment_id'=>$razorpay_payment_id,
   		'purchase_date'=>$pdate,
  'expired_date'=>$expDate,
  'invoice_no'=>$invoice_no
				);
 $this->db->where('id',$user_id);
 $query=$this->db->update('users',$data);

        if ($query == TRUE) {
            echo $data['success'] = json_encode(array('status' => TRUE, 'message' => 'Payment Successful done', 'data' => $data));
        } else {
            echo $data['error'] = json_encode(array('status' => FALSE, 'message' => 'Something went wrong', 'data' => NULL));
        }

}

public function financial_year(){
    $year = date('Y');
    $month = date('m');
    if($month<4){
        $year = $year-1;
    }
    $start_date = date('y',strtotime(($year).'-04-01'));
    $end_date = date('y',strtotime(($year+1).'-03-31'));
    return $start_date . "" . $end_date;
}
public function max_invoice_id(){
    $this->db->select_max('invoice_no', 'max_id');
    $this->db->where('financial_year',$this->financial_year());
    $this->db->order_by('invoice_no','DESC');
    $this->db->limit('1');
    $query = $this->db->get('users')->row();
    if (!empty($query->max_id)) {
        $value2 = substr($query->max_id, 0);
        $value2 = $value2 + 1;
        $value2 = sprintf('%03s', $value2);
        $invoice_no = $value2;
    } else {
        $invoice_no = '001';
    }
    return $invoice_no;
}
public function payment_success(){
      $postdata = json_decode(file_get_contents("php://input"), true);
      $current_month=date('M');
      $order_id = "QPe-" . $this->financial_year().'-'.$current_month.'-'.$this->max_invoice_id();
      $user_id = @$postdata['user_id'];
      $package_id =@$postdata['package_id'];
      $merchant_order_id = $order_id;
      $razorpay_payment_id =$postdata['razorpay_payment_id'];
      $pdate =$postdata['purchase_date'];
      $exp_type = $postdata['exp_type'];
	  $date=date('Y-m-d h:s:a');
	  $startDate=date('Y-m-d');
        if($exp_type==1){
        $expDate=date('Y-m-d',strtotime(date("Y-m-d", time()) . " + 1 month"));
        }else{
        $expDate=date('Y-m-d',strtotime(date("Y-m-d", time()) . " + 365 day"));
        }
// $this->db->select_max('invoice_no');
// $result = $this->db->get('users')->row();
// $inv_no= $result->invoice_no;
// $i_no=(int)$inv_no+1;
// if($i_no<10){
// $invoice_no='00'.$i_no;
// }elseif($i_no<100){
// $invoice_no='0'.$i_no;
// }else{
// $invoice_no=$i_no;
// }

$data=array(
			'package_id'=>$package_id,
 			'merchant_order_id'=>$merchant_order_id,
  		    'razorpay_payment_id'=>$razorpay_payment_id,
   		    'purchase_date'=>$pdate,
            'expired_date'=>$expDate,
            'invoice_no'=>$this->max_invoice_id());
             $this->db->where('id',$user_id);
             $query=$this->db->update('users',$data);

        if ($query == TRUE) {
            $invoice=$this->subscription_mail($user_id);
            $this->output->set_content_type('application/json');
            $invoice1= $this->output->set_output(json_encode($invoice));
            echo $data['success'] = json_encode(array('status' => TRUE, 'message' => 'Payment Successfully done', 'data' => $data,'invoice'=>$invoice1));

        } else {
            echo $data['error'] = json_encode(array('status' => FALSE, 'message' => 'Something went wrong', 'data' => NULL));
        }

}

public function subscription_mail($id='1572') {
    error_reporting(0);
    $get_user= $this->db->query("select * from users where id='".$id."'")->row();
    $email_id =$get_user->email;
    $list = 'qpeofficial@gmail.com';
    $invoice=$get_user->merchant_order_id;

    $message='<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><head>
  <meta content="text/html; charset=utf-8" http-equiv="Content-Type">
  <meta content="width=device-width, initial-scale=1" name="viewport"><title>Qpe Welcome Mail</title>
  <style type="text/css">
  @import url(https://fonts.googleapis.com/css?family=Droid+Sans);

  /* Take care of image borders and formatting */

  img {
    max-width: 600px;
    outline: none;
    text-decoration: none;
    -ms-interpolation-mode: bicubic;
  }

  a {
    text-decoration: none;
    border: 0;
    outline: none;
    color: #bbbbbb;
  }

  a img {
    border: none;
  }



  td, h1, h2, h3  {
    font-family: Helvetica, Arial, sans-serif;
    font-weight: 400;
  }

  td {
    text-align: center;
  }

  body {
    -webkit-font-smoothing:antialiased;
    -webkit-text-size-adjust:none;
    width: 100%;
    height: 100%;
    color: #37302d;
    background: #ffffff;
    font-size: 16px;
  }

   table {
    border-collapse: collapse !important;
  }

  .headline {
    color: #000;
    font-size: 36px;
  }

 .force-full-width {
  width: 100% !important;
 }





  </style>
  <style media="only screen and (max-width: 480px)" type="text/css">
    /* Mobile styles */
    @media only screen and (max-width: 480px) {

      table[class="w320"] {
        width: 320px !important;
      }


    }
  </style><style type="text/css"></style>
</head>
<body bgcolor="#ffffff" class="body" style="padding:0; margin:0; display:block; background:#ffffff; -webkit-text-size-adjust:none">
<table align="center" cellpadding="0" cellspacing="0" height="100%" width="100%">
<tbody><tr>
<td align="center" bgcolor="#ffffff" class="" valign="top" width="100%">
<center class=""><table cellpadding="0" cellspacing="0" class="w320" style="margin: 0 auto;" width="600">
<tbody><tr>
<td align="center" class="" valign="top"><table cellpadding="0" cellspacing="0" style="margin: 0 auto;" width="100%">
<tbody><tr>
<td class="" style="font-size: 30px; text-align:center;"></td>
</tr>
</tbody></table>
<table bgcolor="#ffe600" cellpadding="0" cellspacing="0" class="" style="margin: 0 auto;" width="100%">
<tbody class=""><tr class="">
<td class=""><br>
<img alt="robot picture" class="" height="70" src="https://qpe.co.in/assets/images/logo.png" width="70">
<br></td>
</tr>
<tr class=""><td class="headline">Welcome to Qpe!</td></tr>
<tr>
<td>
<center class=""><table cellpadding="0" cellspacing="0" class="" style="margin: 0 auto;" width="75%"><tbody class=""><tr class="">
<td class="" style="color:#000;"><br>
We would like to greet you with an awesome present. Confirm your account and get 365 days of <span style="font-weight:bold;" class="">QPe</span>
<br>

<br>
<br></td>
</tr>
</tbody></table></center>
</td>
</tr>
<tr>
<td class="">
<div class="">
<a class=""  href="https://store.qpe.co.in/" style="background-color:#000;border-radius:4px;color:#ffffff;display:inline-block;font-family:Helvetica, Arial, sans-serif;font-size:18px;font-weight:normal;line-height:50px;text-align:center;text-decoration:none;width:350px;-webkit-text-size-adjust:none;">Start Your Store</a>
</div>
<br>
<br>
</td>
</tr>
</tbody></table>
<table bgcolor="#f5774e" cellpadding="0" cellspacing="0" class="" style="margin: 0 auto;" width="100%"><tbody class=""><tr class=""><td class="headline" style="border-right: solid; border-left: solid; background-color:#ffffff; color:#000"><br>
Qpe </td></tr>
<tr class=""><td class="" style="background-color: #ffffff; border-right:solid; border-left:solid; color: #000;"><br>
Congratulations and welcome to the QPe India Family. You have successfully On-Boraded to the QPe India Online Platform. Thank you for joining us. We look Forward to make your business grow and thanks for choosing QPe India.
<br>
<br></td></tr>
<tr class="">
<td class=""><center cellspacing="0" class="" style="background-color: #ffffff; border-right:solid; border-left:solid; color: #008ACE;"></center></td>
</tr>
<tr class=""><td class="" style="background-color: #ffffff; border-right:solid; border-left:solid; color: #008ACE;"><div class="">
<a href="https://play.google.com/store/apps/details?id=com.store.qpe"><img style="width:40%" src="https://qpe.co.in/assets/images/play.png"></a>

                          </center>
                  </div>
<br>
<br></td></tr></tbody></table>
<table bgcolor="#414141" cellpadding="0" cellspacing="0" class="force-full-width" style="margin: 0 auto;">
<tbody><tr class=""><td class="" style="background-color:#414141;"></td></tr>
<tr>
<td class="" style="color:#bbbbbb; font-size:12px;"></td>
</tr>
<tr>
<td class="" style="color:#bbbbbb; font-size:12px;"><br>
<br>
<a class="" data-click-track-id="6245" href="#">Terms of Service</a>
&nbsp; • &nbsp;
<a class="" data-click-track-id="285" href="#">Privacy Policy</a>
&nbsp; • &nbsp;
<a class="" data-click-track-id="3945" href="#">Support</a>
&nbsp; • &nbsp;
<br><br>
<a href="https://www.facebook.com/qpeindia"><img alt="facebook" class="" height="32px" src="https://d1pgqke3goo8l6.cloudfront.net/D11l6OhhRVaZGnYCaxtu_Facebook@3x.png" width="32px"></a>
&nbsp;
<a href="https://twitter.com/qpeindia"><img alt="facebook" class="" height="32px" src="https://d1pgqke3goo8l6.cloudfront.net/fRII6ZJ9SEugqa31ignG_Twitter@3x.png" width="32px"></a>
&nbsp;
<a href="#"><img alt="facebook" class="" height="32px" src="https://d1pgqke3goo8l6.cloudfront.net/fVAAOjVyR2mKHKgYR1SF_GooglePlus3x.png" width="32px"></a>
<br>
<br>
© 2021<span class="" style="font-weight:bold;">QPe</span>
<br>
<br></td>
</tr>
</tbody></table></td>
</tr>
</tbody></table></center>
</td>
</tr>
</tbody></table>
</body></html>
';
    $filename = $invoice . ".pdf";
    //$filename = "test.pdf";
    $page_data['user_id']=$id;
    $this->load->view('subscription_invoice',$page_data);
    $html = $this->output->get_output();
    // Load pdf library
    $css_file = base_url() . 'assets/css/bootstrap.min.css';
    $this->load->library('pdf');
    $this->pdf->set_base_path($css_file);
    $this->pdf->loadHtml($html);
    $this->pdf->setPaper('A4', 'landscape');
    $this->pdf->render();
    $output = $this->pdf->output();
    file_put_contents('upload/test/'.$filename, $output);
    $attachment_file = base_url().'upload/test/'.$filename;

$this->load->library('email');
$mail_config['smtp_host'] = 'mail.qpe.co.in';
$mail_config['smtp_port'] = '587';
$mail_config['smtp_user'] = 'business@qpe.co.in';
$mail_config['_smtp_auth'] = TRUE;
$mail_config['smtp_pass'] = 'businesss#6706';
$mail_config['smtp_crypto'] = 'tls';
$mail_config['protocol'] = 'smtp';
$mail_config['mailtype'] = 'html';
$mail_config['send_multipart'] = FALSE;
$mail_config['charset'] = 'utf-8';
$mail_config['wordwrap'] = TRUE;
$this->email->initialize($mail_config);
$this->email->set_newline("\r\n");
    $this->email->from('business@qpe.co.in','QPe Business');
    $this->email->to($email_id);
     $this->email->cc($list);
    $this->email->subject('Welcome to Qpe');
    $this->email->message($message);
    $this->email->attach($attachment_file);
    if ($this->email->send()) {
         //echo 'Email send.';
    } else {
        //show_error($this->email->print_debugger());
    }
}



public function login() {
        $email_id = $this->input->get('email_id');
        $password = md5($this->input->get('password'));
        $q = $this->db->query("SELECT * FROM users WHERE email='" . $email_id . "' AND password='" . $password . "' AND status='1'");
        if ($q->num_rows() > 0) {
            $query = $q->row();
            $temp_user_data=array();
            $temp_store_data=array();
            $user_id = $query->id;
            $store_data = $this->db->query("SELECT * FROM ecommerce_store WHERE user_id='" . $user_id . "' AND status='1'")->row();
            $temp_store_data['store_data']['store_link'] = base_url('store/'.$store_data->slug) ;
            $temp_store_data['store_data']['slug'] = $store_data->slug;
            $temp_store_data['store_data']['store_id'] = $store_data->id;
            $temp_store_data['store_data']['store_unique_id'] = $store_data->store_unique_id;
            $temp_store_data['store_data']['store_name']=$store_data->store_name;
            $temp_store_data['store_data']['store_logo'] = $store_data->store_logo ? base_url('upload/ecommerce/'.$store_data->store_logo) : '';
            $temp_store_data['store_data']['store_favicon']= $store_data->store_favicon ? base_url('upload/ecommerce/'.$store_data->store_favicon) :'';
            $temp_user_data['user_data']=$query;
            $final_data= array_merge($temp_store_data, $temp_user_data);
            echo $data['success'] = json_encode(array('status' => TRUE, 'message' => 'success', 'data' => $final_data));
        } else {
            echo $data['error'] = json_encode(array('status' => FALSE, 'message' => 'No record found', 'data' => NULL));
        }
    }

    public function forget_password() {
        $email_id = $this->input->get('email_id');
        $query = $this->db->query("SELECT * FROM users WHERE email='" . $email_id . "' AND status='1'");
        if ($query->num_rows() > 0) {
            $row = $query->row();
            echo $data['success'] = json_encode(array('status' => TRUE, 'message' => 'success'));
        } else {
            echo $data['error'] = json_encode(array('status' => FALSE, 'message' => 'No record found', 'data' => NULL));
        }
    }

    public function chnage_password() {
        $new_password = $this->input->get('new_password');
        $confirm_password = $this->input->get('confirm_password');
        $user_id = $this->input->get('user_id');
        if ($new_password == $confirm_password) {
            $this->db->query("UPDATE users SET password='" . md5($confirm_password) . "' WHERE id=");
            echo $data['success'] = json_encode(array('status' => TRUE, 'message' => 'success', 'data' => $row));
        } else {
            echo $data['error'] = json_encode(array('status' => FALSE, 'message' => 'New password and confirm password do not match.!!', 'data' => NULL));
        }
    }
        public function dashboard() {

        $current_date =date('Y-m-d');
        $user_id = $this->input->get('user_id');
        $store_id=$this->input->get('store_id');

        //$this->package_expiry_notificatin($user_id);
        $unread=$this->db->query("SELECT is_read FROM `notification_log` WHERE user_id='" . $user_id . "' AND is_read=1")->num_rows();
        $get_total_category=$this->db->query("SELECT id FROM `ecommerce_category` WHERE user_id='" . $user_id . "'")->num_rows();
        $data = $this->db->query("SELECT *, DATE(ordered_at) as Date, DAYNAME(ordered_at) as 'day', COUNT(id) as total_order,SUM(payment_amount) AS sell FROM ecommerce_cart WHERE date(ordered_at) > DATE_SUB(NOW(), INTERVAL 1 WEEK) AND MONTH(ordered_at) = MONTH(CURDATE()) AND YEAR(ordered_at) = YEAR(CURDATE()) AND user_id='" . $user_id . "' AND action_type='checkout' GROUP BY DAYNAME(ordered_at) ORDER BY (ordered_at)")->result();
        $get_today_data = $this->db->query("SELECT *, ecommerce_cart.status AS order_status, ecommerce_cart.id AS cart_id, ecommerce_cart_item.id AS item_id,ecommerce_cart_item.product_id,SUM(ecommerce_cart_item.unit_price) AS total_amount,ecommerce_cart_item.quantity,ecommerce_cart_item.attribute_info,ecommerce_product.product_name,ecommerce_product.thumbnail FROM `ecommerce_cart` INNER JOIN ecommerce_cart_item ON ecommerce_cart_item.cart_id=ecommerce_cart.id INNER JOIN ecommerce_product ON ecommerce_product.id=ecommerce_cart_item.product_id WHERE DATE(ecommerce_cart.ordered_at)='".$current_date ."' AND ecommerce_cart.store_id='".$store_id."' AND ecommerce_cart.action_type='checkout' GROUP BY ecommerce_cart_item.cart_id ORDER BY ecommerce_cart_item.cart_id DESC")->result();
        $get_total_order = $this->db->query("SELECT *, COUNT(user_id) AS total_order,SUM(payment_amount) AS total_amount FROM `ecommerce_cart` WHERE user_id='" . $user_id . "' AND action_type='checkout'")->result();
        $get_total_checkout = $this->db->query("SELECT COUNT(action_type) AS total_checkout_order FROM `ecommerce_cart` WHERE user_id='" . $user_id . "' AND action_type='checkout'")->result();
        $get_total_product=$this->db->query("select id from ecommerce_product WHERE user_id='" . $user_id . "' AND store_id='".$store_id."' ")->num_rows();
        $get_user_date=$this->db->query("select add_date,expired_date,package_id from users WHERE id='" . $user_id . "' ")->row();

        $temp = array();
        $temp_1 = array();
        $temp_2 = array();
        $temp_3 = array();
        $temp_chart = array();
        $temp_chart = $temp;
        $total_order = 0;
        foreach ($data as $value) {
            $temp['Day'] = $value->day;
            $temp['Sell'] = $value->sell;
            $temp_chart['chart_data'][] = $temp;
        }
        $temp_1['total_earning'] = round($get_total_order[0]->total_amount);
        $temp_1['total_order'] = $get_total_order[0]->total_order;
        $temp_1['total_checkout_order'] = $get_total_checkout[0]->total_checkout_order;
        $temp_1['recovered_cart'] = 0;
        $temp_1['total_product'] = $get_total_product;
        $temp_1['total_category'] = $get_total_category;
        $temp_1['add_date'] = $get_user_date->add_date;
        $temp_1['expired_date'] = $get_user_date->expired_date;
        $temp_1['package_id'] = $get_user_date->package_id;
        $temp_1['unread_notification']=$unread;
        $temp_3['order'] = $get_today_data;
        $temp_2 = array_merge($temp_1, $temp_chart);
        $final_data = array_merge($temp_3, $temp_2);
        echo $data['success'] = json_encode(array('status' => TRUE, 'message' => 'success', 'data' => $final_data));
    }
    public function upload_image($img='',$filename='',$ext=''){
        $file = 'upload/ecommerce/' . $filename;
        $success = file_put_contents($file, $img);
    }
    public function profile() {
        $postdata = json_decode(file_get_contents("php://input"), true);
        $user_id=$postdata['user_id'];
        $is_logo_file=$postdata['Is_logo_file'];
        $base_path = FCPATH . 'member';
        $photo='';
        if (!file_exists($base_path))
            mkdir($base_path, 0755);
         if($is_logo_file){

         $file_data=$postdata['logo_file_file_data'];
         $file_ext=$postdata['logo_file_file_type'];
         $file_name=$postdata['logo_file_file_name'];
         $profile_data = base64_decode($file_data);
         $file = $user_id. '_' . time() . substr(uniqid(mt_rand(), true), 0, 6) .$file_ext;
         $success = file_put_contents($base_path.'/'.$file, $profile_data);
        $photo=$file;
        }
        $profile = array('name' => $postdata['name'],
            'email' => $postdata['email'],
            'address' => @$postdata['address'],
            'time_zone' => @$postdata['time_zone']);

        if ($photo != "") {
            $profile["brand_logo"] = $photo;
        }

        $this->db->where('id', $user_id);
        $query = $this->db->update('users', $profile);
        if ($query == TRUE) {
            echo $data['success'] = json_encode(array('status' => TRUE, 'message' => 'success', 'data' => NULL));
        } else {
            echo $data['error'] = json_encode(array('status' => FALSE, 'message' => 'Something went wrong', 'data' => NULL));
        }
    }
  public function get_store_setting(){
        $user_id = $this->input->get('user_id');
        $county_list= $this->db->query("SELECT * FROM country_list")->result();
        $data=$this->db->query("SELECT * FROM ecommerce_store WHERE user_id='".$user_id."'")->result();
        echo $data['success'] = json_encode(array('status' => TRUE, 'message' => 'success', 'data' => $data[0],'county_list'=>$county_list));
    }
 public function get_country(){
        $county_list= $this->db->query("SELECT * FROM country_list")->result();
        echo $data['success'] = json_encode(array('status' => TRUE, 'message' => 'success', 'data' => $county_list));
    }

    public function store_setting() {
        $postdata = json_decode(file_get_contents("php://input"), true);
       // print_r($postdata);
        $postdata['store_name'];
        $user_id =$postdata['user_id'];
        $store_id =$postdata['store_id'];
        $is_store_file = $postdata['is_store_logo'];
        $is_slug_change = $postdata['is_slug_change'];
        if(!empty($is_store_file)){
        $file_data=$postdata['store_logo_file_data'];
        $file_ext=$postdata['store_logo_file_type'];
        $file_name=$postdata['store_logo_file_name'];
        $store_data = base64_decode($file_data);
        $store_logo = "storelogo_" . $user_id . '_' . time() . substr(uniqid(mt_rand(), true), 0, 6) .$file_ext;
        file_put_contents('upload/ecommerce/'.$store_logo, $store_data);
        }

        $is_store_favicon_file = $postdata['is_store_favicon'];
        if($is_store_favicon_file ==TRUE){
        $fav_file_data=$postdata['store_favicon_file_data'];
        $fav_ext=$postdata['store_favicon_file_type'];
        $fav_file_name=$postdata['store_favicon_file_name'];
        $store_fav_data = base64_decode($fav_file_data);
        $storefavicon = "storefavicon_" . $user_id . '_' . time() . substr(uniqid(mt_rand(), true), 0, 6) .$fav_ext;
        file_put_contents('upload/ecommerce/'.$storefavicon, $store_fav_data);
        }
        if($is_slug_change==TRUE){
        $slug=$postdata['store_slug'];
        $q = $this->db->query("SELECT * FROM ecommerce_store WHERE slug='" . $slug. "'");
        if ($q->num_rows() > 0) {
        $slg='1';
        }else{
        $slg='2';
        }
        }else{$slg='0';}
        $ecommerce_store = array(

            'store_email' =>$postdata['store_email'],
            'store_phone' =>$postdata['store_phone'],
            'store_country' =>$postdata['store_country'],
            'store_state' =>$postdata['store_state'],
            'store_city' =>$postdata['store_city'],
            'store_address' =>$postdata['store_address'],
            'store_zip' =>$postdata['store_zip'],
            'store_locale' =>$postdata['store_locale'],
            'pixel_id' =>$postdata['pixel_id'],
            'google_id' =>$postdata['google_id'],
            'status' =>$postdata['status'],
            'terms_use_link' =>$postdata['terms_use_link'],
            'refund_policy_link' =>$postdata['refund_policy_link'],
            'latitude' => $postdata['latitude'],
            'longitude' => $postdata['longitude'],
        );
          if($is_store_file==TRUE){
           $ecommerce_store['store_logo'] =$store_logo;
          }
           if($is_store_favicon_file ==TRUE){
             $ecommerce_store['store_favicon'] =$storefavicon;
           }
            if($slg!='0'){
                if($slg=='2'){
             $ecommerce_store['store_name'] =$postdata['store_name'];
             $ecommerce_store['slug']=$slug;
             }
            }
        $this->db->where('id', $store_id);
        $query = $this->db->update('ecommerce_store', $ecommerce_store);
        if ($query == TRUE) {
        if($slg=='1'){
         echo $data['success'] = json_encode(array('status' => FALSE, 'is_slug_change'=>'FALSE','message' => 'Slug is allready exits', 'data' => NULL));
        }else{
          echo $data['success'] = json_encode(array('status' => TRUE, 'message' => 'success', 'data' => NULL));
       }
        } else {
            echo $data['error'] = json_encode(array('status' => FALSE, 'message' => 'Something went wrong', 'data' => NULL));
        }
    }
public function create_store() {
        $postdata = json_decode(file_get_contents("php://input"), true);
        $cdate=date('Y-m-d h:s:i');
        $postdata['store_name'];
        $user_id =$postdata['user_id'];
        $store_unique_id =$postdata['store_unique_id'];
        $store_slug =$postdata['store_slug'];
        $is_store_file = $postdata['is_store_logo'];
        if($is_store_file==TRUE){
        $file_data=$postdata['store_logo_file_data'];
        $file_ext=$postdata['store_logo_file_type'];
        $file_name=$postdata['store_logo_file_name'];
        $store_data = base64_decode($file_data);
        $store_logo = "storelogo_" . $user_id . '_' . time() . substr(uniqid(mt_rand(), true), 0, 6) .$file_ext;
        file_put_contents('upload/ecommerce/'.$store_logo, $store_data);

        }
        $is_store_favicon_file = $postdata['is_store_favicon'];
         if($is_store_favicon_file ==TRUE){
        $fav_file_data=$postdata['store_favicon_file_data'];
        $fav_ext=$postdata['store_favicon_file_type'];
        $fav_file_name=$postdata['store_favicon_file_name'];
        $store_fav_data = base64_decode($fav_file_data);
        $storefavicon = "storefavicon_" . $user_id . '_' . time() . substr(uniqid(mt_rand(), true), 0, 6) .$fav_ext;
        file_put_contents('upload/ecommerce/'.$storefavicon, $store_fav_data);
        }
        $nums=$this->db->query("select * from ecommerce_store where slug='".$store_slug."'")->num_rows();
        if($nums>0){
          echo $data['success'] = json_encode(array('status' => FALSE,'slug'=>TRUE, 'message' => 'Slug is already avilable', 'data' => NULL));
        }else{

        $ecommerce_store = array(
            'store_unique_id'=>$postdata['store_unique_id'],
            'slug'=>$store_slug,
            'user_id'=>$user_id,
            'store_name' =>$postdata['store_name'],
            'store_email' =>$postdata['store_email'],
            'store_phone' =>$postdata['store_phone'],
            'store_country' =>$postdata['store_country'],
            'store_state' =>$postdata['store_state'],
            'store_city' =>$postdata['store_city'],
            'store_address' =>$postdata['store_address'],
            'store_zip' =>$postdata['store_zip'],
            'store_locale' =>$postdata['store_locale'],
            'pixel_id' =>$postdata['pixel_id'],
            'google_id' =>$postdata['google_id'],
            'status' =>$postdata['status'],
            'terms_use_link' =>$postdata['terms_use_link'],
            'refund_policy_link' =>$postdata['refund_policy_link'],
            'latitude' => $postdata['latitude'],
            'longitude' => $postdata['longitude'],
            'created_at'=>$cdate
        );
          if($is_store_file==TRUE){
           $ecommerce_store['store_logo'] =$store_logo;
          }
           if($is_store_favicon_file ==TRUE){
             $ecommerce_store['store_favicon'] =$storefavicon;
           }
       // $this->db->where('id', $store_id);
        $query = $this->db->insert('ecommerce_store', $ecommerce_store);
        $store_id = $this->db->insert_id();
          $config=array(
        'user_id'=>$user_id,
        'store_id'=>$store_id,
        );
        $configQry=$this->db->insert('ecommerce_config',$config);
        if ($query == TRUE) {
         $ecommerce_store['store_id'] = $store_id;
            echo $data['success'] = json_encode(array('status' => TRUE,'slug'=>TRUE, 'message' => 'success', 'data' =>$ecommerce_store));
        } else {
            echo $data['error'] = json_encode(array('status' => FALSE, 'message' => 'Something went wrong', 'data' => NULL));
        }
        }
    }
    function timeAgo($time_ago) {
        $time_ago = strtotime($time_ago);
        $cur_time = time();
        $time_elapsed = $cur_time - $time_ago;
        $seconds = $time_elapsed;
        $minutes = round($time_elapsed / 60);
        $hours = round($time_elapsed / 3600);
        $days = round($time_elapsed / 86400);
        $weeks = round($time_elapsed / 604800);
        $months = round($time_elapsed / 2600640);
        $years = round($time_elapsed / 31207680);
        // Seconds
        if ($seconds <= 60) {
            return "just now";
        }
        //Minutes
        else if ($minutes <= 60) {
            if ($minutes == 1) {
                return "one minute ago";
            } else {
                return "$minutes minutes ago";
            }
        }
        //Hours
        else if ($hours <= 24) {
            if ($hours == 1) {
                return "an hour ago";
            } else {
                return "$hours hrs ago";
            }
        }
        //Days
        else if ($days <= 7) {
            if ($days == 1) {
                return "yesterday";
            } else {
                return "$days days ago";
            }
        }
        //Weeks
        else if ($weeks <= 4.3) {
            if ($weeks == 1) {
                return "a week ago";
            } else {
                return "$weeks weeks ago";
            }
        }
        //Months
        else if ($months <= 12) {
            if ($months == 1) {
                return "a month ago";
            } else {
                return "$months months ago";
            }
        }
        //Years
        else {
            if ($years == 1) {
                return "one year ago";
            } else {
                return "$years years ago";
            }
        }
    }
    public function order_list(){
        $postdata = json_decode(file_get_contents("php://input"), true);
        $store_id=$postdata['store_id'];
        $user_id =$postdata['user_id'];
        $order_id =$postdata['order_id'];
        $data=$this->db->query("SELECT * FROM ecommerce_cart WHERE id='".$order_id."'")->result();
         echo $data['success'] = json_encode(array('status' => TRUE, 'message' => 'success', 'data' => $data));
    }
    public function order_filter() {
        $today =$this->input->get('today');
        $yesterday = $this->input->get('yesterday');
        $start_week = $this->input->get('start_week');
        $year = $this->input->get('year');
        $month = $this->input->get('month');
          $from_date = $this->input->get('from_date');
        $to_date = $this->input->get('to_date');
        $user_id = $this->input->get('user_id');
        $store_id = $this->input->get('store_id');
        $temp_array = array();
        $temp_1=array();
        if ($today != '' && $store_id !='') {
                $todays=date("Y-m-d", strtotime($today));
                $query = $this->db->query("SELECT *, ecommerce_cart.status as order_status, ecommerce_cart_item.id AS item_id,ecommerce_cart_item.product_id,SUM(ecommerce_cart_item.unit_price) AS total_amount,ecommerce_cart_item.quantity,ecommerce_cart_item.attribute_info,ecommerce_product.product_name,ecommerce_product.thumbnail FROM `ecommerce_cart` INNER JOIN ecommerce_cart_item ON ecommerce_cart_item.cart_id=ecommerce_cart.id INNER JOIN ecommerce_product ON ecommerce_product.id=ecommerce_cart_item.product_id WHERE DATE(ecommerce_cart.ordered_at)='".$todays."' AND ecommerce_cart.store_id='".$store_id."' GROUP BY ecommerce_cart_item.cart_id ORDER BY ecommerce_cart_item.cart_id DESC")->result();
                foreach ($query as $row) {
                $data_array = array(
                "cart_id"=> $row->cart_id,
          			"user_id"=> $row->user_id,
          			"store_id"=> $row->store_id,
          			"subscriber_id"=> $row->subscriber_id,
                      "product_name"=> $row->product_name,
          			"subtotal"=> $row->subtotal,
          			"tax"=> $row->tax,
          			"shipping"=> $row->shipping,
          			"coupon_code"=> $row->coupon_code,
          			"coupon_type"=> $row->coupon_type,
          			"discount"=> $row->discount,
          			"payment_amount"=> $row->payment_amount,
          			"currency"=> $row->currency,
          			"ordered_at"=> $row->ordered_at,
          			"buyer_first_name"=> $row->buyer_first_name,
          			"buyer_last_name"=> $row->buyer_last_name,
          			"buyer_email"=> $row->buyer_email,
          			"buyer_mobile"=> $row->buyer_mobile,
          			"buyer_country"=> $row->buyer_country,
          			"buyer_city"=> $row->buyer_city,
          			"buyer_state"=> $row->buyer_state,
          			"buyer_address"=> $row->buyer_address,
          			"buyer_zip"=> $row->buyer_zip,
          			"bill_first_name"=> $row->bill_first_name,
          			"bill_last_name"=> $row->bill_last_name,
          			"bill_email"=> $row->bill_email,
          			"bill_mobile"=> $row->bill_mobile,
          			"bill_country"=> $row->bill_country,
          			"bill_city"=> $row->bill_city,
          			"bill_state"=> $row->bill_state,
          			"bill_address"=> $row->bill_address,
          			"bill_zip"=> $row->bill_zip,
          			"delivery_note"=> $row->delivery_note,
          			"store_pickup"=> $row->store_pickup,
          			"pickup_point_details"=> $row->pickup_point_details,
          			"transaction_id"=> $row->transaction_id,
          			"card_ending"=> $row->card_ending,
          			"payment_method"=> $row->payment_method,
          			"checkout_account_email"=> $row->checkout_account_email,
          			"checkout_account_receiver_email"=> $row->checkout_account_receiver_email,
          			"checkout_account_country"=> $row->checkout_account_country,
          			"checkout_account_first_name"=> $row->checkout_account_first_name,
          			"checkout_account_last_name"=> $row->checkout_account_last_name,
          			"checkout_amount"=> $row->checkout_amount,
          			"checkout_currency"=> $row->checkout_currency,
          			"checkout_verify_status"=> $row->checkout_verify_status,
          			"checkout_timestamp"=> $row->checkout_timestamp,
          			"checkout_source_json"=> $row->checkout_source_json,
          			"manual_additional_info"=> $row->manual_additional_info,
          			"manual_filename"=> $row->manual_currency,
          			"manual_currency"=> $row->manual_currency,
          			"manual_amount"=> $row->manual_amount,
          			"paid_at"=> $row->paid_at,
          			"order_status"=> $row->order_status,
          			"shipped_through"=> $row->shipped_through,
          			"status_changed_at"=> $row->status_changed_at,
          			"status_changed_note"=> $row->status_changed_note,
          			"updated_at"=> $row->updated_at,
          			"action_type"=> $row->action_type,
          			"confirmation_response"=> $row->confirmation_response,
          			"last_completed_hour"=> $row->last_completed_hour,
          			"is_totally_completed"=> $row->is_totally_completed,
          			"last_sent_at"=>$row->last_sent_at,
          			"initial_date"=> $row->initial_date,
          			"last_processing_started_at"=> $row->last_processing_started_at,
          			"processing_status"=> $row->processing_status,
          			"payment_temp_session"=> $row->payment_temp_session,
          			"delivery_time"=> $row->delivery_time,
                  'img_url' => 'https://store.qpe.co.in/upload/ecommerce/' . $row->thumbnail);
                  array_push($temp_array, $data_array);
            }
            $temp_1['order'] = $temp_array;
            echo $data['success'] = json_encode(array('status' => TRUE, 'message' => 'success', 'data' => $temp_1));
        } if ($yesterday!='') {
            $yesterdays = date("Y-m-d", strtotime($yesterday));
            $query = $this->db->query("SELECT *, ecommerce_cart.status as order_status,ecommerce_cart_item.id AS item_id,ecommerce_cart_item.product_id,SUM(ecommerce_cart_item.unit_price) AS total_amount,ecommerce_cart_item.quantity,ecommerce_cart_item.attribute_info,ecommerce_product.product_name,ecommerce_product.thumbnail FROM `ecommerce_cart` INNER JOIN ecommerce_cart_item ON ecommerce_cart_item.cart_id=ecommerce_cart.id INNER JOIN ecommerce_product ON ecommerce_product.id=ecommerce_cart_item.product_id WHERE DATE(ecommerce_cart.ordered_at)='" . $yesterdays . "' AND ecommerce_cart.store_id='" . $store_id . "' GROUP BY ecommerce_cart_item.cart_id ORDER BY ecommerce_cart_item.cart_id DESC")->result();
            foreach ($query as $row) {
            $data_array = array(
            "cart_id"=> $row->cart_id,
			"user_id"=> $row->user_id,
			"store_id"=> $row->store_id,
			"subscriber_id"=> $row->subscriber_id,
            "product_name"=> $row->product_name,
			"subtotal"=> $row->subtotal,
			"tax"=> $row->tax,
			"shipping"=> $row->shipping,
			"coupon_code"=> $row->coupon_code,
			"coupon_type"=> $row->coupon_type,
			"discount"=> $row->discount,
			"payment_amount"=> $row->payment_amount,
			"currency"=> $row->currency,
			"ordered_at"=> $row->ordered_at,
			"buyer_first_name"=> $row->buyer_first_name,
			"buyer_last_name"=> $row->buyer_last_name,
			"buyer_email"=> $row->buyer_email,
			"buyer_mobile"=> $row->buyer_mobile,
			"buyer_country"=> $row->buyer_country,
			"buyer_city"=> $row->buyer_city,
			"buyer_state"=> $row->buyer_state,
			"buyer_address"=> $row->buyer_address,
			"buyer_zip"=> $row->buyer_zip,
			"bill_first_name"=> $row->bill_first_name,
			"bill_last_name"=> $row->bill_last_name,
			"bill_email"=> $row->bill_email,
			"bill_mobile"=> $row->bill_mobile,
			"bill_country"=> $row->bill_country,
			"bill_city"=> $row->bill_city,
			"bill_state"=> $row->bill_state,
			"bill_address"=> $row->bill_address,
			"bill_zip"=> $row->bill_zip,
			"delivery_note"=> $row->delivery_note,
			"store_pickup"=> $row->store_pickup,
			"pickup_point_details"=> $row->pickup_point_details,
			"transaction_id"=> $row->transaction_id,
			"card_ending"=> $row->card_ending,
			"payment_method"=> $row->payment_method,
			"checkout_account_email"=> $row->checkout_account_email,
			"checkout_account_receiver_email"=> $row->checkout_account_receiver_email,
			"checkout_account_country"=> $row->checkout_account_country,
			"checkout_account_first_name"=> $row->checkout_account_first_name,
			"checkout_account_last_name"=> $row->checkout_account_last_name,
			"checkout_amount"=> $row->checkout_amount,
			"checkout_currency"=> $row->checkout_currency,
			"checkout_verify_status"=> $row->checkout_verify_status,
			"checkout_timestamp"=> $row->checkout_timestamp,
			"checkout_source_json"=> $row->checkout_source_json,
			"manual_additional_info"=> $row->manual_additional_info,
			"manual_filename"=> $row->manual_currency,
			"manual_currency"=> $row->manual_currency,
			"manual_amount"=> $row->manual_amount,
			"paid_at"=> $row->paid_at,
			"order_status"=> $row->order_status,
			"shipped_through"=> $row->shipped_through,
			"status_changed_at"=> $row->status_changed_at,
			"status_changed_note"=> $row->status_changed_note,
			"updated_at"=> $row->updated_at,
			"action_type"=> $row->action_type,
			"confirmation_response"=> $row->confirmation_response,
			"last_completed_hour"=> $row->last_completed_hour,
			"is_totally_completed"=> $row->is_totally_completed,
			"last_sent_at"=>$row->last_sent_at,
			"initial_date"=> $row->initial_date,
			"last_processing_started_at"=> $row->last_processing_started_at,
			"processing_status"=> $row->processing_status,
			"payment_temp_session"=> $row->payment_temp_session,
			"delivery_time"=> $row->delivery_time,
            'img_url' => 'https://store.qpe.co.in/upload/ecommerce/' . $row->thumbnail);
            array_push($temp_array, $data_array);
            }
            $temp_1['order'] = $temp_array;
            echo $data['success'] = json_encode(array('status' => TRUE, 'message' => 'success', 'data' => $temp_1));
        }
        if ($start_week != '') {
            $start_weeks = date("Y-m-d", strtotime($start_week));
            $query = $this->db->query("SELECT *, ecommerce_cart.status as order_status,ecommerce_cart_item.id AS item_id,ecommerce_cart_item.product_id,SUM(ecommerce_cart_item.unit_price) AS total_amount,ecommerce_cart_item.quantity,ecommerce_cart_item.attribute_info,ecommerce_product.product_name,ecommerce_product.thumbnail FROM `ecommerce_cart` INNER JOIN ecommerce_cart_item ON ecommerce_cart_item.cart_id=ecommerce_cart.id INNER JOIN ecommerce_product ON ecommerce_cart_item.product_id=ecommerce_product.id WHERE ecommerce_cart.store_id='" . $store_id . "' AND DATE(ecommerce_cart.ordered_at) >= '".$start_weeks."' - interval 7 day AND ecommerce_cart.action_type='checkout' GROUP BY ecommerce_cart_item.cart_id ORDER BY ecommerce_cart_item.cart_id DESC")->result();
            foreach ($query as $row) {
                $data_array = array(
                    // 'cart_item_id' => $row->ecom_item_id,
                    // 'store_id' => $row->store_id,
                    // 'product_id' => $row->product_id,
                    // 'total_amount' => $row->total_amount,
                    // 'quantity' => $row->quantity,
                    // 'cart_id' => $row->id,
                    // 'user_id' => $row->user_id,
                    // 'shipping' => $row->shipping,
                    // 'ordered_at' => $row->ordered_at,
                    // 'buyer_first_name' => $row->buyer_first_name,
                    // 'buyer_last_name' => $row->buyer_last_name,
                    // 'buyer_email' => $row->buyer_email,
                    // 'buyer_mobile' => $row->buyer_mobile,
                    // 'status' => $row->status,
                    // 'payment_method' => $row->payment_method,
                    // 'product_name' => $row->product_name,
                    // 'time_ago' => $this->timeAgo($row->ordered_at),

            "cart_id"=> $row->cart_id,
			"user_id"=> $row->user_id,
			"store_id"=> $row->store_id,
			"subscriber_id"=> $row->subscriber_id,
            "product_name"=> $row->product_name,
			"subtotal"=> $row->subtotal,
			"tax"=> $row->tax,
			"shipping"=> $row->shipping,
			"coupon_code"=> $row->coupon_code,
			"coupon_type"=> $row->coupon_type,
			"discount"=> $row->discount,
			"payment_amount"=> $row->payment_amount,
			"currency"=> $row->currency,
			"ordered_at"=> $row->ordered_at,
			"buyer_first_name"=> $row->buyer_first_name,
			"buyer_last_name"=> $row->buyer_last_name,
			"buyer_email"=> $row->buyer_email,
			"buyer_mobile"=> $row->buyer_mobile,
			"buyer_country"=> $row->buyer_country,
			"buyer_city"=> $row->buyer_city,
			"buyer_state"=> $row->buyer_state,
			"buyer_address"=> $row->buyer_address,
			"buyer_zip"=> $row->buyer_zip,
			"bill_first_name"=> $row->bill_first_name,
			"bill_last_name"=> $row->bill_last_name,
			"bill_email"=> $row->bill_email,
			"bill_mobile"=> $row->bill_mobile,
			"bill_country"=> $row->bill_country,
			"bill_city"=> $row->bill_city,
			"bill_state"=> $row->bill_state,
			"bill_address"=> $row->bill_address,
			"bill_zip"=> $row->bill_zip,
			"delivery_note"=> $row->delivery_note,
			"store_pickup"=> $row->store_pickup,
			"pickup_point_details"=> $row->pickup_point_details,
			"transaction_id"=> $row->transaction_id,
			"card_ending"=> $row->card_ending,
			"payment_method"=> $row->payment_method,
			"checkout_account_email"=> $row->checkout_account_email,
			"checkout_account_receiver_email"=> $row->checkout_account_receiver_email,
			"checkout_account_country"=> $row->checkout_account_country,
			"checkout_account_first_name"=> $row->checkout_account_first_name,
			"checkout_account_last_name"=> $row->checkout_account_last_name,
			"checkout_amount"=> $row->checkout_amount,
			"checkout_currency"=> $row->checkout_currency,
			"checkout_verify_status"=> $row->checkout_verify_status,
			"checkout_timestamp"=> $row->checkout_timestamp,
			"checkout_source_json"=> $row->checkout_source_json,
			"manual_additional_info"=> $row->manual_additional_info,
			"manual_filename"=> $row->manual_currency,
			"manual_currency"=> $row->manual_currency,
			"manual_amount"=> $row->manual_amount,
			"paid_at"=> $row->paid_at,
			"order_status"=> $row->order_status,
			"shipped_through"=> $row->shipped_through,
			"status_changed_at"=> $row->status_changed_at,
			"status_changed_note"=> $row->status_changed_note,
			"updated_at"=> $row->updated_at,
			"action_type"=> $row->action_type,
			"confirmation_response"=> $row->confirmation_response,
			"last_completed_hour"=> $row->last_completed_hour,
			"is_totally_completed"=> $row->is_totally_completed,
			"last_sent_at"=>$row->last_sent_at,
			"initial_date"=> $row->initial_date,
			"last_processing_started_at"=> $row->last_processing_started_at,
			"processing_status"=> $row->processing_status,
			"payment_temp_session"=> $row->payment_temp_session,
			"delivery_time"=> $row->delivery_time,
            'img_url' => 'https://store.qpe.co.in/upload/ecommerce/' . $row->thumbnail);
            array_push($temp_array, $data_array);
            }
            $temp_1['order'] = $temp_array;
            echo $data['success'] = json_encode(array('status' => TRUE, 'message' => 'success', 'data' => $temp_1));
        }
        if ($year != '' && $month !='') {
           $years = date("Y", strtotime($year));
           $months = date("m", strtotime($month));
            $query = $this->db->query("SELECT *, ecommerce_cart.status AS order_status,ecommerce_cart_item.id AS item_id,ecommerce_cart_item.product_id,SUM(ecommerce_cart_item.unit_price) AS total_amount,ecommerce_cart_item.quantity,ecommerce_cart_item.attribute_info,ecommerce_product.product_name,ecommerce_product.thumbnail FROM `ecommerce_cart` INNER JOIN ecommerce_cart_item ON ecommerce_cart_item.cart_id=ecommerce_cart.id INNER JOIN ecommerce_product ON ecommerce_product.id=ecommerce_cart_item.product_id WHERE YEAR(ecommerce_cart.ordered_at)='" . $year . "' AND MONTH(ecommerce_cart.ordered_at)='" . $month . "' AND ecommerce_cart.store_id='" . $store_id . "' GROUP BY ecommerce_cart_item.cart_id ORDER BY ecommerce_cart_item.cart_id DESC")->result();
            foreach ($query as $row) {
            $data_array = array(
            "cart_id"=> $row->cart_id,
			"user_id"=> $row->user_id,
			"store_id"=> $row->store_id,
			"subscriber_id"=> $row->subscriber_id,
            "product_name"=> $row->product_name,
			"subtotal"=> $row->subtotal,
			"tax"=> $row->tax,
			"shipping"=> $row->shipping,
			"coupon_code"=> $row->coupon_code,
			"coupon_type"=> $row->coupon_type,
			"discount"=> $row->discount,
			"payment_amount"=> $row->payment_amount,
			"currency"=> $row->currency,
			"ordered_at"=> $row->ordered_at,
			"buyer_first_name"=> $row->buyer_first_name,
			"buyer_last_name"=> $row->buyer_last_name,
			"buyer_email"=> $row->buyer_email,
			"buyer_mobile"=> $row->buyer_mobile,
			"buyer_country"=> $row->buyer_country,
			"buyer_city"=> $row->buyer_city,
			"buyer_state"=> $row->buyer_state,
			"buyer_address"=> $row->buyer_address,
			"buyer_zip"=> $row->buyer_zip,
			"bill_first_name"=> $row->bill_first_name,
			"bill_last_name"=> $row->bill_last_name,
			"bill_email"=> $row->bill_email,
			"bill_mobile"=> $row->bill_mobile,
			"bill_country"=> $row->bill_country,
			"bill_city"=> $row->bill_city,
			"bill_state"=> $row->bill_state,
			"bill_address"=> $row->bill_address,
			"bill_zip"=> $row->bill_zip,
			"delivery_note"=> $row->delivery_note,
			"store_pickup"=> $row->store_pickup,
			"pickup_point_details"=> $row->pickup_point_details,
			"transaction_id"=> $row->transaction_id,
			"card_ending"=> $row->card_ending,
			"payment_method"=> $row->payment_method,
			"checkout_account_email"=> $row->checkout_account_email,
			"checkout_account_receiver_email"=> $row->checkout_account_receiver_email,
			"checkout_account_country"=> $row->checkout_account_country,
			"checkout_account_first_name"=> $row->checkout_account_first_name,
			"checkout_account_last_name"=> $row->checkout_account_last_name,
			"checkout_amount"=> $row->checkout_amount,
			"checkout_currency"=> $row->checkout_currency,
			"checkout_verify_status"=> $row->checkout_verify_status,
			"checkout_timestamp"=> $row->checkout_timestamp,
			"checkout_source_json"=> $row->checkout_source_json,
			"manual_additional_info"=> $row->manual_additional_info,
			"manual_filename"=> $row->manual_currency,
			"manual_currency"=> $row->manual_currency,
			"manual_amount"=> $row->manual_amount,
			"paid_at"=> $row->paid_at,
			"order_status"=> $row->order_status,
			"shipped_through"=> $row->shipped_through,
			"status_changed_at"=> $row->status_changed_at,
			"status_changed_note"=> $row->status_changed_note,
			"updated_at"=> $row->updated_at,
			"action_type"=> $row->action_type,
			"confirmation_response"=> $row->confirmation_response,
			"last_completed_hour"=> $row->last_completed_hour,
			"is_totally_completed"=> $row->is_totally_completed,
			"last_sent_at"=>$row->last_sent_at,
			"initial_date"=> $row->initial_date,
			"last_processing_started_at"=> $row->last_processing_started_at,
			"processing_status"=> $row->processing_status,
			"payment_temp_session"=> $row->payment_temp_session,
			"delivery_time"=> $row->delivery_time,
            'img_url' => 'https://store.qpe.co.in/upload/ecommerce/' . $row->thumbnail);
            array_push($temp_array, $data_array);
            }

            $temp_1['order'] = $temp_array;
            echo $data['success'] = json_encode(array('status' => TRUE, 'message' => 'success', 'data' => $temp_1));
        }
        if ($to_date != '' && $from_date !='') {
           $years = date("Y", strtotime($year));
           $months = date("m", strtotime($month));
            $query = $this->db->query("SELECT *, ecommerce_cart.status AS order_status,ecommerce_cart_item.id AS item_id,ecommerce_cart_item.product_id,SUM(ecommerce_cart_item.unit_price) AS total_amount,ecommerce_cart_item.quantity,ecommerce_cart_item.attribute_info,ecommerce_product.product_name,ecommerce_product.thumbnail FROM `ecommerce_cart` INNER JOIN ecommerce_cart_item ON ecommerce_cart_item.cart_id=ecommerce_cart.id INNER JOIN ecommerce_product ON ecommerce_product.id=ecommerce_cart_item.product_id WHERE (DATE(ecommerce_cart.ordered_at) BETWEEN '".$from_date."' AND '".$to_date."') AND ecommerce_cart.store_id='" . $store_id . "' GROUP BY ecommerce_cart_item.cart_id ORDER BY ecommerce_cart_item.cart_id DESC")->result();
            foreach ($query as $row) {
            $data_array = array(
            "cart_id"=> $row->cart_id,
			"user_id"=> $row->user_id,
			"store_id"=> $row->store_id,
			"subscriber_id"=> $row->subscriber_id,
            "product_name"=> $row->product_name,
			"subtotal"=> $row->subtotal,
			"tax"=> $row->tax,
			"shipping"=> $row->shipping,
			"coupon_code"=> $row->coupon_code,
			"coupon_type"=> $row->coupon_type,
			"discount"=> $row->discount,
			"payment_amount"=> $row->payment_amount,
			"currency"=> $row->currency,
			"ordered_at"=> $row->ordered_at,
			"buyer_first_name"=> $row->buyer_first_name,
			"buyer_last_name"=> $row->buyer_last_name,
			"buyer_email"=> $row->buyer_email,
			"buyer_mobile"=> $row->buyer_mobile,
			"buyer_country"=> $row->buyer_country,
			"buyer_city"=> $row->buyer_city,
			"buyer_state"=> $row->buyer_state,
			"buyer_address"=> $row->buyer_address,
			"buyer_zip"=> $row->buyer_zip,
			"bill_first_name"=> $row->bill_first_name,
			"bill_last_name"=> $row->bill_last_name,
			"bill_email"=> $row->bill_email,
			"bill_mobile"=> $row->bill_mobile,
			"bill_country"=> $row->bill_country,
			"bill_city"=> $row->bill_city,
			"bill_state"=> $row->bill_state,
			"bill_address"=> $row->bill_address,
			"bill_zip"=> $row->bill_zip,
			"delivery_note"=> $row->delivery_note,
			"store_pickup"=> $row->store_pickup,
			"pickup_point_details"=> $row->pickup_point_details,
			"transaction_id"=> $row->transaction_id,
			"card_ending"=> $row->card_ending,
			"payment_method"=> $row->payment_method,
			"checkout_account_email"=> $row->checkout_account_email,
			"checkout_account_receiver_email"=> $row->checkout_account_receiver_email,
			"checkout_account_country"=> $row->checkout_account_country,
			"checkout_account_first_name"=> $row->checkout_account_first_name,
			"checkout_account_last_name"=> $row->checkout_account_last_name,
			"checkout_amount"=> $row->checkout_amount,
			"checkout_currency"=> $row->checkout_currency,
			"checkout_verify_status"=> $row->checkout_verify_status,
			"checkout_timestamp"=> $row->checkout_timestamp,
			"checkout_source_json"=> $row->checkout_source_json,
			"manual_additional_info"=> $row->manual_additional_info,
			"manual_filename"=> $row->manual_currency,
			"manual_currency"=> $row->manual_currency,
			"manual_amount"=> $row->manual_amount,
			"paid_at"=> $row->paid_at,
			"order_status"=> $row->order_status,
			"shipped_through"=> $row->shipped_through,
			"status_changed_at"=> $row->status_changed_at,
			"status_changed_note"=> $row->status_changed_note,
			"updated_at"=> $row->updated_at,
			"action_type"=> $row->action_type,
			"confirmation_response"=> $row->confirmation_response,
			"last_completed_hour"=> $row->last_completed_hour,
			"is_totally_completed"=> $row->is_totally_completed,
			"last_sent_at"=>$row->last_sent_at,
			"initial_date"=> $row->initial_date,
			"last_processing_started_at"=> $row->last_processing_started_at,
			"processing_status"=> $row->processing_status,
			"payment_temp_session"=> $row->payment_temp_session,
			"delivery_time"=> $row->delivery_time,
            'img_url' => 'https://store.qpe.co.in/upload/ecommerce/' . $row->thumbnail);
            array_push($temp_array, $data_array);
            }
             $temp_1['order'] = $temp_array;
            echo $data['success'] = json_encode(array('status' => TRUE, 'message' => 'success', 'data' => $temp_1));
}

}
    public function pickup_points_list(){
        $store_id = $this->input->get("store_id");
        $query=$this->db->query("SELECT * FROM ecommerce_cart_pickup_points WHERE store_id='".$store_id."'")->result();
        echo $data['success'] = json_encode(array('status' => TRUE, 'message' => 'Success', 'data' => $query));
    }
    public function add_pickup_points(){
           $postdata = json_decode(file_get_contents("php://input"), true);

            $store_id =$postdata['store_id'];
            $point_name = strip_tags($postdata['point_name']);
           $point_details = strip_tags($postdata['point_details']);
            $status = $postdata['status'];
            $inserted_data = array
                (
                "store_id" => $store_id,
                "point_name" => $point_name,
                "point_details" => $point_details,
                "status" => $status
            );
            if ($this->db->insert("ecommerce_cart_pickup_points", $inserted_data)) {
                echo $data['success'] = json_encode(array('status' => TRUE, 'message' => 'success', 'data' => NULL));
            }else{
                echo $data['error'] = json_encode(array('status' => FALSE, 'message' => 'Something went wrong', 'data' => NULL));
            }
    }
        public function update_pickup_point() {
          $postdata = json_decode(file_get_contents("php://input"), true);
            $id = $postdata['pickup_point_id'];
            $store_id =$postdata['store_id'];
            $point_name = strip_tags($postdata['point_name']);
           $point_details = strip_tags($postdata['point_details']);
            $status = $postdata['status'];
            /*if (!isset($status) || $status == '')
               { $status = '0';}*/
            $updated_data = array
                (
                "point_name" => $point_name,
                "point_details" => $point_details,
                "status" => $status,

            );
            $this->db->where('id', $id);
           $this->db->where('store_id', $store_id);
           if ($this->db->update('ecommerce_cart_pickup_points', $updated_data)) {
                echo $data['success'] = json_encode(array('status' => TRUE, 'message' => 'success', 'data' => $updated_data));
           }else{
                echo $data['error'] = json_encode(array('status' => FALSE, 'message' => 'Something went wrong', 'data' => NULL));
           }
    }
    public function delete_pickup_point(){
     $postdata = json_decode(file_get_contents("php://input"), true);
            $id = $postdata['id'];
             $store_id = $postdata['store_id'];
               $this->db->where('id', $id);
                $this->db->where('store_id', $store_id);
           if ($this->db->delete('ecommerce_cart_pickup_points')) {
                echo $data['success'] = json_encode(array('status' => TRUE, 'message' => 'success', 'data' => NULL));
           }else{
                echo $data['error'] = json_encode(array('status' => FALSE, 'message' => 'Something went wrong', 'data' => NULL));
           }
    }
    public function business_hours_list() {
        $user_id = $this->input->get("user_id");
        $store_id = $this->input->get("store_id");
        $query=$this->db->query("SELECT * FROM ecommerce_store_business_hours WHERE store_id='".$store_id."' AND user_id='".$user_id."'")->result();
        echo $data['success'] = json_encode(array('status' => TRUE, 'message' => 'success', 'data' => $query));
    }
    public function add_business_hours(){
     $postdata = json_decode(file_get_contents("php://input"), true);
        $user_id = $postdata['user_id'];
        $store_id =  $postdata['store_id'];
        $always_open=$postdata['always_open'];
        $data=$postdata['Data'];

       $create=$this->db->query("select * from ecommerce_store_business_hours where store_id='".$store_id."'")->num_rows();
       if($create>0){
        foreach ($data as $key => $value) {
        $schedule_day =  $value["schedule_day"];
        $start_time =  $value["start_time"];
        $end_time =  $value["end_time"];
        $off_day= $value["off_day"];
        if($off_day==true){
        $off='1';
        }else{
        $off='0';
        }
        $id= $value["id"];
          $update_data = array("user_id" => $user_id,
          "store_id" => $store_id,
           "schedule_day" => $schedule_day,
           "start_time" => $start_time,
           "end_time" => $end_time,
           'off_day' => $off,
           'always_open'=>$always_open
           );
           $this->db->where('id',$id);
           $this->db->update('ecommerce_store_business_hours',$update_data);
            }
    echo $data['success'] = json_encode(array('status' => TRUE, 'message' => 'success', 'data' =>'Update'));
       }else{
        foreach ($data as $key => $value) {
        $schedule_day =  $value["schedule_day"];
        $start_time =  $value["start_time"];
        $end_time =  $value["end_time"];
        $off_day= $value["off_day"];
        $id= $value["id"];
         if($off_day==true){
        $off='1';
        }else{
        $off='0';
        }
          $insert_data = array("user_id" => $user_id,
          "store_id" => $store_id,
           "schedule_day" => $schedule_day,
           "start_time" => $start_time,
           "end_time" => $end_time,
           'off_day' => $off,
           'always_open'=>$always_open
           );

           $this->db->insert('ecommerce_store_business_hours',$insert_data);
            }
echo $data['success'] = json_encode(array('status' => TRUE, 'message' => 'success', 'data' =>'Insert'));
       }




    }
public function category_list() {
        $store_id = $this->input->get('store_id');
        $category_list = $this->db->query("select * from ecommerce_category where store_id=$store_id ")->result();
        $temp = array();
        foreach ($category_list as $row) {
            $category_data = array('id' => $row->id,
                'user_id' => $row->user_id,
                'store_id' => $row->store_id,
                'thumbnail' => 'https://store.qpe.co.in/upload/ecommerce/' . $row->thumbnail,
                'category_name' => $row->category_name,
                'status' => $row->status);
            array_push($temp, $category_data);
        }
        echo $data['success'] = json_encode(array('status' => TRUE, 'message' => 'success', 'data' => $temp));
    }

      public function add_category(){
            $postdata = json_decode(file_get_contents("php://input"), true);
            $category_name = strip_tags($postdata['category_name']);
            $store_id = $postdata['store_id'];
            $user_id = $postdata['user_id'];
            $thumbnail = $postdata['is_thumbnail_add'];
            $status = $postdata['status'];
            $photo='';
          //  $upload_dir = APPPATH . 'upload/ecommerce';
          if($thumbnail=='TRUE'){
            $base_path = FCPATH . 'upload/ecommerce';
            if (!file_exists($base_path))
                mkdir($base_path, 0755);
             $file_data=$postdata['file_data'];
             $file_ext=$postdata['file_type'];
             $file_name=$postdata['file_name'];
             $profile_data = base64_decode($file_data);
             $file = $user_id. '_' . time() . substr(uniqid(mt_rand(), true), 0, 6) .$file_ext;
             $success = file_put_contents($base_path.'/'.$file, $profile_data);
            $photo=$file;
          }
          /*  $profile = array('name' => $postdata['name'],
                'email' => $postdata['email'],
                'address' => @$postdata['address'],
                'time_zone' => @$postdata['time_zone']);*/


            $inserted_data = array
                (
                "store_id" => $store_id,
                "category_name" => $category_name,
                "status" => $status,
                "user_id" => $user_id,
                "updated_at" => date("Y-m-d H:i:s")
            );
            if ($photo != "") {
                  $inserted_data["thumbnail"] = $photo;
            }
            if ($this->db->insert("ecommerce_category", $inserted_data)) {
                echo $data['success'] = json_encode(array('status' => TRUE, 'message' => 'success', 'data' => NULL));
            }else{
                echo $data['error'] = json_encode(array('status' => TRUE, 'message' => 'Something went wrong', 'data' => NULL));
            }

    }
    public function update_category() {
           $postdata = json_decode(file_get_contents("php://input"), true);
            $category_name = strip_tags($postdata['category_name']);
             $id = $postdata['id'];
            $store_id = $postdata['store_id'];
            $user_id = $postdata['user_id'];
            $thumbnail = $postdata['is_thumbnail_update'];
            $status = $postdata['status'];
            $photo='';
          //  $upload_dir = APPPATH . 'upload/ecommerce';
          if($thumbnail=='TRUE'){
            $base_path = FCPATH . 'upload/ecommerce';
            if (!file_exists($base_path))
                mkdir($base_path, 0755);
             $file_data=$postdata['file_data'];
             $file_ext=$postdata['file_type'];
             $file_name=$postdata['file_name'];
             $profile_data = base64_decode($file_data);
             $file = $user_id. '_' . time() . substr(uniqid(mt_rand(), true), 0, 6) .$file_ext;
             $success = file_put_contents($base_path.'/'.$file, $profile_data);
            $photo=$file;
          }
          /*  $profile = array('name' => $postdata['name'],
                'email' => $postdata['email'],
                'address' => @$postdata['address'],
                'time_zone' => @$postdata['time_zone']);*/


            $inserted_data = array
                (
                "store_id" => $store_id,
                "category_name" => $category_name,
                "status" => $status,
                "user_id" => $user_id,
                "updated_at" => date("Y-m-d H:i:s")
            );
            if ($photo != "") {
                  $inserted_data["thumbnail"] = $photo;
            }
             $this->db->where('id', $id);
            $this->db->where('store_id', $store_id);
            if ($this->db->update("ecommerce_category", $inserted_data)) {
                echo $data['success'] = json_encode(array('status' => TRUE, 'message' => 'success', 'data' => NULL));
            }else{
                echo $data['error'] = json_encode(array('status' => TRUE, 'message' => 'Something went wrong', 'data' => NULL));
            }
    }

public function attribute_list() {
        $store_id =$this->input->get('store_id');
        $at_list = $this->db->query("SELECT * FROM ecommerce_attribute WHERE store_id=$store_id  ORDER BY attribute_name ASC")->result();
        $temp_at_info = array();
        /*foreach ($at_list as $value) {
            $at_info[$value->id] = $value->attribute_name;
            array_push($temp_at_info, $at_info);
        }*/
        echo $data['success'] = json_encode(array('status' => TRUE, 'message' => 'success', 'data' => $at_list));
    }
public function attribute_product_price() {
        $id =$this->input->get('id');
        $at_list = $this->db->query("SELECT * FROM ecommerce_attribute_product_price WHERE id=$id ")->result();
        $temp_at_info = array();
        /*foreach ($at_list as $value) {
            $at_info[$value->id] = $value->attribute_name;
            array_push($temp_at_info, $at_info);
        }*/
        echo $data['success'] = json_encode(array('status' => TRUE, 'message' => 'success', 'data' => $at_list));
    }
public function add_attribute() {
          $postdata = json_decode(file_get_contents("php://input"), true);
			  $data = array
                    (
			'user_id'=>$postdata['user_id'],
			 'store_id'=>$postdata['store_id'],
			/* 'woocommerce_config_id'=>$postdata['woocommerce_config_id'],
			 'woocommerce_attribute_id'=>$postdata['woocommerce_attribute_id'],
			'woocommerce_attribute_slug'=>$postdata['woocommerce_attribute_slug'],*/
			'attribute_name'=>$postdata['attribute_name'],
			'attribute_values'=>$postdata['attribute_values'],
			 'optional'=>$postdata['optional'],
			 'multiselect'=>$postdata['multiselect'],
			 'status'=>$postdata['status'],
			 'updated_at'=>date("Y-m-d H:i:s"),
			 );
        if ($this->db->insert('ecommerce_attribute', $data)){
        echo $data['success'] = json_encode(array('status' => TRUE, 'message' => 'success', 'data' => $data));
		}else{
			 echo $data['error'] = json_encode(array('status' => FALSE, 'error' => 'success', 'data' => $data));
		}
    }
    public function update_fcmToken() {
          $postdata = json_decode(file_get_contents("php://input"), true);
		  $id=$postdata['user_id'];
 		 $fcm_token=$postdata['fcm_token'];
			  $data = array('fcm_token'=> $fcm_token,);
			 $this->db->where('id',$id);
        if ($this->db->update('users', $data)){
        echo $data['success'] = json_encode(array('status' => TRUE, 'message' => 'success', 'data' => $data));
		}else{
			 echo $data['error'] = json_encode(array('status' => FALSE, 'error' => 'success', 'data' => $data));
		}
    }
public function update_attribute() {
          $postdata = json_decode(file_get_contents("php://input"), true);
		  $id=$postdata['id'];
			  $data = array
                    (

			'user_id'=>$postdata['user_id'],
			 'store_id'=>$postdata['store_id'],
			 /*'woocommerce_config_id'=>$postdata['woocommerce_config_id'],
			 'woocommerce_attribute_id'=>$postdata['woocommerce_attribute_id'],
			'woocommerce_attribute_slug'=>$postdata['woocommerce_attribute_slug'],*/
			'attribute_name'=>$postdata['attribute_name'],
			'attribute_values'=>$postdata['attribute_values'],
			 'optional'=>$postdata['optional'],
			 'multiselect'=>$postdata['multiselect'],
			 'status'=>$postdata['status'],
			 'updated_at'=>date("Y-m-d H:i:s"),
			 );
			 $this->db->where('id',$id);
        if ($this->db->update('ecommerce_attribute', $data)){
        echo $data['success'] = json_encode(array('status' => TRUE, 'message' => 'success', 'data' => $data));
		}else{
			 echo $data['error'] = json_encode(array('status' => FALSE, 'error' => 'success', 'data' => $data));
		}
    }
public function delete_attribute(){
        //$postdata = json_decode(file_get_contents("php://input"), true);
        $id =$this->input->get('id');
       // $id=$postdata['user_id'];
       $this->db->where('id',$id);
       if ($this->db->delete('ecommerce_attribute')){
        echo $data['success'] = json_encode(array('status' => TRUE, 'message' => 'success', 'data' =>NULL));
      }
      else{
      echo $data['error'] = json_encode(array('status' => FALSE, 'message' => 'Error', 'data' =>$data));
      }
    }
public function country_list(){
        $countty_list = $this->db->query("SELECT * FROM country_list")->result();
        echo $data['success'] = json_encode(array('status' => TRUE, 'message' => 'success', 'data' => $countty_list));
    }
public function get_payment_accounts() {
               $postdata = json_decode(file_get_contents("php://input"), true);
                $store_id=$postdata['store_id'];
                $user_id=$postdata['user_id'];
              /*   $temp_2 = array();
                $temp_3 = array();
                $temp=array();*/
$temp_2 = $this->db->query("SELECT * FROM ecommerce_store WHERE id='".$store_id."' AND user_id='".$user_id."'")->row();
$temp_3 = $this->db->query("SELECT * FROM ecommerce_config WHERE store_id='".$store_id."' AND user_id='".$user_id."'")->row();
                echo $data['success'] = json_encode(array('status' => TRUE, 'message' => 'success', 'store' =>$temp_2,'config'=>$temp_3));
            }

public function payment_accounts(){
                $postdata = json_decode(file_get_contents("php://input"), true);
                $store_id=$postdata['store_id'];
                $user_id=$postdata['user_id'];
                $upi_id=$postdata['upi_id'];
                $upi_username=$postdata['upi_username'];
                $razorpay_key_id =strip_tags($postdata['razorpay_key_id']);
                $razorpay_key_secret =strip_tags($postdata['razorpay_key_secret']);
                $razorpay_enabled=strip_tags($postdata['razorpay_enabled']);
                $currency = strip_tags($postdata['currency']);
                $paypal_enabled=strip_tags($postdata['paypal_enabled']);
                $currency_position = strip_tags($postdata['currency_position']);
                $decimal_point = strip_tags($postdata['decimal_point']);
                $thousand_comma = strip_tags($postdata['thousand_comma']);
                $manual_enabled = strip_tags($postdata['manual_enabled']);
                $cod_enabled = strip_tags($postdata['cod_enabled']);
                $razorpay_enabled = strip_tags($postdata['razorpay_enabled']);
                $gst_no = strip_tags($postdata['gst_no']);
                $tax_percentage = strip_tags($postdata['tax_percentage']);
                $shipping_charge = strip_tags($postdata['shipping_charge']);
                $is_store_pickup = strip_tags($postdata['is_store_pickup']);
                $is_home_delivery = strip_tags($postdata['is_home_delivery']);
                $is_checkout_country = strip_tags($postdata['is_checkout_country']);
                $is_checkout_state = strip_tags($postdata['is_checkout_state']);
                $is_checkout_city = strip_tags($postdata['is_checkout_city']);
                $is_checkout_zip = strip_tags($postdata['is_checkout_zip']);
                $is_checkout_email = strip_tags($postdata['is_checkout_email']);
                $is_checkout_phone = strip_tags($postdata['is_checkout_phone']);
                $is_delivery_note = strip_tags($postdata['is_delivery_note']);
                $is_preparation_time = strip_tags($postdata['is_preparation_time']);
                $preparation_time = strip_tags($postdata['preparation_time']);
                $preparation_time_unit = strip_tags($postdata['preparation_time_unit']);
                $is_order_schedule = strip_tags($postdata['is_order_schedule']);
                $order_schedule = strip_tags($postdata['order_schedule']);
                $is_guest_login = strip_tags($postdata['is_guest_login']);
                // $manual_payment=$this->input->post('manual_payment');
                $manual_payment = '1';
                $manual_payment_instruction = $postdata['manual_payment_instruction'];
                $dunzo_secret_key = $postdata['dunzo_secret_key'];
                $dunzo_client_id = $postdata['dunzo_client_id'];
                $dunzo_enabled = $postdata['dunzo_enabled'];
                $borzo_secret_key = $postdata['borzo_secret_key'];
                $borzo_authentication_Key = $postdata['borzo_authentication_Key'];
                $borzo_enabled = $postdata['borzo_enabled'];
                $rapido_secret_key = $postdata['rapido_secret_key'];
                $rapido_user_name = $postdata['rapido_user_name'];
                $shiprocket_api_key = $postdata['shiprocket_api_key'];
                $shiprocket_user_name = $postdata['shiprocket_user_name'];
                $rapido_enabled = $postdata['rapido_enabled'];
                $shiprocket_enabled = $postdata['shiprocket_enabled'];
                $paytm_merchant_key = $postdata['paytm_merchant_key'];
                $paytm_merchant_mid = $postdata['paytm_merchant_mid'];
                $stripe_enabled = $postdata['stripe_enabled'];
                $paypal_mode = $postdata['paypal_mode'];

                $store_type = $postdata['store_type'];
                if ($store_type == 'digital') {
                    $manual_payment_instruction = '';
                    $cod_enabled = '0';
                    $manual_payment = '0';
                }

                if ($manual_payment == "")
                    $manual_payment = "0";
                if ($currency_position == "")
                    $currency_position = "left";
                if ($thousand_comma == "")
                    $thousand_comma = "0";
                if ($decimal_point == "")
                    $decimal_point = "0";
                if ($is_store_pickup == "")
                    $is_store_pickup = "0";
                if ($is_home_delivery == "")
                    $is_home_delivery = "0";
                if ($is_checkout_country == "")
                    $is_checkout_country = "0";
                if ($is_checkout_state == "")
                    $is_checkout_state = "0";
                if ($is_checkout_city == "")
                    $is_checkout_city = "0";
                if ($is_checkout_zip == "")
                    $is_checkout_zip = "0";
                if ($is_checkout_email == "")
                    $is_checkout_email = "0";
                if ($is_checkout_phone == "")
                    $is_checkout_phone = "0";
                if ($is_delivery_note == "")
                    $is_delivery_note = "0";
                if ($is_preparation_time == "")
                    $is_preparation_time = "0";
                if ($is_order_schedule == "")
                    $is_order_schedule = "0";
                if ($is_guest_login == "")
                    $is_guest_login = "0";
                if ($is_preparation_time == '1') {
                    if (!isset($preparation_time) || $preparation_time == "")
                        $preparation_time = "30";
                    if (!isset($preparation_time_unit) || $preparation_time_unit == "")
                        $preparation_time_unit = "minutes";
                }
                if (!isset($order_schedule) || $order_schedule == "")
                    $order_schedule = "any";

                if ($store_type == 'physical') {

                    if ($is_store_pickup == '0' && $is_home_delivery == '0') {
                        echo $data['success'] = json_encode(array('status' => FALSE, 'message' => 'error', 'data' =>NULL));
                        exit();
                    }
                }
                $update_data = array
                    (

                    'razorpay_key_id' => $razorpay_key_id,
                    'razorpay_key_secret' => $razorpay_key_secret,
                    'currency' => $currency,
                    'manual_payment' => $manual_payment,
                    'manual_payment_instruction' => $manual_payment_instruction,
                    'user_id' => $user_id,
                    'store_id' => $store_id,
                    'currency_position' => $currency_position,
                    'decimal_point' => $decimal_point,
                    'thousand_comma' => $thousand_comma,
                    'updated_at' => date("Y-m-d H:i:s"),
                    'is_store_pickup' => $is_store_pickup,
                    'is_home_delivery' => $is_home_delivery,
                    'is_checkout_country' => $is_checkout_country,
                    'is_checkout_state' => $is_checkout_state,
                    'is_checkout_city' => $is_checkout_city,
                    'is_checkout_zip' => $is_checkout_zip,
                    'is_checkout_email' => $is_checkout_email,
                    'is_checkout_phone' => $is_checkout_phone,
                    'is_delivery_note' => $is_delivery_note,
                    'is_preparation_time' => $is_preparation_time,
                    'preparation_time' => $preparation_time,
                    'preparation_time_unit' => $preparation_time_unit,
                    'is_order_schedule' => $is_order_schedule,
                    'order_schedule' => $order_schedule,
                    'is_guest_login' => $is_guest_login,
                    'dunzo_secret_key' => $dunzo_secret_key,
                    'dunzo_client_id' => $dunzo_client_id,
                    'borzo_secret_key' => $borzo_secret_key,
                    'borzo_authentication_Key' => $borzo_authentication_Key,
                    'dunzo_mode' => $dunzo_enabled,
                    'borzo_mode' => $borzo_enabled,
                    'rapido_secret_key' => $rapido_secret_key,
                    'rapido_user_name' => $rapido_user_name,
                    'shiprocket_api_key' => $shiprocket_api_key,
                    'shiprocket_user_name' => $shiprocket_user_name,
                    'paypal_email' => $postdata['paypal_email'],
                    'stripe_billing_address' =>$postdata['stripe_billing_address'],
                    'stripe_secret_key'=>$postdata['stripe_secret_key'],
                    'stripe_publishable_key'=>$postdata['stripe_publishable_key'],
                    'paypal_mode' => $paypal_mode,

                );
                 $this->db->where('store_id',$store_id);
                //$get_data =$this->db->query("ecommerce_config")->result();
                if ($this->db->update("ecommerce_config", $update_data)){
                 $update_store = array
                    (
                    'gst_no' => $gst_no,
                    "cod_enabled" => $cod_enabled,
                    "tax_percentage" => $tax_percentage,
                    "shipping_charge" => $shipping_charge,
                    'dunzo_enabled' => $dunzo_enabled,
                    'borzo_enabled' => $borzo_enabled,
                    'rapido_enabled' => $rapido_enabled,
                    'shiprocket_enabled' => $shiprocket_enabled,
                    'stripe_enabled' => $stripe_enabled,
                    'razorpay_enabled'=>$razorpay_enabled,
                    'manual_enabled'=>$manual_enabled,
                    'paypal_enabled'=>$paypal_enabled

                );
                $this->db->where('id',$store_id);
                $this->db->update("ecommerce_store", $update_store);
                echo $data['success'] = json_encode(array('status' => TRUE, 'message' => 'success', 'data' =>NULL));
            }
                          }
 public function coupon_list(){
        //$postdata = json_decode(file_get_contents("php://input"), true);
        $store_id =$this->input->get('store_id');
        $user_id=$this->input->get('user_id');
        $data=$this->db->query("SELECT * FROM ecommerce_coupon WHERE user_id='".$user_id."' and store_id='".$store_id."'")->result();
        echo $data['success'] = json_encode(array('status' => TRUE, 'message' => 'success', 'data' =>$data));
    }
    public function add_coupon(){
                $postdata = json_decode(file_get_contents("php://input"), true);
                $user_id=$postdata['user_id'];
                $store_id =$postdata['store_id'];
                $product_ids =$postdata['product_ids'];
                $coupon_type =$postdata['coupon_type'];
                $coupon_code =strip_tags($postdata['coupon_code']);
                $coupon_amount =$postdata['coupon_amount'];
                $expiry_date =$postdata['expiry_date'];
                $max_usage_limit =$postdata['max_usage_limit'];
                $free_shipping_enabled =$postdata['free_shipping_enabled'];
                $status =$postdata['status'];
                if ($status == '')
                    $status = '0';
                if ($free_shipping_enabled == '')
                    $free_shipping_enabled = '0';
                /*if (!isset($product_ids) || !is_array($product_ids) || empty($product_ids))
                    $product_ids = '0';
                else
                    $product_ids = implode(',', $product_ids);*/
                $data = array
                    (
                    'store_id' => $store_id,
                    'product_ids' => $product_ids,
                    'coupon_type' => $coupon_type,
                    'coupon_code' => $coupon_code,
                    'coupon_amount' => $coupon_amount,
                    'expiry_date' => $expiry_date,
                    'max_usage_limit' => $max_usage_limit,
                    'free_shipping_enabled' => $free_shipping_enabled,
                    'status' => $status,
                    'updated_at' => date("Y-m-d H:i:s"),
                    'user_id' => $user_id
                );
                if ($this->db->insert('ecommerce_coupon', $data))
                echo $data['success'] = json_encode(array('status' => TRUE, 'message' => 'success', 'data' =>NULL));
                else
                echo $data['success'] = json_encode(array('status' => FALSE, 'message' => 'error', 'data' =>NULL));
    }
    public function edit_coupon() {
                $postdata = json_decode(file_get_contents("php://input"), true);
                $id = $postdata['coupon_id'];
                $user_id=$postdata['user_id'];
                $product_ids =$postdata['product_ids'];
                $coupon_type =$postdata['coupon_type'];
                $coupon_code =strip_tags($postdata['coupon_code']);
                $coupon_amount =$postdata['coupon_amount'];
                $expiry_date =$postdata['expiry_date'];
                $max_usage_limit =$postdata['max_usage_limit'];
                $free_shipping_enabled =$postdata['free_shipping_enabled'];
                $status =$postdata['status'];
                if ($status == '')
                    $status = '0';
                if ($free_shipping_enabled == '')
                    $free_shipping_enabled = '0';
               /* if (!isset($product_ids) || !is_array($product_ids) || empty($product_ids))
                    $product_ids = '0';
                else
                    $product_ids = implode(',', $product_ids);*/
                $data = array
                    (
                    'product_ids' => $product_ids,
                    'coupon_type' => $coupon_type,
                    'coupon_code' => $coupon_code,
                    'coupon_amount' => $coupon_amount,
                    'expiry_date' => $expiry_date,
                    'max_usage_limit' => $max_usage_limit,
                    'free_shipping_enabled' => $free_shipping_enabled,
                    'status' => $status,
                    'updated_at' => date("Y-m-d H:i:s")
                );
                 $this->db->where('id', $id);
           $this->db->where('user_id', $user_id);
                if ($this->db->update('ecommerce_coupon',$data))
                echo $data['success'] = json_encode(array('status' => TRUE, 'message' => 'success', 'data' =>NULL));
                else
                echo $data['success'] = json_encode(array('status' => FALSE, 'message' => 'error', 'data' =>NULL));
    }
    public function delete_coupon(){
        //$postdata = json_decode(file_get_contents("php://input"), true);
        $id =$this->input->get('id');
        $this->db->where('id', $id);

           if ($this->db->delete('ecommerce_coupon')) {
                echo $data['success'] = json_encode(array('status' => TRUE, 'message' => 'success', 'data' => NULL));
           }else{
                echo $data['error'] = json_encode(array('status' => FALSE, 'message' => 'Something went wrong', 'data' => NULL));
           }
    }
public function apperance_setting_data(){
          $store_id=$this->input->get('store_id');
		  $user_id=$this->input->get('user_id');

          $data=$this->db->query("SELECT ecommerce_config.id, ecommerce_config.user_id, ecommerce_config.store_id,ecommerce_config.font, ecommerce_config.is_category_wise_product_view,ecommerce_config.buy_button_title,ecommerce_config.store_pickup_title,ecommerce_config.product_sort, ecommerce_config.product_sort_order, ecommerce_config.product_listing,ecommerce_config.theme_color, ecommerce_config.hide_add_to_cart,ecommerce_config.hide_buy_now,ecommerce_config.whatsapp_send_order_button, ecommerce_config.whatsapp_phone_number,ecommerce_config.whatsapp_send_order_text, ecommerce_config.is_guest_login, ecommerce_config.updated_at,slider_image.slider_img,slider_image.id AS slider_id FROM `ecommerce_config` LEFT OUTER JOIN slider_image ON slider_image.store_id=ecommerce_config.store_id WHERE ecommerce_config.store_id='".$store_id."'")->result();

        echo $data['success'] = json_encode(array('status' => TRUE, 'message' => 'success', 'data' =>$data));
}
public function appearance_settings(){
                $postdata = json_decode(file_get_contents("php://input"), true);
                $store_id=$postdata['store_id'];
                $user_id=$postdata['user_id'];
                $is_category_wise_product_view =strip_tags($postdata['is_category_wise_product_view']);
                $product_listing = strip_tags($postdata['product_listing']);
                $product_sort =strip_tags($postdata['product_sort']);
                $product_sort_order =strip_tags($postdata['product_sort_order']);
                $theme_color =strip_tags($postdata['theme_color']);
                $hide_add_to_cart =strip_tags($postdata['hide_add_to_cart']);
                $hide_buy_now =strip_tags($postdata['hide_buy_now']);
                $buy_button_title =strip_tags($postdata['buy_button_title']);
                $store_pickup_title =strip_tags($postdata['store_pickup_title']);
                $font =strip_tags($postdata['font']);
                $whatsapp_send_order_button =strip_tags($postdata['whatsapp_send_order_button']);
                $whatsapp_phone_number =strip_tags($postdata['whatsapp_phone_number']);
                $whatsapp_send_order_text =$postdata['whatsapp_send_order_text'];
                $slider_id = $postdata['slider_id'];
                if ($hide_add_to_cart == "")
                    $hide_add_to_cart = "0";
                if ($hide_buy_now == "")
                    $hide_buy_now = "0";
                if ($whatsapp_send_order_button == "")
                    $whatsapp_send_order_button = "0";

                   $get_slider_image=$postdata['update_data'];
    $temp_array = '';
foreach ($get_slider_image as $value){
if($value['isNew']==false){
                    if($value['isUpdateBanner']==true){
                    $file_data=$value['file_data'];
                    $file_ext=$value['file_type'];
                    $file_name=$value['file_name'];
                    $img_data = base64_decode($file_data);
                    $slider_images = time() .  substr(uniqid(mt_rand(), true), 0, 6) .$file_ext;
                    $temp_array .=$slider_images.',';
                    file_put_contents('upload/slider_image/'.$slider_images, $img_data);
                    }else{
                    $slider_images =$value['slider_img'];
                    $temp_array .=$slider_images.',';
                    }

        }else{

                    $file_data=$value['file_data'];
                    $file_ext=$value['file_type'];
                    $file_name=$value['file_name'];
                    $img_data = base64_decode($file_data);
                    $slider_images = time(). substr(uniqid(mt_rand(), true), 0, 6) .$file_ext;
                    $temp_array .=$slider_images.',';
                    file_put_contents('upload/slider_image/'.$slider_images, $img_data);
                            }
}
  if (@$slider_id != '') {
                        $slider_data = array('user_id' =>$user_id,
                        'store_id' =>$store_id,
                        'slider_img' => $temp_array,
                        'is_active' => 1,
                        'is_deleted' => 0,
                        'created_date' => time());
                        $this->db->where('id',$slider_id);
                        $this->db->update('slider_image',$slider_data);
                        }else{
                        $slider_data = array('user_id' =>$user_id,
                        'store_id' =>$store_id,
                        'slider_img' => $temp_array,
                        'is_active' => 1,
                        'is_deleted' => 0,
                        'created_date' => time());
                        $this->db->insert('slider_image',$slider_data);
                        }


                $update_data = array
                    (
                    'is_category_wise_product_view' => $is_category_wise_product_view,
                    'product_listing' => $product_listing,
                    'product_sort' => $product_sort,
                    'product_sort_order' => $product_sort_order,
                    'theme_color' => $theme_color,
                    'user_id' => $user_id,
                    'hide_add_to_cart' => $hide_add_to_cart,
                    'hide_buy_now' => $hide_buy_now,
                    'whatsapp_send_order_button' => $whatsapp_send_order_button,
                    'whatsapp_phone_number' => isset($whatsapp_phone_number) ? $whatsapp_phone_number : "",
                    'whatsapp_send_order_text' => isset($whatsapp_send_order_text) ? $whatsapp_send_order_text : "",
                    'buy_button_title' => $buy_button_title,
                    'store_pickup_title' => $store_pickup_title,
                    'font' => $font,
                    'store_id' => $store_id
                );
                $get_data =$this->db->query("SELECT * FROM ecommerce_config WHERE store_id=$store_id")->result_array();
                if (isset($get_data[0])){
                $this->db->where('store_id',$store_id);
                $this->db->update('ecommerce_config',$update_data);
                echo $data['success'] = json_encode(array('status' => TRUE, 'message' => 'success', 'data' =>NULL));
                }else{
                    $this->basic->insert_data("ecommerce_config", $update_data);
                    echo $data['success'] = json_encode(array('status' => FALSE, 'message' => 'error', 'data' =>NULL));
                }
}
public function product_list(){
       //$postdata = json_decode(file_get_contents("php://input"), true);
        $store_id =$this->input->get('store_id');
          $user_id =$this->input->get('user_id');
            //$store_id = $postdata['store_id'];
           //$user_id = $postdata['user_id'];
        $data = $this->db->query("SELECT * FROM ecommerce_product WHERE store_id='".$store_id."' AND user_id='".$user_id."'")->result();
        echo $data['success'] = json_encode(array('status' => TRUE, 'message' => 'success', 'data' =>$data));
}
  public function delete_category(){
          $postdata = json_decode(file_get_contents("php://input"), true);
          //$store_id =$postdata['store_id'];
          //$user_id =$postdata['user_id'];
          $category_id=$postdata['category_id'];
          $this->db->where('id',$category_id);
          $query=$this->db->delete('ecommerce_category');
          if($query==true){
          echo $data['success'] = json_encode(array('status' => TRUE, 'message' => 'success', 'data' =>NULL));
          }else{
          echo $data['success'] = json_encode(array('status' => FALSE, 'message' => 'error', 'data' =>NULL));
          }
}

public function delete_product(){
          $postdata = json_decode(file_get_contents("php://input"), true);
          //$store_id =$postdata['store_id'];
          //$user_id =$postdata['user_id'];
          $product_id=$postdata['product_id'];
          $this->db->where('id',$product_id);
          $query=$this->db->delete('ecommerce_product');
          if($query==true){
          echo $data['success'] = json_encode(array('status' => TRUE, 'message' => 'success', 'data' =>NULL));
          }else{
          echo $data['success'] = json_encode(array('status' => FALSE, 'message' => 'error', 'data' =>NULL));
          }
}

public function product_details(){
        $store_id =$this->input->get('store_id');
        $user_id =$this->input->get('user_id');
        $product_id =$this->input->get('product_id');
        $temp=array();$temp2=array();$data=array();
        $data1 = $this->db->query("SELECT * FROM ecommerce_product WHERE store_id='".$store_id."' AND user_id='".$user_id."'")->result();
        /*$data1=$q->row();
        $cateId=$data1->category_id;
        $attri=$data1->attribute_ids;*/
        $data2 = $this->db->query("SELECT * FROM  ecommerce_category WHERE  store_id='".$store_id."' AND user_id='".$user_id."'")->result();
        $data3 = $this->db->query("SELECT * FROM   ecommerce_attribute WHERE   store_id='".$store_id."' AND user_id='".$user_id."'")->result();
        $data4 = $this->db->query("SELECT * FROM   `ecommerce_attribute_product_price` WHERE product_id='".$product_id."' ")->result();

        echo $data['success'] = json_encode(array('status' => TRUE, 'message' => 'success', 'product' =>$data1,'category'=>$data2,'attribute'=>$data3,'attribute_price'=>$data4));
}
public function add_product(){
        $postdata = json_decode(file_get_contents("php://input"), true);
                $store_id =$postdata['store_id'];
                $user_id =$postdata['user_id'];
                $category_id =$postdata['category_id'];
                $attribtue_ids =$postdata['attribute_ids'];
                $product_name =strip_tags($postdata['product_name']);
                $original_price =$postdata['original_price'];
                $sell_price =$postdata['sell_price'];
                $product_description =strip_tags($postdata['product_description']);
                $purchase_note =strip_tags($postdata['purchase_note']);
               // $thumbnail =$postdata[''];
                //$featured_images =$postdata[''];
                $taxable =$postdata['taxable'];
                $status =$postdata['status'];
                $stock_item =$postdata['stock_item'];
                $stock_display =$postdata['stock_display'];
                $stock_prevent_purchase =$postdata['stock_prevent_purchase'];
                $preparation_time =$postdata['preparation_time'];
                $preparation_time_unit =$postdata['preparation_time_unit'];
                $product_file =$postdata['digital_product_file'];
                $related_product_ids =$postdata['related_product_ids'];
                $upsell_product_id =$postdata['upsell_product_id'];
                $downsell_product_id =$postdata['downsell_product_id'];
                $is_featured =$postdata['is_featured'];
                $attribute_data=$postdata['Add_attribute_data'];
                $is_thumblin_image=$postdata['is_thumbnail_available'];
                //$is_fetaure_image= $postdata['is_featured_images_available'];
                $thumb=' ';
                $fimage=' ';
                if($is_thumblin_image=='TRUE'){
                $base_path = FCPATH . 'upload/ecommerce';
            if (!file_exists($base_path))
                mkdir($base_path, 0755);
             $file_data=$postdata['thumbnail_file_data'];
             $file_ext=$postdata['thumbnail_file_type'];
             $file_name=$postdata['thumbnail_file_name'];
             $profile_data = base64_decode($file_data);
             $file = $user_id. '_' . time() . substr(uniqid(mt_rand(), true), 0, 6) .$file_ext;
             $success = file_put_contents($base_path.'/'.$file, $profile_data);
             $thumb=$file;

          }
            $get_feture_image=$postdata['update_featured_images_data'];
$temp_array = '';
foreach ($get_feture_image as $value){
if($value['isNew']==false){
                    if($value['isUpdateBanner']==true){
                    $file_data=$value['file_data'];
                    $file_ext=$value['file_type'];
                    $file_name=$value['file_name'];
                    $img_data = base64_decode($file_data);
                    $slider_images = time() . substr(uniqid(mt_rand(), true), 0, 6) .$file_ext;
                    $temp_array .=$slider_images.',';
                    file_put_contents('upload/ecommerce/'.$slider_images, $img_data);
                    }else{
                    $slider_images =$value['featured_images'];
                    $temp_array .=$slider_images.',';
                    }

        }else{

                    $file_data=$value['file_data'];
                    $file_ext=$value['file_type'];
                    $file_name=$value['file_name'];
                    $img_data = base64_decode($file_data);
                    $slider_images =time(). substr(uniqid(mt_rand(), true), 0, 6) .$file_ext;
                    $temp_array .=$slider_images.',';
                    file_put_contents('upload/ecommerce/'.$slider_images, $img_data);
                            }
}
$fimage=$temp_array;
                if ($upsell_product_id == '')
                    $upsell_product_id = '0';
                if ($downsell_product_id == '')
                    $downsell_product_id = '0';
                if ($is_featured == '')
                    $is_featured = '0';



                if ($product_description == "<p></p>")
                    $product_description = "";
                if ($purchase_note == "<p></p>")
                    $purchase_note = "";

                if ($status == '')
                    $status = '0';
                if ($taxable == '')
                    $taxable = '0';
                if ($stock_display == '')
                    $stock_display = '0';
                if ($stock_prevent_purchase == '')
                    $stock_prevent_purchase = '0';


                if ($stock_item == "")
                    $stock_item = 0;
                if ($stock_display == "")
                    $stock_display = '0';
                if ($stock_prevent_purchase == "")
                    $stock_prevent_purchase = '0';

                $data = array
                    (
                    'store_id' => $store_id,
                    'category_id' => $category_id,
                    'attribute_ids' => $attribtue_ids,
                    'product_name' => $product_name,
                    'original_price' => $original_price,
                    'sell_price' => $sell_price,
                    'product_description' => $product_description,
                    'purchase_note' => $purchase_note,
                    'digital_product_file' => $product_file,
                    'taxable' => $taxable,
                    'status' => $status,
                    'stock_item' => $stock_item,
                    'stock_display' => $stock_display,
                    'stock_prevent_purchase' => $stock_prevent_purchase,
                    'preparation_time' => $preparation_time,
                    'preparation_time_unit' => $preparation_time_unit,
                    'user_id' => $user_id,
                    'deleted' => '0',
                    'updated_at' => date("Y-m-d H:i:s"),
                    'related_product_ids' => $related_product_ids,
                    'upsell_product_id' => $upsell_product_id,
                    'downsell_product_id' => $downsell_product_id,
                    'is_featured' => $is_featured,
                );
                 if($is_thumblin_image=='TRUE'){
                $data['thumbnail']=$thumb;
                }

                $data['featured_images']=$fimage;

             if ($this->db->insert('ecommerce_product', $data)) {
                    $product_id = $this->db->insert_id();
                    if ($attribtue_ids!='') {
                        foreach ($attribute_data as $at_data) {
                          $at_array=array(
                            'product_id'=>$product_id,
                            'attribute_id'=>$at_data['attribute_id'],
                             'attribute_option_name'=>$at_data['attribute_option_name'],
                             'price_indicator'=>$at_data['price_indicator'],
                             'amount'=>$at_data['amount'],
                              'stock'=>$at_data['stock']
                          );
                        $this->db->insert('ecommerce_attribute_product_price', $at_array);
                        }
                    }
                    echo $data['success'] = json_encode(array('status' => TRUE, 'message' => 'success', 'data' =>NULL));
                } else {
                    $product_id = '';
                    echo $data['success'] = json_encode(array('status' => FALSE, 'message' => 'error', 'data' =>NULL));
                }
}
public function clone_product(){
        $postdata = json_decode(file_get_contents("php://input"), true);
                $store_id =$postdata['store_id'];
                $user_id =$postdata['user_id'];
                $category_id =$postdata['category_id'];
                $attribtue_ids =$postdata['attribute_ids'];
                $product_name =strip_tags($postdata['product_name']);
                $original_price =$postdata['original_price'];
                $sell_price =$postdata['sell_price'];
                $product_description =strip_tags($postdata['product_description']);
                $purchase_note =strip_tags($postdata['purchase_note']);

                $taxable =$postdata['taxable'];
                $status =$postdata['status'];
                $stock_item =$postdata['stock_item'];
                $stock_display =$postdata['stock_display'];
                $stock_prevent_purchase =$postdata['stock_prevent_purchase'];
               $preparation_time =$postdata['preparation_time'];
                $preparation_time_unit =$postdata['preparation_time_unit'];
                $product_file =$postdata['digital_product_file'];
                $related_product_ids =$postdata['related_product_ids'];
                $upsell_product_id =$postdata['upsell_product_id'];
                $downsell_product_id =$postdata['downsell_product_id'];
                $is_featured =$postdata['is_featured'];
                $attribute_data=$postdata['Add_attribute_data'];
                $is_thumblin_image=$postdata['is_thumbnail_available'];
               // $is_fetaure_image=$postdata['is_featured_images_available'];
                $thumb=' ';$fimage=' ';
                if($is_thumblin_image=='TRUE'){
            $base_path = FCPATH . 'upload/ecommerce';
            if (!file_exists($base_path))
                mkdir($base_path, 0755);
             $file_data=$postdata['thumbnail_file_data'];
             $file_ext=$postdata['thumbnail_file_type'];
             $file_name=$postdata['thumbnail_file_name'];
             $profile_data = base64_decode($file_data);
             $file = $user_id. '_' . time() . substr(uniqid(mt_rand(), true), 0, 6) .$file_ext;
             $success = file_put_contents($base_path.'/'.$file, $profile_data);
             $thumb=$file;

          }else{
           $thumbnail =$postdata['thumbnail'];
          $thumb=$thumbnail;
          }
$get_feture_image=$postdata['update_featured_images_data'];
$temp_array = '';
foreach ($get_feture_image as $value){
if($value['isNew']==false){
                    if($value['isUpdateBanner']==true){
                    $file_data=$value['file_data'];
                    $file_ext=$value['file_type'];
                    $file_name=$value['file_name'];
                    $img_data = base64_decode($file_data);
                    $slider_images = $file_name . substr(uniqid(mt_rand(), true), 0, 6) .$file_ext;
                    $temp_array .=$slider_images.',';
                    file_put_contents('upload/ecommerce/'.$slider_images, $img_data);
                    }else{
                    $slider_images =$value['featured_images'];
                    $temp_array .=$slider_images.',';
                    }

        }else{

                    $file_data=$value['file_data'];
                    $file_ext=$value['file_type'];
                    $file_name=$value['file_name'];
                    $img_data = base64_decode($file_data);
                    $slider_images = $file_name. substr(uniqid(mt_rand(), true), 0, 6) .$file_ext;
                    $temp_array .=$slider_images.',';
                    file_put_contents('upload/ecommerce/'.$slider_images, $img_data);
                            }
}
$fimage=$temp_array;
                if ($upsell_product_id == '')
                    $upsell_product_id = '0';
                if ($downsell_product_id == '')
                    $downsell_product_id = '0';
                if ($is_featured == '')
                    $is_featured = '0';



                if ($product_description == "<p></p>")
                    $product_description = "";
                if ($purchase_note == "<p></p>")
                    $purchase_note = "";

                if ($status == '')
                    $status = '0';
                if ($taxable == '')
                    $taxable = '0';
                if ($stock_display == '')
                    $stock_display = '0';
                if ($stock_prevent_purchase == '')
                    $stock_prevent_purchase = '0';


                if ($stock_item == "")
                    $stock_item = 0;
                if ($stock_display == "")
                    $stock_display = '0';
                if ($stock_prevent_purchase == "")
                    $stock_prevent_purchase = '0';

                $data = array
                    (
                    'store_id' => $store_id,
                    'category_id' => $category_id,
                    'attribute_ids' => $attribtue_ids,
                    'product_name' => $product_name,
                    'original_price' => $original_price,
                    'sell_price' => $sell_price,
                    'product_description' => $product_description,
                    'purchase_note' => $purchase_note,
                    'digital_product_file' => $product_file,
                    'taxable' => $taxable,
                    'status' => $status,
                    'stock_item' => $stock_item,
                    'stock_display' => $stock_display,
                    'stock_prevent_purchase' => $stock_prevent_purchase,
                    'preparation_time' => $preparation_time,
                    'preparation_time_unit' => $preparation_time_unit,
                    'user_id' => $user_id,
                    'deleted' => '0',
                    'updated_at' => date("Y-m-d H:i:s"),
                    'related_product_ids' => $related_product_ids,
                    'upsell_product_id' => $upsell_product_id,
                    'downsell_product_id' => $downsell_product_id,
                    'is_featured' => $is_featured,
                    'thumbnail'=>$thumb,
                    'featured_images'=>$fimage,
                );
                 if($is_thumblin_image=='TRUE'){
                $data['thumbnail']=$thumb;
                }

                $data['featured_images']=$fimage;

             if ($this->db->insert('ecommerce_product', $data)) {
                    $product_id = $this->db->insert_id();
                    if ($attribtue_ids!='') {
                        foreach ($attribute_data as $at_data) {
                          $at_array=array(
                            'product_id'=>$product_id,
                            'attribute_id'=>$at_data['attribute_id'],
                             'attribute_option_name'=>$at_data['attribute_option_name'],
                             'price_indicator'=>$at_data['price_indicator'],
                             'amount'=>$at_data['amount'],
                              'stock'=>$at_data['stock']
                          );
                        $this->db->insert('ecommerce_attribute_product_price', $at_array);
                        }
                    }
                    echo $data['success'] = json_encode(array('status' => TRUE, 'message' => 'success', 'data' =>NULL));
                } else {
                    $product_id = '';
                    echo $data['success'] = json_encode(array('status' => FALSE, 'message' => 'error', 'data' =>NULL));
                }
}
public function update_product(){
        $postdata = json_decode(file_get_contents("php://input"), true);
            $id =$postdata['product_id'];
                $store_id =$postdata['store_id'];
                $user_id =$postdata['user_id'];
                $category_id =$postdata['category_id'];
                $attribtue_ids =$postdata['attribute_ids'];
                $product_name =strip_tags($postdata['product_name']);
                $original_price =$postdata['original_price'];
                $sell_price =$postdata['sell_price'];
                $product_description =strip_tags($postdata['product_description']);
                $purchase_note =strip_tags($postdata['purchase_note']);
               // $thumbnail =$postdata[''];
                //$featured_images =$postdata[''];
                $taxable =$postdata['taxable'];
                $status =$postdata['status'];
                $stock_item =$postdata['stock_item'];
                $stock_display =$postdata['stock_display'];
                $stock_prevent_purchase =$postdata['stock_prevent_purchase'];
               $preparation_time =$postdata['preparation_time'];
                $preparation_time_unit =$postdata['preparation_time_unit'];
                $product_file =$postdata['digital_product_file'];
                $related_product_ids =$postdata['related_product_ids'];
                $upsell_product_id =$postdata['upsell_product_id'];
                $downsell_product_id =$postdata['downsell_product_id'];
                $is_featured =$postdata['is_featured'];
                $attribute_data=$postdata['Add_attribute_data'];
                $is_thumblin_image=$postdata['is_thumbnail_available'];
                //$is_fetaure_image=$postdata['is_featured_images_available'];
                $thumb=' ';$fimage=' ';
                if($is_thumblin_image=='TRUE'){
            $base_path = FCPATH . 'upload/ecommerce';
            if (!file_exists($base_path))
                mkdir($base_path, 0755);
             $file_data=$postdata['thumbnail_file_data'];
             $file_ext=$postdata['thumbnail_file_type'];
             $file_name=$postdata['thumbnail_file_name'];
             $profile_data = base64_decode($file_data);
             $file = $user_id. '_' . time() . substr(uniqid(mt_rand(), true), 0, 6) .$file_ext;
             $success = file_put_contents($base_path.'/'.$file, $profile_data);
             $thumb=$file;

          }
$get_feture_image=$postdata['update_featured_images_data'];
$temp_array = '';
foreach ($get_feture_image as $value){
if($value['isNew']==false){
                    if($value['isUpdateBanner']==true){
                    $file_data=$value['file_data'];
                    $file_ext=$value['file_type'];
                    $file_name=$value['file_name'];
                    $img_data = base64_decode($file_data);
                    $slider_images = time() . substr(uniqid(mt_rand(), true), 0, 6) .$file_ext;
                    $temp_array .=$slider_images.',';
                    file_put_contents('upload/ecommerce/'.$slider_images, $img_data);
                    }else{
                    $slider_images =$value['featured_images'];
                    $temp_array .=$slider_images.',';
                    }

        }else{

                    $file_data=$value['file_data'];
                    $file_ext=$value['file_type'];
                    $file_name=$value['file_name'];
                    $img_data = base64_decode($file_data);
                    $slider_images = time(). substr(uniqid(mt_rand(), true), 0, 6) .$file_ext;
                    $temp_array .=$slider_images.',';
                    file_put_contents('upload/ecommerce/'.$slider_images, $img_data);
                            }
}
$fimage=$temp_array;
                if ($upsell_product_id == '')
                    $upsell_product_id = '0';
                if ($downsell_product_id == '')
                    $downsell_product_id = '0';
                if ($is_featured == '')
                    $is_featured = '0';



                if ($product_description == "<p></p>")
                    $product_description = "";
                if ($purchase_note == "<p></p>")
                    $purchase_note = "";

                if ($status == '')
                    $status = '0';
                if ($taxable == '')
                    $taxable = '0';
                if ($stock_display == '')
                    $stock_display = '0';
                if ($stock_prevent_purchase == '')
                    $stock_prevent_purchase = '0';


                if ($stock_item == "")
                    $stock_item = 0;
                if ($stock_display == "")
                    $stock_display = '0';
                if ($stock_prevent_purchase == "")
                    $stock_prevent_purchase = '0';

                $data = array
                    (
                    'store_id' => $store_id,
                    'category_id' => $category_id,
                    'attribute_ids' => $attribtue_ids,
                    'product_name' => $product_name,
                    'original_price' => $original_price,
                    'sell_price' => $sell_price,
                    'product_description' => $product_description,
                    'purchase_note' => $purchase_note,
                    'digital_product_file' => $product_file,
                    'taxable' => $taxable,
                    'status' => $status,
                    'stock_item' => $stock_item,
                    'stock_display' => $stock_display,
                    'stock_prevent_purchase' => $stock_prevent_purchase,
                    'preparation_time' => $preparation_time,
                    'preparation_time_unit' => $preparation_time_unit,
                    'user_id' => $user_id,
                    'deleted' => '0',
                    'updated_at' => date("Y-m-d H:i:s"),
                    'related_product_ids' => $related_product_ids,
                    'upsell_product_id' => $upsell_product_id,
                    'downsell_product_id' => $downsell_product_id,
                    'is_featured' => $is_featured,
                );
                 if($is_thumblin_image=='TRUE'){
                $data['thumbnail']=$thumb;
                }

                $data['featured_images']=$fimage;

                $this->db->where('id',$id);
             if ($this->db->update('ecommerce_product', $data)) {
                    $product_id = $this->db->insert_id();
                    if ($attribtue_ids!='') {
                        foreach ($attribute_data as $at_data) {
                        $pro_id=$at_data['id'];
                          $at_array=array(
                            'product_id'=>$product_id,
                            'attribute_id'=>$at_data['attribute_id'],
                             'attribute_option_name'=>$at_data['attribute_option_name'],
                             'price_indicator'=>$at_data['price_indicator'],
                             'amount'=>$at_data['amount'],
                              'stock'=>$at_data['stock']
                          );
                        $this->db->where('id',$id);
                        $this->db->update('ecommerce_attribute_product_price', $at_array);
                        }
                    }
                    echo $data['success'] = json_encode(array('status' => TRUE, 'message' => 'success', 'data' =>NULL));
                } else {
                    $product_id = '';
                    echo $data['success'] = json_encode(array('status' => FALSE, 'message' => 'error', 'data' =>NULL));
                }
}
public function cart_details(){
  $postdata = json_decode(file_get_contents("php://input"), true);
          $store_id =$postdata['store_id'];
          $user_id =$postdata['user_id'];
          $cart_id =$postdata['cart_id'];
          //$this->db->where('id',$cart_id);
          $temp=array();
          $data=$this->db->query("SELECT * FROM `ecommerce_cart` where id='".$cart_id."' order by id desc  ")->result();
          $article=$this->db->query("SELECT ecommerce_cart_item.id,ecommerce_cart_item.store_id,ecommerce_cart_item.cart_id,ecommerce_cart_item.product_id,ecommerce_cart_item.unit_price,ecommerce_cart_item.quantity,ecommerce_cart_item.coupon_info,ecommerce_cart_item.attribute_info,ecommerce_product.product_name,ecommerce_product.thumbnail FROM ecommerce_cart_item INNER JOIN ecommerce_product ON ecommerce_cart_item.product_id=ecommerce_product.id WHERE ecommerce_cart_item.cart_id='".$cart_id."'")->result();
          $temp=$data;
          echo $data['success'] = json_encode(array('status' => TRUE, 'message' => 'success', 'data' =>$temp[0],'article'=>$article));
}
public function cart_status_update(){
  $postdata = json_decode(file_get_contents("php://input"), true);
          $store_id =$postdata['store_id'];
          $user_id =$postdata['user_id'];
          $cart_id =$postdata['cart_id'];
          $status =$postdata['status'];
          $shipped_through=$postdata['shipped_through'];
          $status_note=$postdata['status_note'];
          $status_date=date('Y-d-m h:s:a');
          $data=array(
           'status'=>$status,
           'shipped_through'=>$shipped_through,
           'status_changed_at'=>$status_date,
           'status_changed_note'=>$status_note
          );
          $this->db->where('id',$cart_id);

          if($this->db->update('ecommerce_cart',$data)){
          echo $data['success'] = json_encode(array('status' => TRUE, 'message' => 'success', 'data' =>NULL));
          }else{
          echo $data['error'] = json_encode(array('status' => FALSE, 'message' => 'Somthing went wrong', 'data' =>NULL));

          }
}
public function package_expiry_notificatin($user_id=''){
    $current_date=date("Y-m-d");
    $get_user= $this->db->query("SELECT id,expired_date,fcm_token FROM users WHERE id='".$user_id."'")->row();
    $expired_date=date("Y-m-d",strtotime($get_user->expired_date));
    $device_token=$get_user->fcm_token;
    $temp_date_7 = date("Y-m-d",strtotime('-7 day' , strtotime ($expired_date)));
    $temp_date_4 = date("Y-m-d",strtotime('-4 day' , strtotime ($expired_date)));
    $temp_date_1 = date("Y-m-d",strtotime('-1 day' , strtotime ($expired_date)));
    $temp_date_0 = date("Y-m-d",strtotime('-0 day' , strtotime ($expired_date)));

    if(strtotime($temp_date_0) <= strtotime($current_date)){
      $user_data=array(
      "notification_type"=>"package",
			"title" => "Renewal Update",
			"body" => "Your plan is going to expire in the next 0 days!! Please renew your plan to continue the services.");
        $data = array(
            'title' => 'Renewal Update',
            'body' => 'Your plan is going to expire in the next 1 days!! Please renew your plan to continue the services.',
        );
        $temp['notification'] = $data;
        $temp_1['data'] = $user_data;
        $temp_2['to'] = $device_token;
        $temp_3 = array_merge($temp, $temp_1);
        $final_data = array_merge($temp_3, $temp_2);
        $this->sendNotification($final_data);

    }else if(strtotime($temp_date_1) <= strtotime($current_date)){
        $user_data=array(
      "notification_type"=>"package",
			"title" => "Renewal Update",
			"body" => "Your plan is going to expire in the next 1 days!! Please renew your plan to continue the services.");
        $data = array(
            'title' => 'Renewal Update',
            'body' => 'Your plan is going to expire in the next 1 days!! Please renew your plan to continue the services.',
        );
        $temp['notification'] = $data;
        $temp_1['data'] = $user_data;
        $temp_2['to'] = $device_token;
        $temp_3 = array_merge($temp, $temp_1);
        $final_data = array_merge($temp_3, $temp_2);
        $this->sendNotification($final_data);
    }else if(strtotime($temp_date_4) <= strtotime($current_date)){
        $user_data=array(
      "notification_type"=>"package",
			"title" => "Renewal Update",
			"body" => "Your plan is going to expire in the next 4 days!! Please renew your plan to continue the services.");
        $data = array(
            'title' => 'Renewal Update',
            'body' => 'Your plan is going to expire in the next 4 days!! Please renew your plan to continue the services.',
        );
        $temp['notification'] = $data;
        $temp_1['data'] = $user_data;
        $temp_2['to'] = $device_token;
        $temp_3 = array_merge($temp, $temp_1);
        $final_data = array_merge($temp_3, $temp_2);
        $this->sendNotification($final_data);
    }else if(strtotime($temp_date_7) <= strtotime($current_date)){
        $user_data=array(
      "notification_type"=>"package",
			"title" => "Renewal Update",
			"body" => "Your plan is going to expire in the next 7 days!! Please renew your plan to continue the services.");
        $data = array(
            'title' => 'Renewal Update',
            'body' => 'Your plan is going to expire in the next 7 days!! Please renew your plan to continue the services.',
        );
        $temp['notification'] = $data;
        $temp_1['data'] = $user_data;
        $temp_2['to'] = $device_token;
        $temp_3 = array_merge($temp, $temp_1);
        $final_data = array_merge($temp_3, $temp_2);
        $this->sendNotification($final_data);
    }
    }

    public function sendNotification($message=''){
        $dataString = json_encode($message);
  $curl = curl_init();
  curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://fcm.googleapis.com/fcm/send',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS =>$dataString,
  CURLOPT_HTTPHEADER => array(
    'Authorization: Bearer AAAAUo5miMw:APA91bG2UJFnYJEs45QjmzClIEDYUA8bHdRoRTzhQToAZKJVyhTQAdxG44khricmvWWBmBg6KCb7KvPk-NJEhoJyrB39xfgpBxshsGEDN9ZG7EY5eu_rIx0-NBJNg9MHeKZgi0pooURS',
    'Content-Type: application/json'
  ),
));
$response = curl_exec($curl);
curl_close($curl);
//echo $response;
    }
     public function notification_log() {
        $postdata = json_decode(file_get_contents("php://input"), true);
        $user_id = $postdata['user_id'];
        $store_id = $postdata['store_id'];
        $get_data = $this->db->query("SELECT notification_log.id,notification_log.massage,notification_log.cart_id AS oredr_id,notification_log.notification_type,notification_log.is_read,notification_log.created_date ,ecommerce_cart.subtotal,ecommerce_cart.buyer_first_name,ecommerce_cart.buyer_last_name FROM `notification_log` LEFT OUTER JOIN ecommerce_cart ON ecommerce_cart.id=notification_log.cart_id WHERE notification_log.user_id='".$user_id."' ORDER BY notification_log.id DESC")->result();
        echo $data['success'] = json_encode(array('status' => TRUE, 'message' => 'success', 'data' => $get_data));
    }
    public function is_notification_read() {
        $postdata = json_decode(file_get_contents("php://input"), true);
        $notification_id= $postdata['notification_id'];
        $this->db->where('id',$notification_id);
        $this->db->set('is_read',0);
        $this->db->set('updated_date',date("Y-m-d"));
        $this->db->update('notification_log');
        echo $data['success'] = json_encode(array('status' => TRUE, 'message' => 'success', 'data' => NULL));
    }
    public function catelog_category() {
        $get_data = $this->db->query("SELECT * FROM `category` where status='1'")->result();
        echo $data['success'] = json_encode(array('status' => TRUE, 'message' => 'success', 'data' => $get_data));
    }
    public function catelog_sub_category() {
     $postdata = json_decode(file_get_contents("php://input"), true);
        $category_id= $postdata['category_id'];
        $get_data = $this->db->query("SELECT * FROM `sub_category` where category_id='".$category_id."' and status='1';")->result();
        echo $data['success'] = json_encode(array('status' => TRUE, 'message' => 'success', 'data' => $get_data));
    }
    public function catelog_product() {
     $postdata = json_decode(file_get_contents("php://input"), true);
        $category_id= $postdata['category_id'];
        $sub_category_id= $postdata['sub_category_id'];
        $get_data = $this->db->query("SELECT * FROM `catalog` where cat_id='".$category_id."' and sub_cat_id='".$sub_category_id."';")->result();
        echo $data['success'] = json_encode(array('status' => TRUE, 'message' => 'success', 'data' => $get_data));
    }
public function add_catalog_product() {
         $postdata = json_decode(file_get_contents("php://input"), true);
         $store_id=$postdata['store_id'];
         $user_id=$postdata['user_id'];
         $category_id=$postdata['category_id'];
         $pro=$postdata['Products'];
         foreach($pro as $proData){
           $sub_id=$proData['sub_category_id'];
           $sub_pro=$proData['sub_category_products'];
            $q = $this->db->query("SELECT * FROM ecommerce_category WHERE catelog_cat_id='" . $category_id . "' and catelog_subcat_id='".$sub_id."' ");
         if ($q->num_rows() > 0) {
           $query = $q->row();
           $cat_id = $query->id;
         }else{
          $category_name = $this->db->query("select * from sub_category where id='" . $sub_id . "'")->row_array();
           $ecommerce_category=array('user_id'=> $user_id,
                  'store_id '=> $store_id,
                  'catelog_cat_id'=> $category_id,
                  'catelog_subcat_id'=> $sub_id,
                  'thumbnail'=> $category_name['thumbnail'],
                  'category_name'=> $category_name['name'],
                  'status'=> '1',
                  'updated_at'=> date("Y-m-d H:i:s"));
             if ($this->db->insert('ecommerce_category',$ecommerce_category)){
                 $cat_id = $this->db->insert_id();
              }
         }
           $sq = $this->db->query("SELECT * FROM ecommerce_sub_category WHERE catelog_cat_id='" . $category_id . "' and catelog_subcat_id='".$sub_id."'");
           if ($sq->num_rows() > 0) {
             $squery = $sq->row();
             $subcat_id = $squery->id;
           }else{
            $sub_category_name = $this->db->query("select * from sub_category where id='" . $sub_id . "'")->row_array();
            $ecommerce_sub_category=array('user_id'=> $user_id,
               'store_id '=> $store_id,
               'category_id'=> $cat_id,
                'catelog_cat_id'=>$category_id,
               'catelog_subcat_id'=>$sub_id,
               'thumbnail'=> $sub_category_name['thumbnail'],
               'sub_category_name'=> $sub_category_name['name'],
               'status'=> '1',
               'updated_at'=> date("Y-m-d H:i:s"));

           if ($this->db->insert('ecommerce_sub_category',$ecommerce_sub_category)){
               $subcat_id = $this->db->insert_id();
           }
         }

           foreach($sub_pro as $subProData){
             $inserted_data = array(
                 'user_id' => $user_id,
                 'store_id' => $store_id,
                 'product_name' => $subProData['name'],
                 'thumbnail'=>$subProData['thumbnail'],
                 'category_id' => $cat_id,
                 'sub_category_id' => $subcat_id,
                 'original_price' => $subProData['price'],
                  'sell_price' => $subProData['price'],
                 'stock_item' => $subProData['qty'],
                 'status' => '1',
                 'deleted' => '0',
                 'updated_at' => date("Y-m-d H:i:s")
             );
             $this->db->insert('ecommerce_product', $inserted_data);
           }

         }
         echo $data['success'] = json_encode(array('status' => TRUE, 'message' => 'success', 'data' => NULL));
   }
   public function app_install() {
        $postdata = json_decode(file_get_contents("php://input"), true);
        $date=date('Y-m-d');
        $status='1';
        $data = array(
     'fcm_token'=>$postdata['fcm_token'],
            'device_id'=>$postdata['device_id'],
             'device_type'=>$postdata['device_type'],
              'login_type'=>$postdata['login_type'],
               'version_relese'=>$postdata['version_relese'],
                'version_sdk_number'=>$postdata['version_sdk_number'],
                 'board'=>$postdata['board'],
                  'bootloader'=>$postdata['bootloader'],
                   'brand'=>$postdata['brand'],
                    'cpu_abi'=>$postdata['cpu_abi'],
                     'manufacturer'=>$postdata['manufacturer'],
                      'model'=>$postdata['model'],
                       'product'=>$postdata['product'],
                        'time'=>$postdata['time'],
                         'type'=>$postdata['type'],
                          'date'=>$date,
                           'status'=>$status,
        );

        $query = $this->db->insert('app_install', $data);
        if ($query == TRUE) {
            echo $data['success'] = json_encode(array('status' => TRUE,'slug'=>TRUE, 'message' => 'success', 'data' =>$data));
        } else {
            echo $data['error'] = json_encode(array('status' => FALSE, 'message' => 'Something went wrong', 'data' => NULL));
        }
    }
}
?>
