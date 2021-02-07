<?php
echo $this->Html->css('bootstrap-multiselect');
echo $this->Html->script('bootstrap-multiselect');
?>
<script type="text/javascript">
    $(document).ready(function () {

        $('#activity').multiselect({
            includeSelectAllOption: true,
            buttonWidth: '400px'
        });
        $("#curr_date").datepicker( "option", "dateFormat", "yy-mm-dd" );

    });
</script>


<section class="content">
    <br>
    <div class="col-md-12 box box-default">
        <div class="box-header">
            <section class="content-header">
                <h1>
                    <?php echo $membership_name; ?>
                    <small><?php echo __("Attendance Report For Corporate Members"); ?></small>
                </h1>
                <ol class="breadcrumb">
                    <a href="<?php echo $this->Gym->createurl("Membership", "membershipList"); ?>"
                       class="btn btn-flat btn-custom"><i class="fa fa-bars"></i> <?php echo __("Membership List"); ?>
                    </a>
                </ol>
            </section>
        </div>
        <hr>
        <div class="box-body">
            <div class="row">
               <?= $this->Form->create("activity",["class"=>"validateForm"]);?>
                <input type="hidden" name="class_id" value="0">
                    <div class="form-group col-md-3">
                        <label class="control-label" for="curr_date"><?php echo __("Select Date"); ?></label>
                        <input id="curr_date" class="form-control validate[required] date" type="text"
                               value=""  data-date-format='yy-mm-dd'
                               name="date">
                    </div>
                    <div class="form-group col-md-3 button-possition">
                        <label for="subject_id">&nbsp;</label>
                        <input type="submit" value="<?php echo __("View Attendance"); ?>" name="submit"
                               class="btn btn-flat btn-success">
                    </div>
                <?= $this->Form->end();?>
            </div>
            <hr>
            <div class="row">
                <table class="table table-striped">
                    <thead>
                    <th>Total Members</th>
                    <th>Total Present</th>
                    <th>Total Absent</th>
                    </thead>
                    <tbody>
                    <tr>
                        <td><? echo isset($totalMembers)?$totalMembers:''; ?></td>
                        <td><? echo isset($presentMembers)?$presentMembers:''; ?></td>
                        <td><? echo isset($absentMembers)?$absentMembers:''; ?></td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>