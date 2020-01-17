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
                    "product-id" :  $('#product-id').val(),	
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
        "footerCallback": function(row, data, start, end, display) {
          var api = this.api(), data;

         // converting to interger to find total
            var intVal = function ( i ) {
                return typeof i === 'string' ?
                    i.replace(/[\$,]/g, '')*1 :
                    typeof i === 'number' ?
                        i : 0;
            };

            var quantity = api
                .column( 6 )
                .data()
                .reduce( function (a, b) {
                    return (intVal(a) + intVal(b)).toFixed(2);
                }, 0 );

            var rate = api
                .column( 7 )
                .data()
                .reduce( function (a, b) {
                    return (intVal(a) + intVal(b)).toFixed(2);
                }, 0 );

            var net = api
                .column( 8 )
                .data()
                .reduce( function (a, b) {
                    return (intVal(a) + intVal(b)).toFixed(2);
                }, 0 );

            var costing = api
                .column( 9 )
                .data()
                .reduce( function (a, b) {
                    return (intVal(a) + intVal(b)).toFixed(2);
                }, 0 );

            var gross = api
                .column( 10 )
                .data()
                .reduce( function (a, b) {
                    return (intVal(a) + intVal(b)).toFixed(2);
                }, 0 );

            var total_cal = api
                .column( 11 )
                .data()
                .reduce( function (a, b) {
                    return (intVal(a) + intVal(b)).toFixed(2);
                }, 0 );

            /*var materialConsumption = api
                .column( 12 )
                .data()
                .reduce( function (a, b) {
                    return (intVal(a) + intVal(b)).toFixed(2);
                }, 0 );*/
            /*var materialConsumptionTotal = 0;
            if(net>0){
                materialConsumptionTotal = (100-((total_cal/net)*100)).toFixed(2);
            }*/
            
			var rateRatio = grossRatio = 0;
			if(quantity>0){
				rateRatio = (net/quantity).toFixed(2);
				grossRatio = (total_cal/quantity).toFixed(2);	
			}
			var materialConsumptionTotal = 0;
            if(net>0){
                materialConsumptionTotal = (100-((grossRatio/net)*100)).toFixed(2);
            }
            // Update footer by showing the total with the reference of the column index 
            $( api.column( 5 ).footer() ).html('Total');
            $( api.column( 6 ).footer() ).html(quantity);
            $( api.column( 7 ).footer() ).html(rateRatio);
            $( api.column( 8 ).footer() ).html(net);
            $( api.column( 9 ).footer() ).html('');
            $( api.column( 10 ).footer() ).html(grossRatio);
            $( api.column( 11 ).footer() ).html(total_cal);
            $( api.column( 12 ).footer() ).html(materialConsumptionTotal);
         
          /*api.columns('.odd', {
            page: 'current'
          }).every(function() {
            var sum = this
              .data()
              .reduce(function(a, b) {
                var x = parseFloat(a) || 0;
                var y = parseFloat(b) || 0;
                return x + y;
              }, 0);
            console.log(sum); //alert(sum);
            $(this.footer()).html(sum);
          });*/
        }
       
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