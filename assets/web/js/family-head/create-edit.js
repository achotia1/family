$(document).ready(function () 
{
  $( '.datepicker' ).datepicker({
    autoclose: true,
    format: 'dd-mm-yyyy',
  })

  $( '.martial_status_married' ).on( 'ifChanged', function(){
    if( this.checked ) {
        $("#wedding_div").removeClass("hide");
    }else {
       $("#wedding_div").addClass("hide");
    }
  });

})

function addHobby() {
    $(hobby).insertAfter($(".select_hobby:last"));
}


// get state wise cities
function getStateCities(element)
{
        
    $this = $(element);
    var state_id = $this.val();
    var action = BASEURL + '/get-state-cities';

    axios.post(action, {state_id:state_id})
    .then(response => 
    {        
        $('#city_id').empty();

        // adding filtred courses into left container
        $('#city_id').append(response.data.cities);
    })
    .catch(error =>
    {

    })
}

function getAge(dateString) {
    var today = new Date();
    var birthDate = new Date(dateString);
    var age = today.getFullYear() - birthDate.getFullYear();
    var m = today.getMonth() - birthDate.getMonth();
    if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
        age--;
    }
    return age;
}

$('#familyHeadForm').validator().on('submit', function (e) 
{

  if (!e.isDefaultPrevented()) {
      const $this = $(this);
      const action = $this.attr('action');
      const formData = new FormData($this[0]);

      $($this).closest('#FamilyHeadForm').LoadingOverlay("show", {
          background: "rgba(165, 190, 100, 0.4)",
      });

      axios.post(action, formData)
      .then(function (response) {                
          const resp = response.data;

          if (resp.status == 'success') 
          {
              $this[0].reset();
              
              toastr.success(resp.msg);

              $($this).closest('#FamilyHeadForm').LoadingOverlay("hide");
              setTimeout(function () {
                  window.location.href = resp.url;
              }, 2000)

          }
          if (resp.status == 'error') 
          {
              $($this).closest('#FamilyHeadForm').LoadingOverlay("hide");
              toastr.error(resp.msg);
          }
      })
      .catch(function (error) {
          $('#submitButton').show();
          $($this).closest('#FamilyHeadForm').LoadingOverlay("hide");

          const errorBag = error.response.data.errors;
          $.each(errorBag, function (fieldName, value) 
          {                    
              $('.err_' + fieldName).closest('div').addClass('has-error has-danger');
              $('.err_' + fieldName).text(value[0]).closest('span').show();
          })
      });
      return false;
  }
})