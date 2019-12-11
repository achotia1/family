$(document).ready(function () 
{
    // adding focus event to first field    
    //$('#product_code').focus();  
    $("#show-stock").hide();
})

// submitting form after validation
$('#batchForm').validator().on('submit', function (e) 
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
function checkStock(txtProd)
{	
	var product_id = $(txtProd).val();
	var selectedProduct = $(txtProd).find("option:selected").text();
	$('#spn_product').html('<b>'+selectedProduct+'</b>');
	var action = ADMINURL + '/rms-store/getAvailableStock';
	
    axios.post(action, {product_id:product_id})
    .then(response => 
    {
   		var html = response.data.html;
		$('#tblProduct tbody').html(html);
		$("#show-stock").show();
			
    })
    .catch(error =>
    {

    })
    return false;
}