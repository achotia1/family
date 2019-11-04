/*
* @Author: sheshkumar
* @Date:   2019-05-17 16:41:25
* @Last Modified by:   sheshkumar
* @Last Modified time: 2019-06-11 16:48:40
*/



function getOnlineCourses()
{
	var state_id = $('#states').val();
	var credit_type = $('#credit_type').val();
	var practice_area = $('#practice_area').val();

	var formData = new FormData();
	formData.append('state_id', state_id);
	formData.append('credit_type', credit_type);
	formData.append('practice_area', practice_area);

	var action = SITEURL + '/my-account/getOnlineCourses';
	$.LoadingOverlay("show",
    {
        background: "rgba(1, 1, 1, 0)",
    }); 

	axios.post(action,formData)
  	.then(function (response) 
    {
    	const resp = response.data;

    	$('.main-div-wrapper').html(resp);

    	$('#states').selectbox();
    	$('#credit_type').selectbox();
    	$('#practice_area').selectbox();

     	$.LoadingOverlay("hide"); 	
  	})
  	.catch(function (error) 
  	{
      $.LoadingOverlay("hide");
  	});
}

function changeState()
{
  var url  = $('#credit-in').val();
  window.location.href = url;
}

// function getApprovedStateCredits()
// {
//   var state_id  = $('#credit-in').val();
//   var course_id = $('#credit-in').attr('data-course');

//   var formData = new FormData();
//   formData.append('state_id', state_id);
//   formData.append('course_id', course_id);

//   var action = SITEURL + '/my-account/getApprovedStateCredits';
//   $.LoadingOverlay("show",
//   {
//       background: "rgba(1, 1, 1, 0)",
//   }); 

//   axios.post(action,formData)
//     .then(function (response) 
//     {
//       const resp = response.data;

//       $('#total_credit').html(resp.total_credit);
//       $('#credit_breakdown').html(resp.credit_breakdown);

//       $.LoadingOverlay("hide");   
//     })
//     .catch(function (error) 
//     {
//       $.LoadingOverlay("hide");
//     });
// }

var interval;        // will update as per defaul interval
var defaultInterval; // constant interval
var totalDuration;   // total duration
var percent;
var inervalTime = 5; // time interval in seconds 
var is_finished = false;

// now palaying
var old_played_time = nowPlaying.played_time;
var old_percentage  = nowPlaying.percentage;
console.log(old_played_time+'/'+old_percentage);

$(function() 
{
    var playbtn    = document.getElementById('video-button');
    var vPlayer    = new Vimeo.Player($('iframe'));

    // onclick action
    playbtn.onclick = function()
    {
      vPlayer.play();
    }

    // onplay action
    vPlayer.on('play', function(data)
    {
      $(playbtn).closest('div').removeClass('video-wrap');
      $(playbtn).hide();

      // getting current percent
      percent = ((data.percent)*100).toFixed(0);

      // setting default interval;
      totalDuration   = (data.duration).toFixed(2);
      defaultInterval = ((parseFloat(inervalTime)/parseFloat(totalDuration))*100).toFixed(2);

      if(percent != 100 && old_percentage != 100)
      {
          var played_time = old_played_time ? old_played_time : 0;

          if (old_played_time == data.duration) 
          {
            is_finished = true;
            played_time = 0;
          }
          else
          if (data.seconds > old_played_time) 
          {
            played_time = data.seconds;
          }


          vPlayer.setCurrentTime(played_time)
          .then(function()
          {
            vPlayer.play();
          });
      }

    });

    vPlayer.on('seeking', function(data)
    {
        var seekingPercent = ((data.percent)*100).toFixed(0);
        newCounter  = (parseFloat(seekingPercent)/parseFloat(defaultInterval)).toFixed(0);
        newCounter  = parseInt(newCounter)+1;
        interval    = (parseFloat(defaultInterval)*newCounter).toFixed(2);
    });

    // onpause action
    vPlayer.on('pause', function(data)
    {
      if (!$(playbtn).closest('div').hasClass('video-wrap')) 
      {
         $(playbtn).closest('div').addClass('video-wrap');
      }
      $(playbtn).show();
    });

    // ontimeupdate action
    vPlayer.on('timeupdate', function(timeupdate)
    {
      if (!is_finished) 
      {
        var checkPercent  = ((timeupdate.percent)*100).toFixed(2);

        /*------------------------------------------
        |   Interval
        ------------------------------------------*/
        console.log(interval +'<='+ checkPercent);
        // console.log(parseFloat(interval)<=parseFloat(checkPercent));
        if (parseFloat(interval) <= parseFloat(checkPercent)) 
        {
            // set updated interval
            interval = (parseFloat(interval)+parseFloat(defaultInterval)).toFixed(2);

            // insert data if not finished
            var played_percentage = old_percentage ? old_percentage : null;
            if (played_percentage == null) 
            {
              storeDetails(timeupdate);
            }
            else
            if(checkPercent == 100)
            {
              // console.log('finished');
              // storeDetails(nowPlaying,timeupdate); 
            }
            else
            if(parseFloat(checkPercent) > parseFloat(played_percentage))
            {
               storeDetails(timeupdate); 
            }
        }

        /*------------------------------------------
        |   check if finished
        ------------------------------------------*/
        if(checkPercent == 100)
        {
            storeDetails(timeupdate);
            is_finished = true;
        }
      }
    });


    function storeDetails(timeupdate)
    {
        var formData = new FormData();
        formData.append('nowPlaying',  JSON.stringify(nowPlaying));
        formData.append('seconds', timeupdate.seconds);
        formData.append('percent', timeupdate.percent);
        formData.append('duration', timeupdate.duration);

        action = SITEURL+'/my-account/track/updateVideoDetails';
      
        axios.post(action,formData)
        .then(function (response) 
        {
            const resp = response.data;
            console.log(resp.status);
            console.log(resp.nowPlaying);
            nowPlaying = resp.nowPlaying;
            console.log(nowPlaying);
            
        })
    }
})
