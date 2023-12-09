<?php

namespace App\Http\Livewire;

use App\Models\Category;
use App\Models\Product;
use Gloudemans\Shoppingcart\Facades\Cart;
use Livewire\Component;

class CategoryComponent extends Component
{
//    use WithPagination;
    public $pageSize = 12;
    public $orderBy = 'Default Sorting';
    public $slug;

    public function store($product_id, $product_name, $product_price)
    {
        Cart::add($product_id, $product_name, 1, $product_price)->associate('\App\Models\Product');//3rd param is quantity
        session()->flash('success_message', 'Item added to cart');
        return redirect()->route('shop.cart');
    }

    public function changePageSize($size)
    {
        $this->pageSize = $size;
    }

    public function changeOrderBy($order)
    {
        $this->orderBy = $order;
    }

    public function mount($slug)
    {
        $this->slug = $slug;
    }

    public function render()
    {
        $category = Category::where('slug',$this->slug)->first();
        if ($this->orderBy == 'Price: Low to High') {
            $products = Product::where('category_id',$category->id)->orderBy('regular_price', 'ASC')->paginate($this->pageSize);
        } else if ($this->orderBy == 'Price: High to Low') {
            $products = Product::where('category_id',$category->id)->orderBy('regular_price', 'DESC')->paginate($this->pageSize);
        } else if ($this->orderBy == 'Sort By Newnesss') {
            $products = Product::where('category_id',$category->id)->orderBy('created_at', 'DESC')->paginate($this->pageSize);
        } else {
            $products = Product::where('category_id',$category->id)->paginate($this->pageSize);
        }
        $categories = Category::orderBy('name','ASC')->get();
        return view('livewire.category-component', ['products' => $products,'categories'=>$categories, 'category_name'=>$category->name]);
    }
}
