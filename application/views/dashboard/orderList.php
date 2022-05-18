<section class="section">
    <div class="section-header">
        <h1><i class="fas fa-laptop-code"></i> <?php echo $this->lang->line('All Store'); ?> </h1>
    </div>
    <div class="section-body">
        <table id="example" class="table table-striped table-bordered" style="width:100%">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Date</th>
                    <th>Name</th>
                    <th>Address</th>
                    <th>Amount</th>
                    <th>Mode</th>
                    <th>Status</th>
                    <th>Type</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $count = 1;
                foreach ($cart as $row) {
                    $store_id = $row['id'];
                    // $product = $this->db->query("SELECT COUNT(*) AS total_product FROM `ecommerce_product` WHERE store_id=$store_id")->result();
                    // $order=$this->db->query("select COUNT(*) AS total_order,sum(payment_amount) as total_amount from ecommerce_cart where store_id=$store_id")->result();
                    // $users=$this->db->query("SELECT count(*) as total_user FROM `messenger_bot_subscriber` where store_id=$store_id")->result();
                    ?>
                    <tr>
                        <td><?= $count ?></td>
                        <td> <?= date('M d, Y', strtotime($row['ordered_at'])) ?></td>
                        <td>
                          <div><?= $row['buyer_first_name'].' '.$row['buyer_last_name'] ?></div>
                            <div class="text-small">
                              <?= $row['mobile'].' '.$row['email'] ?>
                            </div>
                        </td>
                        <td><div><?= $row['buyer_address'] ?></div>
                          <div class="text-small">
                            <?= $row['buyer_city'].' '.$row['buyer_state'] ?>
                          </div></td>
                        <td><?= round($row['payment_amount'],2) ?></td>
                        <td><?= $row['payment_method'] ?></td>
                        <td><?= $row['status'] ?></td>
                        <td><a href="<?= base_url('dashboard/download/'.$row['id']) ?>" class="btn btn-success"><i class="fa fa-eyes"></i></a></td>
                    </tr>
                    <?php
                    $count++;
                }
                ?>
            </tbody>
        </table>
    </div>
</section>
<script>
    $(document).ready(function () {
        $('#example').DataTable();
    });
</script>
