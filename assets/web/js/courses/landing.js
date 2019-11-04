$(document).ready(function () {
    
    var course_ids = $("#course_ids").val();
    var state_id = $("#online-landing").val();
    var landing_course_id = $("#landing_course_id").val();
    getAllCreditData(course_ids, state_id,landing_course_id);

    $(document).on('change', '#online-landing', function () {
        var course_ids  =   $("#course_ids").val();
        var state_id    =   $(this).val();
        var landing_course_id = $("#landing_course_id").val();

        getAllCreditData(course_ids, state_id,landing_course_id);
    });
});


function getAllCreditData(course_ids, state_id,landing_course_id) {
    // console.log(course_ids);
    //  console.log(state_id);
    // return false;
    const action = SITEURL + '/course/credits?landing_course_id='+landing_course_id+'&course_ids=' + course_ids + '&state_id=' + state_id;
    $.LoadingOverlay("show", {
        background: "rgba(165, 190, 100, 0.4)",
    });
    axios.post(action)
        .then(function (response) {
            const resp = response.data;
            $.LoadingOverlay("hide");
            if (resp.status == 'success') {
                
                $("#credit_details").html(resp.credit_details);
                $("#total_credit_breakdown").html(resp.credits+" CREDITS IN ");
                $(".add-cart").html(resp.cart_button)

            }
            if (resp.status == 'error') {
                toastr.error(resp.msg);
            }
        })
        .catch(function (error) {
            $.LoadingOverlay("hide");
        });

}