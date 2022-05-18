<section class="section">
    <div class="section-header">
        <h1><i class="fas fa-laptop-code"></i> <?php echo $this->lang->line('All Store'); ?> </h1>
    </div>
    <div class="section-body">
        <table id="example" class="table table-striped table-bordered" style="width:100%">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Store Name</th>
                    <th>Total Product</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $count = 1;
                foreach ($store as $row) {
                    $store_id = $row['id'];
                    $product = $this->db->query("SELECT COUNT(*) AS total_product FROM `ecommerce_product` WHERE store_id=$store_id")->result();
                    //print_r($product);
                    ?>
                    <tr>
                        <td><?= $count ?></td>
                        <td><?= $row['store_name'] ?></td>
                        <td><?= $product[0]->total_product ?></td>
                        <td><a href="<?= base_url('dashboard/download/'.$row['id']) ?>" class="btn btn-success">Download</a></td>
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





