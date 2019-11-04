/*
* @Author: sheshkumar
* @Date:   2019-05-17 16:41:25
* @Last Modified by:   sheshkumar
* @Last Modified time: 2019-05-27 22:29:42
*/

$(document).ready(function()
{
  getApprovedStateCredits();
})


function getApprovedStateCredits()
{
  
  var state_id  = $('#credit-in').val();
  var lecture_id = $('#credit-in').attr('data-course');

  var formData = new FormData();
  formData.append('state_id', state_id);
  formData.append('lecture_id', lecture_id);

  var action = SITEURL + '/my-account/getApprovedStateCreditsForLectures';
  $.LoadingOverlay("show",
  {
      background: "rgba(1, 1, 1, 0)",
  }); 

  axios.post(action,formData)
    .then(function (response) 
    {
      const resp = response.data;

      $('#total_credit').html(resp.total_credit);
      $('#credit_breakdown').html(resp.credit_breakdown);

      $.LoadingOverlay("hide");   
    })
    .catch(function (error) 
    {
      $.LoadingOverlay("hide");
    });

}

