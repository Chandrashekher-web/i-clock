<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <h4 class="page-title"><strong><?php echo empty($form_caption) ? "" : $form_caption; ?></strong></h4>
        </div>
    </div>
</div>     
<!-- end page title --> 

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

                    $attributes = array('id' => 'send-command-form', 'class' => 'form-horizontal');
                    echo form_open_multipart($form_action, $attributes);
                ?>   
                <div class="form-group row">                        
                    <label class="col-md-3 col-form-label" for="command">Command <span style="color: red">(for help right click)</span></label>
                    <div class="col-md-9">
                        <?php
                            $data = array(
                                'name' => 'command',
                                'id' => 'command',
                                //'value' => set_value('card', empty($card) ? NULL : $card),
                                'class' => 'form-control context-menu',
                                'placeholder' => 'Enter Command',
                            );
                            echo form_input($data);
                        ?>
                    </div>
                </div>
                <div class="form-group row" id="site_dropdown_div">                        
                    <label class="col-md-3 col-form-label" for="sites">Site ID</label>
                    <div class="col-md-9">
                        <?php
                            echo form_dropdown('sites', empty($arr_sites) ? Null : ($arr_sites), empty($sites) ? NULL : $sites, 'class="form-control input-block-level" id="sites"');
                        ?>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-md-3 col-form-label" for="command">Check Reader </label>
                    <div class="col-md-9">
                        <div class="card-bodyx">
                            <div class="tbl-content">
                                <table class="table table-hover table-centered mb-0" id="myTable">
                                    <thead>
                                        <tr>
                                            <th width="25px;" class="text-right"><input type="checkbox" id="checkAll"/></th>
                                            <th width="60px;">SNo.</th>
                                            <th width="300px;">Reader Name</th>                                                                                 
                                        </tr>
                                    </thead>    
                                    <tbody id="allreader">
                                        <?php
                                            $sno = 1;
                                            if (is_array($reader_list))
                                            {
                                                foreach ($reader_list as $key => $reader)
                                                {
                                                    ?>
                                                    <tr>
                                                        <td width="25px;" class="text-right">
                                                            <input type="checkbox" name="reader[]" id="reader_<?php echo $reader["reader_id"]; ?>"  value="<?php echo $reader["reader_id"]; ?>"  />
                                                        </td>
                                                        <td width="300px;"><label for="reader_<?php echo $reader["reader_id"]; ?>"><?php echo $reader["sn"]; ?></label></td>
                                                        <td width="100px;"><label for="reader_<?php echo $reader["reader_id"]; ?>"><?php echo $reader["name"]; ?></label>   </td>
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
                <div class="form-group row">
                    <div class="col-md-9 offset-sm-3">
                        <button type="submit" name="save"  id="button" class="btn btn-primary mr-2"> <i class="fa fa-save" aria-hidden="true"></i> Save</button>
                    </div>
                </div>
                <?php echo form_close(); ?>
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
                url: "<?php echo base_url(); ?>admin/reader/get_reader_data_ajax",
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
