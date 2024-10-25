<?php

namespace App\Http\Controllers;

use App\Models\MenuModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class MenuController extends Controller
{
    public function __construct()
    {
        // $this->middleware('auth:api', ['except' => ['login', 'register', 'getMenu']]);
    }
    public function getMenu()
    {
        $user = Auth::guard('api')->user();
        if (!$user === "admin") {
            return response()->json('Unauthorized', 400);
        }
        $dt_menu = MenuModel::get();
        return response()->json($dt_menu);
    }

    public function addMenu(Request $req)
{
    // Middleware configuration for other methods except 'getMenu'
    // $this->middleware('auth:api', ['except' => ['login', 'register']]);

    $user = Auth::guard('api')->user();

    if($user->role === 'admin'){
        $validator = Validator::make($req->all(), [
            'nama_menu' => 'required',
            'jenis' => 'required',
            'deskripsi' => 'required',
            'gambar' => 'required|image', // Ensure it's an image
            'harga' => 'required|numeric' // Ensure it's a number
        ]);
        
        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson());
        }
    
        // Handle image upload
        if ($req->hasFile('gambar')) {
            $image = $req->file('gambar');
            $imageName = time() . '_' . $image->getClientOriginalName();
            
            // Use the correct directory path
            $imagePath = $image->storeAs('menu', $imageName); // Save in 'menu' directory
    
            // Update image path in the database
            $data['gambar'] = $imagePath;
        }
    
        $save = MenuModel::create([
            'nama_menu' => $req->get('nama_menu'),
            'jenis' => $req->get('jenis'),
            'deskripsi' => $req->get('deskripsi'),
            'gambar' => isset($data['gambar']) ? $data['gambar'] : null, // Use the stored image path
            'harga' => $req->get('harga'),
        ]);
    
        if ($save) {
            return response()->json(['status' => true, 'message' => 'Berhasil menambah menu']);
        } else {
            return response()->json(['status' => false, 'message' => 'Gagal menambah menu']);
        }
    } else {
        return response()->json(['status' => false, 'message' => 'user bukan admin']);
    }
    
   
}

public function updateMenu(Request $req, $id)
{
    // Middleware configuration for other methods except 'getMenu'
    $this->middleware('auth:api', ['except' => ['login', 'register']]);
    
    // Validate incoming request
    $validator = Validator::make($req->all(), [
        'nama_menu' => 'required',
        'jenis' => 'required',
        'deskripsi' => 'required',
        'gambar' => 'required|image', // Keep gambar as required
        'harga' => 'required|numeric'
    ]);
    
    if ($validator->fails()) {
        return response()->json($validator->errors()->toJson());
    }

    // Handle image upload
    $image = $req->file('gambar');
    $imageName = time() . '_' . $image->getClientOriginalName();
    $imagePath = $image->storeAs('menu', $imageName); // Save in 'menu' directory

    // Update the menu entry
    $ubah = MenuModel::where('id_menu', $id)->update([
        'nama_menu' => $req->input('nama_menu'),
        'jenis' => $req->input('jenis'),
        'deskripsi' => $req->input('deskripsi'),
        'gambar' => $imagePath, // Use the new image path
        'harga' => $req->input('harga'),
    ]);

    if ($ubah) {
        return response()->json(['status' => true, 'message' => 'Sukses mengedit menu']);
    } else {
        return response()->json(['status' => false, 'message' => 'Gagal mengedit menu']);
    }
}


    public function getMenuId($id)
    {
        $dt=MenuModel::where('id_menu',$id)->first();
        return response()->json($dt);
    }

    public function deleteMenu($id)
    {
        {
            // Middleware configuration for other methods except 'getMenu'
            $this->middleware('auth:api', ['except' => ['login', 'register']]);
        }
        $hapus=MenuModel::where('id_menu',$id)->delete();
        if($hapus){
            return response()->json(['status' => true, 'message' => 'Sukses Menghapus data menu']);
        } else {
            return response()->json(['status' => false, 'message' => 'Gagal Menghapus data menu']);
        }
    }
}