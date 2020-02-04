$(document).ready(function() 
{
    var date = new Date();
    /*$("#from-date").val(date.getDate()+"-"+(date.getMonth()+1)+"-"+date.getFullYear());
    $("#to-date").val(date.getDate()+"-"+(date.getMonth()+1)+"-"+date.getFullYear());*/
    $("#from-date").val(getTwoDigitDateFormat(date.getDate())+"-"+getTwoDigitDateFormat(date.getMonth()+1)+"-"+date.getFullYear());
    $("#to-date").val(getTwoDigitDateFormat(date.getDate())+"-"+getTwoDigitDateFormat(date.getMonth()+1)+"-"+date.getFullYear());
    
    $('#from-date').datepicker({
            format: 'dd-mm-yyyy',
            autoclose: true,
        });
    $('#to-date').datepicker({
            format: 'dd-mm-yyyy',
            autoclose: true,
        });


    var action = ADMINURL+'/wastage-stock-report/getWasteStockRecords'; 

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
            { "data": "name"},            
            { "data": "opening_stock"},
            { "data": "received_qty"},            
            { "data": "issued_qty"},            
            { "data": "balance_qty"},                       
        ],
        "aoColumnDefs": [{ "bSortable": false, "aTargets": [0,9] }],
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
