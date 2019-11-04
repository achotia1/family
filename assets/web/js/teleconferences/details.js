$(document).ready(function() 
{    
    // get list records 
    gerCreditsForState();

});

function gerCreditsForState()
{    
    // loading overlay
    $.LoadingOverlay("show", 
    {
        background  : "rgba(1, 1, 1, 0)",
    });

    // data 
    var state_id = $('#credit-in').val() != '' ? $('#credit-in').val():0;

    // action 
    action = SITEURL + '/teleconferences/getCreditForState';

    // records 
    axios.post(action,{teleconference_id:teleconference_id,state_id:state_id})
    .then(response => 
    {        
        $('#live_credits_text').hide();

        var credits     = response.data.credits;
        var credit_types= response.data.credit_types;
        var live_credit = response.data.live_credit;
        var state_name =  $("#credit-in option:selected").text();
        
        if (live_credit) 
        {
            credits = credits +' Live ';
        }

        $('#tel-credits').html(credits);
        $('#credit_types').html(credit_types);
        $(".multiple_buttons").html(resp.cart_buttons);

        if (live_credit) 
        {
            $('#live_credits_text').show();
            $('#live_credits_state_text').html(state_name);
        }

        $.LoadingOverlay("hide");
    })
    .catch(error => 
    {
        $.LoadingOverlay("hide");
        console.log(error.response.data);
    })
}
