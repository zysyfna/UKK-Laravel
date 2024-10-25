<?php

namespace App\Http\Controllers;

use App\Models\UserModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register', 'getUser']]);
    }
    public function getUser()
    {
        $dt_user = UserModel::get();
        return response()->json($dt_user);
    }

    public function addUser(request $req)
    {
        
    {
        // Middleware configuration for other methods except 'getUser'
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }
        $validator = validator::make($req->all(), [
            'nama_user' => 'required',
            'role' => 'required',
            'username' => 'required',
            'password' => 'required'
            
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson());
        }
        $save = UserModel::create([
            'nama_user' => $req->get('nama_user'),
            'role'=> $req->get('role'),
            'username' => $req->get('username'),
            'password' => $req->get('password'),
           
            
        ]);
        if ($save) {
            return response()->json(['status' => true, 'message' => 'Berhasil menambah user']);
        } else {
            return response()->json(['status' => false, 'message' => 'Gagal menambah user']);
        }
    }

    public function updateUser(Request $req, $id)
    {
        {
            // Middleware configuration for other methods except 'getUser'
            $this->middleware('auth:api', ['except' => ['login', 'register']]);
        }
        $validator = Validator::make($req->all(), [
            'nama_user' => 'required',
            'role' => 'required',
            'username' => 'required',
            'password' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson());
        }
        $ubah = UserModel::where('id_user', $id)->update([
            'nama_user' => $req->input('nama_user'),
            'role'=> $req->input('role'),
            'username' => $req->input('username'),
            'password'=> $req->input('password'),
            
        ]);
        if ($ubah) {
            return response()->json(['status' => true, 'message' => 'Sukses mengedit user']);
        } else {
            return response()->json(['status' => false, 'message' => 'Gagal mengedit user']);
        }
    }

    public function getUserId($id)
    {
        $dt=UserModel::where('id_user',$id)->first();
        return response()->json($dt);
    }

    public function deleteUser($id)
    {
        {
            // Middleware configuration for other methods except 'getUser'
            $this->middleware('auth:api', ['except' => ['login', 'register']]);
        }
        $hapus=UserModel::where('id_user',$id)->delete();
        if($hapus){
            return response()->json(['status' => true, 'message' => 'Sukses Menghapus data user']);
        } else {
            return response()->json(['status' => false, 'message' => 'Gagal Menghapus data user']);
        }
    }
}