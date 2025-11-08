<?php

namespace App\Http\Controllers\Admin;

use App\Models\DistributorProduct;
use App\Models\DistributorProductSpecification;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
class DistributorProductController extends Controller
{
    public function index(Request $request)
    {
        if(isset($request->keyword)){
            $keyword = (!empty($request->keyword) && $request->keyword!='')?$request->keyword:'';
            $data = DistributorProduct::where('title','LIKE','%'.$keyword.'%')->orderby('id','desc')->paginate(25);
        }else{
            $data = DistributorProduct::orderby('id','desc')->paginate(25);
        }  
        return view('admin.distributor.product.index', compact('data','request'));
    }

    public function create(Request $request)
    {
        return view('admin.distributor.product.create');
    }

    public function store(Request $request)
    {
         //dd($request->all());

        $request->validate([
            "title" => "required|string|max:255",
            "short_desc" => "nullable",
            "desc" => "nullable",
            "image" => "required",
			"amount" => "required",
        ]);
        $storeData=new DistributorProduct();
        $storeData->title=$request->title;
        $storeData->short_desc=$request->short_desc;
        $storeData->desc=$request->desc;
        $storeData->amount=$request->amount;
        $storeData->position =$storeData->position+1;
        // slug generate
        $slug = \Str::slug($request['title'], '-');
        $slugExistCount = DistributorProduct::where('slug', $slug)->count();
        if ($slugExistCount > 0) $slug = $slug . '-' . ($slugExistCount + 1);
        $storeData->slug = $slug;
        if (isset($request['image'])) {
            $upload_path = "public/uploads/retailer/product/";
            $image = $request['image'];
            $imageName = time() . "." . $image->getClientOriginalName();
            $image->move($upload_path, $imageName);
            $uploadedImage = $imageName;
            $storeData->image = $upload_path . $uploadedImage;
        }
        $storeData->save();
		$multipleColorData = [];
		 if (isset($request['name']) || isset($request['description'])) {
			
            foreach ($request['name'] as $nameKey => $nameValue) {
				 if (is_null($request->name[$nameKey])) {
                      continue;
                 }
                $multipleColorData[] = [
                    'product_id' => $storeData->id,
                    'name' => $nameValue,
                ];
            }

            foreach ($request['description'] as $descriptionKey => $descriptionValue) {
				 if (is_null($request->description[$descriptionKey])) {
                      continue;
                 }
                $multipleColorData[$descriptionKey]['description'] = $descriptionValue;
            }

            // dd($multipleColorData);

            DistributorProductSpecification::insert($multipleColorData);
        }
        if ($storeData) {
            return redirect()->route('admin.distributor.product.index');
        } else {
            return redirect()->route('admin.distributor.product.create')->withInput($request->all());
        }
    }

    public function show(Request $request, $id)
    {
        $data = DistributorProduct::where('id',$id)->first();
        $spec=DistributorProductSpecification::where('product_id',$id)->get();
        return view('admin.distributor.product.detail', compact('data','spec'));
    }


    public function edit(Request $request, $id)
    {
        $data = DistributorProduct::where('id',$id)->first();
        $spec=DistributorProductSpecification::where('product_id',$id)->get();
        return view('admin.distributor.product.edit', compact('id', 'data','spec'));
    }

    public function update(Request $request,$id)
    {
        // dd($request->all());

        $request->validate([
            "title" => "required|string|max:255",
            "short_desc" => "nullable",
            "desc" => "nullable",
            "amount" => "nullable",
        ]);

        $storeData=DistributorProduct::findOrFail($id);
        $storeData->title=$request->title;
        $storeData->short_desc=$request->short_desc;
        $storeData->desc=$request->desc;
        $storeData->amount=$request->amount;
        $storeData->position =$storeData->position+1;
        // slug generate
        if ($request->title!=$storeData->title) {
            $slug = \Str::slug($request['title'], '-');
            $slugExistCount = DistributorProduct::where('slug', $slug)->count();
            if ($slugExistCount > 0) $slug = $slug . '-' . ($slugExistCount + 1);
            $storeData->slug = $slug;
        }
        if (isset($request['image'])) {
            $upload_path = "public/uploads/distributor/product/";
            $image = $request['image'];
            $imageName = time() . "." . $image->getClientOriginalName();
            $image->move($upload_path, $imageName);
            $uploadedImage = $imageName;
            $storeData->image = $upload_path . $uploadedImage;
        }
        $storeData->save();
		 if (!empty($request['name']) && !empty($request['description'])) {
            $multipleColorData = [];

            foreach ($request['name'] as $nameKey => $nameValue) {
                $multipleColorData[] = [
                    'product_id' => $storeData->id,
                    'name' => $nameValue,
                ];
            }

            foreach ($request['description'] as $descriptionKey => $descriptionValue) {
                $multipleColorData[$descriptionKey]['description'] = $descriptionValue;
            }

            // dd($multipleColorData);

            DistributorProductSpecification::insert($multipleColorData);
        }
        if ($storeData) {
            return redirect()->back()->with('success', 'Product updated successfully');
        } else {
            return redirect()->route('admin.distributor.product.update', $request->product_id)->withInput($request->all());
        }
    }

    public function status(Request $request, $id)
    {
        $storeData = DistributorProduct::findOrFail($id);

        $status = ($storeData->status == 1) ? 0 : 1;
        $storeData->status = $status;
        $storeData->save();
        if ($storeData) {
            return redirect()->route('admin.distributor.product.index');
        } else {
            return redirect()->route('admin.distributor.product.create')->withInput($request->all());
        }
    }

    public function destroy(Request $request, $id)
    {
        $isReferenced = DB::table('dkg_order_products')->where('product_id', $id)->exists();
    
        if ($isReferenced) {
            return redirect()->route('admin.distributor.product.index')->with('error', 'Product cannot be deleted because it is referenced in another table.');
        }
        $data=DistributorProduct::destroy($id);

        return redirect()->route('admin.distributor.product.index')->with('success','Deleted successfully');
    }


    //export csv for product 
    public function exportCSV(Request $request)
    {
        if(isset($request->keyword)){
            $keyword = (!empty($request->keyword) && $request->keyword!='')?$request->keyword:'';
            $data = DistributorProduct::where('title',$keyword)->orderby('id','desc')->get();
        }else{
            $data = DistributorProduct::orderby('id','desc')->get();
        }  

        if (count($data) > 0) {
            $delimiter = ",";
            $filename = "Dkg-dream-gift-report-".date('Y-m-d').".csv";

            // Create a file pointer 
            $f = fopen('php://memory', 'w');

            // Set column headers 
            $fields = array('SR','PRODUCT NAME',  'DESCRIPTION','POINTS','STATUS', 
            'DATE','TIME');
            fputcsv($f, $fields, $delimiter); 

            $count = 1;

            foreach($data as $row) {
               
                $date = date('j M Y', strtotime($row['created_at']));
                $time = date('g:i A', strtotime($row['created_at']));
                $lineData = array(
                    $count,
                    $row['title'] ?? '',
                   
                   strip_tags($row['desc']) ?? '',
                    $row['amount'] ?? '',
                    ($row->status == 1) ? 'Active' : 'Inactive',
                    $date,
                    $time
                );

                fputcsv($f, $lineData, $delimiter);

                $count++;
            }

            // Move back to beginning of file
            fseek($f, 0);

            // Set headers to download file rather than displayed
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="' . $filename . '";');

            //output all remaining data on a file pointer
            fpassthru($f);
        }
    }
   //add specification

   public function specificationAdd(Request $request)
   {
         //dd($request->all());

        $request->validate([
            "name" => "required|string|max:255",
            "description" => "required",
           
        ]);
        $storeData=new DistributorProductSpecification();
        $storeData->product_id=$request->product_id;
        $storeData->name=$request->name;
        $storeData->description=$request->description;
       
        $storeData->save();
		
        if ($storeData) {
            return redirect()->route('admin.distributor.product.edit',$storeData->product_id)->with('success', 'Product updated successfully');
        } else {
            return redirect()->route('admin.distributor.product.create')->withInput($request->all());
        }
    }

    public function specificationDestroy(Request $request, $id)
    {
        $data=DistributorProductSpecification::destroy($id);

        return redirect()->back()->with('success', 'Product updated successfully');
    }

    public function specificationEdit(Request $request,$id)
    {
          //dd($request->all());
 
         $request->validate([
             "name" => "required|string|max:255",
             "description" => "required",
            
         ]);
         $storeData= DistributorProductSpecification::findOrFail($id);
         $storeData->product_id=$request->product_id;
         $storeData->name=$request->name;
         $storeData->description=$request->description;
        
         $storeData->save();
         
         if ($storeData) {
            return redirect()->back()->with('success', 'Product updated successfully');
         } else {
             return redirect()->route('admin.distributor.product.create')->withInput($request->all());
         }
     }
}
