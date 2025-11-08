<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (!empty($request->term)) 
        {
            $data=Category::where('name',$request->term)->orderBy('position')->paginate(30);
        }else{
            $data=Category::orderBy('position')->paginate(30);
        }
        return view('admin.category.index', compact('data','request'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        return view('admin.category.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
       
        $request->validate([
            "name" => "required|string|max:255"
           
        ]);
        $collection = $request->except('_token');
        $upload_path = "uploads/category/";
        $data = new Category;
        $data->name = $collection['name'];
        $data->description = $collection['description'] ?? '';
        $data->slug = slugGenerate($collection['name'],'categories');
        //$catData = Category::select('position')->latest('id')->first();
       // if (empty($catData)) {
            // if (!empty($data->position)) {
            //     $new_position = (int) $data->position + 1;
            // } else {
            //     $new_position = 1;
            // }
            // $data->position = $new_position;
            // icon image
            if(isset($collection['icon_path'])){
                $image = $collection['icon_path'];
                $imageName = time().".".mt_rand().".".$image->getClientOriginalName();
                $image->move($upload_path, $imageName);
                $uploadedImage = $imageName;
                $data->icon_path = $upload_path.$uploadedImage;
            }
            // thumb image
            if(isset($collection['image_path'])){
                $image = $collection['image_path'];
                $imageName = time().".".mt_rand().".".$image->getClientOriginalName();
                $image->move($upload_path, $imageName);
                $uploadedImage = $imageName;
                $data->image_path = $upload_path.$uploadedImage;
            }
            // banner image
            if(isset($collection['banner_image'])){
                $image = $collection['banner_image'];
                $imageName = time().".".mt_rand().".".$image->getClientOriginalName();
                $image->move($upload_path, $imageName);
                $uploadedImage = $imageName;
                $data->banner_image = $upload_path.$uploadedImage;
            }
            // sketch icon
            if(isset($collection['sketch_icon'])){
                $image = $collection['sketch_icon'];
                $imageName = time().".".mt_rand().".".$image->getClientOriginalName();
                $image->move($upload_path, $imageName);
                $uploadedImage = $imageName;
                $data->sketch_icon = $upload_path.$uploadedImage;
            }
            $data->save();
        //}
        if ($data) {
            return redirect()->route('admin.categories.index');
        } else {
            return redirect()->route('admin.categories.create')->withInput($request->all());
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data=Category::where('id',$id)->first();
        return view('admin.category.details',compact('data'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data=Category::findOrfail($id);
        return view('admin.category.edit',compact('data'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            "name" => "required|string|max:255",
            "description" => "nullable|string",
            "icon_path" => "nullable|mimes:jpg,jpeg,png,svg,gif|max:10000000"
        ]);
        $collection = $request->except('_token');
        $upload_path = "uploads/category/";
        $data = Category::findOrfail($id);
        $data->name = $collection['name'] ?? '';
        $data->description = $collection['description']?? '';
        if($data->name != $collection['name']){
            $data->slug = slugGenerate($collection['name'],'categories');
        }
        // icon image
        if(isset($collection['icon_path'])){
            $image = $collection['icon_path'];
            $imageName = time().".".mt_rand().".".$image->getClientOriginalName();
            $image->move($upload_path, $imageName);
            $uploadedImage = $imageName;
            $data->icon_path = $upload_path.$uploadedImage;
        }
        // thumb image
        if(isset($collection['image_path'])){
            $image = $collection['image_path'];
            $imageName = time().".".mt_rand().".".$image->getClientOriginalName();
            $image->move($upload_path, $imageName);
            $uploadedImage = $imageName;
            $data->image_path = $upload_path.$uploadedImage;
        }
        // banner image
        if(isset($collection['banner_image'])){
            $image = $collection['banner_image'];
            $imageName = time().".".mt_rand().".".$image->getClientOriginalName();
            $image->move($upload_path, $imageName);
            $uploadedImage = $imageName;
            $data->banner_image = $upload_path.$uploadedImage;
        }
        // sketch icon
        if(isset($collection['sketch_icon'])){
            $image = $collection['sketch_icon'];
            $imageName = time().".".mt_rand().".".$image->getClientOriginalName();
            $image->move($upload_path, $imageName);
            $uploadedImage = $imageName;
            $data->sketch_icon = $upload_path.$uploadedImage;
        }
        $data->save();
        
        if ($data) {
            return redirect()->route('admin.categories.index');
        } else {
            return redirect()->route('admin.categories.create')->withInput($request->all());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $isReferenced = DB::table('products')->where('cat_id', $id)->exists();
    
        if ($isReferenced) {
            return redirect()->route('admin.categories.index')->with('error', 'Category cannot be deleted because it is referenced in another table.');
        }
        $data=Category::destroy($id);
        if ($data) {
            return redirect()->route('admin.categories.index')->with('success', 'Category deleted successfully.');
        } else {
            return redirect()->route('admin.categories.index')->with('error', 'Failed to delete category.');
        }
    }
    /**
     * status change the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function status(Request $request, $id)
    {
        $category = Category::findOrFail($id);
        $status = ( $category->status == 1 ) ? 0 : 1;
        $category->status = $status;
        $category->save();
        if ($category) {
            return redirect()->route('admin.categories.index');
        } else {
            return redirect()->route('admin.categories.create')->withInput($request->all());
        }
    }
    /**
     * csv export the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function csvExport(Request $request)
    {
        if (!empty($request->term)) 
        {
            $data=Category::where('name',$request->term)->orderBy('position')->paginate(30);
        }else{
            $data=Category::orderBy('position')->paginate(30);
        }
        if (count($data) > 0) {
            $delimiter = ",";
            $filename = "Lux-Product-Category-".date('Y-m-d').".csv";

            // Create a file pointer
            $f = fopen('php://memory', 'w');

            // Set column headers
            // $fields = array('SR', 'STORE', 'FIRM', 'MOBILE', 'EMAIL', 'WHATSAPP', 'DISTRIBUTOR', 'ASE', 'ASM', 'RSM', 'VP', 'ADDRESS', 'AREA', 'STATE', 'CITY', 'PINCODE', 'OWNER', 'OWNER DATE OF BIRTH', 'OWNER DATE OF ANNIVERSARY', 'CONTACT PERSON', 'CONTACT PERSON PHONE', 'CONTACT PERSON WHATSAPP', 'CONTACT PERSON DATE OF BIRTH', 'CONTACT PERSON DATE OF ANNIVERSARY', 'GST NUMBER', 'STATUS', 'DATETIME');
            $fields = array('SR', 'Name', 'Description',  'STATUS', 'DATETIME');
            fputcsv($f, $fields, $delimiter);

            $count = 1;

            foreach($data as $row) {
                $datetime = date('j F, Y', strtotime($row['created_at']));
                $lineData = array(
                    $count,
                    ucwords($row->name),
                    $row->description,
                    ($row->status == 1) ? 'Active' : 'Inactive',
                    $datetime
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


}
