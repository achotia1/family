$(document).ready(function() 
{
    var action = ADMINURL+'/materials-out/getRecords'; 

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
                	"batch_id" :  $('#batch-id').val(),
                	"product_code" :  $('#product-code').val(),
                	"sellable_qty" :  $('#sellable-qty').val(),
                	"loss_material" :  $('#loss-material').val(),
                	"yield" :  $('#yield').val(),
                }
            }
        },
        "columns": [
            { "data": "id",  "visible": false, },
            { "data": "select"},
            { "data": "batch_id"},
            { "data": "product_code"},
            { "data": "sellable_qty"},
            { "data": "loss_material"},
            { "data": "yield"},            
            { "data": "actions"}
        ],
        "aoColumnDefs": [{ "bSortable": false, "aTargets": [0,1,7] }],
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
        /*$('.my-select').selectbox();

        var wrapper = this.parent();

        // set global class
        wrapper.find('.dataTables_info').addClass('card-subtitle pb-0');

        // entries info class
        wrapper.find('tbody tr').addClass('inner-td');
        
        // for each tr td
        wrapper.find('tbody tr').each(function(index, element)
        {
          if (index != '0') 
          {
             if($(element).find('td:nth-child(4)').text()=="Inactive"){
                  $(element).addClass('bg-light-gray');
              }
             // $(element).find('td:nth-child(3)').addClass('text-center');            
             $(element).find('td:nth-child(5)').addClass('text-center');            
          }
        })

        // for search only
        wrapper.find('tbody tr').first().addClass('inner-td theme-bg-blue-light vertical-align-middle');
        wrapper.find('tbody tr').first().find('td').last().addClass('text-center');

        // pagination 
        if(wrapper.find("a.first").hasClass("disabled"))
        {
          wrapper.find("a.first").html(`<a href="#" class="arrow hover-img"><span><img src="${BASEURL}/assets/admin/images/icons/left_arrow.svg" alt=" view"></span></a>`);
        }

        if(wrapper.find("a.previous").hasClass("disabled"))
        {
          wrapper.find("a.previous").html(`<a href="#" class="arrow hover-img"><span><img src="${BASEURL}/assets/admin/images/icons/left_double_arrow.svg" alt=" view"></span></a>`);
        }

        if(wrapper.find("a.last").hasClass("disabled"))
        {
          wrapper.find("a.last").html(`<a href="#" class="arrow hover-img"><span><img src="${BASEURL}/assets/admin/images/icons/right_arrow.svg" alt=" view"></span></a>`);
        }

        if(wrapper.find("a.next").hasClass("disabled"))
        {
          wrapper.find("a.next").html(`<a href="#" class="arrow hover-img"><span><img src="${BASEURL}/assets/admin/images/icons/right_double_arrow.svg" alt=" view"></span></a>`);
        } */
    }
});

function doSearch(element)
{
  $('#listingTable').DataTable().draw();
}

function removeSearch(element)
{ 
	$('#batch-id').val('')
	$('#product-code').val('')
	$('#sellable-qty').val('')
	$('#loss-material').val('')
	$('#yield').val('')
	$('#listingTable').DataTable().draw();
}

function deleteCollections(element)
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
	
      action = ADMINURL+'/materials-out/bulkDelete';

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
}