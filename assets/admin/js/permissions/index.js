 $(document).ready(function() {
 	$('ul.notice-lists.list-group.list-checkbox li').each(function(i) {
        $(this).find('.checkbox-container input').change(function() {
            if ($(this).prop("checked") == true) {
                $(this).parents('li').find('.action-wrapper').addClass('active');
            } else {
                // $(this).parents().find  ('.action-wrapper').removeClass('active');
                console.log($(this).parents('li').find('.action-wrapper').removeClass('active'));
            }
        })
    });
    $(".sub-menu").change(function() {
        var text = '';
        if ($(this).find("em").text() === "on") {
            text = 'off';
        } else {
            text = 'on';
        }
        $(this).find("em").text(text);
    });
 });

$('#permissionsForm').validator().on('submit', function (e) 
{
   	if (!e.isDefaultPrevented()) 
   	{
      	const $this    = $(this); 
  		const action   = $this.attr('action');
  		const formData = new FormData($this[0]); 

	  	$('.card').LoadingOverlay("show", {
	   	     background  : "rgba(165, 190, 100, 0.4)",
	  	});

	  	axios.post(action,formData)
	  	.then(function (response) 
	  	{
	 		const resp =  response.data;

	     	if (resp.status == 'success') 
	     	{
	            toastr.success(resp.msg);
	            $('.card').LoadingOverlay("hide");
	     	}

	     	if (resp.status == 'error') 
	     	{
	     		$('.card').LoadingOverlay("hide");
	        	toastr.error(resp.msg);
	     	}
	  	})
	  	.catch(function (error) 
	  	{
	        $('.card').LoadingOverlay("hide");

	        $.each(errorBag, function(fieldName, value) 
	        {
	        	$('.err_'+fieldName).closest('div').addClass('has-error has-danger'); 
	        	$('.err_'+fieldName).text(value[0]).closest('span').show(); 
	        })
	  	});

  		return false;
   	}
})

$('#AddRoleForm').validator().on('submit', function (e) 
{
   	if (!e.isDefaultPrevented()) 
   	{
      	const $this    = $(this); 
  		const action   = $this.attr('action');
  		const formData = new FormData($this[0]); 

	  	$($this).closest('.modal-content').LoadingOverlay("show", {
     		background  : "rgba(165, 190, 100, 0.2)",
      	});

	  	axios.post(action,formData)
	  	.then(function (response) 
	  	{
	 		const resp =  response.data;

	     	if (resp.status == 'success') 
	     	{
	     		$this[0].reset()
	            toastr.success(resp.msg);
	            $($this).closest('.modal-content').LoadingOverlay("hide");

	            setTimeout(function()
	            {
	            	$('#addRole').modal('hide');
	            	setTimeout(function()
		            {
		     			location.reload();

		            },1000)

	            },1000)
	     	}

	     	if (resp.status == 'error') 
	     	{
	     		$($this).closest('.modal-content').LoadingOverlay("hide");
	        	toastr.error(resp.msg);
	     	}
	  	})
	  	.catch(function (error) 
	  	{
	        $($this).closest('.modal-content').LoadingOverlay("hide");

	        const errorBag = error.response.data.errors;
	        $.each(errorBag, function(fieldName, value) 
	        {
	        	$('.err_'+fieldName).closest('div').addClass('has-error has-danger'); 
	        	$('.err_'+fieldName).text(value[0]).closest('span').show(); 
	        })
	  	});

  		return false;
   	}
})

function getPermissions(element)
{
	$this = $(element);

	// remove all checkboex
	$('.checkbox-permissions').each( function(){
		$(this).prop( "checked", false);
		$(this).closest('.sub-menu').find("em").text('off');

	})

	// variables
	$id = $this.val(); 
	
	// formdata
	const formData = new FormData();
	formData.append('id', $id);

	// action 
	const action = ADMINURL+'/permissions/byRole';

	axios.post(action,formData)
	.then(response => {
		
		if(response.data.object.length !== 0)
		{
			$object = response.data.object;

			$.each($object, function(key, value)
			{
				var permission = value.permissions.name;

				var element = $('#permission-'+permission);

				$(element).prop( "checked", true);


				// set text 
			  	var text = '';
		        if ($(element).closest('.sub-menu').find("em").text() === "on") 
		        {
		            text = 'off';
		        } 
		        else 
		        {
		            text = 'on';
		        }

		        $(element).closest('.sub-menu').find("em").text(text);

			})
		}

	})
	.catch(error => {
		console.log(error);
	})
}

