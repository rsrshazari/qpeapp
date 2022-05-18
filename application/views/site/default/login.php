<div class="container mt-5">
  <div class="row">
    <div class="col-12 col-sm-6 offset-sm-2 col-md-6 offset-md-2 col-lg-4 offset-lg-3 col-xl-4 offset-xl-4">
      <div class="login-brand">
        <a href="<?php echo base_url();?>"><img src="<?php echo base_url(); ?>assets/img/logo.png" alt="<?php echo $this->config->item('product_name');?>" width="200"></a>
      </div>

      <div class="card card-primary">
        <div class="card-header"><h4><i class="fas fa-sign-in-alt"></i> <?php echo $this->lang->line("Login"); ?></h4></div>
        <?php
          if($this->session->flashdata('login_msg')!='')
          {
              echo "<div class='alert alert-danger text-center'>";
                  echo $this->session->flashdata('login_msg');
              echo "</div>";
          }
          if($this->session->flashdata('reset_success')!='')
          {
              echo "<div class='alert alert-success text-center'>";
                  echo $this->session->flashdata('reset_success');
              echo "</div>";
          }
          if($this->session->userdata('reg_success') != ''){
            echo '<div class="alert alert-success text-center">'.$this->session->userdata("reg_success").'</div>';
            $this->session->unset_userdata('reg_success');
          }
          if(form_error('mobileno') != ''  )
          {
            $form_error="";
            if(form_error('mobileno') != '') $form_error.=form_error('mobileno');

            echo "<div class='alert alert-danger text-center'>".$form_error."</div>";

          }

          $default_user = $default_pass ="";
          if($this->is_demo=='1'){
            $default_user = "admin@xerochat.com";
            $default_pass="123456";
          }
        ?>
        <div class="card-body">
          <form method="POST" action="<?php echo base_url('home/login'); ?>">
              <h5 id="slice_mob"></h5>
            <div id="mobile_div" class="form-group">
              <label for="mobile"><?php echo $this->lang->line("Mobile No"); ?></label>
              <input  id="mobileno" type="text" value="<?php echo $default_user ?>" class="form-control" name="mobileno" tabindex="1"  maxlength="10">
              <!-- <div class="invalid-feedback">
                Please fill in your email
              </div> -->
            </div>

            <div id="otp_div" style="display:none" class="form-group">

               <div class="d-block">
              	<label for="opt" class="control-label"><?php echo $this->lang->line("Verification Code"); ?></label>
                <div class="float-right">
                 <span id="resend" class="text-small" style="font-weight:bold;cursor:pointer" onclick="resend()">
                   <?php echo $this->lang->line("Want to re-send?"); ?>
                 </span>
               </div>
              </div>
             <input id="otp" type="text" value="<?php echo $default_pass ?>"  class="form-control" name="otp" tabindex="2" required maxlength="6">

              <input type="hidden" name="csrf_token" id="csrf_token" value="<?php echo $this->session->userdata('csrf_token_session'); ?>">

              <!-- <div class="invalid-feedback">
                please fill in your password
              </div> -->
            </div>

            <!-- <div class="form-group">
              <div class="custom-control custom-checkbox">
                <input type="checkbox" name="remember" class="custom-control-input" tabindex="3" id="remember-me">
                <label class="custom-control-label" for="remember-me">Remember Me</label>
              </div>
            </div> -->

            <div class="form-group">
              <button style="display:none" id="login_btn" type="submit" class="btn btn-primary btn-lg btn-block login_btn" tabindex="4">
                <i class="fa fa-sign-in-alt"></i> <?php echo $this->lang->line("Go to Dashboard") ?>
              </button>
              <button id="otp_btn" type="button" onclick="get_varification_coad()" class="btn btn-primary btn-lg btn-block login_btn" tabindex="4">
                <i class="fa fa-sign-in-alt"></i> <?php echo $this->lang->line("Get Verification Code"); ?>
              </button>
            </div>
          </form>

       <!--   <?php if($this->config->item('enable_signup_form')!='0') : ?>
          <div class="row sm-gutters">
            <div class="col-12 col-sm-12 col-md-12 col-lg-6" style="padding-top: 15px;">
            	<?php echo $google_login_button2=str_replace("ThisIsTheLoginButtonForGoogle",$this->lang->line("Login with Google"), $google_login_button); ?>
             </div>
            <div class="col-12 col-sm-12 col-md-12 col-lg-6" style="padding-top: 15px;">
            	<?php echo $fb_login_button2=str_replace("ThisIsTheLoginButtonForFacebook",$this->lang->line("Login with Facebook"), $fb_login_button); ?>
            </div>-->

            <!-- <div class="col-12">
	             <div class="text-muted text-center">
	            	<br><?php echo $this->lang->line("Do not have an account?"); ?> <a href="<?php echo base_url('home/sign_up'); ?>"><?php echo $this->lang->line("Create one"); ?></a>
	        	</div>
	      	 </div> -->
          </div>
	      	<?php endif; ?>

        </div>
      </div>
    </div>
  </div>
</div>
<script>
function get_varification_coad() {
//  $('#loader').show();
var mob1 = $('#mobileno').val();
var NumberRegex = /^[0-9]*$/;
if(mob1.length == 10){
if(NumberRegex.test(mob1)){
  var mobslice=mob1.slice(mob1.length - 3);
$.ajax({
url: '<?= base_url() ?>home/get_verification_code',
type: "POST",
data: {
mob: mob1
},
success: function (data) {
  var d=data.split('#');
  if(d[0]==1){
    $('#slice_mob').html(d[1]);
    $('#otp_div').show();
    $('#login_btn').show();
    $('#mobile_div').hide();
    $('#otp_btn').hide();
  }else{
    $('#slice_mob').html(d[1]);
    $('#otp_div').hide();
    $('#login_btn').hide();
    $('#mobile_div').show();
    $('#otp_btn').show();
  }
//$('#loader').hide();
}
});
}else{
  alert('Enter valid mobile no');
}
}else{
  alert('Enter valid mobile no');
}
}
function resend(){
  $('#slice_mob').html('');
  $('#otp_div').hide();
  $('#login_btn').hide();
  $('#mobile_div').show();
  $('#otp_btn').show();
}
</script>
