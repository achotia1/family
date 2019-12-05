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


    var action = ADMINURL+'/contribution-report/getContributionRecords'; 

    const table = $('#listingTable').DataTable( 
    {
        "stateSave": false,
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
                }
            }
        },
        "columns": [
            { "data": "id",  "visible": false, },            
            { "data": "invoice_date"},
            { "data": "invoice_no"},
            { "data": "customer_name"},
            { "data": "product_name"},            
            { "data": "batch_code"},
            { "data": "quantity"},
            { "data": "rate"},
            { "data": "net_cost"},
            { "data": "costing"},
            { "data": "gross"},
            { "data": "total"},
            { "data": "material_consumption"},
        ],
        "aoColumnDefs": [{ "bSortable": false, "aTargets": [0] }],
        "lengthMenu": [[20, 25, 50, 100], [20, 25, 50, 100]],
        "aaSorting": [[0, 'DESC']],
       
    });
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
  $('#listingTable').DataTable().draw();

}