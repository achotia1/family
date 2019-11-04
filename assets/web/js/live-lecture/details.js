$(document).ready(function () {


    $("#online-landing").change(function () {
        $id = $("#hidden_id").val();
        $state = $(this).val();
        $type = $("#hidden_type").val();
        getCreditData($id, $state, $type);
    })


    $('#frmEmailFriend').validator().on('submit', function (e) {
        if (!e.isDefaultPrevented()) {
            const $this = $(this);
            const action = $this.attr('action');
            alert(action);
            $.LoadingOverlay("show", {
                background: "rgba(165, 190, 100, 0.4)",
            });
            const formData = new FormData($this[0]);
            axios.post(action, formData)
                .then(function (response) {
                    const resp = response.data;
                    if (resp.status == 'success') {
                        $this[0].reset();
                        toastr.success(resp.msg);
                        $.LoadingOverlay("hide");
                        setTimeout(function () {
                            window.location.href = resp.url;
                        }, 2000)
                    }

                    if (resp.status == 'error') {
                        $LoadingOverlay("hide");
                        toastr.error(resp.msg);
                    }
                })
                .catch(function (error) {
                    $('.card').LoadingOverlay("hide");
                    const errorBag = error.response.data.errors;
                    $.each(errorBag, function (fieldName, value) {
                        fieldName = fieldName.replace('.', '');
                        fieldName = fieldName.replace('.', '');
                        $('.err_' + fieldName).closest('.form-group').addClass('has-error has-danger');
                        $('.err_' + fieldName).text(value[0]).closest('span').show();
                    })
                });
            return false;
        }
    })

    $id = $("#hidden_id").val();
    $type = $("#hidden_type").val();
    $state = $("#online-landing").val();

    getCreditData($id, $state, $type);

});


function getCreditData($id, $state, $type) 
{
    const action = SITEURL + '/live-lectures/credit_types?id=' + $id + '&state=' + $state + '&type=' + $type;

    $.LoadingOverlay("show", 
    {
        background: "rgba(1, 1, 1, 0)",
    });

    axios.post(action)
        .then(function (response) {

            const resp = response.data;

            $.LoadingOverlay("hide");
            if (resp.status == 'success') {

                $(".creditsum").html(resp.sum_credit);
                $("#credit_types").html(resp.credit_name);
                $(".add-cart").html(resp.cart_buttons);

                if (resp.sum_coursesBundles != 0) {
                    $("#online_types").html(resp.coursesBundles_name);
                } else {
                    $("#online_types").html(resp.coursesBundles_name);
                    $("#online_types").parent('p').parent('div').addClass('hide');
                }

                $("#online_cle").html(resp.onlineCle);
                $("#onlinesum").html(resp.sum_coursesBundles);
                setTimeout(function () {
                    $(".panel-title b").replaceWith(function () { return $(this).contents(); });
                    $(".text-wrap b").replaceWith(function () { return $(this).contents(); });

                }, 500);

            }
            if (resp.status == 'error') {
                toastr.error(resp.msg);
            }
        })
        .catch(function (error) {
            $.LoadingOverlay("hide");
        });

}