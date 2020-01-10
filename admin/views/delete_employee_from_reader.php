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
                    $attributes = array('id' => 'send-command-form', 'class' => 'form-horizontal');
                    echo form_open_multipart($form_action, $attributes);
                ?>   
                <div class="form-group row">
                    <input type="hidden" name="mode" id="mode" value="submit">
                    <div class="col-md-12">
                        <div class="card-bodyx">
                            <div class="tbl-content">
                                <table class="table table-hover table-centered mb-0" id="myTable">
                                    <thead>
                                        <tr>
                                            <th  class="text-left">Employee Name</th>
                                            <th>Employee Pin</th>
                                            <th>Reader Name</th>                                                                                 
                                        </tr>
                                    </thead>    
                                    <tbody id="allreader">
                                        <?php
                                            if (!empty($employee_reader))
                                            {
                                                foreach ($employee_reader as $emp_reader)
                                                {
                                                    ?>
                                                    <tr>
                                                <input type="hidden" name="reader_emp_arr[<?php echo $emp_reader['reader_id']; ?>][]" value="<?php echo $emp_reader["employee_id"]; ?>"/>
                                                <td class="text-left"> <?php echo $emp_reader["emp_name"]; ?>   </td>
                                                <td> <?php echo $emp_reader["emp_pin"]; ?></td>
                                                <td><?php echo $emp_reader["reader_name"]; ?>
                                                </td>
                                                </tr>
                                                <?php
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

                        <button type="submit" onclick="return confirm('Are you sure you want to Delete Employee From Reader ?')" name="submit"  id="button" class="btn btn-primary mr-2"> <i class="fa fa-save" aria-hidden="true"></i> Submit</button>
                    </div>
                </div>
                <?php echo form_close(); ?>
            </div> <!-- end card-body -->
        </div> <!-- end card-->
    </div><!-- end col -->
</div>
<!-- end row -->

