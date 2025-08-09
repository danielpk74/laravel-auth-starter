<?php

namespace Danielpk74\LaravelAuthStarter\Http\Controllers;

use Illuminate\Http\Request;

class ApplicationController extends \Illuminate\Routing\Controller
{
    public function __invoke(Request $request)
    {
        $path = $request->path();
        
        // Check if the route is an authentication route
        if (in_array($path, ['login', 'register'])) {
            return view('auth.app');
        }
        
        // Default to admin layout
        return view('admin.layouts.app');
    }
}
