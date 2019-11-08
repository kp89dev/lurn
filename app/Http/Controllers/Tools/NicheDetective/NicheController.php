<?php
namespace App\Http\Controllers\Tools\NicheDetective;

use App\Http\Controllers\Controller;
use App\Models\NicheDetective\Category;
use App\Models\NicheDetective\Niche;
use Illuminate\Http\Request;

class NicheController extends Controller
{
    public function index()
    {
        $categories = Category::all();

        return view('niche-detective.index', compact('categories'));
    }

    public function getNicheCategories(Request $request)
    {
        $niches = Niche::whereCategoryId($request->input('id'))->get();

        if(count($niches)>0) {
            $response = [
                'status' => '0', // success
                'niches' => $niches,
                'msg' => 'success'
            ];
        } else {
            $response = [
                'status' => '1', // error
                'niches' => "",
                'msg' => 'error'
            ];
        }

        return response()->json($response);
    }

    public function nicheReport($id)
    {
        $modelNiche = Niche::find($id);
        $category = Category::find($modelNiche->category_id);

        return view('niche-detective.report', compact('modelNiche', 'category'));
    }
}
