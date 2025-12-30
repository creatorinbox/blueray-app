

//On Enter Move the cursor to desigtation Id

function shift_cursor(kevent,target){



    if(kevent.keyCode==13){

		$("#"+target).focus();

    }

	

}




/* Delivery Order */
$('#delivery').click(function (e) {

	var base_url=$("#base_url").val().trim();



    //Initially flag set true

    var flag=true;
  

	if(flag==false)

	{

		toastr["error"]("You have missed Something to Fillup!");

		return;

	}



	//Atleast one record must be added in sales table 

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



    var this_id=this.id;

    

			//if(confirm("Do You Wants to Save Record ?")){

				e.preventDefault();

				data = new FormData($('#delivery-form')[0]);//form name

        /*Check XSS Code*/

        if(!xss_validation(data)){ return false; }

        

        $(".box").append('<div class="overlay"><i class="fa fa-refresh fa-spin"></i></div>');

        $("#"+this_id).attr('disabled',true);  //Enable Save or Update button

				$.ajax({

				type: 'POST',

				url: base_url+'sales/delivery_save?command='+this_id+'&rowcount='+rowcount+'&tot_subtotal_amt='+tot_subtotal_amt+'&tot_discount_to_all_amt='+tot_discount_to_all_amt+'&tot_round_off_amt='+tot_round_off_amt+'&tot_total_amt='+tot_total_amt+"&other_charges_amt="+other_charges_amt,

				data: data,

				cache: false,

				contentType: false,

				processData: false,

				success: function(result){

         // alert(result);return;

				result=result.split("<<<###>>>");

					if(result[0]=="success")

					{

						location.href=base_url+"sales/delivery_print/"+result[1];

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

	  check_field("customer_id");

    check_field("sales_date");

    check_field("jobcard_status");


    if($("#invoice_type").val()==''){

      toastr["warning"]("Please Select Invoice Type!!");

      return;

    }


    // check_field("invoice_type");
    // invoice_type

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



	//Atleast one record must be added in sales table 

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



    if($("#customer_id").val().trim()==1){

      if(parseFloat($("#total_amt").text())!=parseFloat($("#amount").val())){

        $("#amount").focus();

        toastr["warning"]("Walk-in Customer Should Pay Complete Amount!!");

        return;

      }

      /*  if($("#payment_type").val()==''){

          toastr["warning"]("Please Select Payment Type!!");

          return;

        }

        if($("#paid_type").val()!='Paid'){

          toastr["warning"]("Walk-in Customer Should Pay Paid Amount!!");

          return;

        } */

    }



    var tot_subtotal_amt=$("#subtotal_amt").text();

    var other_charges_amt=$("#other_charges_amt").text();//other_charges include tax calcualated amount

    var tot_discount_to_all_amt=$("#discount_to_all_amt").text();

    var tot_round_off_amt=$("#round_off_amt").text();

    var tot_total_amt=$("#total_amt").text();



    var this_id=this.id;

    

			//if(confirm("Do You Wants to Save Record ?")){

				e.preventDefault();

				data = new FormData($('#sales-form')[0]);//form name

        /*Check XSS Code*/

        if(!xss_validation(data)){ return false; }

        

        $(".box").append('<div class="overlay"><i class="fa fa-refresh fa-spin"></i></div>');

        $("#"+this_id).attr('disabled',true);  //Enable Save or Update button

				$.ajax({

				type: 'POST',

				url: base_url+'jobcard/sales_save_and_update?command='+this_id+'&rowcount='+rowcount+'&tot_subtotal_amt='+tot_subtotal_amt+'&tot_discount_to_all_amt='+tot_discount_to_all_amt+'&tot_round_off_amt='+tot_round_off_amt+'&tot_total_amt='+tot_total_amt+"&other_charges_amt="+other_charges_amt,

				data: data,

				cache: false,

				contentType: false,

				processData: false,

				success: function(result){

         // alert(result);return;

				result=result.split("<<<###>>>");

					if(result[0]=="success")

					{

						location.href=base_url+"jobcard/invoice/"+result[1];

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

                /*warehouse_id:$("#warehouse_id").val().trim(),*/

            },

            success: function(res){

              //console.log(res);

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

                            label: el.item_code +'--[Qty:'+el.stock+'] --'+ el.label,

                            value: '',

                            id: el.id,

                            item_name: el.value,

                            stock: el.stock,

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

              var stock=ui.content[0].stock;

              var item_id=ui.content[0].id;
              var product_type=ui.content[0].product_type;

            }

            else{

              console.log("manual Selected");

              var stock=ui.item.stock;

              var item_id=ui.item.id;

            }
            if(product_type == 'simple'){
            if(parseFloat(stock)<=0){

              toastr["warning"](stock+" Items in Stock!!");

              failed.currentTime = 0; 

              failed.play();

              return false;

            }
          }

            if(restrict_quantity(item_id)){

              return_row_with_data(item_id);  

            }

            $("#item_search").val('');

            

        },   

        //loader end

});

function sales_type_approve(id,statusa){

  console.log(id);
    if(confirm('Are you sure for approval?')){
    
  $.post("sales/change_sales_type",{id:id},function(result){
    if(result=="success")
    {
      toastr["success"]("Status Updated Successfully!");
      //alert("Status Updated Successfully!");
      success.currentTime = 0; 
      success.play();
      status="Approved";
      var span_class="label label-success";
      $("#span_"+id).attr('class',span_class);
				  $("#span_"+id).html(status);
				  return false;
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

// paymentchange

function paymentchange(id){

  console.log(id);
    if(confirm('Are you sure for paid?')){
    
  $.post("sales/paymentchange",{id:id},function(result){
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



function return_row_with_data(item_id){

  $("#item_search").addClass('ui-autocomplete-loader-center');

	var base_url=$("#base_url").val().trim();

	var rowcount=$("#hidden_rowcount").val();

	$.post(base_url+"jobcard/return_row_with_data/"+rowcount+"/"+item_id,{},function(result){

        //alert(result);

        $('#sales_table tbody').append(result);

       	$("#hidden_rowcount").val(parseFloat(rowcount)+1);

        success.currentTime = 0;

        success.play();

        enable_or_disable_item_discount();

        $("#item_search").removeClass('ui-autocomplete-loader-center');

    }); 

}

//INCREMENT ITEM

function increment_qty(rowcount){

  

  var flag = restrict_quantity($("#tr_item_id_"+rowcount).val().trim());

  if(!flag){ return false;}



  var item_qty=$("#td_data_"+rowcount+"_3").val();

  var available_qty=$("#tr_available_qty_"+rowcount+"_13").val();

  if(parseFloat(item_qty)<parseFloat(available_qty)){



    new_item_qty=parseFloat(item_qty)+1;



    if(parseFloat(new_item_qty)>parseFloat(available_qty)){

      new_item_qty = available_qty;

    }



    $("#td_data_"+rowcount+"_3").val(new_item_qty);

  }

  calculate_tax(rowcount);

}

//DECREMENT ITEM

function decrement_qty(rowcount){

  var item_qty=$("#td_data_"+rowcount+"_3").val();



  if(item_qty<1){

     $("#td_data_"+rowcount+"_3").val((item_qty).toFixed(3));

     toastr["warning"]("Minimum Stock!");

     return;

  }



  if(item_qty<=1){

    $("#td_data_"+rowcount+"_3").val(1);

      toastr["warning"]("Minimum Stock!");

    return;

  }

  $("#td_data_"+rowcount+"_3").val((parseFloat(item_qty)-1).toFixed(3));

  calculate_tax(rowcount);

}



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

   $.post(base_url+"sales/delete_payment",{payment_id:payment_id},function(result){

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

function delete_sales(q_id)

{

  

   if(confirm("Do You Wants to Delete Record ?")){

    $(".box").append('<div class="overlay"><i class="fa fa-refresh fa-spin"></i></div>');

    $.post("sales/delete_sales",{q_id:q_id},function(result){

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

      url: 'sales/multi_delete',

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



function pay_now(sales_id){

  $.post('sales/show_pay_now_modal', {sales_id: sales_id}, function(result) {

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

function view_payments(sales_id){

  $.post('sales/view_payments_modal', {sales_id: sales_id}, function(result) {

    $(".view_payments_modal").html('').html(result);

    $('#view_payments_modal').modal('toggle');

  });

}



function save_payment(sales_id){

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





    var payment_date=$("#payment_date").val().trim();

    var amount=$("#amount").val().trim();

    var payment_type=$("#payment_type").val().trim();

    var payment_note=$("#payment_note").val().trim();
    var cheque_no=$("#cheque_no").val().trim();
    var cheque_date=$("#cheque_date").val().trim();
    var paid_status=$("#paid_status").val().trim();
    var paid_version = $("#paid_version").val().trim();


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

    $.post('sales/save_payment', {sales_id: sales_id,payment_type:payment_type,amount:amount,payment_date:payment_date,payment_note:payment_note,cheque_no:cheque_no,cheque_date:cheque_date,paid_status:paid_status,paid_version:paid_version}, function(result) {

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



function delete_sales_payment(payment_id){

 if(confirm("Do You Wants to Delete Record ?")){

    var base_url=$("#base_url").val().trim();

    $(".box").append('<div class="overlay"><i class="fa fa-refresh fa-spin"></i></div>');

   $.post(base_url+"sales/delete_payment",{payment_id:payment_id},function(result){

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



  function restrict_quantity(item_id) {

  	var rowcount=$("#hidden_rowcount").val();

  	var available_qty = 0;

  	var count_item_qty = 0;

  	var selected_item_id = 0;

      for(i=1;i<=rowcount;i++){

        if(document.getElementById("tr_item_id_"+i)){

        	selected_item_id = $("#tr_item_id_"+i).val().trim();

            if(parseFloat(item_id)==parseFloat(selected_item_id)){

	             available_qty = parseFloat($("#tr_available_qty_"+i+"_13").val().trim());

	             count_item_qty += parseFloat($("#td_data_"+i+"_3").val().trim());

          }

        }

      }//end for

      if(available_qty!=0 && count_item_qty>=available_qty){

        toastr["warning"]("Only "+available_qty+" Items in Stock!!");

        failed.currentTime = 0; 

        failed.play();

      	return false;

      }

      return true;

  }
/*
  $(document).on("change","#sales_status",function(){
    if(this.value == 'Final'){
      $('#payments_divtable').show();
      $('#previous_paymentdiv').show();
      $('#invoice_type').show();
    }else{
      $('#previous_paymentdiv').hide();
      $('#payments_divtable').hide();
      $('#invoice_type').hide();
     
    }
  });
  */
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


  /*$("#warehouse_id").change(function(event) {

    $('#sales_table tbody').html('');

    final_total();

    if($("#warehouse_id").val().trim()!=''){

      $("#item_search").attr({ disabled: false,});

    }

    else{

     $("#item_search").attr({ disabled: true,}); 

    }

  });*/

