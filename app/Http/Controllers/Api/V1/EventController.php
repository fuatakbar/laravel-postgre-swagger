<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{ Event, EventImage };
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class EventController extends Controller
{
    public function __construct() {
        $this->middleware('auth:api', ['except' => ['publicUpcomings']]);
    }

    public function create(Request $req) {
        //
    }

    public function update(Request $req) {
        //
    }

    public function delete(Request $req) {
        //
    }

    public function storeImage(Request $req) {
        //
    }

    public function updateImage(Request $req) {
        //
    }

    public function showList() {
        //
    }

    public function showDetail() {
        //
    }

    // PUBLIC ROUTES
    public function publicUpcomings() {
        //
    }
}
