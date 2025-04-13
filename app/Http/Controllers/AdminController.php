<?php
namespace App\Http\Controllers;

use App\Models\Articulo;

use Illuminate\Http\Request;

class AdminController extends Controller
{
   
    public function users()
    {
        return view('admin.adminUser'); 
    }

   
    public function items()
    {
        return view('admin.adminArticles'); 
    }
}
