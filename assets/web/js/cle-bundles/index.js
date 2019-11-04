var action 	= SITEURL+'/cle-bundles/getRecords';

$(document).ready(function()
{
	// loading records
	var state_id = $('#stae_drop_head').val();
	getAllData(action, state_id);
	
});

// filtering records via state
$(document).on("change", "#stae_drop_head",function()
{ 
   	var state_id	= this.value;
    getAllData(action,state_id);
});


function getAllData(action, state_id)
{
	$.LoadingOverlay("show", 
	{
	     background  : "rgba(1, 1, 1, 0)",
	});
	
	axios.post(action,{state_id:state_id})
	.then(function (response) 
	{
	    const resp = response.data;
	    // console.log(resp);
	    $.LoadingOverlay("hide");
	    $('#listing-cle-bundles').empty();
	    $('#listing-cle-bundles').append(resp.html);

	    $('html, body').animate({ scrollTop: 0 }, '1000');

	})
	.catch(function (error) 
	{
		$.LoadingOverlay("hide");
	});
}

$(document).on('click', '.pagination-links a', function()
{
    var state_id	= $("#stae_drop_head").val();
	var action = $(this).attr('href');
    getAllData(action,state_id);
	return false;
});

