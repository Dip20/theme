<?= $this->extend(THEME . 'templete') ?>
<?= $this->section('content') ?>
<style>
   .error {
   color: red;
   }
</style>
<div class="page-header">
   <div>
      <h2 class="main-content-title tx-24 mg-b-5"> Sale </h2>
      <ol class="breadcrumb">
         <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
         <li class="breadcrumb-item active" aria-current="page"><?=$title?></li>
      </ol>
   </div>
</div>
<!--colorpicker css-->
<link href="<?=ASSETS?>/plugins/spectrum-colorpicker/spectrum.css" rel="stylesheet">
<?php helper('form'); helper('text');  echo form_open("SaleController/index");?>
<br><br>
<div class="row">
   <div class="col-lg-3">
      <label for="">Sales Bill NO: *</label>
      <input type="text" class="form-control" readonly value="<?=random_string('alnum', 16);?>" name="sale_number">
   </div>
   <div class="col-lg-3">
      <label for="">Sales Date: *</label>
      <input type="text" class="form-control"  name="sale_Date" readonly value="<?=date("d-m-Y");?>"> 
   </div>
   <div class="col-lg-3">
      <label for="">Select Product: *</label>
      <select class="form-control" id="code" name='code'>
         <option value="default"  disabled >--Select--</option>
         <?php
            // foreach ($products->getResult() as $row)
            // {
            //         echo '<option value="'.$row->id.'" data-price="'.$row->sales_price.'" data-code="'.$row->code.'">'.$row->name.'</option>';
            // }
            ?>
      </select>
   </div>
</div>
<table class="table table-bordered mt-4">
   <thead>
      <th>#</th>
      <th>Product Name</th>
      <th>Price</th>
      <th>QTY</th>
      <th>IGST(%)</th>
      <th>CGST(%)</th>
      <th>SGST(%)</th>
      <th>Sub Total</th>
   </thead>
   <tbody class="item_body">
      <tr>
         <td colspan="7" class="text-right">QTY:<span class="qty">0</span> &nbsp; |  Total</td>
         <td><input type="text" class="form-control total_amt" id="total_amt" name="total" value="0" readonly></td>
      </tr>
      <tr>
         <td colspan="7" class="text-right">CGST</td>
         <td><input type="text" class="form-control" id="cgst" name="cgst" value="0" readonly></td>
      </tr>
      <tr>
         <td colspan="7" class="text-right">SGST</td>
         <td><input type="text" class="form-control" id="sgst" name="sgst" value="0" readonly></td>
      </tr>
      <tr>
         <td colspan="7" class="text-right">Rounding</td>
         <td><input type="text" class="form-control" id="rounding" name="rounding" value="0" readonly></td>
      </tr>
      <tr>
         <td colspan="7" class="text-right">Grand Total</td>
         
         <td><input type="text" class="form-control" id="grand_total" name="grand_total" value="0" readonly></td>
      </tr>
   </tbody>
</table>
<br>
<div class="row mt-5">
   <div class="col-lg-3">
      <button type="submit" class="btn btn-primary btn-md">Submit</button>
   </div>
</div>
</form>
<?= $this->endSection() ?>
<?=$this->section('scripts')?>
<!-- Specturm-colorpicker js-->
<script src="<?=ASSETS;?>/plugins/spectrum-colorpicker/spectrum.js"></script>
<script src="<?=ASSETS;?>js/jquery.validate.js"></script>
<script>
   var form_loading = true;
   
   
   $(document).ready(function() 
   {
       /*Init. select 2 */
       $("#code").select2({
           width: 'resolve',
           placeholder: 'Type Item Name ',
           ajax: {
               url: PATH + "Sales/Getdata/Item",
               type: "post",
               allowClear: true,
               dataType: 'json',
               delay: 250,
               data: function(params) {
                   return {
                       searchTerm: params.term // search term
                   };
               },
               processResults: function(response) {
                   return {
                       results: response
                   };
               },
               cache: true
           }
       });


       /* on click select item */
      
       $('#code').on('select2:select', function(e) 
       {
           var suggestion = e.params.data;
           // console.log(suggestion);
   
           var selected     = $("#code option:selected");
           var product_name =  selected.text();
   
           let x = Math.floor((Math.random() * 100000) + 1);
   
           /* Prepare the item */
           var html = '<tr class="item_row"> <td> <button class="btn btn-outline-light text-danger btn_'+x+'" type="button"  onclick="delete_row('+x+","+suggestion.price.sales_price+')"><i class="fa fa-times text-danger"></i></button></td> ';
   
           html+='<td><input type="hidden"  class="form-control"  value="'+suggestion.text+'" name="product_name[]"> <p>'+suggestion.text+'</p></td>';
           html+='<td><input type="text"  class="form-control" onkeyup="sum(this)"  value="'+suggestion.price.sales_price+'" name="price[]"></td>';
           html+='<td><input type="text"  class="form-control" onkeyup="sum(this)"  value="1" name="qty[]"></td>';
           html+='<td><input type="text"  class="form-control" onkeyup="cal_gst(this)" value="'+suggestion.price.igst+'" name="igst[]"></td>';
           html+='<td><input type="text"  class="form-control" value="'+suggestion.price.cgst+'" name="cgst[]"></td>';
           html+='<td><input type="text"  class="form-control" value="'+suggestion.price.sgst+'" name="sgst[]"></td>';
           html+='<td><input type="text"  class="form-control" name="subtotal[]" class="form-control sub_total sub_total'+x+'" readonly value="'+suggestion.price.sales_price+'"></td>';
   
   
           /* prepend the new product in table */
           $(".item_body").prepend(html);
   
           calculate();
       
       });
   
   });//document load
   
       /* Delete product row */
       function delete_row(p_id,price) 
       {
           var total_qty = parseInt($(".qty").text());
           $(".btn_"+p_id).closest("tr").remove();  
           
           calculate();
           
       }
   
       /*Calculate */
       function calculate() 
       {
           /* Total qty */
   
           var qty = $('input[name="qty[]"]').map(function() {
               return parseInt(this.value); // $(this).val()
               }).get();
   
           var sum = 0;
           for (let i = 0; i < qty.length; i++) 
           {
               sum += qty[i];
           }
       
           $(".qty").text(sum);
   
   
           /* calculate total amount */
   
           var subtotal = $('input[name="subtotal[]"]').map(function() {
               return parseFloat(this.value); // $(this).val()
               }).get();
   
           var sub_total_amt = 0;
           for (let i = 0; i < subtotal.length; i++) 
           {
               sub_total_amt += subtotal[i];
           }
       
           $(".total_amt").val((sub_total_amt).toFixed(2));
   
           /* Round off */
           
           var total_amount = $("#total_amt").val();
           var round = 0;
           var diff = 0;
   
           round = Math.round(parseFloat(total_amount)).toFixed(2);
           diff = (parseFloat(round) - parseFloat(total_amount)).toFixed(2);
   
           $("#rounding").val(diff);
           $("#grand_total").val(round);
       }
   
   
       /* Calculate gst */
       function cal_gst(x) 
       {
           var igst = $(x).val();
           if (igst == '' || igst == 'undefined' || isNaN(igst)) 
           {
               igst = 0;
           }
   
           $(x).closest('.item_row').find('input[name="cgst[]"]').val(parseFloat(igst) / 2);
           $(x).closest('.item_row').find('input[name="sgst[]"]').val(parseFloat(igst) / 2);
           calculate();
      }  
   
       /* calculate price x qty */
       function sum(x) 
       {
           var _price =  $(x).closest('.item_row').find('input[name="price[]"]').val();
           var _qty   =  $(x).closest('.item_row').find('input[name="qty[]"]').val();
           $(x).closest('.item_row').find('input[name="subtotal[]"]').val((_price*_qty).toFixed(2));
           
           calculate();
       }
   
   
       
   
   
</script>
<?= $this->endSection() ?>