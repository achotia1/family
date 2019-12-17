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
	if (typeof $.cookie('dm-fdate') != 'undefined'){
 		$('#from-date').val($.cookie("dm-fdate")); 		
	} 
	if (typeof $.cookie('dm-tdate') != 'undefined'){
 		$('#to-date').val($.cookie("dm-tdate"));		
	}
	if (typeof $.cookie('dm-mid') != 'undefined'){
 		$('#material-id').val($.cookie("dm-mid"));	
	}
	
	$('.cls-show-result').click(function() {
		$.cookie("dm-fdate", $('#from-date').val());
		$.cookie("dm-tdate", $('#to-date').val());
		$.cookie("dm-mid", $('#material-id').val());
	});	
	/* END SET COOKIES TO PRESERVE PREVIOUS STATE */
	
    var action = ADMINURL+'/deviation-material/getdeviationMaterialRecords'; 

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
                    "material-id" :  $('#material-id').val(),
                }
            }
        },
        "columns": [
            { "data": "id",  "visible": false, },
            // { "data": "select"},
            { "data": "lot_no"},
            { "data": "materialName"},
            { "data": "balance_corrected_at"},
            //{ "data": "loss_material"},
            //{ "data": "yield"},            
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