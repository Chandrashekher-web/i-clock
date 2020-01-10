<div class="card-block search_row">
    <div class="pull-right">
        <span class="remove_field btn btn-danger"><i class="fa fa-minus-circle" ></i></span>
    </div>
    <div class="media">
        <div class="col-sm-3">
            <select name="filter_condition[]" id="filter_condition<?php echo $id; ?>" class="form-control">
                <option value="--Select one">---Select One---</option>
                <option value="and">And</option>
                <option value="or">Or</option>
            </select>
        </div>
        <div class="col-sm-3">
            <?php
                echo form_dropdown('commands_filter[]', empty($commands_filter_arr) ? NULL : $commands_filter_arr, '', 'id="commands_filter_' . $id . '", class="form-control input-block-level"');
            ?>
        </div>

        <div class="col-sm-3" id="status_div<?php echo $id; ?>" style="display:none">
            <?php
                echo form_dropdown('status[]', empty($status_arr) ? NULL : $status_arr, '', 'id="status' . $id . '", class="form-control input-block-level"');
            ?> 
        </div>

        <div class="col-sm-3" id="date_filter_operator_div<?php echo $id; ?>" style="display:none">
            <?php
                echo form_dropdown('date_filter_operator[]', empty($date_condition_arr) ? NULL : $date_condition_arr, '', 'id="date_filter_operator' . $id . '", class="form-control input-block-level"');
            ?> 
        </div>

        <div class="col-sm-3" style="display:none" id="command_date_list<?php echo $id; ?>">
            <?php
                $data = array(
                    'name' => 'date[]',
                    'id' => 'date' . $id . '',
                    'value' => set_value('date', empty($date) ? NULL : $date),
                    'class' => 'form-control datepicker',
                    'placeholder' => 'Command Date',
                );
                echo form_input($data);
            ?>
        </div>

        <div class="col-sm-3" id="filter_operator_div<?php echo $id; ?>">
            <?php
                echo form_dropdown('filter_operator[]', empty($condition_arr) ? NULL : $condition_arr, '', 'id="filter_operator' . $id . '", class="form-control input-block-level"');
            ?> 
        </div>

        <div class="col-sm-3" id="filter_value<?php echo $id; ?>">
            <input type="text" name="filter_value[]" id="filter_value_text<?php echo $id; ?>" value="" class="form-control"
                   placeholder="Enter search string"/>
        </div>
    </div>
    <div class="line pull-in"></div>
</div>

<script>
    $(document).ready(function () {
        var id = <?php echo $id; ?>;
        $('#commands_filter_' + id).on('change', function () {
            $('#filter_operator' + id).prop('selectedIndex', 0);
            $('#date_filter_operator' + id).prop('selectedIndex', 0);
            $("#filter_value_text" + id).val("");
            $('#date' + id).prop('selectedIndex', 0);
            $('#status' + id).prop('selectedIndex', 0);
            if (this.value == 'date')
            {
                $('#date_filter_operator_div' + id).show();
                $('#command_date_list' + id).show();
                $('#filter_operator_div' + id).hide();
                $('#filter_value' + id).hide();
                $('#status_div' + id).hide();
            } else if (this.value == 'status')
            {
                $('#date_filter_operator_div' + id).hide();
                $('#command_date_list' + id).hide();
                $('#filter_operator_div' + id).hide();
                $('#filter_value' + id).hide();
                $('#status_div' + id).show();
            } else
            {
                $('#date_filter_operator_div' + id).hide();
                $('#command_date_list' + id).hide();
                $('#filter_operator_div' + id).show();
                $('#filter_value' + id).show();
                $('#status_div' + id).hide();
            }
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