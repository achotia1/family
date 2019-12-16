$(document).ready(function() 
{
    $("#interval-time").val(10);


    var action = ADMINURL+'/aged-products/getAgedProductRecords'; 

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
                	"interval-time" :  $('#interval-time').val(),
                	"product-id" :  $('#product-id').val(),
                }
            }
        },
        "columns": [
            { "data": "id",  "visible": false, },
            // { "data": "select"},
            { "data": "batch"},
            { "data": "product"},
            { "data": "stock_balance"},
            { "data": "last_used_at"},            
            { "data": "stock_in_date"}
        ],
        "aoColumnDefs": [{ "bSortable": false, "aTargets": [0] }],
        "lengthMenu": [[20, 25, 50, 100], [20, 25, 50, 100]],
        "aaSorting": [[0, 'DESC']],       
    });    
});

function doSearch(element)
{ 
  $('#listingTable').DataTable().draw();
}