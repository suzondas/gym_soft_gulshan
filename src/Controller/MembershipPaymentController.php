<?php

namespace App\Controller;

use App\Controller\AppController;
use Cake\ORM\TableRegistry;
use Gmgt_paypal_class;
use Cake\Datasource\ConnectionManager;
use Cake\I18n\Time;
use Cake\I18n\Date;

class MembershipPaymentController extends AppController
{
    public function initialize()
    {
        parent::initialize();
        require_once(ROOT . DS . 'vendor' . DS . 'paypal' . DS . 'paypal_class.php');
        $this->loadComponent("GYMFunction");
    }

    public function paymentList()
    {
        $new_session = $this->request->session();
        $session = $this->request->session()->read("User");
        if ($session["role_name"] == "member") {
            $data = $this->MembershipPayment->find("all")->contain(["Membership", "GymMember"])->where(["GymMember.id" => $session["id"]])->hydrate(false)->toArray();
        } else {
            $data = $this->MembershipPayment->find("all")->contain(["Membership", "GymMember"])->hydrate(false)->toArray();
        }
//    var_dump($data);exit;
        $this->set("data", $data);

        if ($this->request->is("post")) {
            $mp_id = $this->request->data["mp_id"];
            $row = $this->MembershipPayment->get($mp_id);
            if ($this->request->data["payment_method"] == "Paypal" && $session["role_name"] == "member") {
                // var_dump($row->member_id);die;
                $mp_id = $this->request->data["mp_id"];
                $user_id = $row->member_id;
                $membership_id = $row->membership_id;
                $custom_var = $mp_id;
                $user_info = $this->MembershipPayment->GymMember->get($user_id);

                $new_session->write("Payment.mp_id", $mp_id);
                $new_session->write("Payment.amount", $this->request->data["amount"]);

                // var_dump($user_info);die;
                require_once(ROOT . DS . 'vendor' . DS . 'paypal' . DS . 'paypal_process.php');
            } else {
                $row->paid_amount = $row->paid_amount + $this->request->data["amount"];
                $this->MembershipPayment->save($row);

                $hrow = $this->MembershipPayment->MembershipPaymentHistory->newEntity();
                $data['mp_id'] = $mp_id;
                $data['amount'] = $this->request->data["amount"];
                $data['payment_method'] = $this->request->data["payment_method"];
                $data['paid_by_date'] = date("Y-m-d");
                $data['created_by'] = $session["id"];
                $data['transaction_id'] = "";
                $data['memo_no'] = $this->request->data["memo_no"];

                $hrow = $this->MembershipPayment->MembershipPaymentHistory->patchEntity($hrow, $data);
                if ($this->MembershipPayment->MembershipPaymentHistory->save($hrow)) {
                    $this->Flash->success(__("Success! Payment Added Successfully."));
                }
            }
            return $this->redirect(["action" => "paymentList"]);
        }
    }

    public function addCustomMember()
    {
        if ($this->request->is('post')) {
            $conn = ConnectionManager::get('default');
            $custom_member_id = addslashes($this->request->data['custom_member_id']);
            $first_name = addslashes($this->request->data['first_name']);
            $last_name = addslashes($this->request->data['last_name']);
            $address = addslashes($this->request->data['address']);
            $mobile = addslashes($this->request->data['mobile']);
            $stmt = $conn->execute("insert into gym_member (member_id,first_name,last_name,role_name,branch, mobile, address) values($custom_member_id, '$first_name', '$last_name','member','gulshan','$mobile','$address')");
            return $this->redirect(["action" => "addIncome"]);
        } else {
            $lastid = 0;

            $conn = ConnectionManager::get('default');
            $stmt = $conn->execute("select * from gym_member order by id desc limit 1");
            $data = $stmt->fetchAll('assoc');

            $member_id = intval($data[0]['member_id']) + 1;
            $this->set('member_id', $member_id);
            return $this->render('addCustomMember');
        }

    }

    public function incomeReport()
    {
        $this->set("edit", false);
        if($this->Auth->user('role_name') === 'administrator'){
            $members = $this->MembershipPayment->GymMember->find("list", ["keyField" => "id", "valueField" => "name"])->where(["role_name !=" => "member"]);
            $members = $members->select(["id", "name" => $members->func()->concat(["first_name" => "literal", " ", "last_name" => "literal"])])->hydrate(false)->toArray();
            $members['all'] = 'All';
        }else{
            $members = $this->MembershipPayment->GymMember->find("list", ["keyField" => "id", "valueField" => "name"])->where(["id" => $this->Auth->user('id')]);
            $members = $members->select(["id", "name" => $members->func()->concat(["first_name" => "literal", " ", "last_name" => "literal"])])->hydrate(false)->toArray();
        }

        $this->set("members", $members);

        if ($this->request->is('post')) {
            if ($this->request->data["SearchingCatergory"] == 'today') {
                $today = new Time('today');
                $data = $this->MembershipPayment->GymIncomeExpense->find("all")->contain(["supplierName", "receiverName"])->where(["invoice_type" => "income", "invoice_date" => $today])->hydrate(false)->toArray();
                if ($this->request->data['receiver_id'] != 'all') {
                    $data = $this->MembershipPayment->GymIncomeExpense->find("all")->contain(["supplierName", "receiverName"])->where(["invoice_type" => "income", "invoice_date" => $today, 'receiver_id' => $this->request->data['receiver_id']])->hydrate(false)->toArray();
                } else {
                    $data = $this->MembershipPayment->GymIncomeExpense->find("all")->contain(["supplierName", "receiverName"])->where(["invoice_type" => "income", "invoice_date" => $today])->hydrate(false)->toArray();
                }
                $this->set("data", $data);
            }
            if ($this->request->data["SearchingCatergory"] == 'specific') {
                $date = new Date($this->request->data['startDateSpecific']);
                $specificDate = $date->format('Y-m-d');
                if ($this->request->data['receiver_id'] != 'all') {
                    $data = $this->MembershipPayment->GymIncomeExpense->find("all")->contain(["supplierName", "receiverName"])->where(["invoice_type" => "income", "invoice_date" => $specificDate, 'receiver_id' => $this->request->data['receiver_id']])->hydrate(false)->toArray();
                } else {
                    $data = $this->MembershipPayment->GymIncomeExpense->find("all")->contain(["supplierName", "receiverName"])->where(["invoice_type" => "income", "invoice_date" => $specificDate])->hydrate(false)->toArray();
                }
                $this->set("data", $data);
            }
            if ($this->request->data["SearchingCatergory"] == 'range') {
                $sDate = new Date($this->request->data['startDateRange']);
                $startDateRange = $sDate->format('Y-m-d');

                $eDate = new Date($this->request->data['endDateRange']);
                $endDateRange = $eDate->format('Y-m-d');
                if ($this->request->data['receiver_id'] != 'all') {
                    $data = $this->MembershipPayment->GymIncomeExpense->find("all")->contain(["supplierName", "receiverName"])->where(["invoice_type" => "income", "invoice_date >=" => $startDateRange, "invoice_date <=" => $endDateRange, 'receiver_id' => $this->request->data['receiver_id']])->hydrate(false)->toArray();
                } else {
                    $data = $this->MembershipPayment->GymIncomeExpense->find("all")->contain(["supplierName", "receiverName"])->where(["invoice_type" => "income", "invoice_date >=" => $startDateRange, "invoice_date <=" => $endDateRange])->hydrate(false)->toArray();
                }
//                var_dump($data);exit;
                $this->set("data", $data);
            }
        }

    }

    public function generatePaymentInvoice()
    {
        $this->set("edit", false);
        $members = $this->MembershipPayment->GymMember->find("list", ["keyField" => "id", "valueField" => "name"])->where(["role_name" => "member"]);
        $members = $members->select(["id", "name" => $members->func()->concat(["first_name" => "literal", " ", "last_name" => "literal"])])->hydrate(false)->toArray();
        $this->set("members", $members);

        $membership = $this->MembershipPayment->Membership->find("list", ["keyField" => "id", "valueField" => "membership_label"]);
        $this->set("membership", $membership);

        if ($this->request->is('post')) {
            $mid = $this->request->data["user_id"];
            $start_date = date("Y-m-d", strtotime($this->request->data["membership_valid_from"]));
            $end_date = date("Y-m-d", strtotime($this->request->data["membership_valid_to"]));
            $row = $this->MembershipPayment->newEntity();
            $pdata["member_id"] = $mid;
            $pdata["membership_id"] = $this->request->data["membership_id"];
            $pdata["membership_amount"] = $this->request->data["membership_amount"];
            $pdata["paid_amount"] = 0;
            $pdata["start_date"] = $start_date;
            $pdata["end_date"] = $end_date;
            $pdata["membership_status"] = "Continue";
            $pdata["payment_status"] = 0;
            $pdata["created_date"] = date("Y-m-d");
            $pdata["discount_amount"] = $this->request->data["discount_amount"];
            $row = $this->MembershipPayment->patchEntity($row, $pdata);
            $this->MembershipPayment->save($row);
            ################## MEMBER's Current Membership Change ##################
            $member_data = $this->MembershipPayment->GymMember->get($mid);
            $member_data->selected_membership = $this->request->data["membership_id"];
            $member_data->membership_valid_from = $start_date;
            $member_data->membership_valid_to = $end_date;
            $this->MembershipPayment->GymMember->save($member_data);
            #####################Add Membership History #############################
            $mem_histoty = $this->MembershipPayment->MembershipHistory->newEntity();
            $hdata["member_id"] = $mid;
            $hdata["selected_membership"] = $this->request->data["membership_id"];
            $hdata["membership_valid_from"] = $start_date;
            $hdata["membership_valid_to"] = $end_date;
            $hdata["created_date"] = date("Y-m-d");
            $hdata = $this->MembershipPayment->MembershipHistory->patchEntity($mem_histoty, $hdata);
            if ($this->MembershipPayment->MembershipHistory->save($mem_histoty)) {
                $this->Flash->success(__("Success! Payment Added Successfully."));
                return $this->redirect(["action" => "paymentList"]);
            }
        }
    }

    public function membershipEdit($eid)
    {
        $this->set("edit", true);
        $members = $this->MembershipPayment->GymMember->find("list", ["keyField" => "id", "valueField" => "name"])->where(["role_name" => "member"]);
        $members = $members->select(["id", "name" => $members->func()->concat(["first_name" => "literal", " ", "last_name" => "literal"])])->hydrate(false)->toArray();
        $this->set("members", $members);

        $membership = $this->MembershipPayment->Membership->find("list", ["keyField" => "id", "valueField" => "membership_label"]);
        $this->set("membership", $membership);

        $data = $this->MembershipPayment->get($eid);
        $this->set("data", $data->toArray());
        // var_dump($data->toArray());die;

        if ($this->request->is("post")) {
            $mid = $this->request->data["user_id"];
            $start_date = date("Y-m-d", strtotime($this->request->data["membership_valid_from"]));
            $end_date = date("Y-m-d", strtotime($this->request->data["membership_valid_to"]));

            $row = $this->MembershipPayment->get($eid);
            $row->member_id = $mid;
            $row->membership_id = $this->request->data["membership_id"];
            $row->membership_amount = $this->request->data["membership_amount"];
            $row->discount_amount = $this->request->data["discount_amount"];
            $row->paid_amount = 0;
            $row->start_date = $start_date;
            $row->end_date = $end_date;
            $row->membership_status = "Continue";
            $this->MembershipPayment->save($row);
            ###############################################################
            $member_data = $this->MembershipPayment->GymMember->get($mid);
            $member_data->selected_membership = $this->request->data["membership_id"];
            $member_data->membership_valid_from = $start_date;
            $member_data->membership_valid_to = $end_date;
            $this->MembershipPayment->GymMember->save($member_data);
            ###########################################################
            $this->Flash->success(__("Success! Record Updated Successfully."));
            return $this->redirect(["action" => "paymentList"]);
        }
        $this->render("generatePaymentInvoice");
    }

    public function deletePayment($mp_id)
    {
        $row = $this->MembershipPayment->get($mp_id);
        if ($this->MembershipPayment->delete($row)) {
            $this->Flash->success(__("Success! Payment Record Deleted Successfully."));
            return $this->redirect(["action" => "paymentList"]);
        }
    }

    public function incomeList()
    {
        if($this->Auth->user('role_name') === 'staff_member'){
            $data = $this->MembershipPayment->GymIncomeExpense->find("all")->contain(["GymMember"])->where(["invoice_type" => "income","receiver_id"=>$this->Auth->user('id')])->hydrate(false)->toArray();
//        var_dump($data);exit;
            $this->set("data", $data);
        }else{
            $data = $this->MembershipPayment->GymIncomeExpense->find("all")->contain(["GymMember"])->where(["invoice_type" => "income"])->hydrate(false)->toArray();
//        var_dump($data);exit;
            $this->set("data", $data);
        }

    }

    public function addIncome()
    {
        $session = $this->request->session()->read("User");
        $this->set("edit", false);
        $members = $this->MembershipPayment->GymMember->find("list", ["keyField" => "id", "valueField" => "name"])->where(["role_name" => "member"]);
        $members = $members->select(["id", "name" => $members->func()->concat(["first_name" => "literal", " ", "last_name" => "literal"])])->hydrate(false)->toArray();
        $this->set("members", $members);

        if ($this->request->is("post")) {
            $row = $this->MembershipPayment->GymIncomeExpense->newEntity();
            $data = $this->request->data;
            $total_amount = null;
            foreach ($data["income_amount"] as $amount) {
                $total_amount += $amount;
            }
            $row->total_amount = $total_amount;
            $row->supplier_name = $this->request->data['supplier_name'];
            $row->entry = $this->get_entry_records($data);
            $row->receiver_id = $session["id"];
            $row->invoice_type = 'income';
            $row->payment_status = $this->request->data['payment_status'];
            $row->remarks = $this->request->data['remarks'];
            $row->due_amount = $this->request->data['due_amount'];
            $row->invoice_label = $this->request->data['invoice_label'];
            $row->invoice_date = date("Y-m-d", strtotime($data["invoice_date"]));
            if ($this->MembershipPayment->GymIncomeExpense->save($row)) {
                $this->Flash->success(__("Success! Record Saved Successfully."));
                return $this->redirect(["action" => "incomeList"]);
            }
        }
    }

    public function get_entry_records($data)
    {
        $all_income_entry = $data['income_entry'];
        $all_income_amount = $data['income_amount'];

        $entry_data = array();
        $i = 0;
        foreach ($all_income_entry as $one_entry) {
            $entry_data[] = array('entry' => $one_entry,
                'amount' => $all_income_amount[$i]);
            $i++;
        }
        return json_encode($entry_data);
    }

    public function incomeEdit($eid)
    {
        $this->set("edit", true);
        $members = $this->MembershipPayment->GymMember->find("list", ["keyField" => "id", "valueField" => "name"])->where(["role_name" => "member"]);
        $members = $members->select(["id", "name" => $members->func()->concat(["first_name" => "literal", " ", "last_name" => "literal"])])->hydrate(false)->toArray();
        $this->set("members", $members);

        $row = $this->MembershipPayment->GymIncomeExpense->get($eid);
        $this->set("data", $row->toArray());

        if ($this->request->is("post")) {
//            var_dump($row);exit;
            $data = $this->request->data;
            $total_amount = null;
            foreach ($data["income_amount"] as $amount) {
                $total_amount += $amount;
            }
            $data["total_amount"] = $total_amount;
            $data["entry"] = $this->get_entry_records($data);
            $data["invoice_date"] = date("Y-m-d", strtotime($data["invoice_date"]));

            $row = $this->MembershipPayment->GymIncomeExpense->patchEntity($row, $data);
            if ($this->MembershipPayment->GymIncomeExpense->save($row)) {
                $this->Flash->success(__("Success! Record Updated Successfully."));
                return $this->redirect(["action" => "incomeList"]);
            }
        }
        $this->render("addIncome");
    }

    public function deleteIncome($did)
    {
        $row = $this->MembershipPayment->GymIncomeExpense->get($did);
        if ($this->MembershipPayment->GymIncomeExpense->delete($row)) {
            $this->Flash->success(__("Success! Record Deleted Successfully."));
            return $this->redirect($this->referer());
        }
    }

    public function printInvoice()
    {
        $id = $this->request->params["pass"][0];
        $invoice_type = $this->request->params["pass"][1];
        $in_ex_table = TableRegistry::get("GymIncomeExpense");
        $setting_tbl = TableRegistry::get("GeneralSetting");
        $income_data = array();
        $expense_data = array();
        $invoice_data = array();

        $sys_data = $setting_tbl->find()->select(["name", "address", "gym_logo", "date_format", "office_number", "country"])->hydrate(false)->toArray();

        if ($invoice_type == "income") {
            $income_data = $this->MembershipPayment->GymIncomeExpense->find("all")->contain(["GymMember", "receiverName"])->where(["GymIncomeExpense.id" => $id])->hydrate(false)->toArray();

            $membership_data = $this->MembershipPayment->MembershipPayment->find("all")->where(["member_id" => $income_data[0]["supplier_name"]])->hydrate(false)->toArray();
//            var_dump($membership_data);exit;
            $this->set("income_data", $income_data[0]);
            $this->set("membership_data", $membership_data[0]);
            $this->set("expense_data", $expense_data);
            $this->set("invoice_data", $invoice_data);
        } else if ($invoice_type == "expense") {
            $expense_data = $this->MembershipPayment->GymIncomeExpense->find("all")->where(["GymIncomeExpense.id" => $id])->select($this->MembershipPayment->GymIncomeExpense);
            $expense_data = $expense_data->leftjoin(["GymMember" => "gym_member"],
                ["GymIncomeExpense.receiver_id = GymMember.id"])->select($this->MembershipPayment->GymMember)->hydrate(false)->toArray();
            $expense_data[0]["gym_member"] = $expense_data[0]["GymMember"];
            unset($expense_data[0]["GymMember"]);
            $this->set("income_data", $income_data);
            $this->set("expense_data", $expense_data[0]);
            $this->set("invoice_data", $invoice_data);
        }

        $this->set("sys_data", $sys_data[0]);

    }

    public function expenseList()
    {
        $data = $this->MembershipPayment->GymIncomeExpense->find("all")->where(["invoice_type" => "expense"])->hydrate(false)->toArray();
        $this->set("data", $data);
    }

    public function addExpense()
    {
        $this->set("edit", false);
        $session = $this->request->session()->read("User");

        if ($this->request->is("post")) {
            $row = $this->MembershipPayment->GymIncomeExpense->newEntity();
            $data = $this->request->data;
            $total_amount = null;
            foreach ($data["income_amount"] as $amount) {
                $total_amount += $amount;
            }
            $data["total_amount"] = $total_amount;
            $data["entry"] = $this->get_entry_records($data);
            $data["receiver_id"] = $session["id"];//current userid;
            $data["invoice_date"] = date("Y-m-d", strtotime($data["invoice_date"]));
            $row = $this->MembershipPayment->GymIncomeExpense->patchEntity($row, $data);
            if ($this->MembershipPayment->GymIncomeExpense->save($row)) {
                $this->Flash->success(__("Success! Record Saved Successfully."));
                return $this->redirect(["action" => "expenseList"]);
            }
        }
    }

    public function expenseEdit($eid)
    {
        $this->set("edit", true);

        $row = $this->MembershipPayment->GymIncomeExpense->get($eid);
        $this->set("data", $row->toArray());

        if ($this->request->is("post")) {
            $data = $this->request->data;
            $total_amount = null;
            foreach ($data["income_amount"] as $amount) {
                $total_amount += $amount;
            }
            $data["total_amount"] = $total_amount;
            $data["entry"] = $this->get_entry_records($data);
            $data["invoice_date"] = date("Y-m-d", strtotime($data["invoice_date"]));

            $row = $this->MembershipPayment->GymIncomeExpense->patchEntity($row, $data);
            if ($this->MembershipPayment->GymIncomeExpense->save($row)) {
                $this->Flash->success(__("Success! Record Updated Successfully."));
                return $this->redirect(["action" => "expenseList"]);
            }
        }
        $this->render("addExpense");
    }

    public function deleteAccountant($id)
    {
        $row = $this->GymAccountant->GymMember->get($id);
        if ($this->GymAccountant->GymMember->delete($row)) {
            $this->Flash->success(__("Success! Accountant Deleted Successfully."));
            return $this->redirect($this->referer());
        }
    }

    public function paymentSuccess()
    {
        $payment_data = $this->request->session()->read("Payment");
        $session = $this->request->session()->read("User");
        $feedata['mp_id'] = $payment_data["mp_id"];
        $feedata['amount'] = $payment_data['amount'];
        $feedata['payment_method'] = 'Paypal';
        $feedata['paid_by_date'] = date("Y-m-d");
        $feedata['created_by'] = $session["id"];
        $row = $this->MembershipPayment->MembershipPaymentHistory->newEntity();
        $row = $this->MembershipPayment->MembershipPaymentHistory->patchEntity($row, $feedata);
        if ($this->MembershipPayment->MembershipPaymentHistory->save($row)) {
            $row = $this->MembershipPayment->get($payment_data["mp_id"]);
            $row->paid_amount = $row->paid_amount + $payment_data['amount'];
            $this->MembershipPayment->save($row);
        }

        $session = $this->request->session();
        $session->delete('Payment');

        $this->Flash->success(__("Success! Payment Successfully Completed."));
        return $this->redirect(["action" => "paymentList"]);
    }

    public function ipnFunction()
    {
        if ($this->request->is("post")) {
            $trasaction_id = $_POST["txn_id"];
            $custom_array = explode("_", $_POST['custom']);
            $feedata['mp_id'] = $custom_array[1];
            $feedata['amount'] = $_POST['mc_gross_1'];
            $feedata['payment_method'] = 'Paypal';
            $feedata['trasaction_id'] = $trasaction_id;
            $feedata['created_by'] = $custom_array[0];
            //$log_array      = print_r($feedata, TRUE);
            //wp_mail( 'bhaskar@dasinfomedia.com', 'gympaypal', $log_array);
            $row = $this->MembershipPayment->MembershipPaymentHistory->newEntity();
            $row = $this->MembershipPayment->MembershipPaymentHistory->patchEntity($row, $feedata);
            if ($this->MembershipPayment->MembershipPaymentHistory->save($row)) {
                $this->Flash->success(__("Success! Payment Successfully Completed."));
            } else {
                $this->Flash->error(__("Paypal Payment IPN save failed to DB."));
            }
            return $this->redirect(["action" => "paymentList"]);
            //require_once SMS_PLUGIN_DIR. '/lib/paypal/paypal_ipn.php';
        }
    }

    public function isAuthorized($user)
    {
        $role_name = $user["role_name"];
        $curr_action = $this->request->action;
        $members_actions = ["paymentList", "paymentSuccess", "ipnFunction"];
        $staff_actions = ["paymentList", "addIncome", "addCustomMember", "incomeReport", "incomeList", "expenseList", "addExpense", "incomeEdit", "expenseEdit","printInvoice"];
        $acc_actions = ["paymentList", "addIncome", "incomeList", "expenseList", "addExpense", "incomeEdit", "expenseEdit", "printInvoice", "deleteIncome"];
        switch ($role_name) {
            CASE "member":
                if (in_array($curr_action, $members_actions)) {
                    return true;
                } else {
                    return false;
                }
                break;

            CASE "staff_member":
                if (in_array($curr_action, $staff_actions)) {
                    return true;
                } else {
                    return false;
                }
                break;

            CASE "accountant":
                if (in_array($curr_action, $acc_actions)) {
                    return true;
                } else {
                    return false;
                }
                break;
        }
        return parent::isAuthorized($user);
    }
}

