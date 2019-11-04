/*
* @Author: sheshkumar
* @Date:   2019-05-14 17:55:17
* @Last Modified by:   sheshkumar
* @Last Modified time: 2019-06-10 14:56:38
*/

$(document).ready(function()
{
  changeStateDiv();
	$('input[name="phone_number"]').mask('999-999-9999');
  $('input[name="zip_code"]').mask('9999999999');
  $('input[name="zip_postal"]').mask('9999999999');
})

// personal information
function removeDisablePersonalInformation(element)
{
	$(element).hide();
	$(element).closest('div').find('*').removeAttr('disabled');
	$(element).closest('div').find('button[type="submit"]').show();
}

$('#personalInformationForm').validator().on('submit', function (e) 
{
  	if (!e.isDefaultPrevented()) 
  	{
        const $this = $(this);
        const action = $this.attr('action');
        const formData = new FormData($this[0]);

        $.LoadingOverlay("show", {
              background: "rgba(165, 190, 100, 0)",
        });

        axios.post(action, formData)
      	.then(function (response) 
        {
          	$.LoadingOverlay("hide");
            const resp = response.data;
            if (resp.status == 'success') 
            {
              	toastr.success(resp.msg);
              	setTimeout(function()
              	{
	              	$this.closest('.my-profile-wrap').find('.edit').show();
	              	$this.find('*').attr('disabled', true);
					$this.find('button[type="submit"]').hide();
              	}, 1000)
            }

            if (resp.status == 'error') 
            {
              	toastr.error(resp.msg);
            }
      	})
      	.catch(function (error) 
      	{
            $.LoadingOverlay("hide");

            const errorBag = error.response.data.errors;

            $.each(errorBag, function (fieldName, value) {
                  $('.err_' + fieldName).closest('.form-group').addClass('has-error has-danger');
                  $('.err_' + fieldName).text(value[0]).closest('span').show();
            })
      	});

        return false;
  	}
})

// reporting information
function removeDisableReportingInformation(element)
{
  $(element).hide();
  
  $(element).closest('div').find('.attorny-states').each(function()
  {
    if ($(this).hasClass('active')) 
    {
      $(this).find('*').removeAttr('disabled');
    }
  })

  // $(element).closest('div').find('*').removeAttr('disabled');
  $(element).closest('div').find('button[type="submit"]').removeClass('hide');
  $(element).closest('div').find('.add_another_state').removeClass('hide');
  $(element).closest('div').find('.state_close_button').removeClass('hide');
  $(element).closest('div').find('button[type="submit"]').removeAttr('disabled');
  $(element).closest('div').find('button[type="submit"]').show();

  if (!hasState) 
  {    
    $('.attorny-states').each(function()
    {
        if ($(this).hasClass('nonAttorny'))
        {
          $(this).addClass('hide');
        }

        if ($(this).hasClass('withAttorny'))
        {
          $(this).removeClass('hide');
        }
    })
  }
}

$('#reportingInformationForm').validator().on('submit', function (e) 
{
    if (!e.isDefaultPrevented()) 
    {
        const $this = $(this);
        const action = $this.attr('action');
        const formData = new FormData($this[0]);

        $.LoadingOverlay("show", {
              background: "rgba(165, 190, 100, 0)",
        });

        axios.post(action, formData)
        .then(function (response) 
        {
            $.LoadingOverlay("hide");
            const resp = response.data;
            if (resp.status == 'success') 
            {
                toastr.success(resp.msg);
                setTimeout(function()
                {
                  $this.closest('.my-profile-wrap').find('.add_another_state').addClass('hide');
                  $this.closest('.my-profile-wrap').find('.edit').show();
                  $this.find('*').attr('disabled', true);
                  $this.find('button[type="submit"]').hide();
                  $this.find('.state_close_button').addClass('hide');;

                  if (resp.empty) 
                  {
                    hasState = false;
                    $('.attorny-states').each(function()
                    {
                        if ($(this).hasClass('nonAttorny'))
                        {
                          $(this).removeClass('hide');
                        }

                        if ($(this).hasClass('withAttorny'))
                        {
                          $(this).addClass('hide');
                        }
                    })
                  }
                },1000)
            }

            if (resp.status == 'error') 
            {
                toastr.error(resp.msg);
            }
        })
        .catch(function (error) 
        {
            $.LoadingOverlay("hide");

            const errorBag = error.response.data.errors;

            $.each(errorBag, function (fieldName, value) {
                  $('.err_' + fieldName).closest('.form-group').addClass('has-error has-danger');
                  $('.err_' + fieldName).text(value[0]).closest('span').show();
            })
        });

        return false;
    }
})

function addAttornyState(element)
{
    var index = $('.attorny-states').length;
    var attorny_states = ` <div class="f-row no-gutters attorny-states active">
                                    <div class="f-col-12 f-col-sm-7 d-flex align-items-center">

                                        <div class="state-icon trtimage requirement_icon hide ">
                                            <img class="state_attorny_image" src="${SITEURL}/assets/web/images/icons/state_2.png" alt="chat">
                                        </div>
                                    
                                        <div class="state-icon nontrtimage ">
                                            <img src="${SITEURL}/assets/web/images/icons/state_1.png" alt="chat">
                                        </div>

                                        <div class="form-group w-100">
                                            <label class="blue">State Admitted</label>
                                            <select  
                                                class="form-control myselect"
                                                name="reporting[${index}][state]" 
                                                onchange="selectReportingImage(this)"
                                            >
                                              ${state_options}
                                            </select>
                                            <span class="help-block with-errors">
                                                <ul class="list-unstyled">
                                                    <li class="err_state"></li>
                                                </ul>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="f-col-12 f-col-12 f-col-sm-5">
                                        <div class="form-group attorney">
                                            <label class="blue">Attorney Registration #</label>
                                            <input 
                                                type="text" 
                                                value="" 
                                                class="form-control" 
                                                name="reporting[${index}][attorney_registration]"
                                            />
                                            <span class="help-block with-errors">
                                                <ul class="list-unstyled">
                                                    <li class="err_attorney_registration"></li>
                                                </ul>
                                            </span>
                                            <a class="right-btn state_close_button" onclick="return removeAttornyState(this)" data-toggle="modal" data-target="#remove-coures">
                                                <img src="${SITEURL}/assets/web/images//icons/close_circle_red.png" alt="Close Icon">
                                            </a>
                                        </div>
                                    </div>
                                </div>`;

    $(attorny_states).insertAfter('.attorny-states:last');
    $(".myselect").selectbox();
}

function removeAttornyState(element)
{
  $(element).closest('.attorny-states').hide();
  $(element).closest('.attorny-states').find('*').attr('disabled', true);
  $(element).closest('.attorny-states').removeClass('active');
  if ($(element).closest('.attorny-states').hasClass('withAttorny') == true) 
  {
    $('.withAttorny').find('*').attr('disabled', true);
  }
}

function selectReportingImage(element)
{
  // alert(trtState == $(element).val());

  if (trtState == $(element).val()) 
  {
    $(element).closest('.attorny-states').find('.trtimage').removeClass('hide');
    $(element).closest('.attorny-states').find('.nontrtimage').addClass('hide');
  }
  else
  {
    $(element).closest('.attorny-states').find('.nontrtimage').removeClass('hide');
    $(element).closest('.attorny-states').find('.trtimage').addClass('hide');
  }
}

// billing address
function removeDisableBillingAddress(element)
{
  $(element).hide();
  $(element).closest('div').find('*').removeAttr('disabled');
  $(element).closest('div').find('button[type="submit"]').show();
}

$('#billingAddressForm').validator().on('submit', function (e) 
{
    if (!e.isDefaultPrevented()) 
    {
        const $this = $(this);
        const action = $this.attr('action');
        const formData = new FormData($this[0]);

        $.LoadingOverlay("show", {
              background: "rgba(165, 190, 100, 0)",
        });

        axios.post(action, formData)
        .then(function (response) 
        {
            $.LoadingOverlay("hide");
            const resp = response.data;
            if (resp.status == 'success') 
            {
                toastr.success(resp.msg);
                setTimeout(function()
                {
                  $this.closest('.my-profile-wrap').find('.edit').show();
                  $this.find('*').attr('disabled', true);
                  $this.find('button[type="submit"]').hide();
                }, 1000)
            }

            if (resp.status == 'error') 
            {
                toastr.error(resp.msg);
            }
        })
        .catch(function (error) 
        {
            $.LoadingOverlay("hide");

            const errorBag = error.response.data.errors;

            $.each(errorBag, function (fieldName, value) {
                  $('.err_' + fieldName).closest('.form-group').addClass('has-error has-danger');
                  $('.err_' + fieldName).text(value[0]).closest('span').show();
            })
        });

        return false;
    }
})

function changeStateDiv()
{
  var county = $('#my-profile-country').val();
  var defaultCountry = $('input[name="defaultCountry"]').val();
  if (county == defaultCountry) 
  {
    $('.us_section').removeClass('hide');
    $('.non_us_section').addClass('hide');
  }
  else
  {
    $('.us_section').addClass('hide');
    $('.non_us_section').removeClass('hide');
  }
}

// billing address
function removeDisableAreasOfInterest(element)
{
  $(element).hide();
  $(element).closest('div').find('*').removeAttr('disabled');
  $(element).closest('div').find('button[type="submit"]').show();
}

$('#areasOfIntersetForm').validator().on('submit', function (e) 
{
    if (!e.isDefaultPrevented()) 
    {
        const $this = $(this);
        const action = $this.attr('action');
        const formData = new FormData($this[0]);

        $.LoadingOverlay("show", {
              background: "rgba(165, 190, 100, 0)",
        });

        axios.post(action, formData)
        .then(function (response) 
        {
            $.LoadingOverlay("hide");
            const resp = response.data;
            if (resp.status == 'success') 
            {
                toastr.success(resp.msg);
                setTimeout(function()
                {
                  $this.closest('.my-profile-wrap').find('.edit').show();
                  $this.find('*').attr('disabled', true);
                  $this.find('button[type="submit"]').hide();
                }, 1000)
            }

            if (resp.status == 'error') 
            {
                toastr.error(resp.msg);
            }
        })
        .catch(function (error) 
        {
            $.LoadingOverlay("hide");

            const errorBag = error.response.data.errors;

            $.each(errorBag, function (fieldName, value) {
                  $('.err_' + fieldName).closest('.form-group').addClass('has-error has-danger');
                  $('.err_' + fieldName).text(value[0]).closest('span').show();
            })
        });

        return false;
    }
})