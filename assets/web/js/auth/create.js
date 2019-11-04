$(function() 
{
	 // var max_fields      = 10; //maximum input boxes allowed
   // 	var add_button      = $("#add_field_button"); //Add button 
   // 	var wrapper         = $("#input_fields_wrap"); //Fields wrapper
   // 	var append_html     = $("#append_html").html();
   //  var cnt_fields = 1; //initlal text box count
   //  //function addRegistrationFields() { //  }
   //  $("#add_field_button").on('click',function(){
   //    if(cnt_fields < max_fields){ //max input box allowed
   //      cnt_fields++; //text box increment
   //      $(wrapper).append('<div class="row d-flex close-div-re"><div class="f-col-6" id="input_fields_wrap>"'+append_html+'<button class=" close-div"></button></div></div>'); //add input box
   //      $(".drop-select-menu").selectbox();
   //    }
   //  });

  	// $(wrapper).on("click",".close-div", function(e){ //user click on remove text
   //    e.preventDefault(); $(this).parent('div').remove(); cnt_fields--;
   //  });

  $('#registerPage').validator().on('submit', function (e) 
  {
      if (!e.isDefaultPrevented()) 
     {
        const $this    = $(this); 
        const action   = $this.attr('action');
        const formData = new FormData($this[0]); 
        // console.log(action);
        //  console.log(formData);
        //return false;
        $.LoadingOverlay("show", {
           background  : "rgba(1, 1, 1, 0)",
        });

        axios.post(action,formData)
        .then(function (response) 
        {
           const resp =  response.data;

           if (resp.status == 'success') 
           {
              $this[0].reset();

              toastr.success(resp.msg);

              $.LoadingOverlay("hide");

              setTimeout(function()
              {
                  window.location.href = resp.url;

              }, 2000)
           }

           if (resp.status == 'error') 
           {
              toastr.error(resp.msg);
           }
        })
        .catch(function (error) 
        {
          
           $.LoadingOverlay("hide");

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
});

function addNewStateAndAttorny(element)
{
    $this = $(element);
    $this.hide();
    $(stateAndAttrny).insertAfter('.state_and_attorny:last');
    $(".school_admitted").selectbox();
    return false;
} 

function removeStateAndAttorny(element)
{
    $(element).closest('.state_and_attorny').remove();
    if($('.state_and_attorny').length == 1)
    {
      $('.add_field_button').show();
    }
    return false;
}

