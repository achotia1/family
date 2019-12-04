$(document).ready(function() 
{
    $("#interval-time").val(5);


    var action = ADMINURL+'/aged-materials/getAgedMaterialRecords'; 

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
                }
            }
        },
        "columns": [
            { "data": "id",  "visible": false, },
            // { "data": "select"},
            { "data": "lot_no"},
            { "data": "material_id"},
            { "data": "lot_balance"},
            { "data": "last_used_at"},            
            { "data": "created_at"}
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