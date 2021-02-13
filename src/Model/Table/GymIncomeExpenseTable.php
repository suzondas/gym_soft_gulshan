<?php
namespace App\Model\Table;
use Cake\ORM\Table;


class GymIncomeExpenseTable extends Table
{
	public function initialize(array $config)
	{
		$this->addBehavior("Timestamp");
		$this->belongsTo("GymMember",["foreignKey"=>"supplier_name"]);
		$this->belongsTo("MembershipPayment",["foreignKey"=>"supplier_name","targetForeignKey"=>"member_id"]);
        $this->belongsTo("supplierName",["foreignKey"=>"supplier_name",'joinType' => 'INNER', 'className' => 'GymMember']);
        $this->belongsTo("receiverName",["foreignKey"=>"receiver_id",'joinType' => 'INNER', 'className' => 'GymMember']);

	}
}