$(document).ready(function () 
{
    // adding focus event to first field
    // $('input[name="contact_name"]').focus();
    // $('input[name="mobile_number"]').mask('999-999-9999');
    if(customer_id){
       getCustomerProducts(customer_id);
    }

    $('#dd_customer').on('change', function() {
        getCustomerProducts(this.value);
    });
})

function getCustomerProducts(user_id){
    // $this = $(element);
    // var state_id = $this.val();
    // var selectType=$('#selectType').selectbox().val();
    
    $('.card').LoadingOverlay("show", {
        background: "rgba(165, 190, 100, 0.4)",
    });

    var action = ADMINURL + '/customers/getCustomerProducts';

    axios.post(action, {user_id:user_id})
    .then(response => 
    {  
        $('.card').LoadingOverlay("hide");      
        $('#dd_products').html(response.data.productsHtml).selectpicker('refresh');
        
    })
    .catch(error =>
    {
        $('.card').LoadingOverlay("hide");
    })

    
}

// submitting form after validation
$('#customerForm').validator().on('submit', function (e) 
{
    if (!e.isDefaultPrevented()) {

        const $this = $(this);
        const action = $this.attr('action');
        const formData = new FormData($this[0]);
        var user_id = $("#dd_customer").val();
        var product_ids = $("#dd_products").val();
        
        formData.append('user_id',user_id);
        formData.append('product_ids',product_ids);
        
        // console.log(formData);
        // return false;
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



