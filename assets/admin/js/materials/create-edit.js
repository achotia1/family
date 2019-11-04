$(document).ready(function () 
{
    // adding focus event to first field    
    $('input[name="name"]').focus();
    /*$('.datepicker').datepicker({
      autoclose: true,
      format: 'dd-mm-yyyy',
      // startDate: new Date()
    })*/
    $(".cls-unit-price, .cls-total-qty").blur(function(){    	
    	var total_qty = parseFloat($('.cls-total-qty').val());
    	var unit_price = parseFloat($('.cls-unit-price').val());
    	var total_price = unit_price * total_qty;
    	total_price = total_price.toFixed(2);
    	if(!isNaN(total_price))			
			$('.cls-total-price').val(total_price);    		
	});
})

// submitting form after validation
$('#materialForm').validator().on('submit', function (e) 
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