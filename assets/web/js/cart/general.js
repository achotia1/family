// add to cart functionlity
function addToCart(element,state_id,asset_id,asset_type)
{
	$this = $(element);

	$.LoadingOverlay("show",
	{
		background: "rgba(1, 1, 1, 0)",
	});

	// data
	data = new FormData();
	data.append('state_id', state_id);
	data.append('asset_id', asset_id);
	data.append('asset_type', asset_type);

	// action
	var action = SITEURL+'/cart/add';

	axios.post(action, data)
	.then(function (response) 
	{
		const resp = response.data;
		$.LoadingOverlay("hide");
		if (resp.status == 'success') 
		{
			// if multiple buttons then toggle them as well
			if ($this.closest('div').hasClass('multiple_buttons')) 
			{
				$('.multiple_buttons').find('.cart-add-button').hide();
				$('.multiple_buttons').find('.cart-delete-button').show();
			}
			else
			{
				$this.closest('div').find('.cart-add-button').hide();
				$this.closest('div').find('.cart-delete-button').show();
			}

			// check has attribute
			var cart = $this.closest('div').attr('data-cart');
			if (typeof cart !== typeof undefined && cart !== false) 
			{
				$('.'+cart).find('.cart-add-button').hide();
				$('.'+cart).find('.cart-delete-button').show();
			}
			else
			{
				$this.closest('div').find('.cart-add-button').hide();
				$this.closest('div').find('.cart-delete-button').show();
			}

			// update cart count 
			$('#cart_count').html(resp.cart.cart_count);

			// web cart items count
			$('.web-cart-items-count').html(resp.cart.web_cart_items_count);

			// web cart items html
			$('.web-cart-items').html(resp.cart.web_cart_items);

			// web cart items count price
			$('.web-cart-items-count-price').html(resp.cart.web_cart_items_count_price);

			$('.web-empty-cart').hide();
			$('.web-cart').show();
			$('.web-cart').addClass('active');	
		}
		
	})
	.catch(function (error) {
		$.LoadingOverlay("hide");
	});
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
					if ($this.closest('div').hasClass('cart_page')) 
					{
						$this.closest('.cart-page-item').removeClass('d-flex');
						$this.closest('.cart-page-item').hide();
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