<?php
if (isset($system_dashboard))
    $has_system_dashboard = 'yes';
else
    $has_system_dashboard = 'no';
$month_name_array = array(
    '01' => 'January',
    '02' => 'February',
    '03' => 'March',
    '04' => 'April',
    '05' => 'May',
    '06' => 'June',
    '07' => 'July',
    '08' => 'August',
    '09' => 'September',
    '10' => 'October',
    '11' => 'November',
    '12' => 'December'
);
?>
<style>
    .list-unstyled-border li {
        margin-bottom: 45px !important;
    }
    #period_loader {height: 100%;width:100%;display: table;}
    #period_loader i{font-size:60px;display: table-cell; vertical-align: middle;padding:30px 0;}

</style>

<section class="section">
    <?php if ($other_dashboard == 1) : ?>
        <?php if ($system_dashboard == 'yes') : ?>
            <div class="section-header">
                <h1><i class="fas fa-tachometer-alt"></i> <?php echo $this->lang->line('System Dashboard'); ?> </h1>
            </div>
        <?php else : ?>
            <div class="section-header">
                <h1><i class="fas fa-tachometer-alt"></i> <?php echo $this->lang->line('Dashboard for') . " " . $user_name . " [" . $user_email . "]"; ?> </h1>
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <?php if ($other_dashboard == 1) : ?>
        <div class="section-body">
        <?php endif; ?>

        <div class="row">
            <div class="col-lg-3 col-md-3 col-sm-12">
                <div class="card card-statistic-2">
                    <div class="card-stats">
                        <div class="card-stats-title"><?php echo $this->lang->line('Select Month'); ?> -
                            <div class="dropdown d-inline">
                                <a class="font-weight-600 dropdown-toggle" data-toggle="dropdown" href="#" id="orders-month"><?php echo $month_name_array[$month_number]; ?></a>
                                <ul class="dropdown-menu dropdown-menu-sm">
                                    <li class="dropdown-title"><?php echo $this->lang->line('Select Month'); ?></li>
                                    <li><a href="#" class="dropdown-item month_change <?php if ($month_number == '01') echo 'active'; ?>" month_no="01"><?php echo $this->lang->line('January'); ?></a></li>
                                    <li><a href="#" class="dropdown-item month_change <?php if ($month_number == '02') echo 'active'; ?>" month_no="02"><?php echo $this->lang->line('February'); ?></a></li>
                                    <li><a href="#" class="dropdown-item month_change <?php if ($month_number == '03') echo 'active'; ?>" month_no="03"><?php echo $this->lang->line('March'); ?></a></li>
                                    <li><a href="#" class="dropdown-item month_change <?php if ($month_number == '04') echo 'active'; ?>" month_no="04"><?php echo $this->lang->line('April'); ?></a></li>
                                    <li><a href="#" class="dropdown-item month_change <?php if ($month_number == '05') echo 'active'; ?>" month_no="05"><?php echo $this->lang->line('May'); ?></a></li>
                                    <li><a href="#" class="dropdown-item month_change <?php if ($month_number == '06') echo 'active'; ?>" month_no="06"><?php echo $this->lang->line('June'); ?></a></li>
                                    <li><a href="#" class="dropdown-item month_change <?php if ($month_number == '07') echo 'active'; ?>" month_no="07"><?php echo $this->lang->line('July'); ?></a></li>
                                    <li><a href="#" class="dropdown-item month_change <?php if ($month_number == '08') echo 'active'; ?>" month_no="08"><?php echo $this->lang->line('August'); ?></a></li>
                                    <li><a href="#" class="dropdown-item month_change <?php if ($month_number == '09') echo 'active'; ?>" month_no="09"><?php echo $this->lang->line('September'); ?></a></li>
                                    <li><a href="#" class="dropdown-item month_change <?php if ($month_number == '10') echo 'active'; ?>" month_no="10"><?php echo $this->lang->line('October'); ?></a></li>
                                    <li><a href="#" class="dropdown-item month_change <?php if ($month_number == '11') echo 'active'; ?>" month_no="11"><?php echo $this->lang->line('November'); ?></a></li>
                                    <li><a href="#" class="dropdown-item month_change <?php if ($month_number == '12') echo 'active'; ?>" month_no="12"><?php echo $this->lang->line('December'); ?></a></li>
                                    <li><a href="#" class="dropdown-item month_change <?php if ($month_number == 'year') echo 'active'; ?>" month_no="year"><?php echo $this->lang->line('This Year'); ?></a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="text-center waiting hidden" id="loader"><i class="fas fa-spinner fa-spin blue text-center" style="font-size: 40px;"></i></div>
                        <div class="card-stats-items month_change_middle_content">
                            <div class="card-stats-item">
                                <div class="card-stats-item-count" id="subscribed"><?php echo custom_number_format($subscribed); ?></div>
                                <div class="card-stats-item-label"><?php echo $this->lang->line('Active'); ?></div>
                            </div>
                            <div class="card-stats-item">
                                <div class="card-stats-item-count" id="unsubscribed"><?php echo custom_number_format($unsubscribed); ?></div>
                                <div class="card-stats-item-label"><?php echo $this->lang->line('De-Active'); ?></div>
                            </div>

                        </div>
                    </div>
                    <div class="card-icon shadow-primary bg-primary">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4><?php echo $this->lang->line('Total Users'); ?></h4>
                        </div>
                        <div class="card-body" id="total_subscribers">
                            <?php echo custom_number_format($total_subscribers); ?>
                        </div>
                    </div>
                </div>
            </div>
              <div class="col-lg-3 col-md-3 col-sm-12">
                <div class="card card-statistic-2">
                         <div class="card-stats">
                           <div class="card-stats-title"><?php echo $this->lang->line('Select Month'); ?> -
                               <div class="dropdown d-inline">
                                   <a class="font-weight-600 dropdown-toggle" data-toggle="dropdown" href="#" id="orders-month"><?php echo $month_name_array[$month_number]; ?></a>
                                   <ul class="dropdown-menu dropdown-menu-sm">
                                       <li class="dropdown-title"><?php echo $this->lang->line('Select Month'); ?></li>
                                       <li><a href="#" class="dropdown-item month_change <?php if ($month_number == '01') echo 'active'; ?>" month_no="01"><?php echo $this->lang->line('January'); ?></a></li>
                                       <li><a href="#" class="dropdown-item month_change <?php if ($month_number == '02') echo 'active'; ?>" month_no="02"><?php echo $this->lang->line('February'); ?></a></li>
                                       <li><a href="#" class="dropdown-item month_change <?php if ($month_number == '03') echo 'active'; ?>" month_no="03"><?php echo $this->lang->line('March'); ?></a></li>
                                       <li><a href="#" class="dropdown-item month_change <?php if ($month_number == '04') echo 'active'; ?>" month_no="04"><?php echo $this->lang->line('April'); ?></a></li>
                                       <li><a href="#" class="dropdown-item month_change <?php if ($month_number == '05') echo 'active'; ?>" month_no="05"><?php echo $this->lang->line('May'); ?></a></li>
                                       <li><a href="#" class="dropdown-item month_change <?php if ($month_number == '06') echo 'active'; ?>" month_no="06"><?php echo $this->lang->line('June'); ?></a></li>
                                       <li><a href="#" class="dropdown-item month_change <?php if ($month_number == '07') echo 'active'; ?>" month_no="07"><?php echo $this->lang->line('July'); ?></a></li>
                                       <li><a href="#" class="dropdown-item month_change <?php if ($month_number == '08') echo 'active'; ?>" month_no="08"><?php echo $this->lang->line('August'); ?></a></li>
                                       <li><a href="#" class="dropdown-item month_change <?php if ($month_number == '09') echo 'active'; ?>" month_no="09"><?php echo $this->lang->line('September'); ?></a></li>
                                       <li><a href="#" class="dropdown-item month_change <?php if ($month_number == '10') echo 'active'; ?>" month_no="10"><?php echo $this->lang->line('October'); ?></a></li>
                                       <li><a href="#" class="dropdown-item month_change <?php if ($month_number == '11') echo 'active'; ?>" month_no="11"><?php echo $this->lang->line('November'); ?></a></li>
                                       <li><a href="#" class="dropdown-item month_change <?php if ($month_number == '12') echo 'active'; ?>" month_no="12"><?php echo $this->lang->line('December'); ?></a></li>
                                       <li><a href="#" class="dropdown-item month_change <?php if ($month_number == 'year') echo 'active'; ?>" month_no="year"><?php echo $this->lang->line('This Year'); ?></a></li>
                                   </ul>
                               </div>
                           </div>
                            <div class="text-center waiting hidden" id="loader"><i class="fas fa-spinner fa-spin blue text-center" style="font-size: 40px;"></i></div>
                           <div class="card-stats-items month_change_middle_content">
                            <div class="card-stats-item">
                                <div class="card-stats-item-count" id="subscribed"><?php echo custom_number_format($total_merchant-$total_app_merchant); ?></div>
                                <div class="card-stats-item-label"><?php echo $this->lang->line('Web'); ?></div>
                            </div>
                            <div class="card-stats-item">
                                <div class="card-stats-item-count" id="unsubscribed"><?php echo custom_number_format($total_app_merchant); ?></div>
                                <div class="card-stats-item-label"><?php echo $this->lang->line('App'); ?></div>
                            </div>
                        </div>
                        </div>
                        <div class="card-icon shadow-primary bg-primary">
                          <a href="<?=base_url('dashboard/downloadMerchantDetails')?>"  ><i class="fas fa-user-secret"></i></a>
                        </div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4><?php echo $this->lang->line('Total Merchant'); ?></h4>
                            </div>
                            <div class="card-body">
                                <a href="<?= base_url('dashboard/marchant_details')?>"><?php echo custom_number_format($total_merchant); ?></a>
                            </div>
                        </div>
                    </div>
                </div>

            <div class="col-lg-3 col-md-3 col-sm-12">
                <div class="card card-statistic-2">
                    <div class="card-stats">
                        <div class="card-stats-title">
                          <?php echo $this->lang->line('Select Month'); ?> -
                              <div class="dropdown d-inline">
                                  <a class="font-weight-600 dropdown-toggle" data-toggle="dropdown" href="#" id="orders-month"><?php echo $month_name_array[$month_number]; ?></a>
                                  <ul class="dropdown-menu dropdown-menu-sm">
                                      <li class="dropdown-title"><?php echo $this->lang->line('Select Month'); ?></li>
                                      <li><a href="#" class="dropdown-item month_change <?php if ($month_number == '01') echo 'active'; ?>" month_no="01"><?php echo $this->lang->line('January'); ?></a></li>
                                      <li><a href="#" class="dropdown-item month_change <?php if ($month_number == '02') echo 'active'; ?>" month_no="02"><?php echo $this->lang->line('February'); ?></a></li>
                                      <li><a href="#" class="dropdown-item month_change <?php if ($month_number == '03') echo 'active'; ?>" month_no="03"><?php echo $this->lang->line('March'); ?></a></li>
                                      <li><a href="#" class="dropdown-item month_change <?php if ($month_number == '04') echo 'active'; ?>" month_no="04"><?php echo $this->lang->line('April'); ?></a></li>
                                      <li><a href="#" class="dropdown-item month_change <?php if ($month_number == '05') echo 'active'; ?>" month_no="05"><?php echo $this->lang->line('May'); ?></a></li>
                                      <li><a href="#" class="dropdown-item month_change <?php if ($month_number == '06') echo 'active'; ?>" month_no="06"><?php echo $this->lang->line('June'); ?></a></li>
                                      <li><a href="#" class="dropdown-item month_change <?php if ($month_number == '07') echo 'active'; ?>" month_no="07"><?php echo $this->lang->line('July'); ?></a></li>
                                      <li><a href="#" class="dropdown-item month_change <?php if ($month_number == '08') echo 'active'; ?>" month_no="08"><?php echo $this->lang->line('August'); ?></a></li>
                                      <li><a href="#" class="dropdown-item month_change <?php if ($month_number == '09') echo 'active'; ?>" month_no="09"><?php echo $this->lang->line('September'); ?></a></li>
                                      <li><a href="#" class="dropdown-item month_change <?php if ($month_number == '10') echo 'active'; ?>" month_no="10"><?php echo $this->lang->line('October'); ?></a></li>
                                      <li><a href="#" class="dropdown-item month_change <?php if ($month_number == '11') echo 'active'; ?>" month_no="11"><?php echo $this->lang->line('November'); ?></a></li>
                                      <li><a href="#" class="dropdown-item month_change <?php if ($month_number == '12') echo 'active'; ?>" month_no="12"><?php echo $this->lang->line('December'); ?></a></li>
                                      <li><a href="#" class="dropdown-item month_change <?php if ($month_number == 'year') echo 'active'; ?>" month_no="year"><?php echo $this->lang->line('This Year'); ?></a></li>
                                  </ul>
                              </div>
                            </div>
                            <div class="text-center waiting hidden" id="loader"><i class="fas fa-spinner fa-spin blue text-center" style="font-size: 40px;"></i></div>
                            <div class="card-stats-items month_change_middle_content">
                                <div class="card-stats-item">
                                    <div class="card-stats-item-count" id="total_approved"><?php echo custom_number_format($total_approved); ?></div>
                                    <div class="card-stats-item-label"><?php echo $this->lang->line('Approved'); ?></div>
                                </div>
                                <div class="card-stats-item">
                                    <div class="card-stats-item-count" id="total_rejected"><?php echo custom_number_format($total_rejected); ?></div>
                                    <div class="card-stats-item-label"><?php echo $this->lang->line('Rejected'); ?></div>
                                </div>
                            </div>
                            <div class="card-icon shadow-primary bg-primary">
                                <i class="fas fa-shopping-cart"></i>
                            </div>
                            <div class="card-wrap">
                                <div class="card-header">
                                    <h4><?php echo $this->lang->line('Total Orders'); ?></h4>
                                </div>
                                <div class="card-body">
                                    <?php echo custom_number_format($total_orders); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <div class="col-lg-3 col-md-3 col-sm-12 " >
              <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12">
                    <div class="card card-statistic-2">
                        <div class="card-stats">
                            <div class="text-center waiting hidden" id="loader"><i class="fas fa-spinner fa-spin blue text-center" style="font-size: 40px;"></i></div>

                            <div class="card-icon shadow-primary bg-primary">
                                <!-- <i class="fas fa-user-secret"></i> -->
                              <i class="fas fa-rupee-sign"></i>
                            </div>
                            <div class="card-wrap">
                                <div class="card-header">
                                    <h4><?php echo $this->lang->line('Total Income'); ?></h4>
                                </div>
                                <div class="card-body">
                                    <?php echo custom_number_format($total_incomes[0]->total_income); ?>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12 col-md-12 col-sm-12">
                    <div class="card card-statistic-2">
                        <div class="card-icon shadow-primary bg-primary">
                          <i class="fas fa-list"></i>
                        </div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4><?php echo $this->lang->line('Total Product'); ?></h4>
                            </div>
                            <div class="card-body">
                                <?php echo custom_number_format($total_product); ?>

                            </div>
                        </div>
                    </div>
                </div>
              </div>

            </div>
        </div>
        <div class="row">
          <div class="col-md-3">
            <div class="row">
              <div class="col-lg-12 col-md-12 col-sm-12">
                  <div class="card card-statistic-2">
                      <!--          <div class="card-stats">
                                  <div class="card-stats-title"><?php echo $this->lang->line('Select Month'); ?> -
                                <div class="dropdown d-inline">
                                  <a class="font-weight-600 dropdown-toggle" data-toggle="dropdown" href="#" id="orders-month"><?php echo $month_name_array[$month_number]; ?></a>
                                  <ul class="dropdown-menu dropdown-menu-sm">
                                    <li class="dropdown-title"><?php echo $this->lang->line('Select Month'); ?></li>
                                    <li><a href="#" class="dropdown-item month_change <?php if ($month_number == '01') echo 'active'; ?>" month_no="01"><?php echo $this->lang->line('January'); ?></a></li>
                                    <li><a href="#" class="dropdown-item month_change <?php if ($month_number == '02') echo 'active'; ?>" month_no="02"><?php echo $this->lang->line('February'); ?></a></li>
                                    <li><a href="#" class="dropdown-item month_change <?php if ($month_number == '03') echo 'active'; ?>" month_no="03"><?php echo $this->lang->line('March'); ?></a></li>
                                    <li><a href="#" class="dropdown-item month_change <?php if ($month_number == '04') echo 'active'; ?>" month_no="04"><?php echo $this->lang->line('April'); ?></a></li>
                                    <li><a href="#" class="dropdown-item month_change <?php if ($month_number == '05') echo 'active'; ?>" month_no="05"><?php echo $this->lang->line('May'); ?></a></li>
                                    <li><a href="#" class="dropdown-item month_change <?php if ($month_number == '06') echo 'active'; ?>" month_no="06"><?php echo $this->lang->line('June'); ?></a></li>
                                    <li><a href="#" class="dropdown-item month_change <?php if ($month_number == '07') echo 'active'; ?>" month_no="07"><?php echo $this->lang->line('July'); ?></a></li>
                                    <li><a href="#" class="dropdown-item month_change <?php if ($month_number == '08') echo 'active'; ?>" month_no="08"><?php echo $this->lang->line('August'); ?></a></li>
                                    <li><a href="#" class="dropdown-item month_change <?php if ($month_number == '09') echo 'active'; ?>" month_no="09"><?php echo $this->lang->line('September'); ?></a></li>
                                    <li><a href="#" class="dropdown-item month_change <?php if ($month_number == '10') echo 'active'; ?>" month_no="10"><?php echo $this->lang->line('October'); ?></a></li>
                                    <li><a href="#" class="dropdown-item month_change <?php if ($month_number == '11') echo 'active'; ?>" month_no="11"><?php echo $this->lang->line('November'); ?></a></li>
                                    <li><a href="#" class="dropdown-item month_change <?php if ($month_number == '12') echo 'active'; ?>" month_no="12"><?php echo $this->lang->line('December'); ?></a></li>
                                    <li><a href="#" class="dropdown-item month_change <?php if ($month_number == 'year') echo 'active'; ?>" month_no="year"><?php echo $this->lang->line('This Year'); ?></a></li>
                                  </ul>
                                </div>
                              </div>
                              <div class="text-center waiting hidden" id="loader"><i class="fas fa-spinner fa-spin blue text-center" style="font-size: 40px;"></i></div>
                            </div>-->
                      <div class="card-icon shadow-primary bg-primary">
                        <i class="fas fa-store"></i>
                      </div>
                      <div class="card-wrap">
                          <div class="card-header">
                              <h4><?php echo $this->lang->line('Total Store'); ?></h4>
                          </div>
                          <div class="card-body">
                              <a href="<?= base_url('dashboard/store_details')?>"><?php echo custom_number_format($total_store); ?></a>
                          </div>
                      </div>
                  </div>
              </div>
              <div class="col-lg-12 col-md-12 col-sm-12 " style="padding:10px;background:#fff;border-radius:4px;text-align:center" >
                    <canvas id="package_canvas" name="package_canvas" style="height:185px"></canvas>
                    <h6>Package Wise Merchant</h6>
              </div>


            </div>
          </div>
          <div class="col-md-9">
            <div class="row">
              <div class="col-md-12">
                <div class="card card-statistic-2 card-chart">
                   <div class="card-header">
                     <h4>Yearly Onboaring Merchants</h4>
                   </div>
                   <div class="card-body">
                       <canvas id="merchant" name="merchant" style="height:284px" ></canvas>
                   </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <?php if ($other_dashboard == 1) : ?>
        </div>
    <?php endif; ?>
</section>
<script type="text/javascript">
    $(document).on('click', '.no_action', function (event) {
        event.preventDefault();
    });
    var myChart1;
    $(document).ready(function () {
        var stepsize = "100";
        var merchant = document.getElementById('merchant').getContext('2d');
        var myChart1 = new Chart(merchant, {
            type: 'bar',
            data: {
                labels: ['jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'],
                datasets: [{
                        label: '<?php echo $this->lang->line('APP'); ?>',
                        data: [<?php echo implode(',',$graph_app_data)?>],
                        borderWidth: 2,
                        backgroundColor: 'rgba(252, 244, 3)',
                        borderWidth: 1,
                        borderColor: 'transparent',
                        pointBorderWidth: 2,
                        pointRadius: 3.5,
                        pointBackgroundColor: 'transparent',
                        pointHoverBackgroundColor: 'rgba(254,86,83,.8)',
                    },
                    {
                        label: '<?php echo $this->lang->line('Web'); ?>',
                        data: [<?php echo implode(',',$graph_web_data)?>],
                        borderWidth: 2,
                        backgroundColor: 'rgba(41, 41, 38)',
                        borderWidth: 1,
                        borderColor: 'transparent',
                        pointBorderWidth: 2,
                        pointRadius: 3.5,
                        pointBackgroundColor: 'transparent',
                        pointHoverBackgroundColor: 'rgba(63,82,227,.8)',
                    }]
            },
            options: {
                legend: {
                    display: true
                },
                scales: {
                    yAxes: [{
                            gridLines: {
                                // display: false,
                                drawBorder: false,
                                color: '#f2f2f2',
                            },
                            ticks: {
                                beginAtZero: true,
                                stepSize: stepsize,
                                // display: false,
                                // callback: function(value, index, values) {
                                //   return value;
                                // }
                            }
                        }],
                    xAxes: [{
                            gridLines: {
                                display: false,
                                tickMarkLength: 15,
                            }
                        }]
                },
            }
        });
    });
</script>

<script type="text/javascript">
    $(document).on('click', '.no_action', function (event) {
        event.preventDefault();
    });
    var myChart1;
    $(document).ready(function () {
        var stepsize = "100";
        var package_canvas = document.getElementById('package_canvas').getContext('2d');
        var myChart1 = new Chart(package_canvas, {
            type: 'doughnut',
            data: {
                labels: ['Trial','Starter','Basic','Advance'],
                datasets: [{
                        label: '<?php echo $this->lang->line('Trial2'); ?>',
                        data: ['<?=$package_data[0]?>','<?=$package_data[2]?>','<?=$package_data[3]?>','<?=$package_data[4]?>'],
                        borderWidth: 2,
                        backgroundColor: ['rgba(245,57,57,1)','rgba(130,214,22,1)','rgba(71,0,216)','rgba(255,198,0)'],
                        borderWidth: 1,
                        borderColor: 'transparent',
                        pointBorderWidth: 2,
                        pointRadius: 3.5,
                        pointBackgroundColor: 'transparent',
                        pointHoverBackgroundColor: 'rgba(254,86,83,.8)',
                    }
                  ]
            },
            options: {
                legend: {
                    display: false
                }
            }
        });
    });
</script>

<script>
    $(document).ready(function () {

        $(document).on('click', '.month_change', function (e) {
            debugger;
            e.preventDefault();
            $(".month_change").removeClass('active');
            $(this).addClass('active');
            var month_no = $(this).attr('month_no');
            var month_name = $(this).html();
            $("#orders-month").html(month_name);

            $(".month_change_middle_content").hide();
            $("#loader").removeClass('hidden');
            var system_dashboard = "<?php echo $has_system_dashboard; ?>";
            if (system_dashboard == 'yes')
                var url = "<?php echo base_url('dashboard/get_first_div_content/') ?>" + system_dashboard;
            else
                var url = "<?php echo base_url('dashboard/get_first_div_content') ?>";

            $.ajax({
                type: 'POST',
                url: url,
                data: {month_no: month_no},
                dataType: 'JSON',
                success: function (response)
                {
                    $("#loader").addClass('hidden');
                    $("#subscribed").html(response.subscribed);
                    $("#unsubscribed").html(response.unsubscribed);
                    $("#total_subscribers").html(response.total_subscribers);
                    $("#message_sent").html(response.total_message_sent);
                    $(".month_change_middle_content").show();
                }
            });
        });

        $(document).on('click', '.period_change', function (e) {
            e.preventDefault();
            $(".period_change").removeClass('active');
            $(this).addClass('active');
            var period = $(this).attr('period');
            var selected_period = $(this).html();
            $("#selected_period").html(selected_period);

            $("#period_change_content").hide();
            $("#period_loader").removeClass('hidden');
            var system_dashboard = "<?php echo $has_system_dashboard; ?>";
            if (system_dashboard == 'yes')
                var url = "<?php echo base_url('dashboard/get_subscriber_data_div/') ?>" + system_dashboard;
            else
                var url = "<?php echo base_url('dashboard/get_subscriber_data_div') ?>";

            $.ajax({
                type: 'POST',
                url: url,
                data: {period: period},
                dataType: 'JSON',
                success: function (response)
                {
                    $("#period_loader").addClass('hidden');

                    $("#total_email_gain").html(response.email.total_email_gain);
                    $("#email_male_percentage").css('width', response.email.male_percentage);
                    $("#email_male_number").html(response.email.male);
                    $("#email_female_percentage").css('width', response.email.female_percentage);
                    $("#email_female_number").html(response.email.female);

                    $("#total_phone_gain").html(response.phone.total_phone_gain);
                    $("#phone_male_percentage").css('width', response.phone.male_percentage);
                    $("#phone_male_number").html(response.phone.male);
                    $("#phone_female_percentage").css('width', response.phone.female_percentage);
                    $("#phone_female_number").html(response.phone.female);

                    $("#total_birthdate_gain").html(response.birthdate.total_birthdate_gain);
                    $("#birthdate_male_percentage").css('width', response.birthdate.male_percentage);
                    $("#birthdate_male_number").html(response.birthdate.male);
                    $("#birthdate_female_percentage").css('width', response.birthdate.female_percentage);
                    $("#birthdate_female_number").html(response.birthdate.female);

                    $("#period_change_content").show();
                }
            });

        });


    });
</script>
