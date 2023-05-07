<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\MultiImg;
use App\Models\Product;
use App\Models\SubCategory;
use App\Models\User;
use Illuminate\Http\Request;

class IndexController extends Controller
{
    public function index()
    {
        $skip_category_0 = Category::skip(0)->first();
        $skip_product_0 = Product::where('status', 1)->where('category_id', $skip_category_0->id)
            ->orderBy('id', 'DESC')->limit(5)->get();

        $skip_category_2 = Category::skip(2)->first();

        $skip_product_2 = Product::where('status', 1)->where('category_id', $skip_category_2->id)
            ->orderBy('id', 'DESC')->limit(5)->get();

        $skip_category_3 = Category::skip(3)->first();

        $skip_product_3 = Product::where('status', 1)->where('category_id', $skip_category_3->id)
            ->orderBy('id', 'DESC')->limit(5)->get();

        $hotProducts = \App\Models\Product::where('hot_deals', 1)->where('discount_price', '!=', NULL)
            ->orderBy('id', 'DESC')->limit(3)->get();
        $specialOfferProducts = \App\Models\Product::where('special_offer', 1)->orderBy('id', 'DESC')->limit(3)->get();

        $recentlyProducts = \App\Models\Product::where('status', 1)->orderBy('id', 'DESC')->limit(3)->get();

        $specialDealProducts = \App\Models\Product::where('special_deals', 1)->orderBy('id', 'DESC')->limit(3)->get();

        return view('frontend.index', compact('skip_category_0', 'skip_product_0',
            'skip_category_2', 'skip_product_2', 'skip_category_3', 'skip_product_3', 'hotProducts', 'specialOfferProducts', 'recentlyProducts', 'specialDealProducts'));
    }

    public function productDetails($id, $slug)
    {
        $product = Product::with('multi_imgs')->findOrFail($id);

        $color = $product->product_color;
        $product_color = explode(',', $color);

        $size = $product->product_size;
        $product_size = explode(',', $size);

        $tag = $product->product_tags;
        $product_tags = explode(',', $tag);

        $cat_id = $product->category_id;
        $relatedProduct = Product::where('category_id', $cat_id)->where('id', '<>', $id)->orderBy('id', 'DESC')->limit(4)->get();

        $multi_images = MultiImg::where('product_id', $id)->get();

        return view('frontend.product.product_details', compact('product', 'product_color', 'product_size',
            'product_tags', 'multi_images', 'relatedProduct'));
    }

    public function VendorDetails($id)
    {
        $vendor = User::with('products')->where('id', $id)->first();

        return view('frontend.vendor.vendor_details', compact('vendor'));
    }

    public function VendorAll()
    {
        $vendors = User::where('status', 'active')->where('role', 'vendor')->with('products')->orderBy('id', 'DESC')->get();
        return view('frontend.vendor.vendor_all', compact('vendors'));
    }

    public function CatWiseProduct(Request $request, $id, $slug)
    {
        $products = Product::where('status', 1)->where('category_id', $id)->orderBy('id', 'DESC')->get();
        $categories = Category::with('products')->orderBy('category_name', 'ASC')->get();
        $breadCat = Category::where('id', $id)->first();
        $newProducts = Product::orderBy('id', 'DESC')->limit(3)->get();
        return view('frontend.product.category_view', compact('products', 'categories', 'breadCat', 'newProducts'));
    }

    public function SubCatWiseProduct(Request $request, $id, $slug)
    {
        $products = Product::where('status', 1)->where('subcategory_id', $id)->orderBy('id', 'DESC')->get();
        $subcategories = SubCategory::with('products')->orderBy('subcategory_name', 'ASC')->get();
        $breadSubCat = SubCategory::where('id', $id)->with('category')->first();
        $newProducts = Product::orderBy('id', 'DESC')->limit(3)->get();
        $categories = Category::with('products')->orderBy('category_name', 'ASC')->get();
        return view('frontend.product.subcategory_view', compact('products', 'categories', 'subcategories', 'breadSubCat', 'newProducts'));
    }

    public function ProductViewAjax($id)
    {
        $product = Product::with('brand', 'category','vendor')->findOrFail($id);

        $color = $product->product_color;
        $product_color = explode(',', $color);

        $size = $product->product_size;
        $product_size = explode(',', $size);

        return response()->json(array(
            'product' => $product,
            'size' => $product_size,
            'color' => $product_color,
        ));
    }

    public function ProductSearch(Request $request)
    {
        $request->validate(['search' => "required"]);

        $item = $request->search;
        $categories = Category::orderBy('category_name','ASC')->get();
        $products = Product::where('product_name','LIKE',"%$item%")->get();
        $newProduct = Product::orderBy('id','DESC')->limit(3)->get();
        return view('frontend.product.search',compact('products','item','categories','newProduct'));

    }

    public function SearchProduct(Request $request){

        $request->validate(['search' => "required"]);

        $item = $request->search;
        $products = Product::where('product_name','LIKE',"%$item%")->select('product_name','product_slug','product_thambnail','selling_price','id')->limit(6)->get();

        return view('frontend.product.search_product',compact('products'));

    }// End Method

}
