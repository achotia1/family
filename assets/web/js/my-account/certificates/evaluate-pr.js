/*
* @Author: sheshkumar
* @Date:   2019-05-29 11:46:22
* @Last Modified by:   sheshkumar
* @Last Modified time: 2019-06-03 18:26:46
*/
$('#evaluationForm').validator().on('submit', function (e) 
{
    if (!e.isDefaultPrevented()) 
    {

        const $this = $(this);
        const action = $this.attr('action');
        const formData = new FormData($this[0]);

        $.LoadingOverlay("show",
        {
            background: "rgba(165, 190, 100, 0)",
        });

        axios.post(action, formData)
        .then(function (response) 
        {
            const resp = response.data;

            if (resp.status == 'success') 
            {
                $this[0].reset();
                toastr.success(resp.msg);
                $.LoadingOverlay("hide");
                setTimeout(function () 
                {
                    window.location.href = resp.url;
                }, 1000)
            }

            if (resp.status == 'error') 
            {
                toastr.error(resp.msg);
            }
            
            $.LoadingOverlay("hide");
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

function changeState(element)
{
    window.location.href = $(element).val();
}

function addMoreResourceSpeaker(element)
{
    var index = $('.resource_speaker').length;

    var resource_speaker = `<div class="table-responsive resource_speaker">
                                <table class="table">
                                    <tbody>
                                        <tr>
                                            <td class="tabel-title" colspan="7">
                                                <div class="d-flex align-items-center">
                                                <span>Recurso (conferenciante): </span>
                                                <div class="form-group">
                                                    <input type="text" name="resource_speaker[${index}][comment]" class="form-control" placeholder="Respondé aquí." />
                                                </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <span class="d-flex td-title"><span>7.</span> Conocimiento del tema por recurso.</span>
                                            </td>
                                            <td>
                                                <div class="pretty p-default p-round">
                                                    <input type="radio" name="resource_speaker[${index}][subject_by_resource][rating]" value="1" >
                                                    <div class="state">
                                                        <label></label>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="pretty p-default p-round">
                                                    <input type="radio" name="resource_speaker[${index}][subject_by_resource][rating]" value="2" >
                                                    <div class="state">
                                                        <label></label>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="pretty p-default p-round">
                                                    <input type="radio" name="resource_speaker[${index}][subject_by_resource][rating]" value="3" >
                                                    <div class="state">
                                                        <label></label>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="pretty p-default p-round">
                                                    <input type="radio" name="resource_speaker[${index}][subject_by_resource][rating]" value="4" >
                                                    <div class="state">
                                                        <label></label>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="pretty p-default p-round">
                                                    <input type="radio" name="resource_speaker[${index}][subject_by_resource][rating]" value="5" checked >
                                                    <div class="state">
                                                        <label></label>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="pretty p-curve">
                                                    <input type="checkbox" name="resource_speaker[${index}][subject_by_resource][comment]">
                                                    <div class="state p-success">
                                                        <div class="check"></div>
                                                        <label></label>
                                                </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <span class="d-flex td-title"><span>8.</span> La(s) estrategia( utilizada(s) para presentar el tema.</span>
                                            </td>                                
                                            <td>
                                                <div class="pretty p-default p-round">
                                                    <input type="radio" name="resource_speaker[${index}][strategy_used][rating]" value="1" >
                                                    <div class="state">
                                                        <label></label>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="pretty p-default p-round">
                                                    <input type="radio" name="resource_speaker[${index}][strategy_used][rating]" value="2" >
                                                    <div class="state">
                                                        <label></label>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="pretty p-default p-round">
                                                    <input type="radio" name="resource_speaker[${index}][strategy_used][rating]" value="3" >
                                                    <div class="state">
                                                        <label></label>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="pretty p-default p-round">
                                                    <input type="radio" name="resource_speaker[${index}][strategy_used][rating]" value="4" >
                                                    <div class="state">
                                                        <label></label>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="pretty p-default p-round">
                                                    <input type="radio" name="resource_speaker[${index}][strategy_used][rating]" value="5" checked >
                                                    <div class="state">
                                                        <label></label>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="pretty p-curve">
                                                    <input type="checkbox" name="resource_speaker[${index}][strategy_used][comment]">
                                                    <div class="state p-success">
                                                        <div class="check"></div>
                                                        <label></label>
                                                </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <span class="d-flex td-title"><span>9.</span> La organización del contenido.</span>
                                            </td>
                                            <td>
                                                <div class="pretty p-default p-round">
                                                    <input type="radio" name="resource_speaker[${index}][content_of_organisation][rating]" value="1" >
                                                    <div class="state">
                                                        <label></label>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="pretty p-default p-round">
                                                    <input type="radio" name="resource_speaker[${index}][content_of_organisation][rating]" value="2" >
                                                    <div class="state">
                                                        <label></label>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="pretty p-default p-round">
                                                    <input type="radio" name="resource_speaker[${index}][content_of_organisation][rating]" value="3" >
                                                    <div class="state">
                                                        <label></label>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="pretty p-default p-round">
                                                    <input type="radio" name="resource_speaker[${index}][content_of_organisation][rating]" value="4" >
                                                    <div class="state">
                                                        <label></label>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="pretty p-default p-round">
                                                    <input type="radio" name="resource_speaker[${index}][content_of_organisation][rating]" value="5" checked >
                                                    <div class="state">
                                                        <label></label>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="pretty p-curve">
                                                    <input type="checkbox" name="resource_speaker[${index}][content_of_organisation][comment]">
                                                    <div class="state p-success">
                                                        <div class="check"></div>
                                                        <label></label>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>`;

    $(resource_speaker).insertAfter('.resource_speaker:last');
}