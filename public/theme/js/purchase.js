
//On Enter Move the cursor to desigtation Id
function shift_cursor(kevent,target){

    if(kevent.keyCode==13){
		$("#"+target).focus();
    }
	
}


$('#save,#update').click(function (e) {
	var base_url=$("#base_url").val().trim();

    //Initially flag set true
    var flag=true;

    function check_field(id)
    {

      if(!$("#"+id).val().trim() ) //Also check Others????
        {

            $('#'+id+'_msg').fadeIn(200).show().html('Required Field').addClass('required');
           // $('#'+id).css({'background-color' : '#E8E2E9'});
            flag=false;
        }
        else
        {
             $('#'+id+'_msg').fadeOut(200).hide();
             //$('#'+id).css({'background-color' : '#FFFFFF'});    //White color
        }
    }


   //Validate Input box or selection box should not be blank or empty
	  check_field("supplier_id");
    check_field("pur_date");
    check_field("purchase_status");

   // console.log($('#purchase_type :selected').val());
    console.log($("#purchase_type :selected").val());


if($('#purchase_type option:selected').val() == 'local'){

}else{
 // check_field("currency_value");
  check_field("currency_type");
   
}

    //check_field("warehouse_id");
	/*if(!isNaN($("#amount").val().trim()) && parseFloat($("#amount").val().trim())==0){
        toastr["error"]("You have entered Payment Amount! <br>Please Select Payment Type!");
        return;
    }*/
	if(flag==false)
	{
		toastr["error"]("You have missed Something to Fillup!");
		return;
	}

	//Atleast one record must be added in purchase table 
    var rowcount=document.getElementById("hidden_rowcount").value;
	var flag1=false;
	for(var n=1;n<=rowcount;n++){
		if($("#td_data_"+n+"_3").val()!=null && $("#td_data_"+n+"_3").val()!=''){
			flag1=true;
		}	
	}
	
    if(flag1==false){
    	toastr["warning"]("Please Select Item!!");
        $("#item_search").focus();
		return;
    }
    //end

    var tot_subtotal_amt=$("#subtotal_amt").text();
    var other_charges_amt=$("#other_charges_amt").text();//other_charges include tax calcualated amount
    var tot_discount_to_all_amt=$("#discount_to_all_amt").text();
    var tot_round_off_amt=$("#round_off_amt").text();
    var tot_total_amt=$("#total_amt").text();
    var customs_duty_amount = $('#customs_duty_amt').text();
    var customs_duty_vat = $('#customs_duty_vat').val();
    var vat_id = $('#vat_id').val();
    var vat_amount =  $('#vat_amt').text();
    var customs_clearance = $('#customs_clearance_input').val();
    var other_charges_input = $('#other_charges_input').val();
    var other_charges_name = $('#other_charges_name').val();
    var freight_charges =  $('#freight_charges').val();

    var this_id=this.id;
    
			//if(confirm("Do You Wants to Save Record ?")){
				e.preventDefault();
				data = new FormData($('#purchase-form')[0]);//form name
        /*Check XSS Code*/
        if(!xss_validation(data)){ return false; }
        
        $(".box").append('<div class="overlay"><i class="fa fa-refresh fa-spin"></i></div>');
        $("#"+this_id).attr('disabled',true);  //Enable Save or Update button
				$.ajax({
				type: 'POST',
				url: base_url+'purchase/purchase_save_and_update?command='+this_id+'&rowcount='+rowcount+'&tot_subtotal_amt='+tot_subtotal_amt+'&tot_discount_to_all_amt='+tot_discount_to_all_amt+'&tot_round_off_amt='+tot_round_off_amt+'&tot_total_amt='+tot_total_amt+"&other_charges_amt="+other_charges_amt+"&customs_duty_amount="+customs_duty_amount+"&customs_duty_vat="+customs_duty_vat+"&vat_id="+vat_id+"&vat_amount="+vat_amount+"&customs_clearance="+customs_clearance+"&other_charges_input="+other_charges_input+"&other_charges_name="+other_charges_name+"&freight_charges="+freight_charges,
				data: data,
				cache: false,
				contentType: false,
				processData: false,
				success: function(result){
         // alert(result);return;
				result=result.split("<<<###>>>");
					if(result[0]=="success")
					{
						location.href=base_url+"purchase/invoice/"+result[1];
					}
					else if(result[0]=="failed")
					{
					   toastr['error']("Sorry! Failed to save Record.Try again");
					}
					else
					{
						alert(result);
					}
					$("#"+this_id).attr('disabled',false);  //Enable Save or Update button
					$(".overlay").remove();

			   }
			   });
		//}
  
});


$("#item_search").bind("paste", function(e){
    $("#item_search").autocomplete('search');
} );
$("#item_search").autocomplete({
    source: function(data, cb){
        $.ajax({
          autoFocus:true,
          url: $("#base_url").val()+'items/get_json_items_details',
            method: 'GET',
            dataType: 'json',
            /*showHintOnFocus: true,
      autoSelect: true, 
      
      selectInitial :true,*/
      
            data: {
                name: data.term,
				currency_type:$("#currency_type").val()
            },
            success: function(res){
              console.log(res);
                var result;
                result = [
                    {
                        //label: 'No Records Found '+data.term,
                        label: 'No Records Found ',
                        value: ''
                    }
                ];

                if (res.length) {
                    result = $.map(res, function(el){
                        return {
                            label: el.item_code +'--'+ el.label,
                            value: '',
                            id: el.id,
                            item_name: el.value,
                           // mobile: el.mobile,
                            //customer_dob: el.customer_dob,
                            //address: el.address,
                        };
                    });
                }

                cb(result);
            }
        });
    },

        response:function(e,ui){
          if(ui.content.length==1){
            $(this).data('ui-autocomplete')._trigger('select', 'autocompleteselect', ui);
            $(this).autocomplete("close");
          }
          //console.log(ui.content[0].id);
        },

        //loader start
        search: function (e, ui) {
        },
        select: function (e, ui) { 
          
            //$("#mobile").val(ui.item.mobile)
            //$("#item_search").val(ui.item.value);
            //$("#customer_dob").val(ui.item.customer_dob)
            //$("#address").val(ui.item.address)
            //alert("id="+ui.item.id);
            if(typeof ui.content!='undefined'){
              console.log("Autoselected first");
              if(isNaN(ui.content[0].id)){
                return;
              }
              //var stock=ui.content[0].stock;
              var item_id=ui.content[0].id;
            }
            else{
              console.log("manual Selected");
              //var stock=ui.item.stock;
              var item_id=ui.item.id;
            }

            return_row_with_data(item_id);
            $("#item_search").val('');
        },   
        //loader end
});

function return_row_with_data(item_id){
  $("#item_search").addClass('ui-autocomplete-loader-center');
	var base_url=$("#base_url").val().trim();
	var rowcount=$("#hidden_rowcount").val();
  var currency_type = $("#currency_type").val();
  if(currency_type == ''){
    currency_type = 'OMR';
  }

  var currency_value = $("#currency_value").val();
  if(currency_value == ''){
    currency_value = 1;
  }
 // console.log(base_url+"purchase/return_row_with_data/"+rowcount+"/"+item_id+"/"+currency_type+"/"+currency_value);
	$.post(base_url+"purchase/return_row_with_data/"+rowcount+"/"+item_id+"/"+currency_type+"/"+currency_value,{},function(result){
        //alert(result);
        $('#purchase_table tbody').append(result);
       	$("#hidden_rowcount").val(parseFloat(rowcount)+1);
        success.currentTime = 0;
        success.play();
        enable_or_disable_item_discount();
        $("#item_search").removeClass('ui-autocomplete-loader-center');
    }); 
}
//INCREMENT ITEM
function increment_qty(rowcount){
  var item_qty=$("#td_data_"+rowcount+"_3").val();
  var available_qty=$("#tr_available_qty_"+rowcount+"_13").val();
  //if(parseFloat(item_qty)<parseFloat(available_qty)){
    item_qty=parseFloat(item_qty)+1;
    $("#td_data_"+rowcount+"_3").val(item_qty.toFixed(0));
  //}
  calculate_tax(rowcount);
}
//DECREMENT ITEM
function decrement_qty(rowcount){
  var item_qty=$("#td_data_"+rowcount+"_3").val();
  if(item_qty<=1){
    $("#td_data_"+rowcount+"_3").val((1).toFixed(0));
    return;
  }
  $("#td_data_"+rowcount+"_3").val((parseFloat(item_qty)-1).toFixed(0));
  calculate_tax(rowcount);
}

//CALCUALATED SALES PRICE
function calculate_sales_price(rowcount){
  var purchase_price = (isNaN(parseFloat($("#td_data_"+rowcount+"_10").val().trim()))) ? 0 :parseFloat($("#td_data_"+rowcount+"_10").val().trim()); 
  var profit_margin = (isNaN(parseFloat($("#td_data_"+rowcount+"_12").val().trim()))) ? 0 :parseFloat($("#td_data_"+rowcount+"_12").val().trim()); 
  var tax_type = $("#tax_type").val();
  var sales_price =parseFloat(0);
    sales_price = purchase_price + ((purchase_price*profit_margin)/parseFloat(100));
  $("#td_data_"+rowcount+"_13").val(sales_price.toFixed(3));
}
//END
//CALCULATE PROFIT MARGIN PERCENTAGE
function calculate_profit_margin(rowcount){
  var purchase_price = (isNaN(parseFloat($("#td_data_"+rowcount+"_10").val().trim()))) ? 0 :parseFloat($("#td_data_"+rowcount+"_10").val().trim()); 
  var sales_price = (isNaN(parseFloat($("#td_data_"+rowcount+"_13").val().trim()))) ? 0 :parseFloat($("#td_data_"+rowcount+"_13").val().trim());  
  var profit_margin = (sales_price-purchase_price);
  var profit_margin = (profit_margin/purchase_price)*parseFloat(100);
  $("#td_data_"+rowcount+"_12").val(profit_margin.toFixed(3));
}
//END

function update_paid_payment_total() {
  var rowcount=$("#paid_amt_tot").attr("data-rowcount");
  var tot=0;
  for(i=1;i<rowcount;i++){
    if(document.getElementById("paid_amt_"+i)){
      tot += parseFloat($("#paid_amt_"+i).html());
    }
  }
  $("#paid_amt_tot").html(tot.toFixed(3));
}
function delete_payment(payment_id){
 if(confirm("Do You Wants to Delete Record ?")){
    var base_url=$("#base_url").val().trim();
    $(".box").append('<div class="overlay"><i class="fa fa-refresh fa-spin"></i></div>');
   $.post(base_url+"purchase/delete_payment",{payment_id:payment_id},function(result){
   //alert(result);return;
   result=result.trim();
     if(result=="success")
        {
          toastr["success"]("Record Deleted Successfully!");
          $("#payment_row_"+payment_id).remove();
          success.currentTime = 0; 
          success.play();
        }
        else if(result=="failed"){
          toastr["error"]("Failed to Delete .Try again!");
          failed.currentTime = 0; 
          failed.play();
        }
        else{
          toastr["error"](result);
          failed.currentTime = 0; 
          failed.play();
        }
        $(".overlay").remove();
        update_paid_payment_total();
   });
   }//end confirmation   
  }

  //Delete Record start
function delete_purchase(q_id)
{
  
   if(confirm("Do You Wants to Delete Record ?")){
    $(".box").append('<div class="overlay"><i class="fa fa-refresh fa-spin"></i></div>');
    $.post("purchase/delete_purchase",{q_id:q_id},function(result){
   //alert(result);return;
     if(result=="success")
        {
          toastr["success"]("Record Deleted Successfully!");
          $('#example2').DataTable().ajax.reload();
        }
        else if(result=="failed"){
          toastr["error"]("Failed to Delete .Try again!");
        }
        else{
           toastr["error"](result);
        }
        $(".overlay").remove();
        return false;
   });
   }//end confirmation
}
//Delete Record end
function multi_delete(){
  //var base_url=$("#base_url").val().trim();
    var this_id=this.id;
    
    if(confirm("Are you sure ?")){
      data = new FormData($('#table_form')[0]);//form name
      /*Check XSS Code*/
      if(!xss_validation(data)){ return false; }
      
      $(".box").append('<div class="overlay"><i class="fa fa-refresh fa-spin"></i></div>');
      $("#"+this_id).attr('disabled',true);  //Enable Save or Update button
      $.ajax({
      type: 'POST',
      url: 'purchase/multi_delete',
      data: data,
      cache: false,
      contentType: false,
      processData: false,
      success: function(result){
        result=result.trim();
  //alert(result);return;
        if(result=="success")
        {
          toastr["success"]("Record Deleted Successfully!");
          success.currentTime = 0; 
            success.play();
          $('#example2').DataTable().ajax.reload();
          $(".delete_btn").hide();
          $(".group_check").prop("checked",false).iCheck('update');
        }
        else if(result=="failed")
        {
           toastr["error"]("Sorry! Failed to save Record.Try again!");
           failed.currentTime = 0; 
           failed.play();
        }
        else
        {
          toastr["error"](result);
          failed.currentTime = 0; 
            failed.play();
        }
        $("#"+this_id).attr('disabled',false);  //Enable Save or Update button
        $(".overlay").remove();
       }
       });
  }
  //e.preventDefault
}

function pay_now(purchase_id){
  $.post('purchase/show_pay_now_modal', {purchase_id: purchase_id}, function(result) {
    $(".pay_now_modal").html('').html(result);
    //Date picker
    $('.datepicker').datepicker({
      autoclose: true,
    format: 'dd-mm-yyyy',
     todayHighlight: true
    });
    $('#pay_now').modal('toggle');

  });
}
function view_payments(purchase_id){
  $.post('purchase/view_payments_modal', {purchase_id: purchase_id}, function(result) {
    $(".view_payments_modal").html('').html(result);
    $('#view_payments_modal').modal('toggle');
  });
}

function save_payment(purchase_id){
  var base_url=$("#base_url").val().trim();

    //Initially flag set true
    var flag=true;

    function check_field(id)
    {

      if(!$("#"+id).val().trim() ) //Also check Others????
        {

            $('#'+id+'_msg').fadeIn(200).show().html('Required Field').addClass('required');
           // $('#'+id).css({'background-color' : '#E8E2E9'});
            flag=false;
        }
        else
        {
             $('#'+id+'_msg').fadeOut(200).hide();
             //$('#'+id).css({'background-color' : '#FFFFFF'});    //White color
        }
    }


   //Validate Input box or selection box should not be blank or empty
    check_field("amount");
    check_field("payment_date");
    check_field("paid_type");


    var payment_date=$("#payment_date").val().trim();
    var amount=$("#amount").val().trim();
    var payment_type=$("#payment_type").val().trim();
    var payment_note=$("#payment_note").val().trim();
    var paid_status=$("#paid_type").val().trim();


    if(amount == 0){
      toastr["error"]("Please Enter Valid Amount!");
      return false; 
    }

    if(amount > parseFloat($("#due_amount_temp").html().trim())){
      toastr["error"]("Entered Amount Should not be Greater than Due Amount!");
      return false;
    }

    $(".box").append('<div class="overlay"><i class="fa fa-refresh fa-spin"></i></div>');
    $(".payment_save").attr('disabled',true);  //Enable Save or Update button
    $.post('purchase/save_payment', {purchase_id: purchase_id,payment_type:payment_type,amount:amount,payment_date:payment_date,payment_note:payment_note,paid_status:paid_status}, function(result) {
      result=result.trim();
  //alert(result);return;
        if(result=="success")
        {
          $('#pay_now').modal('toggle');
          toastr["success"]("Payment Recorded Successfully!");
          success.currentTime = 0; 
          success.play();
          $('#example2').DataTable().ajax.reload();
        }
        else if(result=="failed")
        {
           toastr["error"]("Sorry! Failed to save Record.Try again!");
           failed.currentTime = 0; 
           failed.play();
        }
        else
        {
          toastr["error"](result);
          failed.currentTime = 0; 
          failed.play();
        }
        $(".payment_save").attr('disabled',false);  //Enable Save or Update button
        $(".overlay").remove();
    });
}

function delete_purchase_payment(payment_id){
 if(confirm("Do You Wants to Delete Record ?")){
    var base_url=$("#base_url").val().trim();
    $(".box").append('<div class="overlay"><i class="fa fa-refresh fa-spin"></i></div>');
   $.post(base_url+"purchase/delete_payment",{payment_id:payment_id},function(result){
   //alert(result);return;
   result=result.trim();
     if(result=="success")
        {
          $('#view_payments_modal').modal('toggle');
          toastr["success"]("Record Deleted Successfully!");
          success.currentTime = 0; 
          success.play();
          $('#example2').DataTable().ajax.reload();
        }
        else if(result=="failed"){
          toastr["error"]("Failed to Delete .Try again!");
          failed.currentTime = 0; 
          failed.play();
        }
        else{
          toastr["error"](result);
          failed.currentTime = 0; 
          failed.play();
        }
        $(".overlay").remove();
   });
   }//end confirmation   
  }

  // $('#purchase_type').on('change', function() {
    $(document).on('change','#purchase_type',function(){
     $selectedval = this.value;
    // $currency_symbol = $(this).data('currency');
    
    if($selectedval == 'international'){
       $('.internat').show();
       $('#currency_type').attr('required',true);
      // $('#currency_value').attr('required',true);
       $('#currency_symbol').val();
      // $('#currency_value').val('');
      
    }else{
       $('.internat').hide();
       $('#currency_type').attr('required',false);
      // $('#currency_value').attr('required',false);
       $('#currency_symbol').val('ر.ع');
     //  $('#currency_value').val(1);
      
    }
 
 
 });

 $(document).on('change','#currency_type',function(){
  var base_url=$("#base_url").val().trim();
  $selectedval = this.value;
  console.log($selectedval);
 // console.log($(this).data("currency"));
  
  $currency_symbol = $(this).find(':selected').data('currency');;
  // console.log(currency_symbol);
   $('#currency_symbol').val($currency_symbol);


   /* $.ajax({
    type: "POST",
    url: "https://blueraynational.alburraqkw.com/currency.json",
    dataType: "text",
    success: function(data) {
    $currency_rate =  data['conversion_rates'][$selectedval];
    $('#currency_value').val($currency_rate);
    },
    error: function(){
        alert("json not found");
    }
}); */

/* $.getJSON('https://blueraynational.alburraqkw.com/currency.json', (data) => {
  $currency_rate =  data['conversion_rates'][$selectedval];
  $('#currency_value').val($currency_rate);
  console.log(data);
}); */

// set endpoint and your API key
 
// execute the conversion using the "convert" endpoint:
$.ajax({
  type: "GET",
  url: base_url + "purchase/convercurrency", 
  data: {to: $selectedval},
  dataType: "json",  
  cache:false,
  success: 
       function(data){
        console.log(data);
        var obj = JSON.parse(JSON.stringify(data));
        console.log(obj);
        
        $('#currency_value').val(obj['currency']);
        $('#last_update_curr_date').val(obj['last_update_curr_date']);
        $('#last_update_curr').val(obj['last_update_curr']);
        // alert(data);  //as a debugging message.
       }
   });// you have missed this bracket




});

/* $(document).on('change','#vat_tax_id',function(){
  $selectedval = this.value;
  console.log($selectedval);
  subtotalvat = 0;
 // console.log($(this).data("currency"));
 if($selectedval == ''){ 

  $vat_calc = 0;
 } else{

  $vat_calc = $(this).find(':selected').data('tax');
 }

 $subtotal = $("#subtotal_amt").text();

 $subtotalvat = $subtotal *  $vat_calc / 100;

 $subtotalvat = parseFloat(subtotalvat).toFixed(3);
 $("#vat_amt").html($subtotalvat);

 $("#vat_amt_c").val($subtotalvat);
  // console.log(currency_symbol);
  // $('#currency_symbol').val($currency_symbol);
}); */
/*
$(document).on('change','#customs_duty_tax_id',function(){
  $selectedval = this.value;
  console.log($selectedval);
  subtotalvat = 0;
 // console.log($(this).data("currency"));
 if($selectedval == ''){ 

  $custom_duty_calc = 0;
 } else{
   $custom_duty_calc = $(this).find(':selected').data('tax');
 }

 $subtotal = $("#subtotal_amt").text();

 $subtotalvat = $subtotal *  $custom_duty_calc / 100;

 $subtotalvat = parseFloat(subtotalvat).toFixed(3);
 $("#customs_duty_amt").html($subtotalvat);
 $("#customs_duty_c").val($subtotalvat);
  // console.log(currency_symbol);
  // $('#currency_symbol').val($currency_symbol);
});

 $(document).on('change','#customs_clearance_input',function(){
  $selectedval = this.value;
   
  subtotalvat = 0;
 // console.log($(this).data("currency"));
 if($selectedval == ''){ 

  $custom_duty_calc = 0;
 } else{
   $custom_duty_calc = $(this).find(':selected').data('tax');
 }

 $subtotal = $("#subtotal_amt").text();

 $subtotalvat = $subtotal *  $custom_duty_calc / 100;

 $subtotalvat = parseFloat(subtotalvat).toFixed(3);
 $("#customs_duty_amt").html($subtotalvat);
 $("#customs_duty_c").val($subtotalvat);
  // console.log(currency_symbol);
  // $('#currency_symbol').val($currency_symbol);
}); */

// customs_clearance_input

function paymentchange(id){

  console.log(id);
    if(confirm('Are you sure for paid?')){
    
  $.post("purchase/paymentchange",{id:id},function(result){
    if(result=="success")
    {
      $('#view_payments_modal').modal('toggle');
      toastr["success"]("Paid Successfully!");
      //alert("Status Updated Successfully!");
      success.currentTime = 0; 
      success.play();
      $('#example2').DataTable().ajax.reload();
      return false;
    }
    else if(result=="failed"){
      toastr["error"]("Failed to Update Status.Try again!");
      failed.currentTime = 0; 
      failed.play();

      return false;
    }
    else{
      toastr["error"](result);
      failed.currentTime = 0; 
      failed.play();
      return false;
    }
  }); 
}

}

$(document).on("change","#payment_type",function(){
    
  if(this.value == 'Cheque'){
    $('#cheque').show();
  }else{
    $('#cheque_no').val('');
    $('#cheque_date').val('');
    $('#cheque').hide();
  }
 });

function payment_type(vale){
  if(vale == 'Cheque'){
    $('#cheque').show();
  }else{
    $('#cheque_no').val('');
    $('#cheque_date').val('');
    $('#cheque').hide();
  }
 }



 