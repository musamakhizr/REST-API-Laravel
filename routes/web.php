<?php

use App\Models\Dog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

//--Sending Response Header--//
Route::get('dogs-res', function (){
    return response(Dog::all())->header("X-Dog-Test", 1255);
});
//--Reading Request Header--//
Route::get('dogs-req', function (Request $request){
   var_dump($request->header('Accept'));
});
//--Pagination--//
Route::get('dogs-pagi', function (){
    return response(Dog::paginate(5));
});
//--Using The paginate() method on a query builder call--//
Route::get('dogs-pagi-qb', function (){
    return DB::table('dogs')->paginate(7);
});

//--Sorting and Filtering--//
    //--Sorting--//
    Route::get('dogs-sort-si', function (Request $request) {

        $sortColumn = $request->input('sort','name');
        return Dog::orderBy($sortColumn)->paginate(7);
    });
    //--With Directions Single Column--//
    Route::get('dogs-sort-si-dir', function (Request $request) {

        $sortColumn = $request->input('sort', 'name');

        $sortDirection = str_contains($sortColumn, '-') ? 'desc' : 'asc';
        $sortColumn = ltrim($sortColumn,'-');
        return Dog::orderBy($sortColumn,$sortDirection)->paginate(10);
    });
    //--With Directions Multiple Columns--//
    Route::get('dogs-sort-mul-dir', function (Request $request) {


        $sortColumns = $request->input('sort') ? explode(',' , $request->input('sort','')) : null;
        //$sortColumns = (count($sortColumns) == 0) ? "false":$sortColumns;

        //dd($sortColumns);
        $query= Dog::query();
        //use when helper function

        $query->when($sortColumns, function () use($sortColumns, $query){

            foreach ($sortColumns as $sortColumn) {
                $sortDirection = str_contains($sortColumn, '-') ? 'desc' : 'asc';
                $sortColumn = ltrim($sortColumn, '-');
                $query->orderBy($sortColumn, $sortDirection);
            }

        });
        //dd($query);
        return $query->paginate(20);

    });
    //--Filtering--//

