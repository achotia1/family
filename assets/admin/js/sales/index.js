$(document).ready(function() 
{

    var action = ADMINURL+'/sales/getRecords'; 

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
                    "invoice_no" : $('#invoice-no').val(),
                    "invoice_date" : $('#invoice-date').val(),
                    "customer_id" : $('#customer-id').val(),
                }
            }
        },
        "columns": [
            { "data": "id",  "visible": false, },
           /* { "data": "select"},*/
            { "data": "invoice_no"},
            { "data": "invoice_date"},
            { "data": "customer_name"},
            { "data": "actions"}
        ],
        "aoColumnDefs": [{ "bSortable": false, "aTargets": [0,4] }],
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

    table.on("draw.dt", function (e) {                    
        setCustomPagingSigns.call($(this));
    }).each(function () {
        setCustomPagingSigns.call($(this)); 
    });
    function setCustomPagingSigns() 
    {

        $('.datepicker').datepicker({
          autoclose: true,
          format: 'dd-mm-yyyy',
          orientation: "bottom"
          // startDate: new Date()
        })

        $('.select2').select2()
    }
});

function doSearch(element)
{
  $('#listingTable').DataTable().draw();
}

function removeSearch(element)
{ 

  $('#material-id').val(''),
  $('#item-code').val(''),
  $('#product-name').val(''),
  $('#quantity').val(''),
  $('#bill-number').val(''),
  $('#listingTable').DataTable().draw();
}

/*function deleteCollections(element)
{

   var $members = $('.rowSelect:checked');

   if ($members.length == 0) 
   {
      swal("Error",'Please select atleast one record.','error');
      return false; 
   }
   else
   {

      var arrEncId = [];
      $members.each(function()
      {
            arrEncId.push($(this).val());            
      })
		//console.log(arrEncId);
      action = ADMINURL+'/return/bulkDelete';

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
        function () 
        {   
            axios.post(action, { arrEncId:arrEncId })
            .then(function (response) 
            {
              if (response.data.status == 'success') 
              {
                swal("Success",response.data.msg,'success');
                $('#listingTable').DataTable().ajax.reload();
                
              }

              if (response.data.status === 'error') 
              {
                swal("Error",response.data.msg,'error');                
              }

            })
            .catch(function (error) 
            {
               // swal("Error",error.response.data.msg,'error');
            }); 
        });
   } 
}*/

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