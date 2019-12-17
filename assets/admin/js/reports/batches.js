$(document).ready(function() 
{
    var date = new Date();
    $("#from-date").val(date.getDate()+"-"+(date.getMonth()+1)+"-"+date.getFullYear());
    $("#to-date").val(date.getDate()+"-"+(date.getMonth()+1)+"-"+date.getFullYear());

    $('#from-date').datepicker({
            format: 'dd-mm-yyyy',
            autoclose: true,
        });
    $('#to-date').datepicker({
            format: 'dd-mm-yyyy',
            autoclose: true,
        });
	
	
	/* SET COKKIES TO PRESERVE PREVIOUS STATE */
	if (typeof $.cookie('batch-fdate') != 'undefined'){
 		$('#from-date').val($.cookie("batch-fdate")); 		
	} 
	if (typeof $.cookie('batch-tdate') != 'undefined'){
 		$('#to-date').val($.cookie("batch-tdate"));		
	}
	if (typeof $.cookie('batch-pid') != 'undefined'){
 		$('#product-id').val($.cookie("batch-pid"));	
	}
	/* ERASE COOKIE */
	/*$.removeCookie("batch-fdate");
	$.removeCookie("batch-tdate");
	$.removeCookie("batch-pid");*/
	
	$('.cls-show-result').click(function() {
		$.cookie("batch-fdate", $('#from-date').val());
		$.cookie("batch-tdate", $('#to-date').val());
		$.cookie("batch-pid", $('#product-id').val());
	});	
	/* END SET COOKIES TO PRESERVE PREVIOUS STATE */
	
    var action = ADMINURL+'/batch-summary/getBatchRecords'; 

    const table = $('#listingTable').DataTable( 
    {
        "stateSave": true,
        "responsive": true,
        "processing": true,
        "bFilter": true, 
        "bInfo": true,
        // "bLengthChange": false,
        // "pagingType": "full_numbers",
        "serverSide": 'true',
        "ajax": {
            "url": action,
            "data": function (object) 
            {
                object.custom = {                	
                	"from-date" :  $('#from-date').val(),
                    "to-date"   :  $('#to-date').val(),
                    "product-id" :  $('#product-id').val(),
                }
            }
        },
        "columns": [
            { "data": "id",  "visible": false, },
            // { "data": "select"},
            { "data": "batch_id"},
            { "data": "product_code"},
            { "data": "sellable_qty"},
            { "data": "loss_material"},
            { "data": "yield"},            
            // { "data": "actions"}
        ],
        "aoColumnDefs": [{ "bSortable": false, "aTargets": [0] }],
        "lengthMenu": [[20, 25, 50, 100], [20, 25, 50, 100]],
        "aaSorting": [[0, 'DESC']],
       /* "language": {
          "processing": "Loading ...",
          "paginate": 
          {
            "first": `<a href="#" class="arrow hover-img"><span><img src="${BASEURL}/assets/admin/images/icons/left_arrow-Active.svg" alt=" view"></span></a>`,
            "previous": `<a href="#" class="arrow hover-img"><span><img src="${BASEURL}/assets/admin/images/icons/left_double_arrow-Active.svg" alt=" view"></span></a>`,
            "next": `<a href="#" class="arrow hover-img"><span><img src="${BASEURL}/assets/admin/images/icons/right_double_arrow-Active.svg" alt=" arrow"></span></a>`,
            "last": `<a href="#" class="arrow hover-img"><span><img src="${BASEURL}/assets/admin/images/icons/right_arrow-Active.svg" alt=" arrow"></span></a>`
          }
        }*/
    });

    /*table.on("draw.dt", function (e) {                    
        setCustomPagingSigns.call($(this));
    }).each(function () {
        setCustomPagingSigns.call($(this)); 
    });*/
});

$(document).on('mouseleave', '.datepicker', function(){
   $('#from-date').datepicker('hide'); 
   $('#to-date').datepicker('hide'); 
});

$(document).on('click', '#from-date', function(){
   $('#from-date').datepicker('show'); 
});
$(document).on('click', '#to-date', function(){
   $('#to-date').datepicker('show'); 
});

function doSearch(element)
{  
  // $(element).addClass('hide');
  // $(element).next('a').removeClass('hide');
  $('#listingTable').DataTable().draw();

}