$(function(){
   $("input:text:visible:first").focus();
});
$('#contactForm').validator().on('submit', function (e) 
{

   //$('.alert-success').hide();
  // $('.alert-danger').hide();

   if (!e.isDefaultPrevented()) 
   {
      const $this    = $(this); 
      const action   = $this.attr('action');
      const formData = new FormData($this[0]); 

      $.LoadingOverlay("show", {
         background  : "rgba(1, 1, 1, 0)",
      });

      $('#submitButton').hide();

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
               // window.location.href = SITEURL+'/contact-us/confirmation';
                window.location.href =resp.url;

            }, 2000)
         }

         if (resp.status == 'error') 
         {
            toastr.error(resp.msg);
         }
      })
      .catch(function (error) 
      {
         $('#submitButton').show();
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