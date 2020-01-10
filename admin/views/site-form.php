<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <a href="<?php echo base_url('admin/site/list_site') ?>" class="btn btn-sm btn-primary">
                    <i class="fa fa-list"></i>&nbsp;Back
                </a>
            </div>
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
                ?>

                <?php
                    $attributes = array('id' => 'site-form', 'class' => 'form-horizontal');
                    echo form_open_multipart($form_action, $attributes);
                ?>   
                <input type="hidden" name="site_id" id="site_id" value="<?php echo empty($site_id) ? NULL : $site_id; ?>">

                <div class="form-group row">                        
                    <label class="col-md-3 col-form-label" for="site_code">Site Code<font color="red">*</font></label>
                    <div class="col-md-9">
                        <?php
                            $data = array(
                                'name' => 'site_code',
                                'id' => 'site_code',
                                'value' => set_value('site_code', empty($site_code) ? NULL : $site_code),
                                'class' => 'form-control',
                                'placeholder' => 'Enter Site Code',
                                'autofocus' => 'autofocus',
                            );
                            echo form_input($data);
                        ?>
                    </div>
                </div>

                <div class="form-group row">                        
                    <label class="col-md-3 col-form-label" for="name">Name<font color="red">*</font></label>
                    <div class="col-md-9">
                        <?php
                            $data = array(
                                'name' => 'name',
                                'id' => 'name',
                                'value' => set_value('name', empty($name) ? NULL : $name),
                                'class' => 'form-control',
                                'placeholder' => 'Enter Name',
                            );
                            echo form_input($data);
                        ?>
                    </div>
                </div>

                <div class="form-group row">                        
                    <label class="col-md-3 col-form-label" for="contact">Contact</label>
                    <div class="col-md-9">
                        <?php
                            $data = array(
                                'name' => 'contact',
                                'id' => 'contact',
                                'value' => set_value('contact', empty($contact) ? NULL : $contact),
                                'class' => 'form-control',
                                'placeholder' => 'Enter Contact',
                            );
                            echo form_input($data);
                        ?>
                    </div>
                </div>

                <div class="form-group row">                        
                    <label class="col-md-3 col-form-label" for="data_format">Data Format<font color="red">*</font></label>
                    <div class="col-md-9">
                        <?php
                            $data = array(
                                'name' => 'data_format',
                                'id' => 'data_format',
                                'value' => empty($data_format) ? NULL : htmlspecialchars_decode($data_format),
                                'class' => 'form-control context-menu',
                            );
                            echo form_input($data);
                        ?>
                    </div>
                </div>

                <div class="form-group row">                        
                    <label class="col-md-3 col-form-label" for="data_format_other">Data Format Other</label>
                    <div class="col-md-9">
                        <?php
                            $data = array(
                                'name' => 'data_format_other',
                                'id' => 'data_format_other',
                                'value' => empty($data_format_other) ? NULL : htmlspecialchars_decode($data_format_other),
                                'class' => 'form-control context-menu',
                            );
                            echo form_input($data);
                        ?>
                    </div>
                </div>

                <div class="form-group row">                        
                    <label class="col-md-3 col-form-label" for="license_validity">License Validity<font color="red">*</font></label>
                    <div class="col-md-9">
                        <?php
                            $data = array(
                                'name' => 'license_validity',
                                'id' => 'license_validity',
                                'value' => set_value('license_validity', empty($license_validity) ? NULL : $license_validity),
                                'class' => 'form-control',
                                'placeholder' => 'Select License Validity',
                            );
                            echo form_input($data);
                        ?>
                    </div>
                </div>

                <?php
                    if (!empty($license_key))
                    {
                        ?>
                        <div class="form-group row">                        
                            <label class="col-md-3 col-form-label" for="license_key">License Key</label>
                            <div class="col-md-9"><?php echo $license_key; ?>
                                <?php
//                            $data = array(
//                                'name' => 'license_key',
//                                'id' => 'license_key',
//                                'value' => set_value('license_key', empty($license_key) ? NULL : $license_key),
//                                'class' => 'form-control',
//                            );
//                            echo form_input($data);
                                ?>
                            </div>
                        </div>
                    <?php } ?>

                <div class="form-group row">                        
                    <label class="col-md-3 col-form-label" for="server_ip">Server IP<font color="red">*</font></label>
                    <div class="col-md-9">
                        <?php
                            $data = array(
                                'name' => 'server_ip',
                                'id' => 'ipv4',
                                'value' => set_value('server_ip', empty($server_ip) ? NULL : $server_ip),
                                'class' => 'form-control ipaddress',
                                'placeholder' => '',
                            );
                            echo form_input($data);
                        ?>
                    </div>
                </div>

                <div class="form-group row">                        
                    <label class="col-md-3 col-form-label" for="notes">Description</label>
                    <div class="col-md-9">
                        <?php
                            $data = array(
                                'name' => 'notes',
                                'id' => 'notes',
                                'value' => set_value('notes', empty($notes) ? NULL : $notes),
                                'class' => 'form-control',
                                'placeholder' => 'Enter Notes',
                                'rows' => 3,
                                'cols' => 4
                            );
                            echo form_textarea($data);
                        ?>
                    </div>
                </div>
                <div class="form-group row">                        
                    <label class="col-md-3 col-form-label" for="access_user">Access User</label>
                    <div class="col-md-9">
                        <?php
                            if (!empty($access_user))
                            {
                                $data = array(
                                    'name' => 'access_user',
                                    'id' => 'access_user',
                                    'value' => 'Yes',
                                    'style' => 'margin:10px',
                                    'checked' => TRUE
                                );
                            }
                            else
                            {
                                $data = array(
                                    'name' => 'access_user',
                                    'id' => 'access_user',
                                    'value' => 'Yes',
                                    'style' => 'margin:10px'
                                );
                            }

                            echo form_checkbox($data);
                        ?>

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

        //input mask bundle ip address
        var ipv4_address = $('#ipv4');
        ipv4_address.inputmask({
            alias: "ip",
            greedy: false //The initial mask shown will be "" instead of "-____".
        });

        $('#license_validity').datepicker({
            todayHighlight: true,
            autoclose: true,
            format: 'dd/mm/yyyy',
//            format: 'mm/dd/yyyy',
            clearBtn: true,
        });

        $("#site-form").validate({
            rules: {
                site_code: {"required": true},
                name: {"required": true},
                data_format: {"required": true},
                license_validity: {"required": true},
                ipv4: {"required": true},
                server_ip: {"required": true}

            },
            messages: {
                site_code: {"required": "Enter Site Code"},
                name: {"required": "Enter Name"},
                data_format: {"required": "Enter Data Format"},
                license_validity: {"required": "Select License Validity"},
                ipv4: {"required": "Enter Server IP"},
                server_ip: {"required": "Enter Server IP"}

            }
        });

        $.contextMenu({
            selector: '.context-menu',
            callback: function (key, options) {
                var m = "clicked: " + key;
                window.console && console.log(m);

                var ele = $(this);
                var str = "";
                switch (key) {

                    case "card":
                        str += "<4";
                        break;
                    case "pin":
                        str += "<4";
                        break;
                    case "day":
                        str += "<2";
                        break;
                    case "month":
                        str += "<2";
                        break;
                    case "year":
                        str += "<4";
                        break;
                    case "hour":
                        str += "<2";
                        break;
                    case "minute":
                        str += "<2";
                        break;
                    case "second":
                        str += "<2";
                        break;
                    case "status":
                        str += "<1";
                        break;
                    case "sn":
                        str += "<2";
                        break;
                    case "mode":
                        str += "<1";
                        break;
                    case "work":
                        str += "<2";
                        break;
                    case "job":
                        str += "<2";
                        break;
                }

                str = str + "," + key + ">";
                ele.caret(str);
            },
            items: {
                "card": {name: "Card"},
                "pin": {name: "Pin"},
                "day": {name: "Day"},
                "month": {name: "Month"},
                "year": {name: "Year"},
                "hour": {name: "Hour"},
                "minute": {name: "Minute"},
                "second": {name: "Second"},
                "status": {name: "Status"},
                "sn": {name: "SN"},
                "mode": {name: "Mode"},
                "work": {name: "Work"},
                "job": {name: "Job"}

            }
        });

        $('.context-menu').on('click', function (e) {
            console.log('clicked', this);
        })
    });
</script>