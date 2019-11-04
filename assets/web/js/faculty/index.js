$(function(){
	getAllData('');

	 $("#stae_drop_head").on("change",function(){ 
        var practiseAreaId= this.value;
        getAllData(practiseAreaId);

    });
});

function getAllData(practiseAreaId){

	const action = SITEURL+'/getFaculties';

	$.LoadingOverlay("show", {
	     background  : "rgba(1, 1, 1, 0)",
	  });
	$("#presenterSection").html(''); 
	
	axios.post(action,{ practiseAreaId:practiseAreaId })
	.then(function (response) {
	    const resp = response.data;
	    $.LoadingOverlay("hide");

	    	if (resp.status == 'success') {
	    		var facultyData='<div class="container">';
	    		//
	    		console.log(resp.faculties.length);
	    		if(resp.faculties.length==0){
					facultyData+='<center><p>No Faculty Members Found</p><center></div>';
	    			$("#presenterSection").html(facultyData); 
	    			return false;
	    		}
	    		$.each(resp.faculties , function(index, faculty) {
    				var practice_area_name='';
    				var practice_areas_html='';
    				var facultyLecturer='';
    				var facultyDetailUrl='';

    				var facultyname=faculty.first_name.toLowerCase()+" "+faculty.last_name.toLowerCase();
    				//facultyname=facultyname.toLowerCase();
    				var substring=" ";
    				if(facultyname.indexOf(substring) !== -1){
    					facultyname=facultyname.replace(/ /g, "-");	
    				}
    				substring="/";
    				if(facultyname.indexOf(substring) !== -1){
    					facultyname=facultyname.replace("/", "-");
    				}
    				substring=".";
    				if(facultyname.indexOf(substring) !== -1){
    					facultyname=facultyname.replace(".", "");
    				}
    				
    				facultyDetailUrl=SITEURL+'/our-faculty/'+faculty.id+'/'+facultyname;
					
					if(faculty.about_lecture.length>0){
 						//var words = faculty.about_lecture.match(/\w+/g).length; //No of words
 						facultyLecturer=faculty.about_lecture.replace(/(<([^>]+)>)/ig,"");
 						const truncateData = (str, len) => str.substring(0, (str + ' ').lastIndexOf(' ', len));
						facultyLecturer=truncateData(facultyLecturer, 200);
						facultyLecturer=facultyLecturer+"...";
 						//console.log(facultyLecturer);
	    			}
	    			
	    			if(faculty.practice_areas.length>0){
	    				practice_area_name=faculty.practice_areas[0]['area']['name'];
	    				if(faculty.practice_areas.length>1){
	    					practice_areas_html=' <span>(+'+(faculty.practice_areas.length-1)+' other areas)</span>';	
	    				}
	    				practice_area_name+=practice_areas_html;
	    				
	    			}
	    			if(faculty.image==null){
						faculty.image=SITEURL+'/assets/web/images/default-grey.jpg';	
	    			}else{
	    				faculty.image=SITEURL+'/storage/app/'+faculty.image;	
	    			}

	    			facultyData+='<div class="f-row wrap-mr">';
								facultyData+='<div class="f-col-sm-4 f-col-lg-3 text-center text-sm-left">';
									facultyData+='<img src="'+faculty.image+'" alt="'+faculty.first_name+' '+faculty.last_name+'" />';
								facultyData+='</div>';
								facultyData+='<div class="f-col-sm-8 f-col-lg-9 d-flex align-items-center">';
									facultyData+='<div class="details-wrap">';
						facultyData+='<h4>'+faculty.first_name+' '+faculty.last_name+'</h4>';
									facultyData+='<div class="para-wrap d-flex flex-wrap justify-content-between">';
										facultyData+='<p>'+faculty.firm_name+'</p>';
										facultyData+='<p class="right-text"><strong class="blue">Practice Area:</strong> '+practice_area_name+'</span></p>';
									facultyData+='</div>';
									facultyData+='<p>'+facultyLecturer+'</p>';
									facultyData+='<a href="'+facultyDetailUrl+'" class="view">View Details</a>';
								facultyData+='</div>';
							facultyData+='</div>';
						facultyData+='</div>';										
	    		});
		facultyData+='</div>';
		$("#presenterSection").html(facultyData); 
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