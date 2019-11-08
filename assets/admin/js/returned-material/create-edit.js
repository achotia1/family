$(document).ready(function () 
{
    $('.datepicker').datepicker({
      autoclose: true,
      format: 'dd-mm-yyyy',
      // startDate: new Date()
    })
    $('#batch_no').on('change', function() {
        var batch_id=this.value;
        getBatchMaterials(batch_id);
     });

    if(batch_id!=""){
        getBatchMaterials(batch_id);
    }
    
})

function getBatchMaterials(batch_id){
    // console.log("getBactchMaterial");
    // console.log(batch_id);
    // return false;
    var action = ADMINURL + '/return/getBatchMaterials';

    axios.post(action, {batch_id:batch_id,material_id:material_id})
    .then(response => 
    {       
        // $("#material_id").empty(); 
        $("#material_id").html(response.data.html); 
    })
    .catch(error =>
    {

    })
    return false;
}


// submitting form after validation
$('#returnForm').validator().on('submit', function (e) 
{
    if (!e.isDefaultPrevented()) {

        const $this = $(this);
        const action = $this.attr('action');
        const formData = new FormData($this[0]);
        
        $('.box-body').LoadingOverlay("show", {
            background: "rgba(165, 190, 100, 0.4)",
        });

        axios.post(action, formData)
            .then(function (response) {
                const resp = response.data;

                if (resp.status == 'success') {
                    $this[0].reset();
                    toastr.success(resp.msg);
                    $('.box-body').LoadingOverlay("hide");
                    setTimeout(function () {
                        window.location.href = resp.url;
                    }, 2000)
                }

                if (resp.status == 'error') {
                    $('.box-body').LoadingOverlay("hide");
                    toastr.error(resp.msg);
                }
            })
            .catch(function (error) {
                $('.box-body').LoadingOverlay("hide");

                const errorBag = error.response.data.errors;

                $.each(errorBag, function (fieldName, value) {
                    $('.err_' + fieldName).closest('.form-group').addClass('has-error has-danger');
                    $('.err_' + fieldName).text(value[0]).closest('span').show();
                })
            });

        return false;
    }
})