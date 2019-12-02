$(document).ready(function () 
{
    // adding focus event to first field    
    //$('#product_code').focus();  
    
})

// submitting form after validation
$('#reviewBatchForm').validator().on('submit', function (e) 
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
});
function sendToBilling(element)
{
	if($('#chk-status').prop("checked") == false){
		swal("Error",'Please check the confirmation checkbox.','error');
      	return false; 
	} else {	
		var material_out_id = $(element).attr("id");
		var batch_id = $(element).attr("data-batch");
		var product_id = $(element).attr("data-product");
		var cost = $(element).attr("data-cost");
		var quantity = $(element).attr("data-quantity");		
		var course = $(element).attr("data-course");
		var rejection = $(element).attr("data-rejection");
		var dust = $(element).attr("data-dust");
		var loose = $(element).attr("data-loose");
		
		action = ADMINURL+'/materials-out/send-to-sale';
		
		swal({
			title: "Are you sure !!",
			text: "You want to add this batch to Sales Stock ?",
			type: "warning",
			showCancelButton: true,
			confirmButtonText: "Send",
			confirmButtonClass: "btn-info",
			closeOnConfirm: false,
			showLoaderOnConfirm: true
	    }, 
	    function () 
	    { 
	        axios.post(action, {id:material_out_id, batch_id:batch_id, product_id:product_id,quantity:quantity, cost:cost, course:course, rejection:rejection, dust:dust, loose:loose})
	        .then(function (response) 
	        {
	          if (response.data.status == 'success') 
	          {
	            swal("Success",response.data.msg,'success');
	            $('#send-section').remove();
	            $('#send-chk').remove();            
	          }
	          if (response.data.status === 'error') 
	          {
	            swal("Error",response.data.msg,'error');                
	          }
	        })
	        .catch(function (error) 
	        {
	           // swal("Error",error.response.data.msg,'error');
	        }); 
	    });
    }
}