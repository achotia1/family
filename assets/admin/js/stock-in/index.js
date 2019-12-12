$(document).ready(function() 
{
    var action = ADMINURL+'/sale-stock/getRecords'; 

    const table = $('#listingTable').DataTable( 
    {
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
                    "batch_code" :  $('#batch-code').val(),
                    "product" : $('#product').val(),
                    "quantity" : $('#quantity').val(),
                    "balance_quantity" : $('#balance-quantity').val(),
                    "manufacturing_cost" : $('#manufacturing-cost').val(),
                    "status" : $('#status').val()
                }
            }
        },
        "columns": [
            { "data": "id",  "visible": false, },
            /*{ "data": "select"},*/
            { "data": "batch_code"},
            { "data": "product"},
            { "data": "quantity"},
            { "data": "balance_quantity"},           
            { "data": "manufacturing_cost"},
            { "data": "status"},
            { "data": "actions"}
        ],
        "aoColumnDefs": [{ "bSortable": false, "aTargets": [0,7] }],
        "lengthMenu": [[20, 25, 50, 100], [20, 25, 50, 100]],
        "aaSorting": [[0, 'DESC']],
        "createdRow": function ( row, data, index ) {			
			if ( data['status'] == 'Yes') {                
                $(row).addClass('opening-stock');
            }                  
		}
    });

    table.on("draw.dt", function (e) {                    
        setCustomPagingSigns.call($(this));
    }).each(function () {
        setCustomPagingSigns.call($(this)); 
    });

    function setCustomPagingSigns() 
    {
        
    }
});

function doSearch(element)
{
	$('#listingTable').DataTable().draw();
}

function removeSearch(element)
{ 
	$('#batch-code').val(''),
	$('#product').val(''),
	$('#quantity').val(''),
	$('#balance-quantity').val(''),
	$('#manufacturing-cost').val('')
	$('#listingTable').DataTable().draw();
}

function deleteCollection(element) 
{
	var $this = $(element);
	var action = $this.attr('data-href');
	
	if (action != '') {
		swal({
			title: "Are you sure !!",
			text: "You want to delete ?",
			type: "warning",
			showCancelButton: true,
			confirmButtonText: "Delete",
			confirmButtonClass: "btn-danger",
			closeOnConfirm: false,
			showLoaderOnConfirm: true
		},
		function () {
			axios.delete(action)
			.then(function (response) {
				if (response.data.status === 'success') {
					swal("Success", response.data.msg, 'success');
					$('#listingTable').DataTable().ajax.reload();
				}
				if (response.data.status === 'error') {
					swal("Error", response.data.msg, 'error');
				}
			})
			.catch(function (error) {
				// swal("Error",error.response.data.msg,'error');
			});
		});
	}
}