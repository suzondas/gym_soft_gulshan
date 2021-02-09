<?php
namespace App\Model\Table;
use Cake\ORM\Table;


class GymIncomeExpenseTable extends Table
{
	public function initialize(array $config)
	{
		$this->addBehavior("Timestamp");
        $this->belongsTo("supplierName",["foreignKey"=>"supplier_name",'joinType' => 'INNER', 'className' => 'GymMember']);
        $this->belongsTo("receiverName",["foreignKey"=>"receiver_id",'joinType' => 'INNER', 'className' => 'GymMember']);
        $this->belongsTo("GymMember",["foreignKey"=>"supplier_name"]);
	}
}