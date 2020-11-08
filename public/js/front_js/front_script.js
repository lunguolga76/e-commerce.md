$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $("#sort").on('change',function () {
        //alert("test");
        var sort= $(this).val();
        //alert(sort);
        var fabric=get_filter('fabric');
        var sleeve=get_filter('sleeve');
        var pattern=get_filter('pattern');
        var fit=get_filter('fit');
        var occasion=get_filter('occasion');
        var url=$("#url").val();
        //alert(url);
        $.ajax({
            url:url,
            method:"post",
            data:{fabric:fabric,sleeve:sleeve,pattern:pattern,fit:fit,occasio:occasio,sort:sort,url:url},
            success: function (data) {
                $('.filter_products').html(data);
            }
        })
    });
    $(".fabric").on('click', function () {
        var fabric=get_filter('fabric');
        var sleeve=get_filter('sleeve');
        var pattern=get_filter('pattern');
        var fit=get_filter('fit');
        var occasion=get_filter('occasion');
        var sort= $("#sort option:selected").text();
        var url=$("#url").val();
        $.ajax({
            url:url,
            method:"post",
            data:{fabric:fabric,sleeve:sleeve,pattern:pattern,fit:fit,occasion:occasion,sort:sort,url:url},
            success: function (data) {
                $('.filter_products').html(data);
            }
        })
    });
    $(".sleeve").on('click', function () {
        var fabric=get_filter('fabric');
        var sleeve=get_filter('sleeve');
        var pattern=get_filter('pattern');
        var fit=get_filter('fit');
        var occasion=get_filter('occasion');
        var sort= $("#sort option:selected").text();
        var url=$("#url").val();
        $.ajax({
            url:url,
            method:"post",
            data:{fabric:fabric,sleeve:sleeve,pattern:pattern,fit:fit,occasion:occasion,sort:sort,url:url},
            success: function (data) {
                $('.filter_products').html(data);
            }
        })
    });
    $(".pattern").on('click', function () {
        var fabric=get_filter('fabric');
        var sleeve=get_filter('sleeve');
        var pattern=get_filter('pattern');
        var fit=get_filter('fit');
        var occasion=get_filter('occasion');
        var sort= $("#sort option:selected").text();
        var url=$("#url").val();
        $.ajax({
            url:url,
            method:"post",
            data:{fabric:fabric,sleeve:sleeve,pattern:pattern,fit:fit,occasion:occasion,sort:sort,url:url},
            success: function (data) {
                $('.filter_products').html(data);
            }
        })
    });
    $(".fit").on('click', function () {
        var fabric=get_filter('fabric');
        var sleeve=get_filter('sleeve');
        var pattern=get_filter('pattern');
        var fit=get_filter('fit');
        var occasion=get_filter('occasion');
        var sort= $("#sort option:selected").text();
        var url=$("#url").val();
        $.ajax({
            url:url,
            method:"post",
            data:{fabric:fabric,sleeve:sleeve,pattern:pattern,fit:fit,occasion:occasion,sort:sort,url:url},
            success: function (data) {
                $('.filter_products').html(data);
            }
        })
    });
    $(".occasion").on('click', function () {
        var fabric=get_filter('fabric');
        var sleeve=get_filter('sleeve');
        var pattern=get_filter('pattern');
        var fit=get_filter('fit');
        var occasion=get_filter('occasion');
        var sort= $("#sort option:selected").text();
        var url=$("#url").val();
        $.ajax({
            url:url,
            method:"post",
            data:{fabric:fabric,sleeve:sleeve,pattern:pattern,fit:fit,occasion:occasion,sort:sort,url:url},
            success: function (data) {
                $('.filter_products').html(data);
            }
        })
    });
    function get_filter(class_name) {
        var filter=[];
        $('.'+class_name+':checked').each(function () {
            filter.push($(this).val());
        });
        return filter;

    }
    $("#getPrice").change(function () {
       // alert("test");
        var size=$(this).val();
        if(size==""){
            alert ("Please Select Size");
            return false;
        }
        var product_id=$(this).attr("product-id");
        //alert(product_id);
        $.ajax({
            url:'/get-product-price',
            data:{size:size,product_id:product_id},
            type:'post',
            success:function (resp) {
                //alert(resp['product_price']);
                //alert(resp['dicounted_price']);
                //return false;
                if(resp['discount']>0){
                    $(".getAttrPrice").html
                    ("<del style='color:red;'><small style='color: red;'> $ " + resp['product_price']+
                        "</small></del> $" + resp['final_price']);
                }else{
                    $(".getAttrPrice").html("$ " + resp['product_price']);
                }

            },error:function(){
                alert("Error");
            }
        });
    });
});
