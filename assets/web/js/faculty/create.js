$(function(){
   $("input:text:visible:first").focus();
});

function validateinput(element) 
{
    var test = element.value;
    length = test.split(' ').length;
    if(length > 100)
    {
        document.getElementById('about_lecture').disabled = true;
    }
    else
    {
        return true;
    }
}

$('#facultyForm').validator().on('submit', function (e) 
{
   //$('.alert-success').hide();
  // $('.alert-danger').hide();

   if (!e.isDefaultPrevented()) 
   {
      const $this    = $(this); 
      const action   = $this.attr('action');
      const formData = new FormData($this[0]); 

      $.LoadingOverlay("show", {
           background  : "rgba(165, 190, 100, 0)",
        });

      axios.post(action,formData)
      .then(function (response) 
      {
         const resp =  response.data;

         if (resp.status == 'success') 
         {
            $this[0].reset();
            $(':input').val('');
            $('#state_drop_bottom').next('div').children(":nth-child(2)").html('Select Practise Area');
            $('#state_drop_bottom').selectbox().val('');

            toastr.success(resp.msg);

            $.LoadingOverlay("hide");

            setTimeout(function()
            {
               // window.location.href = SITEURL+'/contact-us/confirmation';
                window.location.reload();

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