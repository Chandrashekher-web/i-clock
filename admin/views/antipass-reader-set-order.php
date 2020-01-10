<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <h3 class="box-title "></h3>

            <div class="page-title-right" style="margin-right:  10px;">

                <?php echo anchor("admin/reader_access_groups/list_reader_access_groups", "Back", ' class="btn btn-primary pull-right"'); ?>

            </div>

            <h4 class="page-title"><?php echo $this->lang->line('setorder'); ?></h4>
        </div>
    </div>
</div>     
<!-- end page title --> 

<div class="row">
    <?php
        if (!empty($results))
        {
            foreach ($results as $key => $reader_type)
            {
                ?>
                <div class="col-4">
                    <div class="card">
                        <div class="card-body">                
                            <h4 class="page-title"><?php echo $this->lang->line($key); ?></h4>
                            <p   class="alert update_status_<?php echo $key; ?>"></p>
                            <ul  class="list-group sortable sortable_<?php echo $key; ?>" data-readertype="<?php echo $key; ?>">
                                <?php
                                if (!empty($reader_type))
                                {
                                    foreach ($reader_type as $id => $value)
                                    {
                                        ?>
                                        <li id="orderid_<?php echo $value['reader_id']; ?>"  class="ui-state-default custom-class"   draggable="true">
                                             
                                            <?php  echo '<span class="strong">'. ($id + 1) .'</span>'.$value['reader_name']; ?> 
                                        </li>
                                        <?php
                                    }
                                }
                                ?>
                            </ul>	
                        </div> <!-- end card body-->
                    </div> <!-- end card -->
                </div><!-- end col-->
                <?php
            }
        }
    ?>
</div>
<!-- end row-->
<style>
    .custom-class
    {
        list-style: none;
        margin: 5px;
        padding: 5px;
    }
    .strong {
        font-size: 16px;
        font-weight: bold;
        margin-left: 5px;
        margin-right: 25px;
    }
</style>

<script type="text/javascript">
    $(document).ready(function () {
        $(".sortable").sortable({

            update: function () {
                reader_type = $(this).data('readertype');
                update_readerorder(reader_type);

            }
        });

        $('#update').on('click', function (e) {
            update_readerorder();
        });
    });

    function update_readerorder(reader_type)
    {

        var order = $('.sortable_'+reader_type).sortable('toArray');
        $("#sortable_msg").hide();
        $.ajax({
            type: "POST",
            async: true,
            url: base_url + "<?php echo "admin/reader_access_groups/setordersave/" ?>",
            data: {"item": order, reader_type: reader_type,reader_access_groups_id : <?php echo $reader_access_groups_id;?>},
            beforeSend: function () {
                $(".update_status_" + reader_type).html("Processing...").addClass("alert-success").removeClass("hidden");
            },
            dataType: 'json'
        }).done(function (data) {
            if (data.status == "failure")
            {
                $(".update_status_" + reader_type).html("Error occured").addClass("alert-danger").removeClass("hidden alert-success");
            } else
            {
                $(".update_status_" + reader_type).html("Order updated successfully.").addClass("alert-success").removeClass("hidden alert-danger");
            }
        });

    }
</script>