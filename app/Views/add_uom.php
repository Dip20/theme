<?= $this->extend(THEME . 'templete') ?>

<?= $this->section('content') ?>

<style>
.error {
    color: red;
}
</style>
<div class="page-header">
    <div>
        <h2 class="main-content-title tx-24 mg-b-5"> UOM </h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page"><?=$title?></li>
        </ol>
    </div>
    <!-- <div class="btn btn-list">
        <a data-toggle="modal" href="<?//=url('Home/Createuom')?>" data-title="Enter Detail " data-target="#fm_model"
            class="btn ripple btn-primary"><i class="fe fe-external-link"></i>Add New</a>
    </div> -->
</div>
<!--colorpicker css-->
<link href="<?=ASSETS?>/plugins/spectrum-colorpicker/spectrum.css" rel="stylesheet">
<?php helper('form'); echo form_open("Home/add_uom");?>

<br><br>
    <div class="row">
        <div class="col-lg-3">
            <label for="">Name: *</label>
                <input type="text" class="form-control" placeholder="Enter Name" name="name">
        </div>
        <div class="col-lg-3">
                <label for="">Short Name: *</label>
                <input type="text" class="form-control" placeholder="Enter Short Name" name="code"> 
        </div>
        <div class="col-lg-3">
                <label for="">Descimal Digits: *</label>
                <input type="text" class="form-control" placeholder="Enter Descimal Digits" name="decimal">
        </div>
    </div>
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

function validate_autocomplete(obj, val) {
    if ($('#' + val).val() == '') {
        $('.' + val).html('Option Select from dropdown list')
    } else {
        $('.' + val).html('')
    }
}

function calc_gst(igst) {
    var gst = igst / 2;
    $('#cgst').val(gst);
    $('#sgst').val(gst);
}

function calc_opening_total(obj) {
    var rate = obj;
    var opening_stock = $('input[name="opening_stock"]').val();
    var opening_total = 0;


    if(opening_stock == '' || opening_stock =='NaN' || opening_stock =='undefined'){
        $('#opening_error').html("Please Enter Stock First");
    }else{
        $('#opening_error').html('');
        opening_total = parseFloat(rate) * parseFloat(opening_stock);
    }

    if(opening_total == '' || opening_total == 'NaN' || opening_total =='undefined'){
        opening_total = 0;
    }
    $('input[name="opening_total"]').val(opening_total);
    
}

$(document).ready(function() {

    var form = $("#itemform");
    form.validate({
        ignore: "",
        validateHiddenInputs: true,
        errorPlacement: function errorPlacement(error, element) {
            error.insertAfter(element);
            error.insertAfter(element.parent('.input-group'));
        },
        rules: {},
        messages: {}
    });
    // var finishButton = $('.wizard').find('a[href="#finish"]');
    var finishButton = $('.wizard').find('a[href="#finish"]');
    $('#wizard1').steps({
        headerTag: 'h3',
        bodyTag: 'section',
        autoFocus: true,
        titleTemplate: '<span class="number">#index#<\/span> <span class="title">#title#<\/span>',
        onStepChanging: function(event, currentIndex, newIndex) {
            form.validate().settings.ignore = ":disabled,:hidden";
            return form.valid();
        },
        onFinishing: function(event, currentIndex) {
            if (form_loading) {
                return true;
            } else {
                return false;
            }
        },
        onFinished: function(event, currentIndex) {
            var data = form.serialize();
			console.log(data);
			
            finishButton.html("<i class='sl sl-icon-reload'></i> Please wait...");
            //form_loading = false;
            $('.description_error').html('');
            var aurl = $('#itemform').attr('action');
            $.post(aurl, data, function(response) {
                console.log(response)
                if (response.st == 'success') {
                    window.location = "<?= url('Home/additem') ?>"
                    //window.location.replace(response.location);
                } else {
                    finishButton.html("Create Item");
                    form_loading = true;
                    $('.description_error').html(response.msg);
                }
            }).fail(function(response) {
                finishButton.html("Create Item");
                form_loading = true;
                alert('Error');
            });
        }
    });

    $(':input').attr('autocomplete', 'false');
    $('.select2').select2({
        placeholder: 'Choose one',
        searchInputPlaceholder: 'Search',
        width: '100%'
    });

    $('.fc-datepicker').datepicker({
        dateFormat: 'yy-mm-dd',
        showOtherMonths: true,
        selectOtherMonths: true
    });

    $('#showAlpha').spectrum({
        color: 'rgba(23,162,184,0.5)',
        showAlpha: true
    });

   

    $('#taxability').on('select2:select', function(e) {
        var data = e.params.data;
        var gstDiv = document.getElementById("gst_div");
        if (data.id == 'N/A') {
            gstDiv.style.display = "none";
            $('input[name="igst"]').val('');
            $('input[name="cgst"]').val('');
            $('input[name="sgst"]').val('');
        } else {
            gstDiv.style.display = "block";
        }
    });

    $('input[type=radio][name=item_mode]').change(function() {
        var taka = document.getElementById('taka');
        var pcs = document.getElementById('pcs');

        if (this.value == 'general') {
            var general =
                "<option value='Inventory'>Inventory</option><option value='Service'>Service</option><option value='NonInventory' >Non-Inventory</option><option value='Group'>Group</option>";

            $("#type").append(general);
            $("#type option[value='Grey']").remove();
            $("#type option[value='Finish']").remove();
            $("#type option[value='Jobwork']").remove();



            taka.style.display = 'none';
            pcs.style.display = 'block';


        } else {

            var milling =
                "<option value='Grey'>Grey</option><option value='Finish'>Finish</option><option value='Jobwork' >Jobwork</option>";

            $("#type").append(milling);
            $("#type option[value='Inventory']").remove();
            $("#type option[value='Service']").remove();
            $("#type option[value='NonInventory']").remove();
            $("#type option[value='Group']").remove();


            taka.style.display = 'block';
            pcs.style.display = 'none';

        }

    });


    var item_mode = $('input[name="item_mode"]:checked').val();

    var taka = document.getElementById('taka');
    var pcs = document.getElementById('pcs');

    if (item_mode == 'general') {
        var general =
            "<option value='Inventory'>Inventory</option><option value='Service'>Service</option><option value='NonInventory' >Non-Inventory</option><option value='Group'>Group</option>";

        $("#type").append(general);
        $("#type option[value='Grey']").remove();
        $("#type option[value='Finish']").remove();
        $("#type option[value='Jobwork']").remove();



        // taka.style.display = 'none';
        // pcs.style.display = 'block';


    } else {

        var milling =
            "<option value='Grey'>Grey</option><option value='Finish'>Finish</option><option value='Jobwork' >Jobwork</option>";

        $("#type").append(milling);
        $("#type option[value='Inventory']").remove();
        $("#type option[value='Service']").remove();
        $("#type option[value='NonInventory']").remove();
        $("#type option[value='Group']").remove();


        // taka.style.display = 'block';
        // pcs.style.display = 'none';

    }


    $("#item_grp").select2({
        width: 'resolve',
        placeholder: 'Type Item Group',
        ajax: {
            url: PATH + "Master/Getdata/search_itemgrp",
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

    $("#uom").select2({

        width: 'resolve',
        placeholder: 'Type UOM',
        ajax: {
            // url: PATH + "Master/Getdata/search_uom_data",
            url: PATH + "Home/Getdata/search_uom_data",
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

    $("#opening_uom").select2({

        width: '100%',
        placeholder: 'Type UOM',
        ajax: {
            url: PATH + "Master/Getdata/search_uom_data",
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
});
</script>
<?= $this->endSection() ?>