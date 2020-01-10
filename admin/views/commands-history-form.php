<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <h4 class="page-title"><strong><?php echo empty($form_caption) ? "" : $form_caption; ?></strong></h4>
        </div>
    </div>
</div>     
<!-- end page title --> 

<!-- Filter Code -->
<div class="card-header">
    <?php
        $array = array('id' => 'search_commands', 'name' => 'search_commands');
        echo form_open('', $array);
        $is_super_admin = is_super_admin();
        $session_site_id = get_session_site_id();
?>

    <div class="form-group row"  <?php echo   $is_super_admin ? NULL : "hidden"; ?>>                        
        <label class="col-md-3 col-form-label" for="name">Site Name</label>
        <div class="col-md-9">
            <?php
                echo form_dropdown('site_id', empty($arr_sites) ? NULL : ($arr_sites), empty($session_site_id) ? NULL : $session_site_id, 'class="form-control  input-block-level" autofocus="autofocus" id="site_id"');
            ?>
        </div>   
    </div>

    <div class="form-group row" id="reader_dropdown">                        
        <label class="col-md-3 col-form-label" for="name">Reader</label>
        <div class="col-md-9">
            <?php
                echo form_dropdown('reader_id', empty($arr_reader) ? NULL : ($arr_reader), empty($reader_id) ? NULL : $reader_id, 'class="form-control  input-block-level" autofocus="autofocus" id="reader_id"');
            ?>
        </div>   
    </div>

    <div class="pull-right">
        <span name="add_more" class="btn btn-primary add_field_button"><i class="fa fa-plus"></i></span>
    </div>
    <div class="media">
        <div class="col-sm-3">
            <?php
                echo form_dropdown('commands_filter[]', empty($commands_filter_arr) ? NULL : $commands_filter_arr, '', 'id="commands_filter", class="form-control input-block-level"');
            ?>
        </div>

        <div class="col-sm-3" style="display:none" id="command_date_from_list">
            <?php
                $data = array(
                    'name' => 'date_from',
                    'id' => 'date_from',
                    'value' => set_value('date_from', empty($date_from) ? NULL : $date_from),
                    'class' => 'form-control datepicker',
                    'placeholder' => 'Command Date',
                );
                echo form_input($data);
            ?>
        </div>

        <div class="col-sm-3" style="display:none" id="command_date_to_list">
            <?php
                $data = array(
                    'name' => 'date_to',
                    'id' => 'date_to',
                    'value' => set_value('date_to', empty($date_to) ? NULL : $date_to),
                    'class' => 'form-control datepicker',
                    'placeholder' => 'Command Date',
                );
                echo form_input($data);
            ?>
        </div>
        
        <div class="col-sm-3" id="date_filter_operator_div" style="display:none">
            <?php
                echo form_dropdown('date_filter_operator[]', empty($date_condition_arr) ? NULL : $date_condition_arr, '', 'id="date_filter_operator", class="form-control input-block-level"');
            ?> 
        </div>
        
        <div class="col-sm-3" style="display:none" id="command_date_list">
            <?php
                $data = array(
                    'name' => 'date[]',
                    'id' => 'date',
                    'value' => set_value('date', empty($date) ? NULL : $date),
                    'class' => 'form-control datepicker',
                    'placeholder' => 'Command Date',
                );
                echo form_input($data);
            ?>
        </div>

        <div class="col-sm-3" id="filter_operator_div">
            <?php
                echo form_dropdown('filter_operator[]', empty($condition_arr) ? NULL : $condition_arr, '', 'id="filter_operator", class="form-control input-block-level"');
            ?> 
        </div>

        <div class="col-sm-3" id="filter_value">
            <input type="text" name="filter_value[]" id="filter_value_0" value="" class="form-control"
                   placeholder="Enter search string"/>
        </div>
    </div>

    <div class="clearfix">&nbsp;</div>

    <div class="filter_panel"></div>
</div>         
<div class="clearfix">&nbsp;</div>           

<div class="media card-header">
    <div class="col-lg-1">
        <button  id="submitsearch" class="btn btn-primary">Search</button>   

    </div>
    <div class="col-lg-1">
        <button  id="resetsearch" class="btn btn-primary"> Reset</button>   

    </div>
</div>
<div class="mws-button-row" style="padding:0px;">&nbsp;</div>

<?php echo form_close(); ?>


<!-- End Filter Code -->

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
                    $attributes = array('id' => 'command-form', 'class' => 'form-horizontal');
                    echo form_open_multipart($form_action, $attributes);
                    $session_site_id = get_session_site_id();
                ?>   
                <input type="hidden" name="delete_mode" id="delete_mode" value="">

                <div class="form-group row">
                    <div class="col-md-12" id="outstanding_commands_div">

                        <?php echo $table; ?>
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

        $(function () {
            $("select#site_id").change();
        });

        $("#site_id").change(function () {
            var site_id = $(this).val();
            if (site_id != "")
            {
                $.ajax({
                    type: "POST",
                    dataType: "html",
                    url: "<?php echo base_url(); ?>admin/employee/get_readers",
                    data: {site_id: site_id},
                }).done(function (data) {
                    if (data == 'no data')
                    {
                        var reader_html = "<option value=''>-- Select Reader --</option>";
                        $('#reader_id').html(reader_html);

                    } else
                    {
                        $("#reader_id").html(data);
                    }
                })
            }
        });

        $(document).on("click", ".show-modal", function (event) {
            var command_data = $(this).data("options");
            var dialog = bootbox.dialog({
                title: 'Reader Command Info',
                message: '<div class="col-md-12 topic" id="documents-div"></div>'
            });
            dialog.init(function(){
                    dialog.find('#documents-div').html(command_data);
            });
        });
    });
</script>

<script>
    $(document).ready(function () {
        
        var param = '<?php echo $param; ?>';
         var oTable = $('#outstanding_command_table').DataTable();
           oTable.destroy();
      
        $("#submitsearch").on('click', function (){
           // var ajaxurl = oTable.ajax.url();
            var form_data = $('#search_commands').serialize();
            oTable.ajax.url(base_url + 'admin/command_history/list_command_history_data/' + param + '?' + form_data).load();
            return false;
        });

        var max_fields = 100; //maximum input boxes allowed
        var wrapper = $(".filter_panel"); //Fields wrapper
        var add_button = $(".add_field_button"); //Add button ID

//        var x = 0; //initlal text box count
        var x = $("input[name='filter_value[]']").length - 1; //initlal text box count

        $(add_button).click(function (e) { //on add input button click
            e.preventDefault();
            if (x < max_fields) {
                x++;
                $.ajax({
                    url: '<?php echo site_url() . "admin/outstanding_commands/dynamic_filter_view"; ?>',
                    method: "GET",
                    dataType: "HTML",
                    data: "id=" + x,
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function (data) {
                        $(wrapper).append(data); // add input box
                    }
                });
            }
        });
        $(wrapper).on("click", ".remove_field", function (e) { //user click on remove text
            e.preventDefault();
            $(this).closest('.search_row').remove();
            x--;
        });

        $('#commands_filter').on('change', function () {
            $('#filter_operator').prop('selectedIndex', 0);
            $('#date_filter_operator').prop('selectedIndex', 0);
            $("#filter_value_0").val("");
            $('#date_from').prop('selectedIndex', 0);
            $('#date_to').prop('selectedIndex', 0);
            $('#date').prop('selectedIndex', 0);
            if (this.value == 'date_from')
            {
                $('#command_date_from_list').show();
                $('#command_date_to_list').hide();
                $('#date_filter_operator_div').hide();
                $('#command_date_list').hide();
                $('#filter_operator_div').hide();
                $('#filter_value').hide();
            } else if (this.value == 'date_to')
            {
                $('#command_date_to_list').show();
                $('#command_date_from_list').hide();
                $('#date_filter_operator_div').hide();
                $('#command_date_list').hide();
                $('#filter_operator_div').hide();
                $('#filter_value').hide();
            }
            else if (this.value == 'date')
            {
                $('#date_filter_operator_div').show();
                $('#command_date_list').show();
                $('#command_date_to_list').hide();
                $('#command_date_from_list').hide();
                $('#filter_operator_div').hide();
                $('#filter_value').hide();
            }else
            {
                $('#date_filter_operator_div').hide();
                $('#command_date_list').hide();
                $('#command_date_to_list').hide();
                $('#command_date_from_list').hide();
                $('#filter_operator_div').show();
                $('#filter_value').show();
            }
        });

        $("#resetsearch").click(function (e) { //on add input button click
            e.preventDefault();
            $(wrapper).html("");

            $('#commands_filter').prop('selectedIndex', 0);
            $('#date_filter_operator').prop('selectedIndex', 0);
            $('#filter_operator').prop('selectedIndex', 0);
            $("#filter_value_0").val("");
            $('#date_from').prop('selectedIndex', 0);
            $('#date_to').prop('selectedIndex', 0);
            $('#date').prop('selectedIndex', 0);
            $('#command_date_list').hide();
            $('#command_date_from_list').hide();
            $('#command_date_to_list').hide();
            $('#filter_operator_div').show();
            $('#filter_value').show();
            //var ajaxurl = oTable.ajax.url();
            var form_data = $('#search_commands').serialize();
            oTable.ajax.url(base_url + 'admin/command_history/list_command_history_data/'+ param +'?' + form_data).load();
            return false;

        });

        $('.datepicker').datepicker({
            todayHighlight: true,
            autoclose: true,
            format: 'dd/mm/yyyy',
//            startDate:'+0d',
            clearBtn: true,
        });

    });
</script>