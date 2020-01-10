<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <h4 class="page-title"><strong><?php echo empty($form_caption) ? "" : $form_caption; ?></strong></h4>
        </div>
    </div>
</div>     
<!-- end page title --> 
<?php $check_admin_type = check_admin_type(); ?>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <?php
                    $validation_errors = validation_errors();
                    if (!empty($validation_errors))
                    {
                        ?>
                        <div class="col-xs-12 alert alert-danger">
                            <button type="button" class="close" data-dismiss="alert"
                                    aria-hidden="true">
                                &times;
                            </button>
                            <?php echo $validation_errors; ?>
                        </div>
                        <?php
                    }

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
                <?php
                    if ($this->session->userdata("user_type") == "Super Admin")
                    {
                        ?>
                        <div class="form-group row" id="site_dropdown_div">                       
                            <label class="col-md-3 col-form-label" for="sites">Site ID</label>
                            <div class="col-md-9">

                                <?php echo form_dropdown('sites', empty($arr_sites) ? Null : ($arr_sites), empty($sites) ? NULL : $sites, 'class="form-control input-block-level" id="sites"'); ?></div>
                        <?php } ?>
                </div>
                <div class="form-group row">
                    <label class="col-md-3 col-form-label" for="command">Check Reader </label>
                    <div class="col-md-9">
                        <div class="card-bodyx">
                            <div class="tbl-content">
                                <table class="table table-hover table-centered mb-0" id="myTable">
                                    <thead>
                                        <tr>
                                            <th width="60px;">SNo.</th>
                                            <th width="300px;">Reader Name</th>  
                                             <?php 
        if ($check_admin_type == 'Super Admin' || $check_admin_type == 'Site Admin')
        { ?>
                                            <th></th>
        <?php }?>
                                        </tr>
                                    </thead>    
                                    <tbody id="allreader">
                                        <?php
                                            $sno = 1;

                                            if (is_array($reader_list))
                                            {
                                                foreach ($reader_list as $key => $reader)
                                                {
                                                    $color = "";
                                                    if (!empty($this->session->flashdata('command_sent_reader')))
                                                    {
                                                        $data = $this->session->flashdata('command_sent_reader');
                                                        if (in_array($reader['reader_id'], $data))
                                                        {
                                                            $color = "red";
                                                        }
                                                    }
                                                    ?>     
                                                    <tr>

                                                        <td width="300px;"><label for="reader_<?php echo $reader["reader_id"]; ?>"><?php echo $reader["sn"]; ?></label></td>
                                                        <td width="100px;"><label for="reader_<?php echo $reader["reader_id"]; ?>"><?php echo $reader["name"]; ?></label>   </td>
                                                         <?php 
        if ($check_admin_type == 'Super Admin' || $check_admin_type == 'Site Admin')
        { ?>
                                                        <td width="100px;">
                                                            <?php
                                                            $attributes = array('id' => 'send-command-form', 'class' => 'form-horizontal');
                                                            echo form_open_multipart($form_action, $attributes);
                                                            ?>   
                                                            <input type="hidden" name="reader[]" id="reader_<?php echo $reader["reader_id"]; ?>"  value="<?php echo $reader["reader_id"]; ?>"  />
                                                            <center><button style="background-color:<?php echo $color; ?>" type="submit" class="btn btn-success">Open</button><center>
                                                        <?php echo form_close(); ?>
                                                        </td>
        <?php }?>
                                                        </tr>
                                                        <?php
                                                        $sno++;
                                                    }
                                                }
                                            ?>
                                            </tbody>
                                            </table>
                                            </div>
                                            </div>
                                            </div>
                                            </div>
                                            </div> <!-- end card-body -->
                                            </div> <!-- end card-->
                                            </div><!-- end col -->
                                            </div>
                                            <!-- end row -->
                                            <script>
                                                $(document).ready(function () {
                                                    $("#send-command-form").validate({
                                                        rules: {
                                                            command: {"required": true},
                                                        },
                                                        messages: {
                                                            command: {"required": "Enter Command for Reader"},
                                                        }
                                                    });
                                                    if (<?php echo $reader_command; ?>) {
                                                        $.contextMenu({
                                                            selector: '.context-menu',

                                                            callback: function (key, options) {
                                                                var m = "clicked: " + key;
                                                                window.console && console.log(m);

                                                                var ele = $(this);
                                                                var str = "";

                                                                str = key;
                                                                ele.caret(str);
                                                            },
                                                            items: <?php echo $reader_command; ?>
                                                        });
                                                    }
                                                    $('.context-menu').on('click', function (e) {
                                                        console.log('clicked', this);
                                                    })
                                                    $("#sites").change(function () {
                                                        var site_id = $(this).val();
                                                        $.ajax({
                                                            type: "POST",
                                                            dataType: "html",
                                                            url: "<?php echo base_url(); ?>admin/reader/get_manual_reader_data_ajax",
                                                            data: {site_id: site_id},
                                                        }).done(function (data) {

                                                            if (data != '')
                                                            {
                                                                $('#allreader').html(data);

                                                            } else
                                                            {
                                                                $("#allreader").html('');
                                                            }
                                                        }).always(function () {
                                                            $.unblockUI();
                                                        });
                                                    });

                                                    $("#checkAll").change(function () {
                                                        blockUI();
                                                        $("input:checkbox").prop('checked', $(this).prop("checked"));
                                                        $.unblockUI();
                                                    });

                                                });
                                            </script>
