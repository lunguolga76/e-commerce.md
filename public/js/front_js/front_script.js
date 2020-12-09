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
    //Update Cart Items
    $(document).on('click','.btnItemUpdate', function () {
     if($(this).hasClass('qtyMinus')){
     //If qtyMinus buttons gets clicked byUser
     var quantity=$(this).prev().val();
      if(quantity<=1){
       alert("Item quantity must be 1 or greater!")
       return false;
        }else {
       new_qty=parseInt(quantity)-1;

   }
}
        if($(this).hasClass('qtyPlus')) {
            //If qtyPlus buttons gets clicked byUser
            var quantity = $(this).prev().prev().val();
            new_qty = parseInt(quantity)+1;
        }
        var cartid=$(this).data('cartid');
        $.ajax({
            data:{"cartid":cartid,"qty":new_qty},
            url:'/update-cart-item-qty',
            type:'post',
            success:function (resp) {
                if(resp.status==false){
                    alert(resp.message);
                }
                $("#AppendCartItems").html(resp.view);
            },error:function () {
                alert("Error");
            }
    });
    });
    //Delete Cart Items
    $(document).on('click','.btnItemDelete', function () {
        var cartid=$(this).data('cartid');
        var result=confirm("Want to deletewe this Cart Item ");
        if(result){
            $.ajax({
                data:{"cartid":cartid},
                url:'/delete-cart-item',
                type:'post',
                success:function (resp) {
                    $("#AppendCartItems").html(resp.view);
                },error:function () {
                    alert("Error");
                }
            });
        }
    });
    // validate register form on keyup and submit
    $("#registerForm").validate({
        rules: {
            name: "required",
            mobile: {
                required: true,
                minlength: 9,
                maxlength: 9,
                digits: true
            },
            email: {
                required: true,
                email: true,
                remote: "check-email"
            },
            password: {
                required: true,
                minlength: 6
            },
        },
            messages: {
                name: "Please enter your Name",
                mobile: {
                    required: "Please enter your mobile",
                    minlength: "Your mobile must consist of at least 9 digits",
                    maxlength: "Your mobile must consist of maximum 9 digits",
                    digits: "Please enter your Valid Mobile"
                },
                email: {
                    required: "Please enter your Email address",
                    email: "Please enter a valid Email address",
                    remote:"Email already exist"

                },
                password: {
                    required: "Please enter your password",
                    minlength: "Your password must be at least 6 characters long"
                }
            }

    });

    // validate login  form on keyup and submit
    $("#loginForm").validate({
        rules: {

            email: {
                required: true,
                email: true,

            },
            password: {
                required: true,
                minlength: 6
            },
        },
        messages: {

            email: {
                required: "Please enter your Email address",
                email: "Please enter a valid Email address",
              

            },
            password: {
                required: "Please enter your password",
                minlength: "Your password must be at least 6 characters long"
            }
        }

    });

});
