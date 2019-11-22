$(document).ready(function () 
{
    $('.datepicker').datepicker({
      autoclose: true,
      format: 'dd-mm-yyyy',
      // startDate: new Date()
    })
    var plan_options='';
    $('#batch_id').on('change', function() {
        var batch_id=this.value;
        
        checkBatch(batch_id);
        getBatchMaterials(batch_id);
        
     });



    if(batch_id!=""){
        getBatchMaterials(batch_id);
    }
    
})

function getBatchMaterials(batch_id){
    var action = ADMINURL + '/return/getBatchMaterials';
    axios.post(action, {batch_id:batch_id})
    .then(response => 
    {       
        // $("#material_id").empty(); 
        plan_options=response.data.html;
        $("#material_"+index).html(response.data.html); 
    })
    .catch(error =>
    {

    })
    return false;
}

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
                            class="form-control my-select production_material" 
                            placeholder="All Materials"                            
                            name="returned[${counter}][material_id]"
                            id="material_${counter}"
                            onchange="loadLot(this);"
                        >
                            ${plan_options}
                        </select>
                        <span class="help-block with-errors">
                            <ul class="list-unstyled">
                                <li class="err_returned[${counter}][material_id][] err_production_material"></li>
                            </ul>
                        </span>
                    </div>
                    </td>
                    <td>
                        <div class="form-group"> 
                        <select 
                            class="form-control my-select production_lot" 
                            placeholder="Material Lots"
                            name="returned[${counter}][lot_id]"
                            required
                            onchange="setQuantityLimit(${counter});"
                            id="lot_material_${counter}"
                            data-error="Material Lot field is required." 
                        >
                            <option value="">Select Lot</option>
                        </select>
                        <span class="help-block with-errors">
                            <ul class="list-unstyled">
                                <li class="err_returned[${counter}][lot_id][] err_production_lot"></li>
                            </ul>
                        </span>
                        </div>
                    </td>
                    <td><div class="add_quantity form-group">
                        <input 
                            type="number" 
                            class="form-control quantity"
                            name="returned[${counter}][quantity]"
                            id="quantity_${counter}"
                            step="any"                           
                        >
                        <span class="help-block with-errors">
                            <ul class="list-unstyled">
                                <li class="err_returned[${counter}][quantity][] err_quantity"></li>
                            </ul>
                        </span>
                    </div>                    
                    </td>
                    <td>
                    <p class="m-0 red bold deletebtn" style="display:block;cursor:pointer" onclick="return deletePlan(this)"  id="${counter}" style="cursor:pointer">Remove</p>              </td>
                    </tr>`;
    $(plan_area).insertAfter($(".add_plan_area:last"));    
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
    var qtyLimit = $( "#lot_material_"+index+" option:selected" ).attr('data-qty');    

    $("#quantity_"+index).val("");
    $("#quantity_"+index).attr("min",1);
    $("#quantity_"+index).attr("max",qtyLimit);
    $("#quantity_"+index).attr("data-error","You can not select more than available quantity:"+qtyLimit);
}

function loadLot(sel)
{    
    var id = $(sel).attr("id");   
    var material_id = sel.value;
    var batch_id = $("#batch_id").val();

    var selected_val=[];
    $(".production_lot").each(function(){
        if(this.value!=""){
            selected_val.push(this.value);
        }
    });  

    var action = ADMINURL + '/return/getMaterialLots';

    axios.post(action, {batch_id:batch_id,material_id:material_id,selected_val:selected_val})
    .then(response => 
    { 
        $("#lot_"+id).html(response.data.html); 

        //$("#lot_"+id).html(response.data.html); 
    })
    .catch(error =>
    {

    })
    return false;
}

// submitting form after validation
$('#returnForm').validator().on('submit', function (e) 
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

function checkBatch(batch_id)
{    
    //var batch_id = $(batch).val();
    var action = ADMINURL + '/return/getExistingBatch';

    axios.post(action, {batch_id:batch_id})
    .then(response => 
    {
        // console.log(response);
        // return false;
        //var product = response.data.product;
       // $("#product_id").val(product);
        var url = response.data.url;
        if(url != ''){
            window.location.href = url;
        }
    })
    .catch(error =>
    {

    })
    return false;
}