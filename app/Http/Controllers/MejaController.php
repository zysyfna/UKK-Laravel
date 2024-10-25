<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MejaModel;
use Illuminate\Support\Facades\Validator;

class MejaController extends Controller
{
    //Menambah meja
    public function addMeja(Request $req){
        $validator = Validator::make($req->all(),[
            'nomor_meja'=>'required',

        ]);
        if($validator->fails()){
            return response()->json($validator->errors()->toJson());
        }
        $save = MejaModel::create([
            'nomor_meja' => $req->get('nomor_meja'),
            
        ]);
        if($save){
            return response()->json(['status'=>true, 'message'=>'Sukses menambahkan meja']);
        }else{
            return response()->json(['status'=>false, 'message'=>'Gagal menambahkan meja']);
        }
    }
    //MEMANGGIL MEJA
    public function getMeja(){
        $datameja=MejaModel::get();
        return response()->json($datameja);
    }
    //MENGUBAH MEJA
    public function updateMeja(Request $req, $id_meja){
        $validator = Validator::make($req->all(),[
            'nomor_meja'=>'required',
           
        ]);
        if($validator->fails()){
            return response()->json($validator->errors()->toJson());
        }
        $update = MejaModel::where('id_meja',$id_meja)->update([
            'nomor_meja' => $req->get('nomor_meja'),
            
        ]);
        if($update){
            return response()->json(['status'=>true, 'message'=>'Sukses mengubah']);
        }else{
            return response()->json(['status'=>false, 'message'=>'Gagal mengubah']);
        }
    }
    //MENGHAPUS MEJA
    public function deleteMeja($req){
        $delete = MejaModel::where('id_meja',$req)->delete();
        if($delete){
            return response()->json(['status'=>true, 'message'=>'Sukses menghapus']);
        }else{
            return response()->json(['status'=>false, 'message'=>'Gagal menghapus']);
        }
    }
    public function __construct()
    {
        $this->middleware('auth:api');
    }
}
