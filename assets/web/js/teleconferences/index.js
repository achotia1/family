$(document).ready(function() 
{
    // by default calender hide
    $(".main-page").hide();

    $("#calender_view .course-wrp").show();
    
    if( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) 
    {
        $("#calender_view .course-wrp").hide();
    } 
    
    // get list records 
    gerListRecords();

    // check view 
    change_view($("#current_view").val()); 
});

function getCalenderRecords()
{
    // data 
    var credit_type = $('.teleconfereces-credit-type').val() != '' ? $('.teleconfereces-credit-type').val():0;
    var state_id    = $('.teleconfereces-state').val() != '' ? $('.teleconfereces-state').val():0;

    // calender
    $("#eventCalendarCustomDate").empty();
    $("#eventCalendarCustomDate").unbind();
    $("#eventCalendarCustomDate").eventCalendar(
    {
        eventsjson: SITEURL + '/teleconferences/calender/getRecords/'+credit_type+'/'+state_id,
        dateFormat: 'dddd D-MM-YYY',
        startWeekOnMonday: false,
        txt_noEvents: 'There are no teleconferences currently listed for this date'
    });
    $(".course-wrp .eventsCalendar-list-content").mCustomScrollbar({theme:"dark-2"});
}

function gerListRecords(action)
{
    getCalenderRecords();
    
    if (action === undefined) 
    {
         var action  = SITEURL+'/teleconferences/list/getRecords';
    }

    $.LoadingOverlay("show", 
    {
         background  : "rgba(1, 1, 1, 0)",
    });

    // data 
    var credit_type = $('.teleconfereces-credit-type').val() != '' ? $('.teleconfereces-credit-type').val():0;
    var state_id    = $('.teleconfereces-state').val() != '' ? $('.teleconfereces-state').val():0;
    
    // listing 
    axios.post(action,{credit_type:credit_type,state_id:state_id})
    .then(response => 
    {
        $('#list_view').empty();
        $('#search_text').empty();
        
        $('#list_view').append(response.data.list_view);
        $('#search_text').append(response.data.search_text);

        var state   = $(".teleconfereces-state option:selected").text();
        var credit  = $(".teleconfereces-credit-type option:selected").text();

        $('#credit_type_text').html(credit);
        $('#state_id_text').html(state);
        $('#state_id_text2').html(state);

        $('html, body').animate({ scrollTop: 0 }, '1000');

        $.LoadingOverlay("hide");
    })
    .catch(error => 
    {
        $.LoadingOverlay("hide");
        console.log(error.response.data);
    })
}

// pagination 
$(document).on('click', '.pagination-links a', function()
{
    var action = $(this).attr('href');
    gerListRecords(action);
    return false;
});


function show_event(day)
{
    if(day)
    {
        $('.eventsCalendar-list-wrap').css('display','');
        $("#calender_view .course-wrp").show();
    }
}   

function change_view(type)
{
    if(type=='list')
    {
        $('#current_view').val('list');
        $(".main-page").hide();
        $('#list_view').show();
        $('#calender_view').hide();
        $('#list_click').addClass('active');
        $('#calender_click').removeClass('active');
    }
    else 
    if(type=='calender')
    {
        $(".main-page").show();
        $('#current_view').val('calender');
        $('#list_view').hide();
        $('#calender_view').show();
        $('#list_click').removeClass('active');
        if( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) 
        {
            $("#calender_view .course-wrp").hide();
        }
        $('#calender_click').addClass('active');
        $(".monthTitle").click();
    }
}
