<section class="section section_custom pt-1">
  <div class="section-header d-none">
    <h1><i class="fas fa-shopping-cart"></i> <?php echo $page_title;?></h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item"><a href="<?php echo base_url('messenger_bot'); ?>"><?php echo $this->lang->line("Messenger Bot"); ?></a></div>
      <div class="breadcrumb-item"><a href="<?php echo base_url('ecommerce'); ?>"><?php echo $this->lang->line("E-commerce"); ?></a></div>
      <div class="breadcrumb-item"><?php echo $this->lang->line("Orders"); ?></div>
    </div>
  </div>

  <?php $this->load->view('admin/theme/message'); ?>

  <style type="text/css">
    @media (max-width: 575.98px) {
      #search_store_id{width: 75px;}
      #search_status{width: 80px;}
      #select2-search_store_id-container,#select2-search_status-container,#search_value{padding-left: 8px;padding-right: 5px;}
    }
  </style>

  <div class="section-body">
    <div class="row">
      <div class="col-12">
        <div class="card no_shadow">
          <div class="card-body data-card p-0 pr-3">
            <div class="row">
              <div class="col-12 col-md-8">
                <?php
                
                $status_list[''] = $this->lang->line("Status");                
                echo 
                '<div class="input-group mb-3" id="searchbox">
                  <div class="input-group-prepend d-none">
                    '.form_dropdown('search_store_id',$store_list,$this->session->userdata("ecommerce_selected_store"),'class="form-control select2" id="search_store_id"').'
                  </div>
                  <div class="input-group-prepend">
                    '.form_dropdown('search_status',$status_list,'','class="form-control select2" id="search_status"').'
                  </div>
                  <input type="text" class="form-control" id="search_value" autofocus name="search_value" placeholder="'.$this->lang->line("Search...").'" style="max-width:300px;">
                  <div class="input-group-append">
                    <button class="btn btn-primary" type="button" id="search_action"><i class="fas fa-search"></i> <span class="d-none d-sm-inline">'.$this->lang->line("Search").'</span></button>
                  </div>
                </div>'; ?>                                          
              </div>

              <div class="col-12 col-md-4 d-none d-sm-block">
              	<?php
  			          echo $drop_menu ='<a href="javascript:;" id="search_date_range" class="btn btn-outline-primary btn-lg float-right icon-left btn-icon"><i class="fas fa-calendar"></i> '.$this->lang->line("Choose Date").'</a><input type="hidden" id="search_date_range_val">';
  			        ?>                                         
              </div>
              <div class="col-12 text-left">
                <?php
                  echo '<a href="'.base_url("ecommerce/download_csv").'" target="_BLANK" class="btn btn-outline-primary btn-lg float-right"><i class="fas fa-file-csv"></i> '.$this->lang->line("Download").'</a>';
                ?>                                         
              </div>
            </div>

            <div class="table-responsive2">
                <input type="hidden" id="put_page_id">
                <table class="table table-bordered" id="mytable">
                  <thead>
                    <tr>
                      <th>#</th>      
                      <th style="vertical-align:middle;width:20px">
                          <input class="regular-checkbox" id="datatableSelectAllRows" type="checkbox"/><label for="datatableSelectAllRows"></label>        
                      </th>
                      <th><?php echo $this->lang->line("Subscriber ID")?></th>              
                      <th><?php echo $this->lang->line("Store")?></th>              
                      <th><?php echo $this->lang->line("Status")?></th>              
                      <th><?php echo $this->lang->line("Coupon")?></th>                   
                      <th><?php echo $this->lang->line("Amount")?></th>                   
                      <th><?php echo $this->lang->line("Currency")?></th>                   
                      <th><?php echo $this->lang->line("Invoice")?></th>                   
                      <th><?php echo $this->lang->line("Transaction ID")?></th>                   
                      <th><?php echo $this->lang->line("Manual Payment")?></th>                                      
                      <th><?php echo $this->lang->line("Method")?></th>                   
                      <th><?php echo $this->lang->line("Ordered at")?></th>                   
                      <th><?php echo $this->lang->line("Paid at")?></th>                  
                  	</tr>
                  </thead>
                </table>
            </div>
          </div>
        </div>
      </div>       
        
    </div>
  </div>          

</section>



<script>

	var base_url="<?php echo site_url(); ?>";
  var search_param = "<?php echo $search_param;?>";
	
	$('#search_date_range').daterangepicker({
	  ranges: {
	    '<?php echo $this->lang->line("Last 30 Days");?>': [moment().subtract(29, 'days'), moment()],
	    '<?php echo $this->lang->line("This Month");?>'  : [moment().startOf('month'), moment().endOf('month')],
	    '<?php echo $this->lang->line("Last Month");?>'  : [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
	  },
	  startDate: moment().subtract(29, 'days'),
	  endDate  : moment()
	}, function (start, end) {
	  $('#search_date_range_val').val(start.format('YYYY-M-D') + '|' + end.format('YYYY-M-D')).change();
	});

	var perscroll;
	var table1 = '';
	table1 = $("#mytable").DataTable({
	  serverSide: true,
	  processing:true,
	  bFilter: false,
	  order: [[ 12, "desc" ]],
	  pageLength: 10,
	  ajax: {
	      url: base_url+'ecommerce/order_list_data',
	      type: 'POST',
	      data: function ( d )
	      {
	          d.search_store_id = $('#search_store_id').val();
            d.search_status = $('#search_status').val();
	          d.search_value = $('#search_value').val();
	          d.search_date_range = $('#search_date_range_val').val();
	      }
	  },
	  language: 
	  {
	    url: "<?php echo base_url('assets/modules/datatables/language/'.$this->language.'.json'); ?>"
	  },
	  dom: '<"top"f>rt<"bottom"lip><"clear">',
	  columnDefs: [
	    {
	        targets: [1,3,5],
	        visible: false
	    },
	    {
	        targets: [0,2,4,7,8,9,10,11,12,13],
	        className: 'text-center'
	    },
      {
          targets: [5,6],
          className: 'text-right'
      },
	    {
	        targets: [0,4,8,10],
	        sortable: false
	    }
	  ],
	  fnInitComplete:function(){  // when initialization is completed then apply scroll plugin
	         if(areWeUsingScroll)
	         {
	           if (perscroll) perscroll.destroy();
	           perscroll = new PerfectScrollbar('#mytable_wrapper .dataTables_scrollBody');
	         }
	     },
	     scrollX: 'auto',
	     fnDrawCallback: function( oSettings ) { //on paginition page 2,3.. often scroll shown, so reset it and assign it again 
	         if(areWeUsingScroll)
	         { 
	           if (perscroll) perscroll.destroy();
	           perscroll = new PerfectScrollbar('#mytable_wrapper .dataTables_scrollBody');
	         }
	     }
	});


	$("document").ready(function(){	   

	    $(document).on('change', '#search_store_id', function(e) {
	        table1.draw();
	    });

      $(document).on('change', '#search_status', function(e) {
          table1.draw();
      });

	    $(document).on('change', '#search_date_range_val', function(e) {
        	e.preventDefault();
        	table1.draw();
      });

    	$(document).on('keypress', '#search_value', function(e) {
      	if(e.which == 13) $("#search_action").click();
    	});

    	$(document).on('click', '#search_action', function(event) {
      	event.preventDefault(); 
      	table1.draw();
    	});

      setTimeout(function(){ 
        if(search_param!='') 
        {
          $("#search_value").val(search_param);
          $("#search_action").click();
        }
      }, 1000);


      $(document).on('change','.payment_status',function(e){
          var table_id = $(this).attr('data-id');
          var payment_status = $(this).val();
          $("#status_changed_cart_id").val(table_id);
          $("#status_changed_status").val(payment_status);
          $("#status_changed_note").val("");
          $("#order_id").val(table_id );
          $("#change_payment_status_modal").modal();
          if(payment_status=='shipped'){
          $("#show_option").show();
          }else{
          $("#show_option").hide();
          }
      });


    	$(document).on('click','#change_payment_status_submit',function(e){
     debugger;
          var table_id = $("#status_changed_cart_id").val();
          var payment_status = $("#status_changed_status").val();
          var status_changed_note = $("#status_changed_note").val();
          
          var shipped_through= $("#shipped_through").val();
          var store_id= $("#store_id").val();
          $(this).addClass('btn-progress');
          $.ajax({
              context: this,
              type:'POST' ,
              dataType:'JSON',
              url:"<?php echo base_url('ecommerce/change_payment_status')?>",
              data:{table_id:table_id,payment_status:payment_status,status_changed_note:status_changed_note,store_id:store_id,shipped_through:shipped_through},
              success:function(response)
              { 
              debugger;
                  $(this).removeClass('btn-progress');
                  if(response.status == '1')
                  {
                    var success_message=response.message;
                    var span = document.createElement("span");
                    span.innerHTML = success_message;
                    swal({ title:'<?php echo $this->lang->line("Order Status Updated"); ?>', content:span,icon:'success'});
                  }
                  else
                  {
                    swal('<?php echo $this->lang->line("Error")?>',  response.message, 'error');
                  }
                  $("#change_payment_status_modal").modal('hide');
                  table1.draw(false);
              },
              error:function(response){
                 var span = document.createElement("span");
                 span.innerHTML = response.responseText;
                 swal({ title:'<?php echo $this->lang->line("Error!"); ?>', content:span,icon:'error'});
              }
          });
    	        
    	});

      $(document).on('click', '#mp-download-file', function(e) {
        e.preventDefault();

        // Makes reference 
        var that = this;

        // Starts spinner
        $(that).removeClass('btn-outline-info');
        $(that).addClass('btn-info disabled btn-progress');

        // Grabs ID
        var file = $(this).data('id');

        // Requests for file
        $.ajax({
          type: 'POST',
          data: { file },
          dataType: 'JSON',
          url: '<?php echo base_url('ecommerce/manual_payment_download_file') ?>',
          success: function(res) {
            // Stops spinner
            $(that).removeClass('btn-info disabled btn-progress');
            $(that).addClass('btn-outline-info');

            // Shows error if something goes wrong
            if (res.error) {
              swal({
                icon: 'error',
                text: res.error,
                title: '<?php echo $this->lang->line('Error!'); ?>',
              });
              return;
            }

            // If everything goes well, requests for downloading the file
            if (res.status && 'ok' === res.status) {
              window.location = '<?php echo base_url('ecommerce/manual_payment_download_file'); ?>';
            }
          },
          error: function(xhr, status, error) {
            // Stops spinner
            $(that).removeClass('btn-info disabled btn-progress');
            $(that).addClass('btn-outline-info');

            // Shows internal errors
            swal({
              icon: 'error',
              text: error,
              title: '<?php echo $this->lang->line('Error!'); ?>',
            });
          }
        });
      });


      $(document).on('click', '.additional_info', function() { 
        $(this).addClass('btn-progress');       
        var cart_id = $(this).attr('data-id');
        $.ajax({
            context: this,
            type:'POST' ,
            url:"<?php echo base_url('ecommerce/addtional_info_modal_content')?>",
            data:{cart_id:cart_id},
            success:function(response)
            { 
              $('.additional_info').removeClass('btn-progress'); 
              $('#manual-payment-modal .modal-body').html(response);
              $('#manual-payment-modal').modal();
            }
        });
      });


	});

</script>




<div class="modal fade" tabindex="-1" role="dialog" id="manual-payment-modal" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fas fa-file-invoice-dollar"></i> <?php echo $this->lang->line("Manual Payment Information");?></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      </div>
      <div class="modal-footer bg-whitesmoke br"> 
        <button type="button" class="btn btn-secondary btn-lg" data-dismiss="modal"><i class="fa fa-remove"></i> <?php echo $this->lang->line("Close"); ?></button>
      </div>
    </div>
  </div>
</div>



<div class="modal fade" id="change_payment_status_modal" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-mega" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><?php echo $this->lang->line("Update Order Status");?></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <?php
      $store_id=$this->session->userdata('ecommerce_selected_store');
      $user_id=$this->session->userdata('user_id');
      $query=$this->db->query("SELECT ecommerce_config.id,ecommerce_config.user_id,ecommerce_config.store_id,ecommerce_config.dunzo_secret_key,ecommerce_config.dunzo_client_id,ecommerce_config.borzo_secret_key,ecommerce_config.borzo_authentication_Key,ecommerce_config.rapido_secret_key,ecommerce_config.rapido_user_name,ecommerce_config.shiprocket_api_key,ecommerce_config.shiprocket_user_name,ecommerce_store.dunzo_enabled,ecommerce_store.borzo_enabled,ecommerce_store.rapido_enabled,ecommerce_store.shiprocket_enabled,ecommerce_store.store_unique_id,ecommerce_store.store_name,ecommerce_store.store_email,ecommerce_store.store_phone,ecommerce_store.store_country,ecommerce_store.store_city,ecommerce_store.store_state,ecommerce_store.store_zip,ecommerce_store.store_address FROM `ecommerce_config` INNER JOIN ecommerce_store ON ecommerce_store.id=ecommerce_config.store_id WHERE ecommerce_config.user_id=$user_id AND ecommerce_config.store_id=$store_id;")->result();
      //print_r($query);
      ?>
      <div class="modal-body">
      <div id="show_option">
      <div class="row">
      <div class="col-md-3">
      <label><?php echo $this->lang->line("Shipped through"); ?></label>
      <select name="shipped_through" id="shipped_through" class="form-control">
      <option value="">Select</option>
      <?php
      if($query[0]->dunzo_enabled=='1'){
      ?>
      <option value="dunzo"><?php echo $this->lang->line("Dunzo"); ?></option>
      <?php
      }if($query[0]->borzo_enabled=='1'){
      ?>
      <option value="borzo"><?php echo $this->lang->line("Borzo"); ?></option>
      <?php
      }if($query[0]->rapido_enabled=='1'){
      ?>
      <option value="rapido"><?php echo $this->lang->line("Rapido"); ?></option>
      <?php
      }if($query[0]->shiprocket_enabled=='1'){
      ?>
      <option value="shiprocket"><?php echo $this->lang->line("Shiprocket"); ?></option>
      <?php
      }
      ?>
      <input type="hidden" name="store_id" id="store_id" value="<?= @$store_id;?>">
      
      
      
      <input type="hidden" name="dunzo_secret_key" id="dunzo_secret_key" value="<?= @$query[0]->dunzo_secret_key;?>">
      <input type="hidden" name="dunzo_client_id" id="dunzo_client_id" value="<?= @$query[0]->dunzo_client_id;?>">
      <input type="hidden" name="borzo_secret_key" id="borzo_secret_key" value="<?= @$query[0]->borzo_secret_key;?>">
      <input type="hidden" name="borzo_authentication_Key" id="borzo_authentication_Key" value="<?= @$query[0]->borzo_authentication_Key;?>">
      <input type="hidden" name="rapido_secret_key" id="rapido_secret_key" value="<?= @$query[0]->rapido_secret_key;?>">
      <input type="hidden" name="rapido_user_name" id="rapido_user_name" value="<?= @$query[0]->rapido_user_name;?>">
      <input type="hidden" name="shiprocket_api_key" id="shiprocket_api_key" value="<?= @$query[0]->shiprocket_api_key;?>">
      <input type="hidden" name="shiprocket_user_name" id="shiprocket_user_name" value="<?= @$query[0]->shiprocket_user_name;?>">
      <input type="hidden" name="store_title" id="store_title" value="<?= @$query[0]->store_name;?>">
      <input type="hidden" name="store_email" id="store_email" value="<?= @$query[0]->store_email;?>">
      <input type="hidden" name="store_phone" id="store_phone" value="<?= @$query[0]->store_phone;?>">
      <input type="hidden" name="store_country" id="store_country" value="<?= @$query[0]->store_country;?>">
      <input type="hidden" name="store_city" id="store_city" value="<?= @$query[0]->store_city;?>">
      <input type="hidden" name="store_state" id="store_state" value="<?= @$query[0]->store_state;?>">
      <input type="hidden" name="store_zip" id="store_zip" value="<?= @$query[0]->store_zip;?>">
      <input type="hidden" name="store_address" id="store_address" value="<?= @$query[0]->store_address;?>">
      <input type="hidden" name="order_id" id="order_id" value="">
      </div>
      </div>
      </div>
        <label><?php echo $this->lang->line("Additional Note"); ?> (<?php echo $this->lang->line("Optional"); ?>)</label>
        <input type="hidden" id="status_changed_cart_id">
        <input type="hidden" id="status_changed_status">
        <textarea id="status_changed_note" class="form-control" style="min-height: 200px"></textarea>
      </div>
      <div class="modal-footer bg-whitesmoke br"> 
        <button type="button" class="btn btn-primary btn-lg" id="change_payment_status_submit"><i class="fas fa-paper-plane"></i> <?php echo $this->lang->line("Submit"); ?></button>
        <button type="button" class="btn btn-secondary btn-lg" data-dismiss="modal"><i class="fas fa-times"></i> <?php echo $this->lang->line("Close"); ?></button>
      </div>
    </div>
  </div>
</div>
