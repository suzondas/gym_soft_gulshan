<div class="clear" style="clear: both"></div>
<section class="content">
    <div class="col-md-12 box box-default">

        <section class="content-header">

            <br>
            <div class="row breadcrumb">
                <div class="col-md-3">
                    <a href="<?php echo $this->Gym->createurl("MembershipPayment", "addIncome"); ?>"
                       class="btn btn-flat btn-custom"><i class="fa fa-plus"></i> <?php echo __("Add Income"); ?></a>
                </div>
        </section>
        <h1 class="header-title h1"><i class=" fa fa-file-text-o"></i> Income Report</h1>

        <div class="container">
            <form class="form-group" action="income-report" method="post">
                <label>Select Type:</label>
                <select class="form-control" style="width:300px" name="SearchingCatergory" id="SearchingCatergory">
                    <option>Select</option>
                    <option value="today">Today</option>
                    <option value="specific">Specific</option>
                    <option value="range" selected>Range</option>
                </select>
                <br>
                <div class="date-area-specific row" hidden>
                    <div class="col-md-4">
                        Select Start Date:<br>
                        <input class="form-control datepicker" name="startDateSpecific"/>
                    </div>
                </div>

                <div class="date-area-range row">
                    <div class="col-md-4">
                        Select Start Date:<br>
                        <input class="form-control datepicker" name="startDateRange"/>
                    </div>
                    <div class="col-md-4">
                        Select End Date:<br>
                        <input class="form-control datepicker" name="endDateRange"/>
                    </div>
                </div>
                <br>
                <input type="submit" value="Submit"/>
            </form>
        </div>

        <hr>
        <?php if (isset($data)) { ?>

            <h3>Report (<?= sizeof($data) ?>):</h3>
            <table class="table table-striped table-bordered table-condensed">
                <thead>
                <tr>
                    <th>Sl</th>
                    <th>Customer Name</th>
                    <th>Invoice Label</th>
                    <th>Amount</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $totalIncome = 0;
                for ($i = 0; $i < sizeof($data); $i++) {
                    echo "<tr>";
                    echo "<td>";
                    echo $i + 1;
                    echo "</td>";
                    echo "<td>" . $data[$i]["gym_member"]["first_name"] . " " . $data[$i]["gym_member"]["first_name"] . "</td>";
                    echo "<td>" . $data[$i]["invoice_label"] . "</td>";
                    echo "<td>" . $data[$i]["total_amount"] . "</td>";
                    $totalIncome += $data[$i]["total_amount"];
                    echo "</tr>";
                } ?>
                </tbody>
                <tfoot>
                <tr>
                    <td>Total</td>
                    <td></td>
                    <td></td>
                    <td><?= $totalIncome ?></td>
                </tr>
                </tfoot>
            </table>
        <?php } ?>
    </div>

</section>

<script>
    $(document).ready(function () {
        $('.datepicker').datepicker({
            format: 'dd/mm/yyyy'
        });
    });
</script>