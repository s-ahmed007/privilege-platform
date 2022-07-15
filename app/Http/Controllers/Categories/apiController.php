<?php

namespace App\Http\Controllers\Categories;

use App\Http\Controllers\Controller;
use App\Http\Controllers\JsonBranchUserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class apiController extends Controller
{
    public function categories()
    {
        return Response::json((new functionController())->getCategories());
    }

    public function subCategories(Request $request)
    {
        $cat_id = $request->post('id');

        return Response::json((new functionController())->getSubCats($cat_id));
    }

    public function secondSubCategories(Request $request)
    {
        $main_cat_id = $request->post('main_id');
        $cat_id = $request->post('id');

        return Response::json((new functionController())->getSecondSubCats($main_cat_id, $cat_id));
    }

    public function main_cat_partners(Request $request)
    {
        $cat_id = $request->post('id');

        return Response::json(
            (new JsonBranchUserController())->makePagination(
                (new functionController())->getAllMainCatPartners($cat_id), 'partners'));
    }

    public function sub_cat_partners(Request $request)
    {
        $cat_id = $request->post('id');
        $main_cat_id = $request->post('main_id');

        return Response::json(
            (new JsonBranchUserController())->makePagination(
                (new functionController())->getAllSubCatPartners($main_cat_id, $cat_id), 'partners'));
    }

    public function second_sub_cat_partners(Request $request)
    {
        $main_cat_id = $request->post('main_id');
        $sub_cat_id = $request->post('sub_id');
        $id = $request->post('id');

        return Response::json(
            (new JsonBranchUserController())->makePagination(
                (new functionController())->getAllSecondSubCatPartners($main_cat_id, $sub_cat_id, $id), 'partners'));
    }
}
