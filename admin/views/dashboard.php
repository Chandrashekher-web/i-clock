<style type="text/css">
    .loader {
        position: fixed;
        left: 0px;
        top: 0px;
        width: 100%;
        height: 100%;
        z-index: 9999;
        background: url("<?php echo base_url(); ?>assets/images/ajax-loader.gif") 50% 50% no-repeat rgb(249, 249, 249);
        opacity: .8;
    }
    table{
        width:100%;
        table-layout: fixed;
    }
    .tbl-header{
        /*background-color: rgba(255,255,255,0.3);*/
        padding-right:6px;
    }
    .tbl-content{
        height:500px;
        overflow-x:auto;
        margin-top: 0px;
    }
    th{
        /*        padding: 20px 15px;
                text-align: left;
                font-weight: 500;
                font-size: 12px;
                
                text-transform: uppercase;*/
        border:  1px solid grey!important;
        padding:5px!important;
        text-align: center;
    }
    td{
        /*        padding: 15px;
                text-align: left;
                vertical-align:middle;*/
        /*        font-weight: 300;
                font-size: 12px;*/

        border:  1px solid grey!important;
        padding:5px!important;
    }


    body{
        /*        background: -webkit-linear-gradient(left, #25c481, #25b7c4);
                background: linear-gradient(to right, #25c481, #25b7c4);*/

    }

    /* for custom scrollbar for webkit browser*/

    ::-webkit-scrollbar {
        width: 6px;
    } 
    ::-webkit-scrollbar-track {
        -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,0.3); 
    } 
    ::-webkit-scrollbar-thumb {
        -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,0.3); 
    }
    /*    .hidden {
            display:none;
         }*/

</style>

<script type="text/javascript">
    jQuery(document).ready(function () {
        jQuery(".loader").fadeOut("slow");
    });</script>

<div class="loader"></div>

<?php
    $attributes = array('id' => 'employee-reader-form', 'class' => 'form-horizontal');
    echo form_open_multipart($form_action, $attributes);

    $session_site_id = get_session_site_id();
    $session_data = get_loggedin_admin_user_data();
    $is_super_admin = is_super_admin();
    $check_admin_type = check_admin_type();
    if (!empty($session_site_id))
    {
        ?>
        <!-- start page title -->
        <div class="row">
            <div class="col-md-12">
                <div class="page-title-box" style="margin-bottom: 10px;">
                    <h4 class="page-title">Dashboard</h4>
                    <div class="page-title-right" style="width: 100%;">
                        <input type="hidden" id="showemp" value="<?php echo @$session_data['show_emp_type']; ?>" />
                        <div class="form-group row float-right" >  
                            <?php
                            if (!empty($is_super_admin) && 1 == 2)
                            {
                                ?>
                                <button type="submit" name="save2"  id="button2" class="btn btn-primary mr-2 "> <i class="fa fa-save" aria-hidden="true"></i> Update without Commands</button>
                                <?php
                            }
                            if ($check_admin_type == 'Super Admin' || $check_admin_type == 'Site Admin')
                            {
                                ?>

                                <button type="submit" name="save"  id="button" class="btn btn-primary mr-2"> <i class="fa fa-save" aria-hidden="true"></i> Update</button>
                            <?php } ?>
                        </div>
                        <div class="form-group row float-left">                        
                            <label class="col-md-3 col-form-label" for="employee_status_update">Show</label>
                            <div class="col-md-9">
                                <?php
                                echo form_dropdown('employee_show', empty($employee_show) ? Null : ($employee_show), empty($session_data['show_emp_type']) ? 'All' : $session_data['show_emp_type'], 'class="form-control my_show"');
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>    

        <div class="row" style="margin-bottom: 10px;margin-top: 10px; ">
            <div class="col-md-4">
                <?php
                if (!empty($auto_refresh && $auto_refresh == 'yes'))
                {
                    $checked = "checked";
                }
                else
                {
                    $checked = "";
                }
                ?> 
                <button type="button" name="save"  id="refresh" class="btn btn-primary mr-2"> <i class="fa fa-refresh" aria-hidden="true"></i> Refresh</button>
                <input type="checkbox" <?php echo $checked; ?> name="auto-refresh" id="auto-refresh" /> <label class="col-form-label" for="auto-refresh">Auto refresh</label>
            </div>
            <div class="col-md-4">
                <label class="col-form-label" for="search">Search&nbsp;</label><input type="text" id="search"/>
            </div>
            <div class="col-md-4 text-right">
                <label class="col-form-label" for="department_id">Department</label>
                <?php
                echo form_dropdown('department_id', empty($arr_department) ? Null : ($arr_department), empty($session_data['department_id']) ? NULL : $session_data['department_id'], 'id="department_id" class="form-control" style="width:250px;display:inline-block;"');
                ?>
            </div>
        </div>
        <div id="post_data_arr">

        </div>

        <!-- end page title --> 

        <?php
        echo form_close();
    }
?>

<?php
    if (!empty($session_site_id))
    {
        ?>
        <div class="row">
            <div class="col-12">
                <div class="cardx">
                    <div class="card-bodyx">
                        <div class="tbl-header">
                            <table class="table table-hover table-centered mb-0">
                                <thead>
                                    <tr>
                                        <th width="25px;" class="hidden" style="display: none"></th>
                                        <th width="25px;"></th>
                                        <th width="60px;">SNo.</th>
                                        <th width="300px;" id="emp_name" style="cursor: pointer">Name</th>
                                        <th width="100px;" id="emp_cardno" style="cursor: pointer">Emp Card No.</th>
                                        <th width="120px;" id="emp_rf_card" style="cursor: pointer">RF Card No.</th>
                                        <th width="30px;" >FPs</th>
                                        <?php
                                        if (is_array($arr_readers))
                                        {
                                            foreach ($arr_readers as $key => $reader)
                                            {
                                                //ToString("dddd dd, MMM yyyy HH:mm:ss")
                                                $reader = (array) $reader;
                                                $popover_content = "Dep: " . $reader["department"] . "<br />";
                                                $popover_content .= "Name: <b>" . $reader["name"] . "</b><br />";
                                                $popover_content .= "Last seen: " . date("l d, M Y H:i:s", strtotime($reader["seen"])) . "<br />";
                                                $popover_content .= "FP Source: " . $reader["fpsource"] . "<br />";

                                                $icon_color = $reader["onlinestatus"] == "Online" ? "green" : "red";
                                                ?>
                                                <th style="text-align:center;">
                                                    <?php
                                                    $href = '';
                                                    if ($check_admin_type == 'Super Admin' || $check_admin_type == 'Site Admin')
                                                    {
                                                        $href = base_url() . 'admin/reader/add_reader/' . $reader["reader_id"] . '/dashboard';
                                                    }
                                                    ?>
                                                    <a href="<?php echo $href ?>" data-toggle="popover" data-trigger="hover" title="<?php echo $reader["sn"]; ?>"  data-placement="top" data-html="true" data-content="<?php echo $popover_content; ?>">
                                                        <i style="font-size:20px; color:<?php echo $icon_color; ?>" class="fe-globe" ></i></a><br />
                                                    <input type="checkbox" name="chk_select_by_reader_<?php echo $reader["reader_id"]; ?>" id="chk_select_by_reader_<?php echo $reader["reader_id"]; ?>" value="<?php echo $reader["reader_id"]; ?>" class="chk_select_by_reader" />
                                                    <?php // echo $reader["reader_id"];    ?></th>
                                                <?php
                                            }
                                        }
                                        ?>
                                    </tr>

                            </table>
                        </div>
                        <div class="tbl-content">
                            <table class="table table-hover table-centered mb-0" id="myTable">
                                <tbody>
                                    <?php
                                    $sno = 1;
                                    if (is_array($arr_employee_reader_trans))
                                    {
                                        $is_special = '';
                                        foreach ($arr_employee_reader_trans as $key => $employee)
                                        {
                                            if ($employee['priv'] == ADMIN_USER_PRIV)
                                            {
                                                $tr_class = 'class="admin-row"';
                                            }
                                            else
                                            {
                                                $tr_class = '';
                                            }

                                            $href = '';
                                            if ($check_admin_type == 'Super Admin' || $check_admin_type == 'Site Admin')
                                            {
                                                $href = base_url() . 'admin/employee/add_employee/' . $employee["employee_id"] . '/dashboard';
                                            }
                                            ?>
                                            <tr <?php echo $tr_class; ?>>
                                                <td class="hidden" width="25px;" class="text-right" style="display: none">
                                                    <input type="checkbox" name="chk_employee_id_arr[]" id="chk_employee_id_arr_<?php echo $employee["employee_id"]; ?>" value="<?php echo $employee["employee_id"]; ?>" class="chk_employee_id_arr" />
                                                </td>
                                                <td width="25px;" class="text-right">
                                                    <input type="checkbox" name="chk_select_by_employee_<?php echo $employee["employee_id"]; ?>" id="chk_select_by_employee_<?php echo $employee["employee_id"]; ?>" value="<?php echo $employee["employee_id"]; ?>" class="chk_select_by_employee" />
                                                </td>
                                                <td width="60px;" class="text-right"><?php echo $sno; //$employee["employee_id"];                                                                                    ?>
                                                </td>
                                                <td width="300px;"><!--<a href='' class='show-modal' id="<?php echo $employee["employee_id"]; ?>"><?php echo $employee["name"]; ?></a>--><?php echo $employee["name"]; ?></td>
                                                <td width="100px;"><a href='<?php echo $href; ?>'><?php echo $employee["pin"]; ?></a></td>
                                                <td width="120px;"><?php echo $employee["card"]; ?></td>
                                                <td width="30px;"><?php echo $employee["fp"]; ?></td>

                                                <?php
                                                if (!empty($arr_readers))
                                                {
                                                    foreach ($arr_readers as $key => $reader)
                                                    {
                                                        $reader = (array) $reader;
                                                        $employee_id = $employee["employee_id"];
                                                        $reader_id = $reader["reader_id"];
                                                        $checked = array_search($reader_id, $employee["reader_trans"]) === FALSE ? "" : "checked";
                                                        ?>
                                                        <td align="center">
                                                            <input type="checkbox" data-reader="<?php echo $reader_id; ?>" data-employee="<?php echo $employee_id; ?>" id="chk_<?php echo $employee_id . "_" . $reader_id; ?>" <?php echo $checked; ?> value="<?php echo $employee_id; ?>" class="indiv_chk reader_<?php echo $reader_id; ?> employee_<?php echo $employee_id; ?>"  />
                                                        </td>
                                                        <?php
                                                    }
                                                }
                                                ?>

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
        </div>
        <!-- end row -->

        <?php
    }
?>
<script>
    $(document).ready(function () {
// '.tbl-content' consumed little space for vertical scrollbar, scrollbar width depend on browser/os/platfrom. Here calculate the scollbar width .
        $(window).on("load resize ", function () {
            var scrollWidth = $('.tbl-content').width() - $('.tbl-content table').width();
            $('.tbl-header').css({'padding-right': scrollWidth});
        }).resize();
        $("#search").on("keyup", function () {
            var value = $(this).val().toLowerCase();
            $("#myTable tr").filter(function () {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });
        $('[data-toggle="popover"]').popover();


        $(".chk_select_by_reader").change(function () {
            blockUI();
            var reader_id = $(this).val();
            if ($(this).is(':checked') == true)
            {
                $(".reader_" + reader_id + ':checkbox:unchecked').each(function () {
                    employee_id = $(this).data('employee');
                    add_toarray(reader_id, employee_id);
                });
            } else
            {
                $(".reader_" + reader_id + ':checkbox:checked').each(function () {
                    employee_id = $(this).data('employee');
                    remove_from_array(reader_id, employee_id);
                });
            }
            $(".reader_" + reader_id).prop('checked', $(this).prop("checked"));
            $.unblockUI();
        });


        $(".chk_select_by_employee").change(function () {
            blockUI();
            var employee_id = $(this).val();

            if ($(this).is(':checked') == true)
            {
                $(".employee_" + employee_id + ':checkbox:unchecked').each(function () {
                    reader_id = $(this).data('reader');
                    add_toarray(reader_id, employee_id);
                });
            } else
            {
                $(".employee_" + employee_id + ':checkbox:checked').each(function () {
                    reader_id = $(this).data('reader');
                    remove_from_array(reader_id, employee_id);
                });
            }
            $(".employee_" + employee_id).prop('checked', $(this).prop("checked"));
            $.unblockUI();
        });
        $(".indiv_chk").change(function () {
            blockUI();
            var employee_id = $(this).data('employee');
            var reader_id = $(this).data('reader');
            $("#chk_employee_id_arr_" + employee_id).prop('checked', $(this).prop("checked"));
            if ($("#chk_employee_id_arr_" + employee_id).is(':checked') == true)
            {
                add_toarray(reader_id, employee_id);
            } else {
                remove_from_array(reader_id, employee_id);
            }
            $.unblockUI();
        });
        $(document).on('click', '#refresh', function (e) {
            location.reload();
        });
        var auto_refresh_time = '<?php echo $auto_refresh_time; ?>';
        $("#auto-refresh").click(function () {
            setSession();
        });
        if ($('#auto-refresh').is(":checked") == true)
        {
            setInterval(function () {
                location.reload();
            }, auto_refresh_time);
        }

        $(document.body).on('change', '#department_id', function () {
            setSession();
        });
        $(document.body).on('change', '.my_show', function () {
            $('#showemp').val(($(this).val()));
            setSession();
        });
        function setSession()
        {
            if ($('#auto-refresh').is(":checked") == true)
            {
                var auto_refresh = 'yes';
            } else
            {
                var auto_refresh = 'no';
            }

            var department_id = $('#department_id').val();
            var show_emp = $('#showemp').val();
            $.ajax({
                type: "POST",
                dataType: "json",
                data: {auto_refresh: auto_refresh, department_id: department_id, show_emp: show_emp},
                url: "<?php echo base_url(); ?>admin/Index/set_auto_refresh_variable",
                success: function (output) {
                    if (output.msg == 'success' && auto_refresh == 'yes')
                    {
                        setInterval(function () {
                            location.reload();
                        }, auto_refresh_time);
                    } else
                    {
                        location.reload();
                    }
                }
            });
        }

        $(document).on("click", ".show-modal", function (event) {
            event.preventDefault();
            var employee_id = $(this).attr("id");
            blockUI();
            $.ajax({
                dataType: 'html',
                type: "POST",
                url: base_url + 'admin/index/get_employee_info',
            }).done(function (data) {
                var dialog = bootbox.dialog({
                    title: 'Employee Info',
                    message: '<div class="col-md-12 topic" id="documents-div"></div>'
                });
                dialog.init(function () {
                    dialog.find('#documents-div').html(data);
                });
            }).always(function () {
                $.unblockUI();
            });
        });
        function add_toarray(reader_id, employee_id)
        {
            isexist = $('#remove_' + employee_id + "_" + reader_id).val();
            if (isexist === undefined)
            {
                data = '<input type="hidden" name="add_chk[' + reader_id + '][]" style="color:green;width:40px"  value="' + employee_id + '" id="add_' + employee_id + "_" + reader_id + '" /> ';
                $('#post_data_arr').append(data);
            } else if (isexist == employee_id)
            {
                $('#remove_' + employee_id + "_" + reader_id).remove();
            }
        }
        function remove_from_array(reader_id, employee_id)
        {
            isexist = $('#add_' + employee_id + "_" + reader_id).val();
            if (isexist === undefined)
            {
                data = '<input type="hidden" name="remove_chk[' + reader_id + '][]" style="color:red;width:40px"  value="' + employee_id + '" id="remove_' + employee_id + "_" + reader_id + '" /> ';
                $('#post_data_arr').append(data);

            } else if (isexist == employee_id)
            {
                $('#add_' + employee_id + "_" + reader_id).remove();
            }
        }


    });</script>

<script>

    jQuery.fn.sortElements = (function () {

        var sort = [].sort;
        return function (comparator, getSortable) {

            getSortable = getSortable || function () {
                return this;
            };
            var placements = this.map(function () {

                var sortElement = getSortable.call(this),
                        parentNode = sortElement.parentNode,
                        // Since the element itself will change position, we have
                        // to have some way of storing it's original position in
                        // the DOM. The easiest way is to have a 'flag' node:
                        nextSibling = parentNode.insertBefore(
                                document.createTextNode(''),
                                sortElement.nextSibling
                                );
                return function () {

                    if (parentNode === this) {
                        throw new Error(
                                "You can't sort elements if any one is a descendant of another."
                                );
                    }

                    // Insert before flag:
                    parentNode.insertBefore(this, nextSibling);
                    // Remove flag:
                    parentNode.removeChild(nextSibling);
                };
            });
            return sort.call(this, comparator).each(function (i) {
                placements[i].call(getSortable.call(this));
            });
        };
        //  

    })();
    $(document).ready(function () {
        var table = $('table');
        $('#emp_name,#emp_cardno,#emp_rf_card')
                .wrapInner('<span title="sort this column"/>')
                .each(function () {

                    var th = $(this),
                            thIndex = th.index(),
                            inverse = false;
                    th.click(function () {
                        blockUI();
                        table.find('td').filter(function () {
                            //blockUI();
                            return $(this).index() === thIndex;
                        }).sortElements(function (a, b) {

                            return $.text([a]) > $.text([b]) ?
                                    inverse ? -1 : 1
                                    : inverse ? 1 : -1;
                        }, function () {
                            //$.unblockUI(); 
                            // parentNode is the element we want to move
                            return this.parentNode;
                        });
                        inverse = !inverse;
                        $.unblockUI();
                    });
                });
    });
</script>
