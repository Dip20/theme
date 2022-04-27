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
                <option value="default"  disabled selected>--Select--</option>
                <?php
                    foreach ($products->getResult() as $row)
                    {
                            echo '<option value="'.$row->id.'" data-price="'.$row->sales_price.'" data-code="'.$row->code.'">'.$row->name.'</option>';
                    }
              ?>
               </select>
        </div>
    </div>


    <table class="table table-bordered mt-4">
        <thead>
            <th>#</th>
            <th>Product Name</th>
            <th>Product Code</th>
            <th>Price</th>
            <th>QTY</th>
            <th>Sub Total</th>
        </thead>
        <tbody class="item_body">
            <tr>
                <td colspan="5" class="text-right">QTY:<span class="qty">0</span> &nbsp; |  Total</td>
                <td><input type="text" class="form-control total_amt" id="total" name="total" value="0" readonly></td>
            </tr>
            <tr>
                <td colspan="5" class="text-right">CGST</td>
                <td><input type="text" class="form-control" id="total" name="total" value="0" readonly></td>
            </tr>
            <tr>
                <td colspan="5" class="text-right">SGST</td>
                <td><input type="text" class="form-control" id="total" name="total" value="0" readonly></td>
            </tr>
            <tr>
                <td colspan="5" class="text-right">Rounding</td>
                <td><input type="text" class="form-control" id="total" name="total" value="0" readonly></td>
            </tr>
            <tr>
                <td colspan="5" class="text-right">Grand Total</td>
                <td><input type="text" class="form-control" id="total" name="total" value="0" readonly></td>
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
    $('#code').select2({});


    /* on click select item */
    $("#code").on("change", function()
    {
        var selected     = $("#code option:selected");
        var product_id   =  selected.val();
        var product_name =  selected.text();
        var price        =  parseInt(selected.data("price"));
        var product_code =  selected.data("code");

        var total_qty    =  parseInt($(".qty").text());
        $(".qty").text(total_qty+1);

        let x = Math.floor((Math.random() * 100000) + 1);


        /* Prepare the item */
        var html = '<tr> <td> <button class="btn btn-outline-light text-danger btn_'+x+'" type="button"  onclick="delete_row('+x+","+price+')"><i class="fa fa-times text-danger"></i></button> </td> <td>'+product_name+'</td> <td>'+product_code+' <input type="hidden" value="'+product_name+'" name="product_name[]"></td> <td><input type="text" onblur="'+subtotal(x)+'" class="form-control price_'+x+'" name="price[]" value="'+price+'"></td> <td><input type="text" name="qty[]" class="form-control qty_'+x+'"  value="1"></td> <td> <input type="text" name="subtotal[]" class="form-control sub_total sub_total'+x+'" readonly value="'+price+'"> </td> </tr>';

        /* prepend the new product in table */
        $(".item_body").prepend(html);

        calculate();
    
    });

});//document load

    /* Delete product row */
    function delete_row(p_id,price) 
    {
        var total_qty    =  parseInt($(".qty").text());
        $(".qty").text(total_qty-1);
        $(".btn_"+p_id).closest("tr").remove();  
        
        calculate();
        
    }

    /*Calculate */
    function calculate() 
    {
        var arr   =  $(".sub_total");
        var total = 0;
        
        for (let i = 0; i < arr.length; i++) 
        {            
            if(parseInt(arr[i].value))
            total += parseInt(arr[i].value);
        }

        $(".total_amt").val(total);

    }

    /* calculate subtotal */
    function subtotal(x) 
    {
        
        var price = $(".price_"+x).val();
        console.log(price);
        

    }



</script>
<?= $this->endSection() ?>