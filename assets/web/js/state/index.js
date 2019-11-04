$(function(){
	getAllData();
    $("#stae_drop_head").on("change",function(){ 
        var stateId= this.value;
        $(".panel-heading .panel-title a").attr('aria-expanded',false);
        $('.panel-heading').next('div').removeClass('in');

        $("#heading"+stateId+" .panel-title a").attr('aria-expanded',true);
        $("#collapse"+stateId).addClass('in');

        $('html,body').animate({
                scrollTop: Math.round($("#heading"+stateId).offset().top)-100}
                ,1000);

    });
});



function getAllData(){
   

const action = SITEURL+'/cle-requirements/getRecords';

$.LoadingOverlay("show", {
     background  : "rgba(1, 1, 1, 0)",
  });

axios.post(action)
.then(function (response) {
    const resp = response.data;
    $.LoadingOverlay("hide");

    if (resp.status == 'success') {

    	//var stateDropdown='<option value="" selected="selected">Select Your State</option>';
		//stateDropdown+="";
		//$("#stae_drop_head").html(stateDropdown);
		//$("#stae_drop_head").selectbox();

       // $this[0].reset();
       // toastr.success(resp.msg);
        //window.location.href = BASEURL + '/admin/state';
         /* <div class="right">
                                    <p>20 hours General <br/> 4 hours Ethics and Professionalism</p>
                                </div>
                            </div>*/
    var stateRequirementHtml='<div class="container"><div class="f-row justify-content-center"><div class="f-col-md-10"><div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">';                            
    $.each(resp.statesrequirement , function(index, val) { 
	
  //val.id
  //val.state_abbr
    //panel default

    stateRequirementHtml+='<div class="panel panel-default">';
        //panel heading
        stateRequirementHtml+='<div class="panel-heading" role="tab" id="heading'+val.id+'"><h4 class="panel-title"><a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse'+val.id+'" aria-expanded="false" aria-controls="collapse'+val.id+'">'+val.state_name+'</a></h4></div>';

        //collapse
		stateRequirementHtml+='<div id="collapse'+val.id+'" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading'+val.id+'"><div class="panel-body">';
        //stateRequirementHtml+='demo';2
                        
            //wrap
    		stateRequirementHtml+='<div class="wrap">';
                stateRequirementHtml+='<div class="text-wrap"><div class="left"><p><strong class="blue">Total CLE Requirement: </strong></p></div>';
                stateRequirementHtml+='<div class="right"><p>'+val.cle_requirement+'</p></div></div>';
						
                stateRequirementHtml+='<div class="text-wrap"><div class="left"><p><strong class="blue">Credit Format:</strong></p></div>';
                stateRequirementHtml+='<div class="right"><p>'+val.credit_format+'</p></div></div>';

                stateRequirementHtml+='<div class="text-wrap"><div class="left"><p><strong class="blue">Credit Breakdown: </strong></p></div>';
                 stateRequirementHtml+='<div class="right"><p>'+val.credit_breakdown+'</p></div></div>';//issue when value added dynamically
                 //stateRequirementHtml+='<div class="right"><p>20 hours General <br/> 4 hours Ethics and Professionalism</p></div></div>';                   
                                          
				stateRequirementHtml+='<div class="text-wrap"><div class="left"><p><strong class="blue">Compliance Deadline: </strong></p></div>';
                stateRequirementHtml+='<div class="right"><p>'+val.compliance_deadline+'</p></div></div>';   

                stateRequirementHtml+='<div class="text-wrap"><div class="left"><p><strong class="blue">Reporting Deadline: </strong></p></div>';
                stateRequirementHtml+='<div class="right"><p>'+val.reporting_deadline+'</p></div></div>'; 

                stateRequirementHtml+='<div class="text-wrap"><div class="left"><p><strong class="blue">Reporting Information: </strong></p></div>';
                stateRequirementHtml+='<div class="right"><p>'+val.reporting_information+'</p></div></div>';

                stateRequirementHtml+='<div class="text-wrap"><div class="left"><p><strong class="blue">Newly Admitted Requirement: </strong></p></div>';
                stateRequirementHtml+='<div class="right"><p>'+val.admitted_requirement+'</p></div></div>';
//<p><strong>Credit Breakdown:</strong><br/>3.0 Ethics <br/>6.0 Skills <br/>7.0 Law Practice Management and/or areas of Professional Practice</p><p>TRTCLEs <strong><a href="#">Bridge the Gap Compliance Days</a></strong> fulfill all 16 credits with a combination of live programming and online courses.</p>
				stateRequirementHtml+='<div class="text-wrap"><div class="left"><p><strong class="blue">State Contact Information: </strong></p></div>';
                stateRequirementHtml+='<div class="right"><ul class="contact-info"><li><img src="'+SITEURL+'/assets/web/images/icons/location_blue.png" alt="Address" class="location">'+val.contact_information+'</li>';
                stateRequirementHtml+='<li><img src="'+SITEURL+'/assets/web/images/icons/call_blue.png" alt="Numbers" class="call">'+val.contacts_phone+'</li>';
				stateRequirementHtml+='<li><img src="'+SITEURL+'/assets/web/images/icons/contact_us-_blue.png" alt="mail" class="contact">'+val.contacts_links+'</li></ul></div></div>';

                //var coursesbool=val.cle_bundles

				stateRequirementHtml+='<div class="text-wrap"><div class="left"><p><strong class="blue">TRTCLE Courses: </strong></p></div><div class="right"><ul class="contact-info menu">';
                if(val.cle_bundles==1){
                    stateRequirementHtml+='<li><img src="'+SITEURL+'/assets/web/images/icons/box_blue_blue.png" alt="CLE Bundles" class="cle-bundles"><a href="'+SITEURL+'/cle-bundles/'+val.state_abbr+'">CLE Bundles</a></li>';
                }
                if(val.online_courses==1){
                    stateRequirementHtml+='<li><img src="'+SITEURL+'/assets/web/images/icons/online_cource_blue.png" alt="Online Courses" class="online-courses"><a href="'+SITEURL+'/online-courses/'+val.state_abbr+'">Online Courses</a></li>';
                }
                if(val.teleconferences==1){
                    stateRequirementHtml+='<li><img src="'+SITEURL+'/assets/web/images/icons/teleconferences_blue.png" alt="Teleconferences" class="teleconferences"><a href="'+SITEURL+'/teleconferences/'+val.state_abbr+'">Teleconferences</a></li>';
                }
                if(val.live_lectures==1){
                    stateRequirementHtml+='<li><img src="'+SITEURL+'/assets/web/images/icons/live_lectures_blue.png" alt="Live Lectures" class="live-lectures"><a href="'+SITEURL+'/live-lectures/'+val.state_abbr+'">Live Lectures</a></li>';
                }
                if(val.bridge_the_gap==1){
                     stateRequirementHtml+='<li><img src="'+SITEURL+'/assets/web/images/icons/bridge_the_gap_blue.png" alt="Bridge the Gap" class="bridge-the-gap"><a href="'+SITEURL+'/live-lectures">Bridge the Gap</a></li>';
                }

                stateRequirementHtml+='</ul></div></div>';

            stateRequirementHtml+='</div>';//wrapend
        stateRequirementHtml+='</div></div>';//collapseend
    stateRequirementHtml+='</div>';//panel defaultend
                              
    });
				

	stateRequirementHtml+='</div></div></div></div>';

//console.log(stateRequirementHtml);
$("#stateRequirement").html('');
	$("#stateRequirement").html(stateRequirementHtml);
     setTimeout(function () {
       $(".panel-title b").replaceWith(function() { return $(this).contents(); });
       $(".text-wrap b").replaceWith(function() { return $(this).contents(); });
       

        /*var stateId= 7;
        $(".panel-heading .panel-title a").attr('aria-expanded',false);
        $('.panel-heading').next('div').removeClass('in');

        $("#heading"+stateId+" .panel-title a").attr('aria-expanded',true);
        $("#collapse"+stateId).addClass('in');

        $('html,body').animate({
                scrollTop: Math.round($("#heading"+stateId).offset().top)-100}
                ,1000);*/

     }, 500);

	}
    if (resp.status == 'error') {
        //toastr.error(resp.msg);
    }
})
.catch(function (error) {
	 $.LoadingOverlay("hide");

    /*$('#submitButton').show();
    $($this).closest('#stateForm').LoadingOverlay("hide");

    const errorBag = error.response.data.errors;
    $.each(errorBag, function (fieldName, value) {
        $('.err_' + fieldName).closest('div').addClass(
            'has-error has-danger');
        $('.err_' + fieldName).text(value[0]).closest('span').show();
    })*/
});

}