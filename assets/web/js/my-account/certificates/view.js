/*
* @Author: sheshkumar
* @Date:   2019-05-29 17:15:24
* @Last Modified by:   sheshkumar
* @Last Modified time: 2019-05-30 11:56:20
*/

$(document).ready(function()
{
	$('#credit-in').trigger('change');	
})

function getCertificateViaState(eval_id)
{
	var action = SITEURL + '/my-account/getCertificateViaState'
	var state_abbr =  $('#credit-in').val();

	var formData = new FormData();
	formData.append('eval_id',eval_id);
	formData.append('state_abbr',state_abbr);

	$.LoadingOverlay("show",{
		 background: "rgba(165, 190, 100, 0)"
	});

	axios.post(action, formData)
    .then(function (response) 
    {
        const resp = response.data;
        $('.certificate_wrapper').html(resp);
        $.LoadingOverlay("hide");
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
}


function viewCertificate(eval_id, state_abbr)
{
	var action = SITEURL + '/my-account/viewCertificateFile'

	var formData = new FormData();
	formData.append('eval_id',eval_id);
	formData.append('state_abbr',state_abbr);

	axios.post(action, formData)
    .then(function (response) 
    {
        const resp = response.data;
    })
    .catch(function (error) 
    {
    	
    });
}

function downloadCertificate(eval_id, state)
{
	alert('download');
}