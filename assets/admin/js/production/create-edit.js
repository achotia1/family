$(document).ready(function () 
{
    // adding focus event to first field    
    //$('#product_code').focus();
    if(batch_id!=""){
        getBatchMaterials(batch_id);
    }
   	/*$(".quantity").blur(function(){
  		var $this = $(this);
  		console.log($this.attr('id'));
  		//var lotName = $('#aioConceptName').find(":selected").text();
  		
	});*/

    /*$('#batch_no').on('change', function() {
        var batch_id=this.value;
        getBatchMaterials(batch_id);
     });*/

})

function getBatchMaterials(batch_id){
    // console.log("getBactchMaterial");
    // console.log(batch_id);
    // return false;
    var action = ADMINURL + '/production/getBatchMaterials';

    axios.post(action, {batch_id:batch_id,material_id:material_id})
    .then(response => 
    {       
        // $("#material_id").empty(); 
        $("#material_id").html(response.data.html); 
    })
    .catch(error =>
    {

    })
    return false;
}

// submitting form after validation
$('#productionForm').validator().on('submit', function (e) 
{
    if (!e.isDefaultPrevented()) {

        const $this = $(this);
        const action = $this.attr('action');
        const formData = new FormData($this[0]);
        
        $('.box-body').LoadingOverlay("show", {
            background: "rgba(165, 190, 100, 0.4)",
        });

        axios.post(action, formData)
            .then(function (response) {
                const resp = response.data;

                if (resp.status == 'success') {
                    $this[0].reset();
                    toastr.success(resp.msg);
                    $('.box-body').LoadingOverlay("hide");
                    setTimeout(function () {
                        window.location.href = resp.url;
                    }, 2000)
                }

                if (resp.status == 'error') {
                    $('.box-body').LoadingOverlay("hide");
                    toastr.error(resp.msg);
                }
            })
            .catch(function (error) {
                $('.box-body').LoadingOverlay("hide");

                const errorBag = error.response.data.errors;
				//console.log(errorBag);
                $.each(errorBag, function (fieldName, value) {
                    $('.err_' + fieldName).closest('.form-group').addClass('has-error has-danger');
                    $('.err_' + fieldName).text(value[0]).closest('span').show();
                })
            });

        return false;
    }
});

function addPlan() 
{
	var items = parseInt($("#total_items").val()) + 1;	
	$("#total_items").val(items);
	var counter = items;	
	//var counter = $(".plan").length;	
	var plan_area = `<tr class="inner-td add_plan_area plan">                    
                    <td>
                    	<div class="form-group"> 
                        <select 
                            class="form-control my-select production_material" 
                            placeholder="All Materials"                            
                            name="production[${counter}][material_id]"
                            id="${counter}"
                            onchange="loadLot(this);"
                        >
                            <option value="">Select Material</option>
                            ${plan_options}
                        </select>
                        <span class="help-block with-errors">
                            <ul class="list-unstyled">
                                <li class="err_production[${counter}][material_id][] err_production_material"></li>
                            </ul>
                        </span>
                    </div>
                    </td>
                    <td>
                    	<div class="form-group"> 
                        <select 
                            class="form-control my-select production_lot" 
                            placeholder="Material Lots"
                            name="production[${counter}][lot_id]"
                            required
                            id="l_${counter}"
                            data-error="Material Lot field is required." 
                        >
                            <option value="">Select Lot</option>
                        </select>
                        <span class="help-block with-errors">
                            <ul class="list-unstyled">
                                <li class="err_production[${counter}][lot_id][] err_production_lot"></li>
                            </ul>
                        </span>
                    	</div>
                    </td>
                    <td><div class="add_quantity form-group">
                        <input 
                            type="number" 
                            class="form-control quantity"
                            name="production[${counter}][quantity]"
                            id="q_${counter}"
                            onblur="checkBal(this)"
                            step="any"                           
                        >
                        <span class="help-block with-errors">
                            <ul class="list-unstyled">
                                <li class="errq_${counter} err_production[${counter}][quantity][] err_quantity"></li>
                            </ul>
                        </span>
                    </div>                    
                    </td>
                    <td>
                    <p class="m-0 red bold deletebtn" style="display:block;cursor:pointer" onclick="return deletePlan(this,'add_plan_area')"  id="${counter}" style="cursor:pointer">Remove</p>              </td>
                    </tr>`;
    
    if($("#plan-table tr").length > 1){		
		$(plan_area).insertAfter($(".add_plan_area:last"));	
	} else {		
		$(plan_area).insertAfter($(".heading-tr:last"));	
	}
        
}
function deletePlan(element,className)
{
	$(element).closest('.'+className).find('*').attr('disabled', true);
	//$(element).closest('.'+className).hide();
	$(element).closest('.'+className).remove();
}
function loadLot(sel)
{    
    var id = $(sel).attr("id");    
    var material_id = sel.value;
    var action = ADMINURL + '/production/getMaterialLots';
	/* PASS PREVIOUS DROPDOWN SELECTED LOT NOs */
	var selected_val=[];
    $(".production_lot").each(function(){
        selected_val.push(this.value);
    });    
	/* END PASS PREVIOUS DROPDOWN SELECTED LOT NOs */
    axios.post(action, {material_id:material_id, selected_val:selected_val})
    .then(response => 
    {         
        //console.log(response.data.html);
        $("#l_"+id).html(response.data.html);
        
    })
    .catch(error =>
    {

    })
    return false;
}
function checkBal(txtQty)
{	
	var str = $(txtQty).attr("id");
	var qty = $(txtQty).val();
	var i = str.substring(2);
	var lotSelectId = "l_"+i;
	var qtyLimit = $( "#"+lotSelectId+" option:selected" ).attr('data-qty');	
	if(parseFloat(qty) > parseFloat(qtyLimit)){		
		$(".errq_"+i).closest('.form-group').addClass('has-error has-danger');
        $(".errq_"+i).text("You can not select more than available quantity "+qtyLimit).closest('span').show();
        $("#q_"+i).val("");
	}
	return false;
}
function checkBatch(batch)
{    
    var batch_id = $(batch).val();
    var product_id = $(batch).find('option:selected').attr('data-pid');
    var action = ADMINURL + '/production/getExistingBatch';
    axios.post(action, {batch_id:batch_id,product_id:product_id})
    .then(response => 
    {
        // var product = response.data.product;
        // $("#product_id").val(product);
        var url = response.data.url;
        if(url != '')
        	window.location.href = url;
        return false;
    })
    .catch(error =>
    {
         return false;
    })
    return false;
}

//*************************Wastage Plan Material*************************
function getWastageBatches(element)
{    
    // console.log('getWastageBatchesOrMaterials');
    var batch_id = $(element).val();
    var product_id = $(element).find('option:selected').attr('data-pid');
    var flag = 'loadbatch';

    var action = ADMINURL + '/production/getWastageBatchesOrMaterials';
    axios.post(action, {batch_id:batch_id,product_id:product_id,flag:flag})
    .then(response => 
    {

        wastage_batch_options = response.data.batchesHtml;
        $("#wastage_0").html(response.data.batchesHtml); 

        return false;
       
    })
    .catch(error =>
    {
         return false;
    })
    return false;
}

function loadWastageBatchMaterial(element)
{    
    var id = $(element).attr("id");   
    var wastage_batch_id = $(element).val();
    var product_id = $("#batch_id").find('option:selected').attr('data-pid');
    var flag = 'loadmaterial';

    var material_selected_val=[];
    $(".wastage_material").each(function(){
        var str = this.value;
        if(str){
            var res = str.split("||");
            material_selected_val.push(res[1]);
        }
    }); 
   // console.log(material_selected_val);

    var action = ADMINURL + '/production/getWastageBatchesOrMaterials';
    axios.post(action, {wastage_batch_id:wastage_batch_id,product_id:product_id,flag:flag,material_selected_val:material_selected_val})
    .then(response => 
    {

        $("#m_"+id).html(response.data.batchesMaterialHtml); 

        return false;
       
    })
    .catch(error =>
    {
         return false;
    })
    return false;
}

function setQuantityLimit(index)
{
    var qtyLimit = $( "#m_wastage_"+index+" option:selected" ).attr('data-qty');    

    $("#wastage_q_"+index).val("");
    $("#wastage_q_"+index).attr("min",1);
    $("#wastage_q_"+index).attr("max",qtyLimit);
    $("#wastageQuantityLimit_"+index).val(qtyLimit);
    $("#wastage_q_"+index).attr("data-error","You can not select more than available quantity:"+qtyLimit);
}

function addWastageStockPlan() 
{
    var items = parseInt($("#wastage_total_items").val()) + 1;  
    $("#wastage_total_items").val(items);
    var counter = items;    
    //var counter = $(".plan").length;  
    var add_wastage_area = `<tr class="inner-td add_wastage_area wastage">                    
                    <td>
                        <div class="form-group"> 
                        <select 
                            class="form-control my-select wastage_batch" 
                            placeholder="All Batches"             
                            name="wastage[${counter}][batch_id]"
                            id="wastage_${counter}"
                            onchange="loadWastageBatchMaterial(this);"
                        >${wastage_batch_options}</select>
                        <span class="help-block with-errors">
                            <ul class="list-unstyled">
                                <li class="err_wastage[${counter}][batch_id][] err_wastage_batch"></li>
                            </ul>
                        </span>
                    </div>
                    </td>
                    <td>
                        <div class="form-group"> 
                        <select 
                            class="form-control my-select wastage_material" 
                            placeholder="Material"
                            name="wastage[${counter}][material_id]"
                            id="m_wastage_${counter}"
                            onchange="setQuantityLimit(${counter});"
                            required
                            data-error="Material field is required." 
                        >
                            <option value="">Select Material</option>
                        </select>
                        <span class="help-block with-errors">
                            <ul class="list-unstyled">
                                <li class="err_wastage[${counter}][lot_id][] err_wastage_material"></li>
                            </ul>
                        </span>
                        </div>
                    </td>
                    <td>
                    <div class="wastage_add_quantity form-group">
                        <input 
                            type="number" 
                            class="form-control quantity"
                            name="wastage[${counter}][quantity]"
                            id="wastage_q_${counter}"
                            required
                            step="any" 
                            data-error="Quantity should be number."
                        >
                        <input 
                            type="hidden" 
                            id="wastageQuantityLimit_${counter}"
                            name="wastage[${counter}][wastageQuantityLimit]"
                            value="" 
                        >
                        <span class="help-block with-errors">
                            <ul class="list-unstyled">
                                <li class="errq_${counter} err_wastage[${counter}][quantity][] err_wastage_quantity"></li>
                            </ul>
                        </span>
                    </div>                   
                    </td>
                    <td>
                    <p class="m-0 red bold deletebtn" style="display:block;cursor:pointer" onclick="return deletePlan(this,'add_wastage_area')"  id="${counter}" style="cursor:pointer">Remove</p>              </td>
                    </tr>`;
    
    if($("#plan-table tr").length > 1){     
        $(add_wastage_area).insertAfter($(".add_wastage_area:last")); 
    } else {        
        $(add_wastage_area).insertAfter($(".heading-wastage-tr:last"));    
    }
    $(".add_wastage_area").validator();   
}

