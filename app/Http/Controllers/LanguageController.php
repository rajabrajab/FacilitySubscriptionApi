<?php

namespace App\Http\Controllers\Api;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;

class LanguageController extends Controller
{

    public function __construct()
    {
        $this->middleware(\App\Http\Middleware\SetLocale::class);
    }

    public function update(Request $request)
    {
            $request->validate([
                'language' => 'required|in:en,ar'
            ]);

            auth()->user()->update([
                'language' => $request->language
            ]);

        return response()->sendResponse([],__('general.general_success'));
    }
}
