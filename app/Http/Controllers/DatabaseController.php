<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Artisan;

class DatabaseController extends Controller
{
    /**
     * Run artisan migrate
     *
     * @param  Request $request
     * @return Response
     */
    public function migrate(Request $request) {
        // TODO: remove this dummy return and check if it works
        return response()->json([
            'success' => true,
            'dummy' => true
        ]);

        $key = $request->input('key');

        if (strcmp($key, env('MIGRATION_KEY')) === 0) {
            Artisan::call('migrate', [
                '--path' => 'database/migrations',
                '--force' => true
            ]);

            return response()->json([
                'success' => true
            ]);
        }

        return response()->json([
            'success' => false,
            'error' => 'Unauthorized'
        ], 401);
    }
}
