$(document).ready(function () 
{
    // adding focus event to first field
    $('input[name="name"]').focus();
})

$(document).on( 'change', 'input[type="file"]', function(e)
{
    var fileName = '';
    for(var i = 0 ; i < e.target.files.length ; i++)
    {
      if (i > 0) 
      {
        fileName += '<br/>'+e.target.files[i].name;
      }
      else
      {
        fileName += e.target.files[i].name;
      }
    }

    $(this).closest('.fileParentDiv').find('.file-upload-filename').html(fileName);
    // $(this).closest('.fileParentDiv').find('.removefile').show();  
    // $(this).closest('.fileParentDiv').find('.choosefile').hide(); 
})

function removeFile(element)
{
  $(element).closest('.fileParentDiv').find('input[type="file"]').val('');
  $(element).closest('.fileParentDiv').find('.file-upload-filename').html('No file Selected.');
  $(element).closest('.fileParentDiv').find('.removefile').hide();  
  $(element).closest('.fileParentDiv').find('.choosefile').show(); 
  $(element).closest('.fileParentDiv').find('.old_file').val(''); 
}


// submitting form after validation
$('#companyForm').validator().on('submit', function (e) 
{
    if (!e.isDefaultPrevented()) {

        const $this = $(this);
        const action = $this.attr('action');
        const formData = new FormData($this[0]);
        
        $('.card').LoadingOverlay("show", {
            background: "rgba(165, 190, 100, 0.4)",
        });

        axios.post(action, formData)
            .then(function (response) {
                const resp = response.data;

                if (resp.status == 'success') {
                    $this[0].reset();
                    toastr.success(resp.msg);
                    $('.card').LoadingOverlay("hide");
                    setTimeout(function () {
                        window.location.href = resp.url;
                    }, 2000)
                }

                if (resp.status == 'error') {
                    $('.card').LoadingOverlay("hide");
                    toastr.error(resp.msg);
                }
            })
            .catch(function (error) {
                $('.card').LoadingOverlay("hide");

                const errorBag = error.response.data.errors;

                $.each(errorBag, function (fieldName, value) {
                    $('.err_' + fieldName).closest('.form-group').addClass('has-error has-danger');
                    $('.err_' + fieldName).text(value[0]).closest('span').show();
                })
            });

        return false;
    }
})




