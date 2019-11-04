$(document).ready(function () {


	// loading records
	var action = SITEURL + '/online-courses/getRecords';

	var state_id = $("#stae_drop_head").val();
	var credit_type = $(".credit_type").val();
	var practice_area = $(".practice_area").val();

	getAllData(action, state_id, practice_area, credit_type);
	$(".filter-course").addClass('d-none');

	// filtering records via state
	$("#stae_drop_head").on("change", function () {
		var state_id = this.value;
		var credit_type = $(".credit_type").val();
		var practice_area = $(".practice_area").val();
		if (this.value != 0) {
			$(".filter-course").removeClass('d-none');
		}
		getAllData(action, state_id, practice_area, credit_type);
	});

	$(".practice_area").on("change", function () {
		var state_id = $("#stae_drop_head").val();
		var credit_type = $(".credit_type").val();
		var practice_area = this.value;
		getAllData(action, state_id, practice_area, credit_type);
	});

	$(".credit_type").on("change", function () {
		var state_id = $("#stae_drop_head").val();

		var credit_type = this.value;
		var practice_area = $(".practice_area").val();
		getAllData(action, state_id, practice_area, credit_type);
	});



});


function getAllData(action, state_id, practice_area, credit_type) {
	$.LoadingOverlay("show",
		{
			background: "rgba(1, 1, 1, 0)",
		});

	$data = new FormData();

	$data.append('state_id', state_id);
	$data.append('practice_area', practice_area);
	$data.append('credit_type', credit_type);

	axios.post(action, $data)
		.then(function (response) 
		{
			const resp = response.data;
			$.LoadingOverlay("hide");
			$('#listing-courses').empty();
			$('#listing-courses').append(resp.html);
			$('#message_html').html(resp.message_html);
			$('html, body').animate({ scrollTop: 0 }, '700');

			$('.s_name').html($("#stae_drop_head option:selected").html());
			$('.p_name').html($(".practice_area option:selected").html());
			$('.c_name').html($(".credit_type option:selected").html());

		})
		.catch(function (error) {
			$.LoadingOverlay("hide");
		});
}




$(document).on('click', '.pagination-links a', function () {

	var state_id = $("#stae_drop_head").val();
	var credit_type = $(".credit_type").val();
	var practice_area = $(".practice_area").val();

	var action = $(this).attr('href');

	getAllData(action, state_id, practice_area, credit_type);

	return false;
});

