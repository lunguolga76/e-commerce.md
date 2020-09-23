$(document).ready(function () {
    //Verifing if the jQuery works
   // alert("test");
   /* $("#sort").on('change',function () {
        this.form.submit();
    });*/
    $("#sort").on('change',function () {
        //alert("test");
        var sort= $(this).val();
        //alert(sort);
        var url=$("#url").val();
        //alert(url);
        $.ajax({
            url:url,
            method:"post",
            data:{sort:sort,url:url},
            success: function (data) {
                $('.filter_products').html(data);
            }
        })
    });
});
