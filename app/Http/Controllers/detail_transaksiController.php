<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\detail_transaksiModel;
use App\Models\MenuModel;
use App\Models\TransaksiModel;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class detail_transaksiController extends Controller
{

    //Function used to get all data from table
    public function getAll()
    {

        $data = detail_transaksiModel::all();
        return response()->json($data);

    }

    //Function used to get data based on DETAIL primary key
    public function getDetailId($id)
    {

        $data = detail_transaksiModel::with('detailMenu')->find($id);
        return response()->json($data);

    }
    //Function used to get data based on TRANSAKSI primary key
    public function getDetailTransaksiId($id)
    {

        // Gets current user
        $Auth = Auth::user();

        // Fetches all transaction details with related data
        $data = detail_transaksiModel::with(['detailTransaksi.detailPegawai', 'detailMenu'])
            ->where('id_transaksi', $id)
            ->get();

        // Check if any of the transaction details belong to the authenticated user
        $isAuthorized = $data->contains(function ($item) use ($Auth) {
            return $item->detailTransaksi->id_user == $Auth->id_user;
        });

        // Check if the current user is a manager
        if ($Auth->role == 'admin' || $Auth->role == 'MANAJER') {

            // Checks if the user is included in the transaction details
            // or the current user is a manager
            if ($isAuthorized || $Auth->role == 'MANAJER') {

                return response()->json($data);

            } else {

                return response()->json(['status' => false, 'message' => 'Unauthorized'], 401);

            }

        } else {

            return response()->json(['status' => false, 'message' => 'Unauthorized'], 401);

        }

    }

    // Function used to create transaction detail
    public function addDetailTransaksi(Request $request, $id) // INPUT id_transaksi FOR $id
    {
        // Gets current user
        $Auth = Auth::user();

        // Gets transaction data based on primary key from $request
        $CheckTransaction = transaksiModel::find($id);

        // Checks if user is "admin"
        if ($Auth->role == "admin") {

            // Checks if status is set to "BELUM_BAYAR"
            if ($CheckTransaction->status == "belum_bayar") {

                if ($Auth->id_user == $CheckTransaction->id_user) {

                    // Creates a validator to validate the array of menu inputs
                    $validator = Validator::make($request->all(), [
                        'menu_items' => 'required|array',
                        'menu_items.*.id_menu' => 'required|integer',
                        'menu_items.*.jumlah' => 'required|integer',
                    ]);

                    // Checks if validator occurs an error or not
                    if ($validator->fails()) {
                        return response()->json($validator->errors()->toJson());
                    }

                    // Initialize an array to store results
                    $results = [];

                    // Loop through each menu item in the request
                    foreach ($request->menu_items as $menuItem) {
                        // Gets food data based on primary key from each menu item
                        $CheckFood = MenuModel::find($menuItem['id_menu']);

                        if ($CheckFood) {
                            // Creates a variable to save inputted data for each menu item
                            $save = detail_transaksiModel::create([
                                'id_transaksi' => $id, // id_transaksi is taken from $id in parameter
                                'id_menu' => $menuItem['id_menu'],
                                'harga' => $CheckFood->harga,
                                'jumlah_produk' => $menuItem['jumlah'],
                            ]);

                            // Add the result of each save operation to the results array
                            $results[] = $save ? ['status' => true, 'id_menu' => $menuItem['id_menu'], 'message' => 'Berhasil Menambah']
                                : ['status' => false, 'id_menu' => $menuItem['id_menu'], 'message' => 'Gagal Menambah'];
                        } else {
                            // If the menu item is not found, add an error to the results
                            $results[] = ['status' => false, 'id_menu' => $menuItem['id_menu'], 'message' => 'Menu not found'];
                        }
                    }

                    // Return all the results after processing the array
                    return response()->json($results, status: 200);

                } else {
                    return response()->json(['status' => false, 'message' => 'Unauthorized'], status: 401);
                }

            } else {
                return response()->json(['status' => false, 'message' => 'Gagal, transaksi sudah lunas'], status: 500);
            }

        } else {
            return response()->json(['status' => false, 'message' => 'Hanya Admin yang bisa menambah'], status: 500);
        }
    }

    // public function addDetailTransaksi(Request $request, $id) // INPUT id_transaksi FOR $id
    // {

    //     //Gets current user
    //     $Auth = Auth::user();

    //     //Gets food data based on primary key from $request
    //     $CheckFood = MenuModel::find($request->id_menu);

    //     //Gets transaction data based on primary key from $request
    //     $CheckTransaction = transaksiModel::find($id);

    //     //Checks if user is "admin"
    //     if ($Auth->role == "admin") {

    //         //Checks if status is set to "BELUM_BAYAR"
    //         if ($CheckTransaction->status == "BELUM_BAYAR") {

    //             //Creates a validator to validate inputs
    //             $validator = Validator::make($request->all(), [
    //                 'id_menu' => 'required|Integer',
    //             ]);

    //             //Checks if validator occurs an error or not
    //             if ($validator->fails()) {

    //                 //Returns an error if so
    //                 return response()->json($validator->errors()->toJson());

    //             }

    //             //Creates a variable to save inputted data
    //             $save = detail_transaksiModel::create([
    //                 'id_transaksi' => $id, //id_transaksi is taken from $id in parameter
    //                 'id_menu' => $request->id_menu,
    //                 'harga' => $CheckFood->harga,
    //             ]);

    //             //Checks if save is successful
    //             if ($save) {

    //                 //If the $save is successful, return a 200 response
    //                 // with a success message
    //                 return response()->json(['status' => true, 'message' => 'Berhasil Menambah'], status: 200);

    //             } else {

    //                 //else returns an error
    //                 return response()->json(['status' => false, 'message' => 'Gagal menambah'], status: 500);

    //             }

    //         } else {

    //             //else returns an error
    //             return response()->json(['status' => false, 'message' => 'Gagal, transaksi sudah lunas'], status: 500);

    //         }

    //     } else {

    //         //else returns an error
    //         return response()->json(['status' => false, 'message' => 'Hanya admin yang bisa menambah'], status: 500);

    //     }

    // }

    //Function used to update transaction detail
    public function updateDetailTransaksi(Request $request, $id)// INPUT id_detail_transaksi FOR $id
    {

        //Gets current user
        $Auth = Auth::user();

        //Gets food data based on primary key from $request
        $Check = MenuModel::find($request->get('id_menu'));

        //Gets transaction detail data based on primary key 
        $CheckTransactionDetail = detail_transaksiModel::find($id);

        //Gets transaction data based on primary key
        //provided by $CheckTransactionDetail
        $CheckTransaction = transaksiModel::find($CheckTransactionDetail->id_transaksi);

        //Checks if user's role is "admin"
        if ($Auth->role == "admin") {

            //Checks if status is set to "BELUM_BAYAR"
            if ($CheckTransaction->status == "belum_bayar") {

                //Creates a validator to validate inputs
                $validator = Validator::make($request->all(), [
                    'id_menu' => 'required|Integer',
                ]);

                //Checks if validator occurs an error or not
                if ($validator->fails()) {

                    //Returns an error if so
                    return response()->json($validator->errors()->toJson());

                }

                //Creates a variable to save inputted data
                $save = detail_transaksiModel::find($id)->update([
                    'id_menu' => $request->id_menu,
                    'harga' => $Check->harga,
                ]);

                //Checks if save is successful
                if ($save) {

                    //If the $save is successful, return a 200 response
                    // with a success message
                    return response()->json(['status' => true, 'message' => 'Berhasil Mengubah'], status: 200);

                } else {

                    //else returns an error
                    return response()->json(['status' => false, 'message' => 'Gagal mengubah'], status: 500);

                }

            } else {

                //else returns an error
                return response()->json(['status' => false, 'message' => 'Gagal, karena transaksi sudah lunas'], status: 500);

            }

        } else {

            //else returns an error
            return response()->json(['status' => false, 'message' => 'Hanya admin yang bisa mengubah'], status: 500);

        }

    }

    //Function used to delete transaction detail
    public function deleteDetailTransaksi($id)
    {
        //Checks current user
        $Auth = Auth::user();

        //Gets transaction detail data based on primary key 
        $CheckTransactionDetail = detail_transaksiModel::find($id);

        //Gets transaction data based on primary key
        //provided by $CheckTransactionDetail
        $CheckTransaction = transaksiModel::find($CheckTransactionDetail->id_transaksi);

        //Checks if user's role is "admin"
        if ($Auth->role == "admin") {

            //Checks if status transaksi is "BELUM_BAYAR"
            if ($CheckTransaction->status == "belum_bayar") {

                //Checks if the transaction detail belongs to the authenticated user
                if ($CheckTransaction->id_user == $Auth->id_user) {

                    $delete = detail_transaksiModel::find($id)->delete();
                    response()->json([
                        'success' => $delete
                    ]);

                } else {

                    //else returns an error
                    return response()->json(['status' => false, 'message' => 'Unauthorized'], status: 500);

                }

            } else {

                //else returns an error
                return response()->json(['status' => false, 'message' => 'Gagal, karena transaksi sudah lunas'], status: 500);

            }

        } else {

            //else returns an error
            return response()->json(['status' => false, 'message' => 'Hanya admin yang bisa menghapus'], status: 500);

        }

    }

}