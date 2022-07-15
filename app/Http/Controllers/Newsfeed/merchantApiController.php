<?php

namespace App\Http\Controllers\Newsfeed;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Enum\BranchUserRole;
use App\Http\Controllers\JsonBranchUserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Tymon\JWTAuth\Facades\JWTAuth;

class merchantApiController extends Controller
{
    public function addPost(Request $request)
    {
        $title = $request->post('title');
        $caption = $request->post('caption');
        $image = $request->post('image');
        if (! $login = JWTAuth::toUser(JWTAuth::getToken())) {
            return response()->json(['user_not_found'], 404);
        } else {
            if ($login->role >= BranchUserRole::branchScanner) {
                return response()
                    ->json((new functionController())->addPost($login->branchScanner->branch_id, $title, $caption, $image), 200);
            } else {
                return response()->json(['error' => 'You do not have the access.'], 401);
            }
        }
    }

    public function editPost(Request $request)
    {
        $post_id = $request->post('post_id');
        $title = $request->post('title');
        $caption = $request->post('caption');
        $image = $request->post('image');
        if (! $login = JWTAuth::toUser(JWTAuth::getToken())) {
            return response()->json(['user_not_found'], 404);
        } else {
            if ($login->role >= BranchUserRole::branchScanner) {
                $result = (new functionController())->editPost($post_id, $title, $caption, $image);
                if ($result) {
                    return response()->json($result, 200);
                } else {
                    return response()->json('Post not found', 404);
                }
            } else {
                return response()->json(['error' => 'You do not have the access.'], 401);
            }
        }
    }

    public function deletePost(Request $request)
    {
        $post_id = $request->post('post_id');

        if (! $login = JWTAuth::toUser(JWTAuth::getToken())) {
            return response()->json(['user_not_found'], 404);
        } else {
            if ($login->role >= BranchUserRole::branchScanner) {
                return response()
                    ->json((new functionController())->deletePost($post_id), 200);
            } else {
                return response()->json(['error' => 'You do not have the access.'], 401);
            }
        }
    }

    public function getPost(Request $request)
    {
        $post_id = $request->post('post_id');

        if (! $login = JWTAuth::toUser(JWTAuth::getToken())) {
            return response()->json(['user_not_found'], 404);
        } else {
            if ($login->role >= BranchUserRole::branchScanner) {
                return response()
                    ->json((new JsonBranchUserController())
                        ->makePagination((new functionController())
                            ->getAllPosts($login->branchScanner->branch_id, $post_id), 'posts'), 200);
            } else {
                return response()->json(['error' => 'You do not have the access.'], 401);
            }
        }
    }

    public function getAllPosts()
    {
        if (! $login = JWTAuth::toUser(JWTAuth::getToken())) {
            return response()->json(['user_not_found'], 404);
        } else {
            if ($login->role >= BranchUserRole::branchScanner) {
                return response()
                    ->json((new JsonBranchUserController())
                        ->makePagination((new functionController())
                            ->getAllPosts($login->branchScanner->branch_id), 'posts'), 200);
            } else {
                return response()->json(['error' => 'You do not have the access.'], 401);
            }
        }
    }

    //function to register a new customer
    public function postImageUpload()
    {
        //check image url or to upload image to aws
        if (isset($_FILES['image']['name'])) {
            $response['file_name'] = basename($_FILES['image']['name']);
            Storage::disk('s3')->put('dynamic-images/admin_post_image'.$response['file_name'], file_get_contents($_FILES['image']['tmp_name']), 'public');
            $image_url = Storage::disk('s3')->url('dynamic-images/admin_post_image'.$response['file_name']);
            $response['url'] = $image_url;
            try {
                $response['message'] = 'File uploaded successfully!';
            } catch (\Exception $e) {
                // Exception occurred. Make error flag true
                $response['error'] = true;
                $response['message'] = $e->getMessage();
            }

            return response()->json(['result' => $image_url], 200);
        } else {
            return response()->json(['result' => 'No Image'], 200);
        }
    }
}
