<?php $session = $this->request->session()->read("User");?>
<script>
$(document).ready(function(){		
	var table = $(".mydataTable").DataTable({
		"responsive": true,
		"order": [[ 1, "asc" ]],
		"aoColumns":[
	                  {"bSortable": false},
	                  {"bSortable": true},
	                  {"bSortable": true},
	                  {"bSortable": true},
	                  {"bSortable": true},	                           
	                  {"bSortable": false,"visible":false}],
	"language" : {<?php echo $this->Gym->data_table_lang();?>}	
	}); 
	
});		
</script>
<?php
if($session["role_name"] == "administrator")
{ ?>
<script>

$(document).ready(function(){
	var table = $(".mydataTable").DataTable();
	table.column(5).visible( true );
});
</script>
<?php } ?>
<section class="content">
	<br>
	<div class="col-md-12 box box-default">		
		<div class="box-header">
			<section class="content-header">
			  <h1>
				<i class="fa fa-bars"></i>
				<?php echo __("Staff List");?>
				<small><?php echo __("Staff Member");?></small>
			  </h1>
			  <?php
				if($session["role_name"] == "administrator")
				{ ?>
			  <ol class="breadcrumb">
<!--                  <a href="--><?php //echo $this->Url->build(["controller" => "StaffMembers", "action" => "addStaff", "uttara",]);?><!--" class="btn btn-flat btn-custom"><i class="fa fa-plus"></i> --><?php //echo __("Add Staff (Uttara Branch)");?><!--</a>-->
                  <a href="<?php echo $this->Url->build(["controller" => "StaffMembers", "action" => "addStaff", "gulshan",]);?>" class="btn btn-flat btn-custom"><i class="fa fa-plus"></i> <?php echo __("Add Staff (Gulshan Branch)");?></a>
			  </ol>
			  <?php } ?>
			</section>
		</div>
		<hr>
		<div class="box-body">
		<table class="mydataTable table table-striped">
			<thead>
				<tr>
					<th><?php echo __("Photo");?></th>
					<th><?php echo __("Staff Member Name");?></th>
					<th><?php echo __("Staff ID");?></th>
					<th><?php echo __("Branch");?></th>
					<th><?php echo __("Mobile No.");?></th>					
					<th><?php echo __("Action");?></th>
				</tr>
			</thead>
			<tbody>
			<?php			
			foreach($data as $row)
			{				
				echo "
				<tr>					
					<td><img src='".$this->request->webroot ."upload/{$row['image']}' class='membership-img img-circle'></img></td>
					<td>{$row['first_name']} {$row['last_name']}</td>
					<td>{$row['member_id']}</td>
					<td>{$row['branch']}</td>
					<td>{$row['mobile']}</td>
					<td>
					<a href='".$this->Gym->createurl('StaffMembers','editStaff')."/{$row['id']}' class='btn btn-flat btn-primary' title='Edit'><i class='fa fa-edit'></i></a>
					<a href='".$this->Gym->createurl('StaffMembers','deleteStaff')."/{$row['id']}' class='btn btn-flat btn-danger' title='Delete' onClick=\"return confirm('Are you sure you want to delete?')\"><i class='fa fa-trash-o'></i></a>
					</td>
				</tr>
				";
			} 
			?>
			</tbody>
			<tfoot>
				<tr>
					<th><?php echo __("Photo");?></th>
					<th><?php echo __("Staff Member Name");?></th>
					<th><?php echo __("Staff ID");?></th>
					<th><?php echo __("Branch");?></th>
					<th><?php echo __("Mobile No.");?></th>					
					<th><?php echo __("Action");?></th>
				</tr>
			</tfoot>
		</table>
		</div>	
		<div class="overlay gym-overlay">
		  <i class="fa fa-refresh fa-spin"></i>
		</div>
	</div>
</section>