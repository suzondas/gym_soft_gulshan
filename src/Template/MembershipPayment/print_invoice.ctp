<script>window.onload = function () {
        window.print();
    };</script>
<style>
    .table-bordered {
        border: 1px solid black;
    }

    .table-bordered td {
        border: 1px solid black;

    }

    .table-bordered tr {
        border: 1px solid black;

    }
</style>
<div class="modal-body">
    <div id="invoice_print" style="width: 90%;margin:0 auto;">
        <div class="modal-header">
            <h4 class="modal-title"><?php echo $sys_data["name"]; ?></h4>
        </div>
        <?php $sys_data["gym_logo"] = $this->request->base . "/webroot/upload/" . $sys_data["gym_logo"]; ?>
        <table width="100%" border="0">
            <tbody>
            <tr>
                <td width="50%%">
                    <img style="max-height:80px;" src="<?php echo $sys_data["gym_logo"]; ?>">
                </td>
                <td align="right" width="50%">
                    <h5><?php $issue_date = 'DD-MM-YYYY';
                        if (!empty($income_data)) {
                            $issue_date = $income_data["invoice_date"]->format("Y-m-d");
                            $payment_status = $income_data["payment_status"];
                        }
                        if (!empty($invoice_data)) {
                            $issue_date = $invoice_data->payment_date;
                            $payment_status = $invoice_data->payment_status;
                        }
                        if (!empty($expense_data)) {
                            $issue_date = $expense_data["invoice_date"];
                            $payment_status = $expense_data["payment_status"];
                        }
                        echo __('Issue Date') . " : " . date($sys_data["date_format"], strtotime($issue_date)); ?></h5>
                    <h5><?php echo __('Status') . " : " . $payment_status; ?></h5>
                </td>
            </tr>
            </tbody>
        </table>
        <hr>
        <table width="100%" border="0">
            <tbody>
            <tr>
                <td align="left">
                    <h4><?php echo __('Payment To'); ?> </h4>
                </td>
                <td align="right">
                    <h4><?php echo __('Bill To'); ?> </h4>
                </td>
            </tr>
            <tr>
                <td valign="top" align="left">
                    <?php echo $sys_data["name"] . "<br>";
                    echo $sys_data["address"] . ",";
                    echo $sys_data["country"] . "<br>";
                    echo $sys_data["office_number"] . "<br>";
                    ?>
                </td>
                <td valign="top" align="right">
                    <?php
                    if (!empty($expense_data)) {
                        echo $party_name = $expense_data["supplier_name"];
                    } else {
                        if (!empty($income_data))
                            $member_id = $income_data["supplier_name"];
                        if (!empty($invoice_data))
                            $member_id = $invoice_data["member_id"];
                        // $patient=get_userdata($member_id);
                        echo $income_data["gym_member"]["first_name"] . " " . $income_data["gym_member"]["last_name"] . "<br>";
                        echo $income_data["gym_member"]["address"] . ",";
                        echo $income_data["gym_member"]["city"] . ",";
                        echo $income_data["gym_member"]["mobile"] . "<br>";
                    }
                    ?>
                </td>
            </tr>
            </tbody>
        </table>
        <hr>
        <h4><?php echo __('Invoice Entries'); ?></h4>
        <table class="table table-bordered" width="100%" border="1" style="border-collapse:collapse;">
            <thead>
            <tr>
                <!--<th class="text-center">#</th>-->
                <th class="text-center"> <?php echo __('Date'); ?></th>
                <th width="60%"><?php echo __('Entry'); ?> </th>
                <th><?php echo __('Now Paid'); ?></th>
                <th class="text-center"> <?php echo __('Received By'); ?> </th>
            </tr>
            </thead>
            <tbody>
            <?php
            $id = 1;
            $total_amount = 0;
            if (!empty($income_data) || !empty($expense_data)) {
                if (!empty($income_data)) {
                    $entries = json_decode($income_data["entry"]);
                    $i = 1;
                    foreach ($entries as $entry) { ?>
                        <tr>
                            <!--<td><?php echo $i; ?></td>-->
                            <td class="text-center"><?php echo $income_data["invoice_date"]; ?></td>
                            <td class="text-center"><?php echo $entry->entry; ?></td>
                            <td class="text-center"><?php echo $this->Gym->get_currency_symbol(); ?><?php echo $entry->amount; ?></td>
                            <!--									<td class="text-center">-->
                            <?php //echo $income_data["gym_member"]["first_name"] . " ". $income_data["gym_member"]["first_name"];
                            ?><!--</td>-->
                            <td class="text-center"><?php echo $income_data["receiver_name"]["first_name"] . " " . $income_data["receiver_name"]["last_name"]; ?></td>
                        </tr>
                        <?php $i++;
                    }

                } else if (!empty($expense_data)) {
                    $entries = json_decode($expense_data["entry"]);
                    $i = 1;
                    foreach ($entries as $entry) { ?>
                        <tr>
                            <!--<td><?php //echo $i;
                            ?></td>-->
                            <td class="text-center"><?php echo $expense_data["invoice_date"]; ?></td>
                            <td class="text-center"><?php echo $entry->entry; ?></td>
                            <td class="text-center"><?php echo $this->Gym->get_currency_symbol(); ?><?php echo $entry->amount; ?></td>
                            <td class="text-center"><?php echo $expense_data["gym_member"]["first_name"] . " " . $expense_data["gym_member"]["first_name"]; ?></td>
                        </tr>
                        <?php $i++;
                    }
                }

            }
            if (!empty($invoice_data)) {
                $total_amount = $invoice_data->total_amount
                ?>
                <tr>
                    <td class="text-center"><?php echo $id; ?></td>
                    <td class="text-center"><?php echo $invoice_data->payment_date; ?></td>
                    <td><?php echo $invoice_data->title; ?> </td>
                    <td class="text-right"><?php echo $this->Gym->get_currency_symbol(); ?><?php echo $invoice_data->total_amount; ?></td>
                    <td class="text-center"><?php echo gym_get_display_name($invoice_data->receiver_id); ?></td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
        <table width="100%" border="0">
            <table width="100%" border="0" class="table table-bordered">
                <tbody>
                <?php if (!empty($invoice_data)) {
                    $total_amount = $invoice_data->total_amount;
                    $grand_total = $invoice_data->total_amount - $invoice_data->discount;
                    ?>
                    <tr>
                        <td width="80%" align="right"><?php echo __('Subtotal :'); ?></td>
                        <td align="right"><?php echo $this->Gym->get_currency_symbol(); ?><?php echo $total_amount; ?></td>
                    </tr>
                    <tr>
                        <td width="80%" align="right"><?php echo __('Discount :'); ?></td>
                        <td align="right"><?php echo $this->Gym->get_currency_symbol(); ?><?php echo $invoice_data->discount; ?></td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <hr style="margin:0px;">
                        </td>
                    </tr>
                    <?php
                }
                if (!empty($income_data)) {
                    $grand_total = $income_data["total_amount"];
                } else if (!empty($expense_data)) {
                    $grand_total = $expense_data["total_amount"];
                }
                ?>
                <tr>
                    <td><?php echo __('Total Amount:'); ?></td>
                    <td><h4
                                style="padding-bottom:0px;margin-bottom:0px;"><?php echo $this->Gym->get_currency_symbol(); ?><?php echo $grand_total; ?></h4>

                    </td>
                    <td>In Words:</td>
                    <td><i><?php
                            $f = new NumberFormatter("en", NumberFormatter::SPELLOUT);
                            echo $f->format($grand_total);
                            ?> Taka</i></td>
                </tr>
                <tr>
                    <td><?php echo __('Now Paid:'); ?></td>
                    <td><h4
                                style="padding-bottom:0px;margin-bottom:0px;"><?php echo $this->Gym->get_currency_symbol(); ?><?php echo $grand_total - $income_data["due_amount"]; ?></h4>

                    </td>
                    <td>In Words:</td>
                    <td><i><?php
                            $f = new NumberFormatter("en", NumberFormatter::SPELLOUT);
                            echo $f->format($grand_total - $income_data["due_amount"]);
                            ?> Taka</i></td>
                </tr>

                <tr>
                    <td>Due Amount</td>
                    <td><i>
                            <?php
                            echo $income_data["due_amount"];
                            ?> Taka</i></td>
                    <td>Remarks</td>
                    <td>
                        <?= $income_data["remarks"] ?>
                    </td>
                </tr>

                </tbody>
            </table>
    </div>
</div>
<div class="" style="margin-top:35px;border-top:1px dotted black;width:100%"></div>
<div class="modal-body">
    <div id="invoice_print" style="width: 90%;margin:0 auto;">
        <div class="modal-header">
            <h4 class="modal-title"><?php echo $sys_data["name"]; ?></h4>
        </div>
        <!--        --><?php //$sys_data["gym_logo"] = (!empty($sys_data["gym_logo"])) ? $this->request->base . "/webroot/upload/".  $sys_data["gym_logo"] : $this->request->base . "/webroot/img/Thumbnail-img.png"; ?>
        <table width="100%" border="0">
            <tbody>
            <tr>
                <td width="50%%">
                    <img style="max-height:80px;" src="<?php echo $sys_data["gym_logo"]; ?>">
                </td>
                <td align="right" width="50%">
                    <h5><?php $issue_date = 'DD-MM-YYYY';
                        if (!empty($income_data)) {
                            $issue_date = $income_data["invoice_date"]->format("Y-m-d");
                            $payment_status = $income_data["payment_status"];
                        }
                        if (!empty($invoice_data)) {
                            $issue_date = $invoice_data->payment_date;
                            $payment_status = $invoice_data->payment_status;
                        }
                        if (!empty($expense_data)) {
                            $issue_date = $expense_data["invoice_date"];
                            $payment_status = $expense_data["payment_status"];
                        }
                        echo __('Issue Date') . " : " . date($sys_data["date_format"], strtotime($issue_date)); ?></h5>
                    <h5><?php echo __('Status') . " : " . $payment_status; ?></h5>
                </td>
            </tr>
            </tbody>
        </table>
        <hr>
        <table width="100%" border="0">
            <tbody>
            <tr>
                <td align="left">
                    <h4><?php echo __('Payment To'); ?> </h4>
                </td>
                <td align="right">
                    <h4><?php echo __('Bill To'); ?> </h4>
                </td>
            </tr>
            <tr>
                <td valign="top" align="left">
                    <?php echo $sys_data["name"] . "<br>";
                    echo $sys_data["address"] . ",";
                    echo $sys_data["country"] . "<br>";
                    echo $sys_data["office_number"] . "<br>";
                    ?>
                </td>
                <td valign="top" align="right">
                    <?php
                    if (!empty($expense_data)) {
                        echo $party_name = $expense_data["supplier_name"];
                    } else {
                        if (!empty($income_data))
                            $member_id = $income_data["supplier_name"];
                        if (!empty($invoice_data))
                            $member_id = $invoice_data["member_id"];
                        // $patient=get_userdata($member_id);
                        echo $income_data["gym_member"]["first_name"] . " " . $income_data["gym_member"]["last_name"] . "<br>";
                        echo $income_data["gym_member"]["address"] . ",";
                        echo $income_data["gym_member"]["city"] . ",";
                        echo $income_data["gym_member"]["mobile"] . "<br>";
                    }
                    ?>
                </td>
            </tr>
            </tbody>
        </table>
        <hr>
        <h4><?php echo __('Invoice Entries'); ?></h4>
        <table class="table table-bordered" width="100%" border="1" style="border-collapse:collapse;">
            <thead>
            <tr>
                <!--<th class="text-center">#</th>-->
                <th class="text-center"> <?php echo __('Date'); ?></th>
                <th width="60%"><?php echo __('Entry'); ?> </th>
                <th><?php echo __('Now Paid'); ?></th>
                <th class="text-center"> <?php echo __('Received By'); ?> </th>
            </tr>
            </thead>
            <tbody>
            <?php
            $id = 1;
            $total_amount = 0;
            if (!empty($income_data) || !empty($expense_data)) {
                if (!empty($income_data)) {
                    $entries = json_decode($income_data["entry"]);
                    $i = 1;
                    foreach ($entries as $entry) { ?>
                        <tr>
                            <!--<td><?php echo $i; ?></td>-->
                            <td class="text-center"><?php echo $income_data["invoice_date"]; ?></td>
                            <td class="text-center"><?php echo $entry->entry; ?></td>
                            <td class="text-center"><?php echo $this->Gym->get_currency_symbol(); ?><?php echo $entry->amount; ?></td>
                            <!--									<td class="text-center">-->
                            <?php //echo $income_data["gym_member"]["first_name"] . " ". $income_data["gym_member"]["first_name"];
                            ?><!--</td>-->
                            <td class="text-center"><?php echo $income_data["receiver_name"]["first_name"] . " " . $income_data["receiver_name"]["last_name"]; ?></td>
                        </tr>
                        <?php $i++;
                    }

                } else if (!empty($expense_data)) {
                    $entries = json_decode($expense_data["entry"]);
                    $i = 1;
                    foreach ($entries as $entry) { ?>
                        <tr>
                            <!--<td><?php //echo $i;
                            ?></td>-->
                            <td class="text-center"><?php echo $expense_data["invoice_date"]; ?></td>
                            <td class="text-center"><?php echo $entry->entry; ?></td>
                            <td class="text-center"><?php echo $this->Gym->get_currency_symbol(); ?><?php echo $entry->amount; ?></td>
                            <td class="text-center"><?php echo $expense_data["gym_member"]["first_name"] . " " . $expense_data["gym_member"]["first_name"]; ?></td>
                        </tr>
                        <?php $i++;
                    }
                }

            }
            if (!empty($invoice_data)) {
                $total_amount = $invoice_data->total_amount
                ?>
                <tr>
                    <td class="text-center"><?php echo $id; ?></td>
                    <td class="text-center"><?php echo $invoice_data->payment_date; ?></td>
                    <td><?php echo $invoice_data->title; ?> </td>
                    <td class="text-right"><?php echo $this->Gym->get_currency_symbol(); ?><?php echo $invoice_data->total_amount; ?></td>
                    <td class="text-center"><?php echo gym_get_display_name($invoice_data->receiver_id); ?></td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
        <table width="100%" border="0" class="table table-bordered">
            <tbody>
            <?php if (!empty($invoice_data)) {
                $total_amount = $invoice_data->total_amount;
                $grand_total = $invoice_data->total_amount - $invoice_data->discount;
                ?>
                <tr>
                    <td width="80%" align="right"><?php echo __('Subtotal :'); ?></td>
                    <td align="right"><?php echo $this->Gym->get_currency_symbol(); ?><?php echo $total_amount; ?></td>
                </tr>
                <tr>
                    <td width="80%" align="right"><?php echo __('Discount :'); ?></td>
                    <td align="right"><?php echo $this->Gym->get_currency_symbol(); ?><?php echo $invoice_data->discount; ?></td>
                </tr>
                <tr>
                    <td colspan="2">
                        <hr style="margin:0px;">
                    </td>
                </tr>
                <?php
            }
            if (!empty($income_data)) {
                $grand_total = $income_data["total_amount"];
            } else if (!empty($expense_data)) {
                $grand_total = $expense_data["total_amount"];
            }
            ?>
            <tr>
                <td><?php echo __('Total Amount:'); ?></td>
                <td><h4
                            style="padding-bottom:0px;margin-bottom:0px;"><?php echo $this->Gym->get_currency_symbol(); ?><?php echo $grand_total; ?></h4>

                </td>
                <td>In Words:</td>
                <td><i><?php
                        $f = new NumberFormatter("en", NumberFormatter::SPELLOUT);
                        echo $f->format($grand_total);
                        ?> Taka</i></td>
            </tr>
            <tr>
                <td><?php echo __('Now Paid:'); ?></td>
                <td><h4
                            style="padding-bottom:0px;margin-bottom:0px;"><?php echo $this->Gym->get_currency_symbol(); ?><?php echo $grand_total - $income_data["due_amount"]; ?></h4>

                </td>
                <td>In Words:</td>
                <td><i><?php
                        $f = new NumberFormatter("en", NumberFormatter::SPELLOUT);
                        echo $f->format($grand_total - $income_data["due_amount"]);
                        ?> Taka</i></td>
            </tr>
            <tr>
                <td>Due Amount</td>
                <td><i>
                        <?php
                        echo $income_data["due_amount"];
                        ?> Taka</i></td>
                <td>Remarks</td>
                <td>
                    <?= $income_data["remarks"] ?>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</div>
<?php die; ?>