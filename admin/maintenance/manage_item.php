<?php
require_once('../../config.php');
if(isset($_GET['id']) && $_GET['id'] > 0){
    $qry = $conn->query("SELECT * from `item_list` where id = '{$_GET['id']}' ");
    if($qry->num_rows > 0){
        foreach($qry->fetch_assoc() as $k => $v){
            $$k=$v;
        }
    }
}
?>
<div class="container-fluid">
	<form action="" id="item-form">
		<input type="hidden" name ="id" value="<?php echo isset($id) ? $id : '' ?>">
		
		<div class="row">
		<div class="form-group col-8">
			<label for="name" class="control-label">Name</label>
			<input type="text" name="name" id="name" class="form-control rounded-0" value="<?php echo isset($name) ? $name : ''; ?>">
		</div>
		<div class="form-group col-4">
			<label for="cost" class="control-label">Cost ₹</label>
			<input type="number" name="cost" id="cost" step="any" class="form-control rounded-0 text-end" value="<?php echo isset($cost) ? $cost : '0'; ?>">
		</div>
	

		</div>
		<div class="form-group">
			<label for="description" class="control-label">Description</label>
			<textarea name="description" id="description" cols="30" rows="2" class="form-control form no-resize"><?php echo isset($description) ? $description : ''; ?></textarea>
		</div>
		<div class="row">
		<div class="form-group col-6">
			<label for="cost" class="control-label">Unit</label>
			<input type="text" name="unit" id="unit" step="any" class="form-control rounded-0 text-end" value="<?php echo isset($unit) ? $unit : ''; ?>">
		</div>
		<div class="form-group col-6">
			<label for="supplier_id" class="control-label">Supplier</label>
			<input name="supplier_id" type="hidden" value="4" alt="remove it when use dynamically selection" >
			<select name="supplier_id_when use dynamically enable it"  id="supplier_id" class="custom-select select2" disabled>
			<option <?php echo !isset($supplier_id) ? 'selected' : '' ?> disabled></option>
			<?php 
			$supplier = $conn->query("SELECT * FROM `supplier_list` where status = 1 order by `name` asc");
			while($row=$supplier->fetch_assoc()):
			?>
		<!-- id=4 for universal supplier account -->
			<option value="<?php echo $row['id'] ?>" <?php echo isset($supplier_id) ? ($supplier_id == $row['id'] ? "selected" : "") : ($row['id'] == 4 ? "selected" : "") ?>><?php echo $row['name'] ?></option>
			<?php endwhile; ?>
			</select>
		</div>
		
		</div>
		<div class="row">
		<div class="form-group col-6">
			<label for="cost" class="control-label">HSN</label>
			<input type="text" name="hsn" id="hsn" step="any" class="form-control rounded-0 text-end" value="<?php echo isset($hsn) ? $hsn : ''; ?>">
		</div>
		<div class="form-group col-6">
			<label for="cost" class="control-label">GST %</label>
			<input type="number" name="gst_percent" id="gst_percent" step="any" class="form-control rounded-0 text-end" value="<?php echo isset($gst_percent) ? $gst_percent : ''; ?>">
		</div>
		</div>
		<div class="form-group">
			<label for="status" class="control-label">Status</label>
			<input name="status" value="1" type="hidden" alt="remove it when use dynamically selection" >
			<select name="status_currently_aaded_hidden_input_to_make_disable" id="status" class="custom-select selevt" disabled>
			<option value="1" <?php echo isset($status) && $status == 1 ? 'selected' : '' ?>>Active</option>
			<option value="0" <?php echo isset($status) && $status == 0 ? 'selected' : '' ?>>Inactive</option>
			</select>
		</div>
	</form>
</div>
<script>
  
	$(document).ready(function(){
        $('.select2').select2({placeholder:"Please Select here",width:"relative"})
		$('#item-form').submit(function(e){
			e.preventDefault();
            var _this = $(this)
			 $('.err-msg').remove();
			start_loader();
			$.ajax({
				url:_base_url_+"classes/Master.php?f=save_item",
				data: new FormData($(this)[0]),
                cache: false,
                contentType: false,
                processData: false,
                method: 'POST',
                type: 'POST',
                dataType: 'json',
				error:err=>{
					console.log(err)
					alert_toast("An error occured",'error');
					end_loader();
				},
				success:function(resp){
					if(typeof resp =='object' && resp.status == 'success'){
						location.reload();
					}else if(resp.status == 'failed' && !!resp.msg){
                        var el = $('<div>')
                            el.addClass("alert alert-danger err-msg").text(resp.msg)
                            _this.prepend(el)
                            el.show('slow')
                            end_loader()
                    }else{
						alert_toast("An error occured",'error');
						end_loader();
                        console.log(resp)
					}
				}
			})
		})
	})
</script>