$(document).ready(function()
{
	getCartRecords(true);
	$('#userApplyCouponForm').validator();
})

function getCartRecords($loging)
{
	const action = SITEURL+'/cart/getCartRecords';

	if ($loging) 
	{
	    $.LoadingOverlay("show", {
	        background: "rgba(165, 190, 100, 0)",
	    });
	}

    axios.get(action)
    .then(function (response) 
    {
        const resp = response.data;
    	console.log(resp);
        $('.cart-page-details-section').html(resp.cart_data);
    	$.LoadingOverlay("hide");
    	
        if (resp.msg != '') 
        {
        	toastr.error(resp.msg);
        }
    })
    .catch(function (error) 
    {
        $.LoadingOverlay("hide");
    });

    return false;
}

// remove to cart functionlity
function removeFromCart(element,state_id,asset_id,asset_type)
{
	// data
	$this = $(element);

	swal(
	{
      	title: "Are you sure !!",
      	text: "You want to remove item ?",
      	type: "warning",
      	showCancelButton: true,
      	confirmButtonText: "Remove",
      	confirmButtonClass: "btn-warning",
      	closeOnConfirm: false,
      	showLoaderOnConfirm: true
    }, 
    function () 
    {
		// data
		data = new FormData();
		data.append('state_id', state_id);
		data.append('asset_id', asset_id);
		data.append('asset_type', asset_type);

		// action
		var action = SITEURL+'/cart/remove';

		axios.post(action, data)
		.then(function (response) 
		{
			const resp = response.data;

			if (resp.status == 'success') 
			{
				getCartRecords(false);

				swal(
				{
			      	title: "Success",
			      	text: "Item removed successfully",
			      	type: "success",
			      	confirmButtonText: "Ok",
			      	confirmButtonClass: "btn-success",
			      	closeOnConfirm: true,
			    }, 
			    function () 
			    {
			    	// update cart count 
					$('#cart_count').html(resp.cart.cart_count);

					// web cart items count
					$('.web-cart-items-count').html(resp.cart.web_cart_items_count);

					// web cart items count
					$('.web-cart-items').html(resp.cart.web_cart_items);

					// web-cart-items-count-price
					$('.web-cart-items-count-price').html(resp.cart.web_cart_items_count_price);

					if(resp.cart.cart_count == '0') 
					{
						//  if 0 record then show empty cart and hide cart
						$('.web-cart').removeClass('active');	
						$('.web-cart').hide();
						$('.web-empty-cart').show();
						// $('.web-empty-cart').addClass('active');
					}
					else
					{
						// show cart div
						$('.web-empty-cart').removeClass('active');	
						$('.web-empty-cart').hide();
						$('.web-cart').show();
						$('.web-cart').addClass('active');	
					}

					// if multiple buttons then toggle them as well
					if ($this.closest('div').hasClass('multiple_buttons')) 
					{
						$('.multiple_buttons').find('.cart-delete-button').hide();
						$('.multiple_buttons').find('.cart-add-button').show();
					}
					else
					{
						$this.closest('div').find('.cart-delete-button').hide();
						$this.closest('div').find('.cart-add-button').show();
					}
			    });
			}
			else
			{
				swal('error', 'Something went wrong while removing item. Please try again later', 'error');
			}
		})
		.catch(function (error) 
		{
			swal('error', 'Something went wrong while removing item. Please try again later', 'error');
		});	

    });
}

$(document).on('submit','#userApplyCouponForm', function (e) 
{
	if (!e.isDefaultPrevented()) 
    {
		const $this = $(this);
	    const action = $this.attr('action');
	    const formData = new FormData($this[0]);

	    $.LoadingOverlay("show", {
	        background: "rgba(165, 190, 100, 0)",
	    });

	    axios.post(action, formData)
        .then(function (response) 
        {
            const resp = response.data;
            if (resp.status == 'success')
            {
            	getCartRecords(true);
            	toastr.success(resp.msg);
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
            const errorBag = error.response.data.errors;
            $.each(errorBag, function (fieldName, value)
            {
                $('.err_' + fieldName).closest('.form-group').addClass('has-error has-danger');
                $('.err_' + fieldName).text(value[0]).closest('span').show();
            })
        });
    }

    return false;
})