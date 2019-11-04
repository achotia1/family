$(document).ready(function() 
{
    var action = ADMINURL+'/customers/getCustomerOrders/'+customer_id; 

    const table = $('#listingTable').DataTable( 
    {
        "responsive": true,
        "processing": true,
        "bFilter": false, 
        "bInfo": true,
        "bLengthChange": false,
        "pagingType": "full_numbers",
        "serverSide": 'true',
        "ajax": {
            "url": action,
            "data": function (object) 
            {
                object.custom = {
                    "order_number"  : $('#order-number').val(),
                    "customer"      :  $('#order-customer').val(),
                    "name"          :  $('#order-product').val(),
                    "quantity"      : $('#order-quantity').val(),
                    "delivery_date" : $('#order-delivery-date').val(),
                    "dispatch_date" : $('#order-dispatch-date').val(),
                    "comment"       : $('#order-comment').val(),
                }
            }
        },
        "columns": [
            { "data": "id",  "visible": false, },
            { "data": "order_number"},
            // { "data": "customer",  "visible": isVisible,},
            { "data": "name"},
            { "data": "delivery_date"},
            { "data": "dispatch_date"},
            { "data": "quantity"},
            { "data": "cost", "visible": dispatcher,},
            // { "data": "po"},
            // { "data": "comment"},
            { "data": "orderstatus"},
            // { "data": "actions"}
        ],
        "aoColumnDefs": [{ "bSortable": false, "aTargets": [0] }],
        "lengthMenu": [[20, 20, 50, 100, 500], [20, 25, 50, 100, 500]],
        "aaSorting": [[0, 'DESC']],
        "language": {
          "processing": "Loading ...",
          "paginate": 
          {
            "first": `<a href="#" class="arrow hover-img"><span><img src="${BASEURL}/assets/admin/images/icons/left_arrow-Active.svg" alt=" view"></span></a>`,
            "previous": `<a href="#" class="arrow hover-img"><span><img src="${BASEURL}/assets/admin/images/icons/left_double_arrow-Active.svg" alt=" view"></span></a>`,
            "next": `<a href="#" class="arrow hover-img"><span><img src="${BASEURL}/assets/admin/images/icons/right_double_arrow-Active.svg" alt=" arrow"></span></a>`,
            "last": `<a href="#" class="arrow hover-img"><span><img src="${BASEURL}/assets/admin/images/icons/right_arrow-Active.svg" alt=" arrow"></span></a>`
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
        $('.my-select').selectbox();

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
              if($(element).find('td:nth-child(7)').text()=="Cancelled"){
                  $(element).addClass('light-red');
              }  
              if($(element).find('td:nth-child(7)').text()=="Delivered"){
                  $(element).addClass('light-green');
              }           
             // $(element).find('td:nth-child(5)').addClass('text-center');            
          }
        })

        // for search only
        // wrapper.find('tbody tr').first().addClass('inner-td theme-bg-blue-light vertical-align-middle');
        // wrapper.find('tbody tr').first().find('td').last().addClass('text-center');

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
        } 

        $('#order-delivery-date').datepicker({
            format: 'mm-dd-yyyy',
            autoclose: true,
        });
        $('#order-dispatch-date').datepicker({
                format: 'mm-dd-yyyy',
                autoclose: true,
            }); 
    }
});

function doSearch(element)
{
  $('#listingTable').DataTable().draw();
}

function addNote(element)
{
   document.getElementById('AddNoteForm').reset();
   $("#note_order_number").text($(element).attr('order-number'));
   $("#note_order_id").val($(element).attr('order-id'));
}

function removeSearch(element)
{ 
  $('#order-number').val(''),
  $('#order-customer').val(''),
  $('#order-product').val(''),
  $('#order-dispatch-date').val(''),
  $('#order-delivery-date').val(''),
  $('#order-comment').val(''),
  $('#listingTable').DataTable().draw();
}