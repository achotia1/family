$(document).ready(function () 
{
  var action = ADMINURL + '/users/getRecords';

  const table = $('#listingTable').DataTable(
    {
      "responsive": true,
      "processing": true,
      "bFilter": true,
      "bInfo": true,
      "bLengthChange": true,
      //"pagingType": "full_numbers",
      "serverSide": 'true',
      "ajax": action,
      "columns": [
        { "data": "id", "visible": false },
        { "data": "name" },
        { "data": "email" },
        { "data": "mobile_number"},
        { "data": "role" },
        { "data": "actions" }
      ],

      "aoColumnDefs": [{ "bSortable": false, "aTargets": [0,4,5] }],
      "lengthMenu": [[20, 25, 50, 100], [20, 25, 50, 100]],
      "aaSorting": [[0, 'DESC']],
      /*"language": {
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

  table.on("draw.dt", function (e) 
  {
    setCustomPagingSigns.call($(this));
  }).each(function () {
    setCustomPagingSigns.call($(this));
  });

  function setCustomPagingSigns() {
    var wrapper = this.parent();

    // set global class
    wrapper.find('.dataTables_info').addClass('card-subtitle pb-0');

    // entries info class
    wrapper.find('tbody tr').addClass('inner-td');

    // for users module only || add custome class for tr
   /* wrapper.find('tbody tr').each(function () {
      $(this).find('td:nth-child(3)').addClass('theme-green semibold f-18 text-underline');
      // $(this).find('td:nth-child(4)').addClass('text-center');
      $(this).find('td:nth-child(5)').addClass('theme-green semibold f-18');//text-center
    })*/

    
    // for search only
    // wrapper.find('tbody tr').first().addClass('inner-td theme-bg-blue-light vertical-align-middle');
    // wrapper.find('tbody tr').first().find('td').last().addClass('text-center');

    // pagination 
   /* if (wrapper.find("a.first").hasClass("disabled")) {
      wrapper.find("a.first").html(`<a href="#" class="arrow hover-img"><span><img src="${BASEURL}/assets/admin/images/icons/left_arrow.svg" alt=" view"></span></a>`);
    }

    if (wrapper.find("a.previous").hasClass("disabled")) {
      wrapper.find("a.previous").html(`<a href="#" class="arrow hover-img"><span><img src="${BASEURL}/assets/admin/images/icons/left_double_arrow.svg" alt=" view"></span></a>`);
    }

    if (wrapper.find("a.last").hasClass("disabled")) {
      wrapper.find("a.last").html(`<a href="#" class="arrow hover-img"><span><img src="${BASEURL}/assets/admin/images/icons/right_arrow.svg" alt=" view"></span></a>`);
    }

    if (wrapper.find("a.next").hasClass("disabled")) {
      wrapper.find("a.next").html(`<a href="#" class="arrow hover-img"><span><img src="${BASEURL}/assets/admin/images/icons/right_double_arrow.svg" alt=" view"></span></a>`);
    }*/
  }
});

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

