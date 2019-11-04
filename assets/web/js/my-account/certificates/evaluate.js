/*
* @Author: sheshkumar
* @Date:   2019-05-29 11:46:22
* @Last Modified by:   sheshkumar
* @Last Modified time: 2019-05-29 14:31:17
*/
$('#evaluationForm').validator().on('submit', function (e) 
{
    if (!e.isDefaultPrevented()) 
    {


        const $this = $(this);
        const action = $this.attr('action');
        const formData = new FormData($this[0]);

        var videos = [];
        if ($('.video').length != 0) 
        {
        	console.log(videos);
        	$i = 0;
            $('.video').each(function (item, element) 
            {

           		tmp = {};
           		tmp.video_id = 	$(element).attr('name');
           		tmp.video_value = $(element).val();
           		videos[$i] = tmp;
           		$i++;
            })
        }

        formData.append('videos', JSON.stringify(videos));

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