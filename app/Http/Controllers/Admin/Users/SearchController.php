<?php
namespace App\Http\Controllers\Admin\Users;

use App\Http\Controllers\Controller;
use App\Models\ImportedUser;
use App\Models\User;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $searchTerm = trim($request->get('term'));
        $users = User::searchByEmailOrName($searchTerm, 10)->get();

        if (count($users) > 5) {
            return response()->json($users);
        }

        $importedUsers = ImportedUser::searchByEmailOrName($searchTerm, 10)->get();

        return response()->json($users->merge($importedUsers));
    }
    
    public function search(Request $request)
    {
        $searchTerm = trim($request->get('term'));
        $users = User::searchByEmailOrName($searchTerm, -1)->orderBy('id', 'DESC')->pluck('id');
        return redirect()->back()->with('users', $users);
    }
}
