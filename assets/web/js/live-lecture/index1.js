$(document).ready(function () {

    $(".tab").click(function () {
        $(".tab").removeClass('active');
        $(this).addClass('active');
        $(".tab_sections").addClass('hide');

        $("." + $(this).attr('id') + "-wrap").removeClass('hide');
        $page = 0;
        $course_type = $(".course_dropdown").val();
        $state = $(".state_dropdown").val();
        getAllData($page, $state, $course_type);
    });


    $(".state_dropdown").change(function () {
        $page = 0;
        $course_type = $(".course_dropdown").val();
        $state = $(this).val();
        getAllData($page, $state, $course_type);
    })

    $(".course_dropdown").change(function () {
        $page = 0;
        $course_type = $(this).val();
        $state = $(".state_dropdown").val();
        getAllData($page, $state, $course_type);
    })

    // $("#eventCalendarCustomDate").eventCalendar({
    //     eventsjson: 'http://localhost/trtcle/live-lectures/get_live_lecture_events/' + $(".course_dropdown").val() + "/" + $(".state_dropdown").val(),
    //     dateFormat: 'dddd D-MM-YYY',
    //     startWeekOnMonday: false
    // });

    $(".course-wrp .eventsCalendar-list-content").mCustomScrollbar({
        theme: "dark-2"
    });


    $page = 0;
    $course_type = $(".course_dropdown").val();
    $state = $(".state_dropdown").val();

    getAllData($page, $state, $course_type);


    $(document).on('click', '.pagination a', function (event) {
        event.preventDefault();
        $('li').removeClass('active');
        $(this).parent('li').addClass('active');

        $page = $(this).attr('href').split('page=')[1];
        $course_type = $(".course_dropdown").val();
        $state = $(".state_dropdown").val();

        getAllData($page, $state, $course_type);
    });



    $id = $("#hidden_id").val();
    $state = $("#online-landing").val();
    $type = $("#hidden_type").val();
    getAllDetails($id, $state, $type);

    $("#online-landing").change(function () {
        $id = $("#hidden_id").val();
        $state = $("#online-landing").val();
        $type = $("#hidden_type").val();
        getAllDetails($id, $state, $type);
    })

});

function getAllData($page, $state, $course) {
    const action = SITEURL + '/live-lectures/getRecords?page=' + $page + '&state=' + $state + '&course_type=' + $course;
    $.LoadingOverlay("show", {
        background: "rgba(165, 190, 100, 0.4)",
    });
    axios.post(action)
        .then(function (response) {
            const resp = response.data;
            $.LoadingOverlay("hide");
            if (resp.status == 'success') {
                liveLecturesHtml = resp.live_lectures;

                $("#live-lectures").html('');
                $("#state_name").html($(".state_dropdown").val());
                $("#live-lectures").html(liveLecturesHtml);
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


function getAllDetails($id, $state, $type) {
    const action = SITEURL + '/live-lectures/credit_types?id=' + $id + '&state=' + $state + '&type=' + $type;
    $.LoadingOverlay("show", {
        background: "rgba(165, 190, 100, 0.4)",
    });
    axios.post(action)
        .then(function (response) {
            const resp = response.data;
            $.LoadingOverlay("hide");
            if (resp.status == 'success') {

                credit_sum = resp.sum_credit;
                credit_names = resp.credit_name;
                online_sum = resp.sum_coursesBundles;
                online_names = resp.coursesBundles_name;

                $(".creditsum").html(credit_sum);
                $("#credit_types").html(credit_names);
                $("#onlinesum").html(online_sum);
                $("#online_types").html(online_names);

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