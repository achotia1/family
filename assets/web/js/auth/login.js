$('#userLogin').validator().on('submit', function (e) 
  {
      if (!e.isDefaultPrevented()) 
     {
        const $this    = $(this); 
        const action   = $this.attr('action');
        const formData = new FormData($this[0]); 
        // console.log(action);
        //  console.log(formData);
        // return false;
        $.LoadingOverlay("show", {
           background  : "rgba(165, 190, 100, 0)",
        });

        axios.post(action,formData)
        .then(function (response) 
        {
           const resp =  response.data;
           $.LoadingOverlay("hide");
           if (resp.status == 'success') 
           {
              $this[0].reset();
              
              toastr.success(resp.msg);
              setTimeout(function()
              {
                  window.location.href = resp.url;

              }, 1500)
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