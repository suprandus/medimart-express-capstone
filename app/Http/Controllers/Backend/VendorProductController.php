<?php

namespace App\Http\Controllers\Backend;

use App\DataTables\VendorProductDataTable;
use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\ChildCategory;
use App\Models\Product;
use App\Models\ProductImageGallery;
use App\Models\ProductVariant;
use App\Models\SubCategory; // Import SubCategory model
use App\Traits\ImageUploadTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Str;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;

class VendorProductController extends Controller
{
    use ImageUploadTrait;

    /**
     * Display a listing of the resource.
     */
    public function index(VendorProductDataTable $dataTable)
    {
        $lowStockProducts = Product::where('vendor_id', Auth::user()->vendor->id)
            ->where('qty', '<', 10)
            ->get();

        return $dataTable->render('vendor.product.index', compact('lowStockProducts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::all();
        $brands = Brand::all();
        return view('vendor.product.create', compact('categories', 'brands'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'image' => ['required', 'image', 'max:3000'],
            'name' => ['required', 'max:200'],
            'category' => ['required'],
            'price' => ['required'],
            'qty' => ['required'],
            'short_description' => ['required', 'max: 600'],
            'long_description' => ['required'],
            'seo_title' => ['nullable', 'max:200'],
            'seo_description' => ['nullable', 'max:250'],
            'status' => ['required']
        ]);

        $imagePath = $this->uploadImage($request, 'image', 'uploads');

        $product = new Product();
        $product->thumb_image = $imagePath;
        $product->name = $request->name;
        $product->slug = Str::slug($request->name);
        $product->vendor_id = Auth::user()->vendor->id;
        $product->category_id = $request->category;
        $product->sub_category_id = $request->sub_category;
        $product->child_category_id = $request->child_category;
        $product->brand_id = $request->brand;
        $product->qty = $request->qty;
        $product->short_description = $request->short_description;
        $product->long_description = $request->long_description;
        $product->video_link = $request->video_link;
        $product->sku = $request->sku;
        $product->price = $request->price;
        $product->offer_price = $request->offer_price;
        $product->offer_start_date = $request->offer_start_date;
        $product->offer_end_date = $request->offer_end_date;
        $product->product_type = $request->product_type;
        $product->status = $request->status;
        $product->is_approved = 0;
        $product->seo_title = $request->seo_title;
        $product->seo_description = $request->seo_description;
        $product->save();

        activity()
            ->performedOn($product)
            ->causedBy(Auth::user())
            ->withProperties(['name' => $product->name])
            ->log('Product created');

        toastr('Created Successfully!', 'success');

        return redirect()->route('vendor.products.index');
    }

    /**
     * Show the form for editing a product.
     */
    public function edit(string $id)
    {
        $product = Product::findOrFail($id);

        if ($product->vendor_id != Auth::user()->vendor->id) {
            abort(404);
        }

        $categories = Category::all();
        $brands = Brand::all();
        $subCategories = SubCategory::where('category_id', $product->category_id)->get();
        $childCategories = ChildCategory::where('sub_category_id', $product->sub_category_id)->get();

        return view('vendor.product.edit', compact('product', 'categories', 'brands', 'subCategories', 'childCategories'));
    }

    /**
     * Update the specified product in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'image' => ['nullable', 'image'],
            'name' => ['required', 'string'],
            'category' => ['required', 'integer'],
            'sub_category' => ['nullable', 'integer'],
            'child_category' => ['nullable', 'integer'],
            'brand' => ['required', 'integer'],
            'sku' => ['required', 'string'],
            'price' => ['required', 'numeric'],
            'offer_price' => ['nullable', 'numeric'],
            'offer_start_date' => ['nullable', 'date', 'after_or_equal:today'],
            'offer_end_date' => ['nullable', 'date', 'after_or_equal:offer_start_date'],
            'qty' => ['required', 'integer', 'min:0'],
            'video_link' => ['nullable', 'url'],
            'short_description' => ['nullable', 'string'],
            'long_description' => ['nullable', 'string'],
            'seo_title' => ['nullable', 'string'],
            'seo_description' => ['nullable', 'string'],
            'status' => ['required', 'boolean'],
        ]);

        $product = Product::findOrFail($id);

        if ($product->vendor_id != Auth::user()->vendor->id) {
            abort(404);
        }

        $imagePath = $this->updateImage($request, 'image', 'uploads', $product->thumb_image);

        $product->thumb_image = empty(!$imagePath) ? $imagePath : $product->thumb_image;
        $product->name = $request->name;
        $product->slug = Str::slug($request->name);
        $product->vendor_id = Auth::user()->vendor->id;
        $product->category_id = $request->category;
        $product->sub_category_id = $request->sub_category;
        $product->child_category_id = $request->child_category;
        $product->brand_id = $request->brand;
        $product->qty = $request->qty;
        $product->short_description = $request->short_description;
        $product->long_description = $request->long_description;
        $product->video_link = $request->video_link;
        $product->sku = $request->sku;
        $product->price = $request->price;
        $product->offer_price = $request->offer_price;
        $product->offer_start_date = $request->offer_start_date;
        $product->offer_end_date = $request->offer_end_date;
        $product->product_type = $request->product_type;
        $product->status = $request->status;
        $product->is_approved = $product->is_approved;
        $product->seo_title = $request->seo_title;
        $product->seo_description = $request->seo_description;
        $product->save();

        activity()
            ->performedOn($product)
            ->causedBy(Auth::user())
            ->withProperties(['name' => $product->name])
            ->log('Product updated');

        toastr('Updated Successfully!', 'success');

        return redirect()->route('vendor.products.index');
    }

    /**
     * Remove the specified product from storage.
     */
    public function destroy(string $id)
    {
        $product = Product::findOrFail($id);
    
        // Check if the authenticated vendor owns the product
        if ($product->vendor_id != Auth::user()->vendor->id) {
            abort(404);
        }
    
        $productName = $product->name;
        $product->delete();
    
        activity()
            ->performedOn($product)
            ->causedBy(Auth::user())
            ->withProperties(['name' => $productName])
            ->log('Product deleted');
    
        return response(['status' => 'success', 'message' => 'Deleted Successfully!']);
    }
    
}
