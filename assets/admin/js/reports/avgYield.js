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
	if (typeof $.cookie('yield-fdate') != 'undefined'){
 		$('#from-date').val($.cookie("yield-fdate")); 		
	} 
	if (typeof $.cookie('yield-tdate') != 'undefined'){
 		$('#to-date').val($.cookie("yield-tdate"));		
	}
	if (typeof $.cookie('yield-pid') != 'undefined'){
 		$('#product-id').val($.cookie("yield-pid"));	
	}
	/* ERASE COOKIE */
	
	$('.cls-show-result').click(function() {
		$.cookie("yield-fdate", $('#from-date').val());
		$.cookie("yield-tdate", $('#to-date').val());
		$.cookie("yield-pid", $('#product-id').val());
	});	
	/* END SET COOKIES TO PRESERVE PREVIOUS STATE */
	
    var action = ADMINURL+'/avg-yield/getAvgYieldRecords'; 

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
            { "data": "batch_card_no"},
            { "data": "product"},
            { "data": "input_material"},
            { "data": "sellable_qty"},            
            { "data": "yield"},
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