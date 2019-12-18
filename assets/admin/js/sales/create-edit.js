$(document).ready(function () 
{
    $('.datepicker').datepicker({
      autoclose: true,
      format: 'dd-mm-yyyy',
      // startDate: new Date()
    })
    /*var plan_options='';
    $('#plan_id').on('change', function() {
        var plan_id=this.value;
        
        checkExistingRecord(plan_id);
        getPlanMaterials(plan_id);
        
     });
     if(plan_id!=""){
        getPlanMaterials(plan_id);
    }*/

})

function addPlan() 
{
    // var counter = $(".plan").length;    
    var items = parseInt($("#total_items").val()) + 1;
    $("#total_items").val(items);
    var counter = items;    
    var plan_area = `<tr class="inner-td add_plan_area plan">                    
                    <td>
                        <div class="form-group"> 
                        <select 
                            class="form-control my-select products" 
                            placeholder="All Products"
                            name="sales[${counter}][product_id]"
                            id="product_${counter}"
                            required
                            onchange="loadBatches(this);"
                            data-error="Product field is required." 
                        >
                            ${plan_options}
                        </select>
                        <span class="help-block with-errors">
                            <ul class="list-unstyled">
                                <li class="err_sales[${counter}][product_id][] err_production_product"></li>
                            </ul>
                        </span>
                    </div>
                    </td>
                    <td>
                        <div class="form-group"> 
                        <select 
                            class="form-control my-select batch_id" 
                            placeholder="All Batches"
                            name="sales[${counter}][batch_id]"
                            onchange="setQuantityLimit(${counter});"
                            id="batches_product_${counter}"
                            required
                            data-error="Batch field is required." 
                        >
                            <option value="">Select Batch</option>
                        </select>
                        <span class="help-block with-errors">
                            <ul class="list-unstyled">
                                <li class="err_sales[${counter}][batch_id][] err_batch_id"></li>
                            </ul>
                        </span>
                       </div>
                    </td>
                    <td>
                    <div class="add_quantity form-group">
                        <input 
                            type="number" 
                            class="form-control quantity"
                            name="sales[${counter}][quantity]" 
                            id="quantity_${counter}"
                            required
                            step="any" 
                            data-error="Quantity should be number."
                        >
                        <input 
                            type="hidden" 
                            class="form-control quantity"
                            name="sales[${counter}][quantityLimit]"
                            id="quantityLimit_${counter}"
                            value="" 
                        >
                        <span class="help-block with-errors">
                            <ul class="list-unstyled">
                                <li class="err_sales[${counter}][quantity][] err_quantity"></li>
                            </ul>
                        </span>
                    </div>
                    </td>
                    <td>
                    <div class="add_rate form-group">
                        <input 
                            type="number" 
                            class="form-control rate"
                            name="sales[${counter}][rate]" 
                            id="rate_${counter}"
                            required
                            step="any" 
                            data-error="Rate should be number."
                        >
                        <span class="help-block with-errors">
                            <ul class="list-unstyled">
                                <li class="err_sales[${counter}][rate][] err_rate"></li>
                            </ul>
                        </span>
                    </div>
                    </td>
                    <td>
                    <p class="m-0 red bold deletebtn" style="display:block;cursor:pointer" onclick="return deletePlan(this)"  id="${counter}" style="cursor:pointer">Remove</p>              </td>
                    </tr>`;
    // $(plan_area).insertAfter($(".add_plan_area:last"));    
    if($("#plan-table tr").length > 1){     
        $(plan_area).insertAfter($(".add_plan_area:last")); 
    } else {        
        $(plan_area).insertAfter($(".heading-tr:last"));    
    }
    $(".add_plan_area").validator();
    
}

function deletePlan(element)
{
    $(element).closest('.add_plan_area').find('*').attr('disabled', true);
   // $(element).closest('.add_plan_area').hide();
   $(element).closest('.add_plan_area').remove();
}

function setQuantityLimit(index)
{
    var qtyLimit = $( "#batches_product_"+index+" option:selected" ).attr('data-qty');    

    $("#quantity_"+index).val("");
    $("#quantity_"+index).attr("min",1);
    $("#quantity_"+index).attr("max",qtyLimit);
    $("#quantityLimit_"+index).val(qtyLimit);

    $("#quantity_"+index).attr("data-error","You can not select more than available quantity:"+qtyLimit);
}

function loadBatches(sel)
{    
    var id = $(sel).attr("id");   
    var product_id = sel.value;
    // var plan_id = $("#plan_id").val();
    var selected_val=[];
    $(".batch_id").each(function(){
        if(this.value!=""){
            selected_val.push(this.value);
        }
    });  
    $('.box-body').LoadingOverlay("show", {
        background: "rgba(165, 190, 100, 0.4)",
    });

    var action = ADMINURL + '/sales/getProductBatches';
    axios.post(action, {sale_invoice_id:sale_invoice_id,editFlag:editFlag,product_id:product_id,selected_val:selected_val})
    .then(response => 
    { 
        $("#batches_"+id).html(response.data.html); 
        $('.box-body').LoadingOverlay("hide");
    })
    .catch(error =>
    {
        $('.box-body').LoadingOverlay("hide");
    })
    return false;
}

// submitting form after validation
$('#salesForm').validator().on('submit', function (e) 
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

                $.each(errorBag, function (fieldName, value) {
                    $('.err_' + fieldName).closest('.form-group').addClass('has-error has-danger');
                    $('.err_' + fieldName).text(value[0]).closest('span').show();
                })
            });

        return false;
    }
})

