<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <?php
                $is_super_admin = is_super_admin();
                $check_admin_type = check_admin_type();
                if ($is_super_admin)
                {
                    ?>
                    <div class="page-title-right">
                        <a href="<?php echo base_url('admin/reader/add_reader') ?>" class="btn btn-sm btn-primary">
                            <i class="fa fa-list"></i>&nbsp;Add Reader
                        </a>
                    </div>
                    <?php
                }
                if ($check_admin_type == 'Super Admin' || $check_admin_type == 'Site Admin')
                {
                    ?>
                    <div class="page-title-right" style="margin-right:  10px;">
                        <a href="<?php echo base_url('admin/reader/setorder') ?>" class="btn btn-sm btn-primary">
                            <i class="fa fa-list"></i>&nbsp;Reorder Readers
                        </a>
                    </div>
                <?php } ?>
            <h4 class="page-title">Readers</h4>
        </div>
    </div>
</div>     
<!-- end page title --> 

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <?php
                    if (!empty($message))
                    {
                        ?>
                        <div class="alert alert-success">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            <?php echo $message; ?>
                        </div>
                        <?php
                    }
                ?>

                <?php echo $table; ?>
            </div> <!-- end card body-->
        </div> <!-- end card -->
    </div><!-- end col-->
</div>
<!-- end row-->

<script>

    $(document).ready(function () {
        $(document).on("click", ".show-modal", function (event) {
            event.preventDefault();
            var id = $(this).attr("id");

//            blockUI();
            $.ajax({
                dataType: 'json',
                type: "POST",
                url: base_url + '/admin/reader/get_ping_info/' + id,
            }).done(function (data) {
//                alert(data);
                var dialog = bootbox.dialog({
                    title: 'Reader Ping Info',
                    message: '<div class="col-md-12" id="documents-div"></div>'
                });
                dialog.init(function () {
                    dialog.find('#documents-div').html(data.replace(/(\r\n|\n\r|\r|\n)/g, "<br>"));
                });

            }).always(function () {
//                    $.unblockUI();
            });

        });

    });

</script>
