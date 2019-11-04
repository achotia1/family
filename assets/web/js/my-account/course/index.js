/*
* @Author: sheshkumar
* @Date:   2019-05-17 16:41:25
* @Last Modified by:   sheshkumar
* @Last Modified time: 2019-05-21 17:33:52
*/

$(document).ready(function()
{
	getOnlineCourses();
})

function getOnlineCourses()
{
	$state_id = $('#states').val();
	$credit_type = $('#credit_type').val();
	$practice_area = $('#practice_area').val();

	var formData = new FormData();
	formData.append('state_id', $state_id);
	formData.append('credit_type', $credit_type);
	formData.append('practice_area', $practice_area);

	var action = SITEURL + '/my-account/getOnlineCourses';
	$.LoadingOverlay("show",
    {
        background: "rgba(1, 1, 1, 0)",
    }); 

	axios.post(action,formData)
  	.then(function (response) 
    {
    	const resp = response.data;

    	$('.main-div-wrapper').html(resp);

    	$('#states').selectbox();
    	$('#credit_type').selectbox();
    	$('#practice_area').selectbox();

     	$.LoadingOverlay("hide"); 	
  	})
  	.catch(function (error) 
  	{
        $.LoadingOverlay("hide");
  	});
}