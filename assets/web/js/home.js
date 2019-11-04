$('#newsletter-form').validator().on('submit', function (e) {
      if (!e.isDefaultPrevented()) {
            const $this = $(this);
            const action = $this.attr('action');
            const formData = new FormData($this[0]);
            $('#submitButton').hide();
            axios.post(action, formData)
                  .then(function (response) {
                       const resp = response.data;
                        if (resp.status == 'success') {
                              toastr.success(resp.msg);
                              $this[0].reset();
                              setTimeout(function () {
                                   $('#submitButton').show();
                              }, 1500)
                        }
                        if (resp.status == 'error') {
                              toastr.error(resp.msg);
                              $('#submitButton').show();
                        }
                  })
                  .catch(function (error) {
                        const errorBag = error.response.data.errors;
                        $.each(errorBag, function (fieldName, value) {
                              $('.err_' + fieldName).closest('div').addClass('has-error has-danger');
                              $('.err_' + fieldName).text(value[0]).closest('span').show();
                        })
                  });
            return false;
      }
});

function setViewCourseUrl(type,state_id){
          var url      = "";
         // console.log(type);
         // console.log(state_id);

        if(type=='cle_bundles'){
            url="/cle-bundles";
        }else if(type=='online_courses'){
            url="/online-courses";   
        }else if(type=='teleconferences'){
            url="/teleconferences";   
        }else if(type=='live_lectures'){
            url="/live-lectures";   
        }else if(type=='bridge_the_gap'){
            url=SITEURL+"/live-lectures";
        }else if(type=='unlimited_cle'){
            url="/unlimited-cle";
        }

        if(type!='bridge_the_gap'){
            url=SITEURL+url+"/"+state_id;
        }

        $("#view_courses").attr('href',url);
      }

$(document).ready(function(){

      $('#online-course-head').on('change', function() {
         var type     = this.value;
         var state_id = $('#stae_drop_head').selectbox().val();
         
         setViewCourseUrl(type,state_id);
        
      });

      $('#stae_drop_head').on('change', function() {
         var state_id   =  this.value;
         var type       =  $('#online-course-head').selectbox().val();
         setViewCourseUrl(type,state_id);
        
      });

});
