
<?php use App\Product; ?>
d        <table class="table table-bordered">
            <thead>
            <tr>
                <th>Product</th>
                <th colspan="2">Description</th>
                <th>Quantity/Update</th>
                <th>MRP</th>
                <th>Category/Product <br>Discount</br></th>
                <th>Sub Total</th>
            </tr>
            </thead>
            <tbody>
            <?php $total_price=0; ?>
            @foreach($userCartItems as $item)
                <?php $attrPrice=Product::getDiscountedAttrPrice($item['product_id'],$item['size']);       ?>
            <tr>
                <td> <img width="60" src="{{asset('images/product_images/small/'.$item['product']['main_image'])}}" alt=""/></td>
                <td colspan="2">{{$item['product']['product_name']}} ({{$item['product']['product_code']}})<br/>Color : {{$item['product']['product_color']}}
                    <br/>Size : {{$item['size']}}</td>
                <td>
                    <div class="input-append">
                        <input class="span1" style="max-width:34px" value="{{$item['quantity']}}" id="appendedInputButtons" size="16" type="text">
                        <button class="btn btnItemUpdate qtyMinus" type="button" data-cartid="{{$item['id']}}"><i class="icon-minus"></i></button>
                        <button class="btn btnItemUpdate qrtPlus" type="button" data-cartid="{{$item['id']}}"><i class="icon-plus"></i></button>
                        <button class="btn btn-danger" type="button"><i class="icon-remove icon-white"></i></button>				</div>
                </td>
                <td>$ {{$attrPrice['product_price']}}</td>
                <td>$ {{$attrPrice['discount']}}</td>
                <td>$ {{$attrPrice['final_price']*$item['quantity']}}</td>
            </tr>
                <?php $total_price=$total_price+($attrPrice['final_price']*$item['quantity']);?>
            @endforeach
            <tr>
                <td colspan="6" style="text-align:right">Sub Total:	</td>
                <td> $ {{$total_price}}</td>
            </tr>
            <tr>
                <td colspan="6" style="text-align:right">Voucher Discount:	</td>
                <td> $ 0.00</td>
            </tr>
            <tr>
                <td colspan="6" style="text-align:right"><strong>GRAND TOTAL ($ {{$total_price}} - $.0) =</strong></td>
                <td class="label label-important" style="display:block"> <strong>$ {{$total_price}} </strong></td>
            </tr>
            </tbody>
        </table>


