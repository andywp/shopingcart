<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use \Cart as Cart;
use Validator;
use App\Models\Product;

class CartController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //$userID ini biasane diambil dari session login $userId = auth()->user()->id;
        $userID = 2; 
        //$userID=auth()->user()->id;
       // Cart::session($userID);
        //Cart::getContent();
        return view('cart',['user' => $userID ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //$userID ini biasane diambil dari session login $userId = auth()->user()->id;
        $userID = 2; 
        //$userID=auth()->user()->id;
        $Product = Product::find($request->id);

       /*param cart*/
        $param=[
                    'id' => time(),
                    'name' => $request->name,
                    'price' => $request->price,
                    'quantity' => 1,
                    'attributes' => array(),
                    'associatedModel' => $Product
                ];


        Cart::session($userID)->add($param);
        return redirect('cart')->withSuccessMessage('Item was added to your cart!');


    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        // Validation on max quantity
        $validator = Validator::make($request->all(), [
            'quantity' => 'required|numeric'
        ]);
		/* echo  $request->quantity;
		dd( $request->all());die(); */
         if ($validator->fails()) {
            session()->flash('error_message', 'Quantity must be between 1 and 5.');
            return response()->json(['success' => false]);
         }

		//https://github.com/darryldecode/laravelshoppingcart
		// you may also want to update a product's quantity
		/**
		 * update a cart
		 *
		 * @param $id (the item ID)
		 * @param array $data
		 *
		 * the $data will be an associative array, you don't need to pass all the data, only the key value
		 * of the item you want to update on it
		 */
		
		//$userID ini biasane diambil dari session login $userId = auth()->user()->id;
        $userID = 2; 
        //$userID=auth()->user()->id;
        Cart::session($userID)->update($id, array(
		  'quantity' => $request->quantity, // so if the current product has a quantity of 4, another 2 will be added so this will result to 6
		));
        //Cart::getContent();
		
		
		
        session()->flash('success_message', 'Quantity was updated successfully!');

        return response()->json(['success' => true]);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $userID=2;
		Cart::session($userID)->remove($id);
        return redirect('cart')->withSuccessMessage('Item has been removed!');
    }

    /**
     * Remove the resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function emptyCart()
    {
        //Cart::destroy();
		//$total = Cart::clear();

		// for a specific user
		 $userID = 2; 
		Cart::session($userID)->clear();
		
        return redirect('cart')->withSuccessMessage('Your cart has been cleared!');

    }

    /**
     * Switch item from shopping cart to wishlist.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function switchToWishlist($id)
    {
        $item = Cart::get($id);

        Cart::remove($id);

        $duplicates = Cart::instance('wishlist')->search(function ($cartItem, $rowId) use ($id) {
            return $cartItem->id === $id;
        });

        if (!$duplicates->isEmpty()) {
            return redirect('cart')->withSuccessMessage('Item is already in your Wishlist!');
        }

        Cart::instance('wishlist')->add($item->id, $item->name, 1, $item->price)
                                  ->associate('App\Product');

        return redirect('cart')->withSuccessMessage('Item has been moved to your Wishlist!');

    }
}
