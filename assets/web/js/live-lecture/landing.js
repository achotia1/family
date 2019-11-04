$(document).ready(function(){
	
	// console.log(landing_id);
	// console.log(live_lecture_id);

	var state_id=$("#online-landing").selectbox().val();
	getCreditData(landing_id,live_lecture_id,state_id);

	$("#online-landing").change(function () {
        var state_id = $(this).val();
        getCreditData(landing_id,live_lecture_id, state_id);
    })
});

function getCreditData(id,live_lecture_id, state_id) {
    
    const action = SITEURL + '/live-lecture/landing_credit_types?id=' + id 
    			+ '&live_lecture_id=' + live_lecture_id+ '&state_id=' + state_id;

    $.LoadingOverlay("show", {
        background: "rgba(1, 1, 1, 0)",
    });
    axios.post(action)
        .then(function (response) {

            const resp = response.data;
           
            $.LoadingOverlay("hide");
            if (resp.status == 'success') {

                $("#total_credit_breakdown").html(resp.credit_breakdown_total+' CREDITS IN');
                $("#live_cle_total_credits").html(resp.credit_breakdown_total+' Credits/Hours');
                $("#credit_breakdown_string").html(resp.credit_breakdown_string);
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
