$(document).ready(function()
{
    checkBillingInfoCouponStatus();
});

function checkBillingInfoCouponStatus()
{
    var action = SITEURL + '/cart/checkUserCouponStatus';
    axios.get(action)
        .then(function (response) 
        {
            if(response.data.msg != '')
            {
                toastr.error(response.data.msg)
            }
            
        })
        .catch(function (error) 
        {
        });

    return false;
}

function changeStateDiv()
{
	var county = $('#my-profile-country').val();
	if (county == '231') 
	{
		$('.us_section').removeClass('hide');
		$('.non_us_section').addClass('hide');
	}
	else
	{
		$('.us_section').addClass('hide');
		$('.non_us_section').removeClass('hide');
	}
}

// submitting form after validation
var billingType = '';
function submitBillingForm(type)
{
    billingType = type;
	$('#billingInformationForm').submit();
}

$('#billingInformationForm').validator().on('submit', function (e) 
{
    if (!e.isDefaultPrevented()) 
    {

        const $this = $(this);
        const action = $this.attr('action')+'/'+billingType;
        const formData = new FormData($this[0]);
        formData.append('payment', billingType);

        $.LoadingOverlay("show", {
            background: "rgba(165, 190, 100, 0)",
        });

        axios.post(action, formData)
            .then(function (response) 
            {
                const resp = response.data;
                if (resp.status == 'success') 
                {
                    if (billingType = 'paypal') 
                    {
                        window.location.href = resp.url;
                    }
                }

                if(resp.status == 'error')
                {
                    toastr.error(resp.msg)
                }
                    
                setTimeout(function()
                {
                    $.LoadingOverlay("hide");
                },3000);
            })
            .catch(function (error) 
            {
                $.LoadingOverlay("hide");
                const errorBag = error.response.data.errors;
                $.each(errorBag, function (fieldName, value) {
                    $('.err_' + fieldName).closest('.form-group').addClass('has-error has-danger');
                    $('.err_' + fieldName).text(value[0]).closest('span').show();
                })
            });

        return false;
    }
})

function payWithPaypal()
{
	$.LoadingOverlay("show", 
	{
        background: "rgba(165, 190, 100, 0)",
    });

	action = SITEURL + '/cart/billing/paypal'
	axios.post(action)
        .then(function (response) 
        {
            const resp = response.data;
            if (resp.status == 'success') 
            {
            	window.location.href = resp.url;
            }
            else
            {
            	toastr.error(resp.msg);
            }
            
            $.LoadingOverlay("hide");
            
        })
        .catch(function (error) 
        {
            $.LoadingOverlay("hide");
            toastr.error('Something went wrong, Please try again later.');
        });

    return false;
}
