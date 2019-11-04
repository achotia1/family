$(document).ready(function () {

    
    $id = $("#hidden_id").val();
    $state = $("#credit-in").val();
    getAllCredit($id, $state);

    $(document).on('change', '#credit-in', function () {
        $id = $("#hidden_id").val();
        $state = $(this).val();
        getAllCredit($id, $state);
    });
});


function getAllCredit($id, $state)
{
    const action = SITEURL + '/online-courses/credit_types?id=' + $id + '&state=' + $state;
    
    $.LoadingOverlay("show", 
    {
        background: "rgba(1, 1, 1, 0)",
    });

    axios.post(action)
    .then(function (response) 
    {
        const resp = response.data;
        $.LoadingOverlay("hide");
        if (resp.status == 'success') 
        {
            $("#credit_details").html(resp.credit_name);
            $("#credit_value").html(resp.credits);
            $(".multiple_buttons").html(resp.cart_buttons);

            setTimeout(function () 
            {
                $(".panel-title b").replaceWith(function () { return $(this).contents(); });
                $(".text-wrap b").replaceWith(function () { return $(this).contents(); });

            }, 500);

        }
        if (resp.status == 'error') 
        {
            toastr.error(resp.msg);
        }
    })
    .catch(function (error) 
    {
        $.LoadingOverlay("hide");
    });
}