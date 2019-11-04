$(document).ready(function(){
	
	// console.log(landing_id);
	// console.log(teleconference_id);

	var state_id=$("#online-landing").selectbox().val();
	getCreditData(landing_id,teleconference_id,state_id);

	$("#online-landing").change(function () {
        var state_id = $(this).val();
        getCreditData(landing_id,teleconference_id, state_id);
    })
});

function getCreditData(id,teleconference_id, state_id) {
    
    const action = SITEURL + '/teleconference/landing_credit_types?id=' + id 
    			+ '&teleconference_id=' + teleconference_id+ '&state_id=' + state_id;

    $.LoadingOverlay("show", {
        background: "rgba(1, 1, 1, 0)",
    });
    axios.post(action)
        .then(function (response) {

            const resp = response.data;
           
            $.LoadingOverlay("hide");
            if (resp.status == 'success') {

                $(".add-cart").html(resp.cart_button)
                $("#total_credit_breakdown").html(resp.credit_breakdown_total_string);
                $("#live_credit_string").html(resp.live_credit_string);
                $("#credit_breakdown_string").html(resp.credit_breakdown_string);

                // $(".creditsum").html(resp.sum_credit);
                // $("#credit_types").html(resp.credit_name);
                // $("#online_types").html(resp.coursesBundles_name);
                // $("#online_cle").html(resp.onlineCle);
                // $("#onlinesum").html(resp.sum_coursesBundles);
               

            }
            if (resp.status == 'error') {
                toastr.error(resp.msg);
            }
        })
        .catch(function (error) {
            $.LoadingOverlay("hide");
        });

}
