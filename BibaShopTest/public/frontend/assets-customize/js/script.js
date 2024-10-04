/// Start Add in Wishlist

function addToWishList(product_id) {
    // Get CSRF token from the meta tag
    var csrfToken = $('meta[name="csrf-token"]').attr('content');

    $.ajax({
        type: "POST",
        dataType: 'json',
        url: "/add-to-wishlist/" + product_id,
        // Set CSRF token in headers
        headers: {
            'X-CSRF-TOKEN': csrfToken
        },
        success: function(data) {
            // Handle success or error response
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            });

            if ($.isEmptyObject(data.error)) {
                Toast.fire({
                    type: 'success',
                    icon: 'success',
                    title: data.success,
                });
            } else {
                Toast.fire({
                    type: 'error',
                    icon: 'error',
                    title: data.error,
                });
            }
        }
    });
}

/// End Add in Wishlist

/// Start Load Wishlist Data

function wishlist() {


    $.ajax({
        type: "GET",
        dataType: 'json',
        url: "/get-wishlist-product/",

        success: function(response) {

        }
    });
}

  /// End Load Wishlist Data

  /// Start Remove in Wishlist


   /// End Remove in Wishlist





  /// Start product view with Modal


  $.ajaxSetup({
    headers:{
        'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')
    }
})

  function productView(id){
    // alert(id);
    $.ajax({
     type: 'GET',
     url: '/product/view/modal/'+id,
     dataType: 'json',
     success:function(data){
        $('#pname').text(data.product.product_name);
        $('#pprice').text(data.product.selling_price);
        $('#pcode').text(data.product.product_code);
        $('#pcategory').text(data.product.category.category_name);
        $('#pbrand').text(data.product.brand.brand_name);
        $('#pimage').attr('src' , '/'+data.product.product_thambnail);
        $('#pname').text(data.product.product_name);
        $('#pvendor_id').text(data.product.vendor_id);

        $('#product_id').val(id);
        $('#qty').val(1);
        // Product Price
        if (data.product.discount_price == null) {
            $('#pprice').text('');
            $('#oldprice').text('');
            $('#pprice').text('$'+data.product.selling_price);
        }
        else{
            $('#pprice').text('$'+data.product.discount_price);
            $('#oldprice').text('$'+data.product.selling_price);
        }

        // Start Stock Option

        if( data.product.product_qty > 0){
            $("#aviable").text('');
            $("#stockout").text('');
            $("#aviable").text('Available');
        }
        else{
            $("#aviable").text('');
            $("#stockout").text('');
            $("#stockout").text('Stockout');
        }



        // End Stock Option


        // Start Size
        $('select[name="size"]').empty();
        $.each(data.size , function(key , value){
            $('select[name = "size"]').append('<option value="'+value+'">'+value+'</option');
            if(data.size == ""){
                $('#sizeArea').hide();
            }
            else{
                $('#sizeArea').show();
            }
        })
        // End Size

          // Start Color
          $('select[name="color"]').empty();
          $.each(data.color , function(key , value){
              $('select[name = "color"]').append('<option value="'+value+'">'+value+'</option');
              if(data.color == ""){
                  $('#colorArea').hide();
              }
              else{
                  $('#colorArea').show();
              }
          })
          // End Color




     }
    })

  }

/// End Product View With Modal



  /// Start Add To Cart Prodcut

  function addToCart(){

    var product_name = $('#pname').text();
    var id = $('#product_id').val();
    var vendor_id = $('#pvendor_id').text();
    var color = $('#color option:selected').text();
    var size = $('#size option:selected').text();
    var quantity = $('#qty').val();
    $.ajax({
       type: "POST",
       dataType : 'json',
       data:{
            color:color,
            size:size,
            quantity:quantity,
            product_name:product_name,
            vendor_id:vendor_id
       },
       url: "/cart/data/store/"+id,
       success:function(data){
           miniCart();
           $('#closeModal').click();
             // console.log(data)

            // Start Message

            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                icon: 'success',
                showConfirmButton: false,
                timer: 3000
          })
          if ($.isEmptyObject(data.error)) {

                  Toast.fire({
                  type: 'success',
                  title: data.success,
                  })

          }else{

         Toast.fire({
                  type: 'error',
                  title: data.error,
                  })
              }

            // End Message
       }
    })

   }

   /// End Add To Cart Product


 function miniCart(){
    $.ajax({
        type: 'GET',
        url: '/product/mini/cart',
        dataType: 'json',
        success:function(response){
        console.log(response);

        $('span[id="cartSubTotal"]').text('$' + response.totalPrice);
        $('#cartQty').text(response.cartQty);

        var miniCart = "";


        $.each(response.cartContent,  function(key,value){
           miniCart += `<ul>
           <li>
               <div class="shopping-cart-img">
                   <a href="shop-product-right.html"><img alt="Nest" src="/${value.attributes.image} " style="width:50px;height:50px;" /></a>
               </div>
               <div class="shopping-cart-title" style="margin: -73px 74px 14px; width" 146px;>
                   <h4><a href="shop-product-right.html"> ${value.name} </a></h4>
                   <h4><span>${value.quantity} × </span>$${value.price}</h4>
               </div>
               <div class="shopping-cart-delete" style="margin: -85px 1px 0px;">
                   <a type="submit" id="${value.id}" onclick="miniCartRemove(this.id)" ><i class="fi-rs-cross-small"></i></a>
               </div>
           </li>
       </ul>
       <hr><br>`
          });

            $('#miniCart').html(miniCart);

        }

    })
}
miniCart();

/// Start mini cart remove

function miniCartRemove(id){
    $.ajax({
       type: 'GET',
       url: '/minicart/product/remove/'+id,
       dataType:'json',
       success:function(data){
       miniCart();
            // Start Message

           const Toast = Swal.mixin({
                 toast: true,
                 position: 'top-end',
                 icon: 'success',
                 showConfirmButton: false,
                 timer: 3000
           })
           if ($.isEmptyObject(data.error)) {

                   Toast.fire({
                   type: 'success',
                   title: data.success,
                   })

           }else{

          Toast.fire({
                   type: 'error',
                   title: data.error,
                   })
               }

             // End Message

       }

    })
  }

/// End mini cart remove




  /// Start Details Page Add To Cart Product

  function addToCartDetails(){

    var product_name = $('#dpname').text();
    var id = $('#dproduct_id').val();
    var vendor = $('#vproduct_id').val();
    var color = $('#dcolor option:selected').text();
    var size = $('#dsize option:selected').text();
    var quantity = $('#dqty').val();
    $.ajax({
       type: "POST",
       dataType : 'json',
       data:{
            color:color,
            size:size,
            quantity:quantity,
            product_name:product_name,
            vendor: vendor,
       },
       url: "/dcart/data/store/"+id,
       success:function(data){
           miniCart();

             // console.log(data)

            // Start Message

            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                icon: 'success',
                showConfirmButton: false,
                timer: 3000
          })
          if ($.isEmptyObject(data.error)) {

                  Toast.fire({
                  type: 'success',
                  title: data.success,
                  })

          }else{

         Toast.fire({
                  type: 'error',
                  title: data.error,
                  })
              }

            // End Message
       }
    })

   }

  /// End Details Page Add To Cart Product



  /// Start Load My Cart

 function cart(){
    $.ajax({
        type: 'GET',
        url: '/get-cart-product',
        dataType: 'json',
        success:function(response){
        console.log(response);



        var rows = "";


        $.each(response.cartContent, function(key,value){
            rows += `<tr class="pt-30">
            <td class="custome-checkbox pl-30">

            </td>
            <td class="image product-thumbnail pt-40"><img src="/${value.attributes.image}" alt="#"></td>
            <td class="product-des product-name">
                <h6 class="mb-5"><a class="product-name mb-10 text-heading" href="shop-product-right.html">${value.name} </a></h6>

            </td>
            <td class="price" data-title="Price">
                <h4 class="text-body">$${value.price} </h4>
            </td>

              <td class="price" data-title="Price">
              ${value.attributes.color == null ? `<span>.... </span>` : `<h6 class="text-body">${value.attributes.color} </h6>` }
            </td>

               <td class="price" data-title="Price">
              ${value.attributes.size == null ? `<span>.... </span>` : `<h6 class="text-body">${value.attributes.size} </h6>` }
            </td>


            <td class="text-center detail-info" data-title="Stock">
                <div class="detail-extralink mr-15">
                    <div class="detail-qty border radius">

     <a type="submit" class="qty-down" id="${value.id}" onclick="cartDecrement(this.id)"><i class="fi-rs-angle-small-down"></i></a>

      <input type="text" name="quantity" class="qty-val" value="${value.quantity}" min="1">

     <a  type="submit" class="qty-up" id="${value.id}" onclick="cartIncrement(this.id)"><i class="fi-rs-angle-small-up"></i></a>

                    </div>
                </div>
            </td>
            <td class="price" data-title="Price">
                <h4 class="text-brand">$${value.quantity * value.price } </h4>
            </td>
            <td class="action text-center" data-title="Remove">
            <a type="submit" class="text-body"  id="${value.id}" onclick="cartRemove(this.id)" ><i class="fi-rs-trash"></i></a></td>
        </tr>`
          });

            $('#cartPage').html(rows);

        }

    })
}
cart();
  /// End Load My Cart

  /// Cart Remove Start

  function cartRemove(id){
    $.ajax({
        type: "GET",
        dataType: 'json',
        url: "/cart-remove/"+id,

        success:function(data){
            cart();
            miniCart();
            couponCalculation();
             // Start Message

    const Toast = Swal.mixin({
          toast: true,
          position: 'top-end',

          showConfirmButton: false,
          timer: 3000
    })
    if ($.isEmptyObject(data.error)) {

            Toast.fire({
            type: 'success',
            icon: 'success',
            title: data.success,
            })

    }else{

   Toast.fire({
            type: 'error',
            icon: 'error',
            title: data.error,
            })
        }

      // End Message


        }
    })
}

  /// Cart Remove End



  /// Cart Decrement Start
   function cartDecrement(id){
    $.ajax({
        type: 'GET',
        url: "/cart-decrement/"+id,
        dataType: 'json',
        success:function(data){

            cart();
            miniCart();
            couponCalculation();

        }
    });

   }
  /// Cart Decrement End

 /// Cart Increment Start
    function cartIncrement(id){
        $.ajax({
            type: 'GET',
            url: "/cart-increment/"+id,
            dataType: 'json',
            success:function(data){

                cart();
                miniCart();
                couponCalculation();

            }
        });

       }
    /// Cart Increment End




/// Start Apply Coupon

function applyCoupon(){
    var coupon_name = $('#coupon_name').val();
    $.ajax({
        type: "POST",
        dataType: 'json',
        data: {coupon_name:coupon_name},

        url: "/coupon-apply",

        success:function(data){
            couponCalculation();

            if (data.validity == true) {
                $('#couponField').hide();
            }


             // Start Message

    const Toast = Swal.mixin({
          toast: true,
          position: 'top-end',

          showConfirmButton: false,
          timer: 3000
    })
    if ($.isEmptyObject(data.error)) {

            Toast.fire({
            type: 'success',
            icon: 'success',
            title: data.success,
            })

    }else{

   Toast.fire({
            type: 'error',
            icon: 'error',
            title: data.error,
            })
        }

      // End Message


        }
    })
}


/// End Apply Coupon


// Start CouponCalculation Method
function couponCalculation(){
    $.ajax({
        type: 'GET',
        url: "/coupon-calculation",
        dataType: 'json',
        success:function(data){
        if (data.total) {
            $('#couponCalField').html(
            `<tr>
                <td class="cart_total_label">
                    <h6 class="text-muted">Subtotal</h6>
                </td>
                <td class="cart_total_amount">
                    <h4 class="text-brand text-end">$${data.total}</h4>
                </td>
            </tr>

            <tr>
                <td class="cart_total_label">
                    <h6 class="text-muted">Grand Total</h6>
                </td>
                <td class="cart_total_amount">
                    <h4 class="text-brand text-end">$${data.total}</h4>
                </td>
            </tr>
            ` )
        }else{
            $('#couponCalField').html(
                `<tr>
                <td class="cart_total_label">
                    <h6 class="text-muted">Subtotal</h6>
                </td>
                <td class="cart_total_amount">
                    <h4 class="text-brand text-end">$${data.subtotal}</h4>
                </td>
            </tr>

            <tr>
                <td class="cart_total_label">
                    <h6 class="text-muted">Coupon </h6>
                </td>
                <td class="cart_total_amount">
<h6 class="text-brand text-end">${data.coupon_name} <a type="submit" onclick="couponRemove()"><i class="fi-rs-trash"></i> </a> </h6>
                </td>
            </tr>

            <tr>
                <td class="cart_total_label">
                    <h6 class="text-muted">Discount Amount  </h6>
                </td>
                <td class="cart_total_amount">
<h4 class="text-brand text-end">$${data.discount_amount}</h4>
                </td>
            </tr>


            <tr>
                <td class="cart_total_label">
                    <h6 class="text-muted">Grand Total </h6>
                </td>
                <td class="cart_total_amount">
      <h4 class="text-brand text-end">$${data.total_amount}</h4>
                </td>
            </tr> `
                )
        }

        }
    })
 }

couponCalculation();
 // End CouponCalculation Method


     // Coupon Remove Start
     function couponRemove(){
        $.ajax({
            type: "GET",
            dataType: 'json',
            url: "/coupon-remove",

            success:function(data){
               couponCalculation();
               $('#couponField').show();
                 // Start Message

        const Toast = Swal.mixin({
              toast: true,
              position: 'top-end',

              showConfirmButton: false,
              timer: 3000
        })
        if ($.isEmptyObject(data.error)) {

                Toast.fire({
                type: 'success',
                icon: 'success',
                title: data.success,
                })

        }else{

       Toast.fire({
                type: 'error',
                icon: 'error',
                title: data.error,
                })
            }

          // End Message


            }
        })
    }
// Coupon Remove End
