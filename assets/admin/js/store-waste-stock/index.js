$(document).ready(function() 
{
    var action = ADMINURL+'/wastage-material/getRecords'; 

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
                    "batch_id" :  $('#batch-id').val(),
                    "product_code" :  $('#product-code').val(),
                    "balance_course" :  $('#balance-course').val(),
                    "balance_rejection" :  $('#balance-rejection').val(),
                    "balance_dust" :  $('#balance-dust').val(),
                    "balance_loose" :  $('#balance-loose').val(),                    
                }
            }
        },
        "columns": [
            { "data": "id",  "visible": false, },
            /*{ "data": "select"},*/
            { "data": "batch_id"},
            { "data": "product_code"},            
            { "data": "balance_course"},
            { "data": "balance_rejection"},
            { "data": "balance_dust"},
            { "data": "balance_loose"},
            { "data": "actions"}
        ],
        "aoColumnDefs": [{ "bSortable": false, "aTargets": [0,7] }],
        "lengthMenu": [[20, 25, 50, 100], [20, 25, 50, 100]],
        "aaSorting": [[0, 'DESC']],
        "createdRow": function ( row, data, index ) {			
			if ( data['review_status'] == 'Closed') {                
                $(row).addClass('batch-closed');
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
        $('.select2').select2();
    }
});

function doSearch(element)
{
  $('#listingTable').DataTable().draw();
}

function removeSearch(element)
{
  $('#batch-id').val(''),
  $('#product-code').val(''),
  $('#quantity').val(''),
  $('#review-status').val(''),
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