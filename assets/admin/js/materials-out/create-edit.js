$(document).ready(function () 
{
    // adding focus event to first field    
    //$('#product_code').focus();  
    
})

// submitting form after validation
$('#materialOutForm').validator().on('submit', function (e) 
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
});

function checkPlan(plan)
{    
    var plan_id = $(plan).val();
    var action = ADMINURL + '/materials-out/getExistingPlan';
    axios.post(action, {plan_id:plan_id})
    .then(response => 
    {               
        var url = response.data.url;
        if(url != '')
        	window.location.href = url;
    })
    .catch(error =>
    {

    })
    return false;
}