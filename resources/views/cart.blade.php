@extends('master')

@section('content')

    <div class="container">
        <p><a href="{{ url('shop') }}">Home</a> / Cart</p>
        <h1>Your Cart</h1>

        <hr>

        @if (session()->has('success_message'))
            <div class="alert alert-success">
                {{ session()->get('success_message') }}
            </div>
        @endif

        @if (session()->has('error_message'))
            <div class="alert alert-danger">
                {{ session()->get('error_message') }}
            </div>
        @endif

        @if (Cart::session($user)->getContent()->count() > 0)

            <table class="table">
                <thead>
                    <tr>
                        <th class="table-image"></th>
                        <th>Product</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th class="column-spacer"></th>
                        <th></th>
                    </tr>
                </thead>
                <pre>
                
                
                <tbody>
                    @foreach ((object) Cart::session($user)->getContent()->toArray() as $item)
					
                    <tr>
                        <td class="table-image">
							<a href="{{ url('shop', [$item['associatedModel']['slug']]) }}">
								<img src="{{ asset('img/' . $item['associatedModel']['image']) }}" alt="product" class="img-responsive cart-image">
							</a>
						</td>
                        <td><a href="{{ url('shop', $item['associatedModel']['slug']) }}">{{ $item['name'] }}</a></td>
                        <td>
							
							<div class="input-group spinner">
								<input  type="text" class="form-control quantity" value="{{ $item['quantity']}}"  data-id="{{ $item['id'] }}"  >
								<!--<div class="input-group-btn-vertical">
								  <button class="btn btn-default" type="button" data-id="{{ $item['id'] }}"><i class="fa fa-caret-up" ></i></button>
								  <button class="btn btn-default" type="button" data-id="{{ $item['id'] }}"><i class="fa fa-caret-down" ></i></button>
								</div> -->
							</div>
							
                        </td>
                        <td>${{ $item['price'] }}</td>
                        <td class=""></td>
                        <td>
                            <form action="{{ url('cart', [$item['id']]) }}" method="POST" class="side-by-side">
                                {!! csrf_field() !!}
                                <input type="hidden" name="_method" value="DELETE">
                                <input type="submit" class="btn btn-danger btn-sm" value="Remove">
                            </form>

                           
                        </td>
                    </tr>

                    @endforeach
                    <tr>
                        <td class="table-image"></td>
                        <td></td>
                        <td class="small-caps table-bg" style="text-align: right">Subtotal</td>
                        <td>${{  Cart::session($user)->getSubTotal() }}</td>
                        <td></td>
                        <td></td>
                    </tr>
                   

                    <tr class="border-bottom">
                        <td class="table-image"></td>
                        <td style="padding: 40px;"></td>
                        <td class="small-caps table-bg" style="text-align: right">Your Total</td>
                        <td class="table-bg">${{  Cart::session($user)->getTotal() }}</td>
                        <td class="column-spacer"></td>
                        <td></td>
                    </tr>

                </tbody>
            </table>

            <a href="{{ url('/shop') }}" class="btn btn-primary btn-lg">Continue Shopping</a> &nbsp;
            <a href="#" class="btn btn-success btn-lg">Proceed to Checkout</a>

            <div style="float:right">
                <form action="{{ url('/emptyCart') }}" method="POST">
                    {!! csrf_field() !!}
                    <input type="hidden" name="_method" value="DELETE">
                    <input type="submit" class="btn btn-danger btn-lg" value="Empty Cart">
                </form>
            </div>

        @else

            <h3>You have no items in your shopping cart</h3>
            <a href="{{ url('/shop') }}" class="btn btn-primary btn-lg">Continue Shopping</a>

        @endif

        <div class="spacer"></div>

    </div> <!-- end container -->

@endsection

@section('extra-js')
    <script>
        (function(){

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('.quantity').on('change', function() {
                /* var id = $(this).attr('data-id'); */
                var id = $(this).attr('data-id');
				//console.log(id,'idnya');
                $.ajax({
                  type: "PATCH",
                  url: '{{ url("/cart") }}' + '/' + id,
                  data: {
                    'quantity': this.value,
                  },
                  success: function(data) {
                    //window.location.href = '{{ url('/cart') }}';
                  }
                });

            });
			
			
			var updateQTY=function(id,val){
				
               /*  var id = $(this).attr('data-id');
				console.log(id,'idnya'); */
                $.ajax({
                  type: "PATCH",
                  url: '{{ url("/cart") }}' + '/' + id,
                  data: {
                    'quantity': val,
                  },
                  success: function(data) {
                    window.location.href = '{{ url('/cart') }}';
                  }
                });
				
				
			};
			
			
			
			/*  $('.spinner .btn:first-of-type').on('click', function() {
				var id = $('.spinner input').attr('data-id');
				console.log(id,'idnya');
				$('.spinner input').val( parseInt($('.spinner input').val(), 10) + 1);
				 //$(this).siblings('input').change();
			  });
			  $('.spinner .btn:last-of-type').on('click', function() {
				$('.spinner input').val( parseInt($('.spinner input').val(), 10) - 1);
				// $(this).siblings('input').change();
			  }); */
			
			$('.spinner .btn:first-of-type').on('click', function() {
				var i=parseInt($(this).closest('.spinner').find($( "input")).val(), 10) + 1;
				//console.log(i,'upnya yess');
				$(this).closest('.spinner').find($( "input")).val(i);
				  var id = $(this).attr('data-id');
				  updateQTY(id,i);
				//console.log(id,'up');
			  });
			  $('.spinner .btn:last-of-type').on('click', function() {
				var i=parseInt($(this).closest('.spinner').find($( "input")).val(), 10) - 1;
				$(this).closest('.spinner').find($( "input")).val(i);
				//console.log(i,'doen yess');
				var id = $(this).attr('data-id');
				  updateQTY(id,i);
			  });
			  
			  
			  
			})();

    </script>
@endsection
