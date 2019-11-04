$(document).ready(function () {
    // by default calender hide
    $(".main-page").hide();

    $("#calender_view .course-wrp").show();

    if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
        $("#calender_view .course-wrp").hide();
    }

    gerRecords();

    $("#calender_click").click(function()
    {
         gerRecords();
    });
    $("#list_click").click(function()
    {
          gerRecords();
    });
    change_view($("#current_view").val());

    $(document).on('change', '.course_dropdown', function () {
        gerRecords();
        return false;
    });
    $(document).on('change', '.state_dropdown', function () {
        gerRecords();
        return false;
    });

    $(document).on('click', '.pagination-links a', function () {
        $action = $(this).attr('href');
        gerRecords($action);
        return false;
    });



});

function gerRecords($action = false) {
    $.LoadingOverlay("show",
        {
            background: "rgba(1, 1, 1, 0)",
        });

    // data 
    var course_type = $('.course_dropdown').val() != '' ? $('.course_dropdown').val() : 0;
    var state_id = $('.state_dropdown').val() != '' ? $('.state_dropdown').val() : 0;
    var action = $action != '' ? $action : SITEURL + '/live-lectures/list/getRecords';


    // calender
    $("#eventCalendarCustomDate").empty();
    $("#eventCalendarCustomDate").unbind();
    $("#eventCalendarCustomDate").eventCalendar(
        {
            eventsjson: SITEURL + '/live-lectures/calender/getRecords/' + course_type + '/' + state_id,
            dateFormat: 'dddd D-MM-YYY',
            startWeekOnMonday: false,
            txt_noEvents: 'There are no live lectures currently listed for this date'
        });

    $(".course-wrp .eventsCalendar-list-content").mCustomScrollbar({ theme: "dark-2" });

    // listing 
    axios.post(action, { course_type: course_type, state_id: state_id })
        .then(response => {

            $('#list_view').empty();
            $('#search_text').empty();

            $('#list_view').append(response.data.list_view);
            $('#search_text').append(response.data.search_text);

        })
        .catch(error => {
            console.log(error.response.data);
        })
    $('html, body').animate({ scrollTop: 0}, '500');
    $.LoadingOverlay("hide");

    $("#dynamic_state_name").html($(".state_dropdown option:selected").html());
}

function add_to_cart(id, state_id) {
    console.log(id);
    console.log(state_id);
}

function remove_from_cart(id, state_id) {
    console.log(id);
    console.log(state_id);
}

function show_event(day) {
    if (day) {
        $('.eventsCalendar-list-wrap').css('display', '');
        $("#calender_view .course-wrp").show();
    }
}

function change_view(type) {
    if (type == 'list') {
        $('#current_view').val('list');
        $(".main-page").hide();
        $('#list_view').show();
        $('#calender_view').hide();
        $('#list_click').addClass('active');
        $('#calender_click').removeClass('active');

    }
    else
        if (type == 'calender') {
            $(".main-page").show();
            $('#current_view').val('calender');
            $('#list_view').hide();
            $('#calender_view').show();
            $('#list_click').removeClass('active');
            if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
                $("#calender_view .course-wrp").hide();
            }
            $('#calender_click').addClass('active');
            $(".monthTitle").click();

        }
}
