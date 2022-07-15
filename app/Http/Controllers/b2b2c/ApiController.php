<?php

namespace App\Http\Controllers\b2b2c;

use App\Admin;
use App\B2b2cInfo;
use App\B2b2cUser;
use App\CardDelivery;
use App\CustomerInfo;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Enum\DeliveryType;
use App\Http\Controllers\Enum\PostType;
use App\Http\Controllers\functionController;
use App\Http\Controllers\jsonController;
use App\Http\Controllers\JsonControllerV2;
use App\LikePost;
use App\Post;
use App\SharePost;
use App\Subscribers;
use App\TransactionTable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;

class ApiController extends Controller
{
    //function for login of admin
    public function authenticate(Request $request)
    {
        $username = $request->get('username');
        $password = $request->get('password');
        $password = (new functionController)->encrypt_decrypt('decrypt', $password);

        $user = Admin::where([['username', $username], ['password', $password], ['type', 'b2b2c']])->first();

        if (! $user) {
            return response()->json(['error' => 'There is no account with this username.'], 201);
        } else {
            try {
                if (! $token = JWTAuth::fromUser($user)) {
                    return response()->json(['error' => 'invalid_credentials'], 400);
                }
            } catch (JWTException $e) {
                return response()->json(['error' => 'could_not_create_token'], 500);
            }
        }

        return response()->json(compact('token'));
    }

    public function getAuthenticatedUser()
    {
        try {
            if (! $login = JWTAuth::toUser(JWTAuth::getToken())) {
                return response()->json(['user_not_found'], 404);
            } else {
                $user = B2b2cInfo::where('id', $login->b2b2c_id)->with('users')->get();
            }
        } catch (TokenExpiredException $e) {
            return response()->json(['token_expired'], $e->getStatusCode());
        } catch (TokenInvalidException $e) {
            return response()->json(['token_invalid'], $e->getStatusCode());
        } catch (JWTException $e) {
            return response()->json(['token_absent'], $e->getStatusCode());
        }

        return response()->json(compact('user'));
    }

    //function get customer list
    public function allCustomers()
    {
        if (! $login = JWTAuth::toUser(JWTAuth::getToken())) {
            return response()->json(['user_not_found'], 404);
        } else {
            $customers = B2b2cUser::with('customerInfo')->orderBy('id', 'DESC')->paginate(20);

            return Response::json(['results' => $customers], 200);
        }
    }

    //function to store customer info
    public function createCustomer()
    {
        if (! $login = JWTAuth::toUser(JWTAuth::getToken())) {
            return response()->json(['user_not_found'], 404);
        } else {
            if ((new jsonController)->emailExist($_POST['email']) || (new jsonController)->partnerEmailExist($_POST['email'])) {
                return Response::json(['result' => 'Email already exists'], 201);
            } elseif ((new jsonController)->phoneNumberExist($_POST['phone']) || (new jsonController)->partnerPhoneNumberExist($_POST['phone'])) {
                return Response::json(['result' => 'Phone number already exists'], 201);
            } else {
                $first_name = $_POST['first_name'];
                $last_name = $_POST['last_name'];
                $email = $_POST['email'];
                $phone = $_POST['phone'];
                $password = $_POST['password'];
                $customer_id = $_POST['customer_id'];
                $customer_id_6 = substr($customer_id, -6);
                $shipping_address = $_POST['shipping_address'];
                // make password encrypted
                $password = preg_replace('/\s+/', '', $password);
                $encrypted_password = (new functionController)->encrypt_decrypt('encrypt', $password);
                $image_url = 'https://s3-ap-southeast-1.amazonaws.com/royalty-bd/static-images/registration/user.png';

                //generate referral number
                A://come back to regenerate refer code again if exists
                $token = '';
                $codeAlphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
                $codeAlphabet .= 'abcdefghijklmnopqrstuvwxyz';
                $codeAlphabet .= '0123456789';
                $max = strlen($codeAlphabet); // edited
                for ($i = 0; $i < 5; $i++) {
                    $token .= $codeAlphabet[random_int(0, $max - 1)];
                }
                $refer_exists = DB::table('customer_info')->where('referral_number', $token)->count();
                //regenerate refer number if already exists
                if ($refer_exists > 0) {
                    goto A;
                }
                try {
                    DB::beginTransaction(); //to do query rollback

                    if ((new jsonController)->usernameExist((new JsonControllerV2)->getUsernameFromEmail($email))) {
                        B:
                        $username = (new JsonControllerV2)->randomUsername($first_name);
                        if ((new jsonController)->usernameExist($username)) {
                            goto B;
                        }
                    } else {
                        $username = (new JsonControllerV2)->getUsernameFromEmail($email);
                    }

                    //save data in customer_account table
                    DB::table('customer_account')->insert([
                        'customer_id' => $customer_id,
                        'customer_serial_id' => $customer_id_6,
                        'customer_username' => $username,
                        'password' => $encrypted_password,
                        'moderator_status' => 2,
                    ]);
                    //save data in customer_info table
                    DB::table('customer_info')->insert([
                        'customer_id' => $customer_id,
                        'customer_first_name' => $first_name,
                        'customer_last_name' => $last_name,
                        'customer_full_name' => $first_name.' '.$last_name,
                        'customer_email' => $email,
                        'customer_contact_number' => $phone,
                        'customer_profile_image' => $image_url,
                        'customer_type' => 2,
                        'month' => 0,
                        'expiry_date' => '1971-03-26',
                        'member_since' => date('Y-m-d'),
                        'referral_number' => $token,
                        'firebase_token' => 0,
                        'card_active' => 1,
                        'card_activation_code' => 0,
                        'delivery_status' => 1,
                    ]);
                    //save email in subscribers table
                    $subscribers = Subscribers::firstOrNew(['email' => $email]);
                    $subscribers->save();

                    //insert info into card delivery table
                    $card_delivery = new CardDelivery();
                    $card_delivery->customer_id = $customer_id;
                    $card_delivery->delivery_type = DeliveryType::b2b2c_user;
                    $card_delivery->shipping_address = $shipping_address;
                    $card_delivery->order_date = date('Y-m-d');
                    $card_delivery->save();

                    //enlist this customer to b2b2c user list
                    $b2b2c_user = new B2b2cUser();
                    $b2b2c_user->b2b2c_id = $login->b2b2c_id;
                    $b2b2c_user->customer_id = $customer_id;
                    $b2b2c_user->save();

                    DB::commit(); //to do query rollback
                } catch (\Exception $e) {
                    DB::rollBack();

                    return Response::json(['results' => 'Something went wrong'], 500);
                }
            }

            return Response::json(['results' => 'Customer added successfully'], 200);
        }
    }

    //function to update customer info
    public function updateCustomer()
    {
        if (! $login = JWTAuth::toUser(JWTAuth::getToken())) {
            return response()->json(['user_not_found'], 404);
        } else {
            $first_name = $_POST['first_name'];
            $last_name = $_POST['last_name'];
            $email = $_POST['email'];
            $phone = $_POST['phone'];
            $prev_customer_id = $_POST['prev_customer_id'];
            $customer_id_6 = substr($prev_customer_id, -6);
            $new_customer_id = $_POST['new_customer_id'];
            $shipping_address = $_POST['shipping_address'];

            if ($_POST['first_name'] == '') {
                return Response::json(['results' => 'First name required'], 449);
            }
            if ($_POST['last_name'] == '') {
                return Response::json(['results' => 'Last name required'], 449);
            }
            $prev_email = CustomerInfo::where('customer_id', $prev_customer_id)->select('customer_email')->first();

            if ($_POST['email'] != '') {
                if ($prev_email->customer_email != $email) {
                    if ((new jsonController)->emailExist($_POST['email'])) {
                        return Response::json(['results' => 'Email already exists'], 201);
                    }
                }
            } else {
                return Response::json(['results' => 'Email required'], 449);
            }

            if ($_POST['phone'] != '') {
                $prev_phone = CustomerInfo::where('customer_id', $prev_customer_id)->select('customer_contact_number')->first();
                if ($prev_phone->customer_contact_number != $phone) {
                    if ((new jsonController)->phoneNumberExist($_POST['phone']) || (new jsonController)->partnerPhoneNumberExist($_POST['phone'])) {
                        return Response::json(['results' => 'Phone number already exists'], 201);
                    }
                }
            } else {
                return Response::json(['results' => 'Phone number required'], 449);
            }

            if ($_POST['new_customer_id'] != '') {
                if ($prev_customer_id != $new_customer_id) {
                    $count = CustomerInfo::where('customer_id', $new_customer_id)->count();
                    if ($count > 0) {
                        return Response::json(['results' => 'This card has been assigned already'], 201);
                    }
                }
            } else {
                return Response::json(['results' => 'Customer id required'], 449);
            }

            try {
                DB::beginTransaction(); //to do query rollback

                //save data in customer_info table
                DB::table('customer_info')->where('customer_id', $prev_customer_id)->update([
                    'customer_id' => $new_customer_id,
                    'customer_first_name' => $first_name,
                    'customer_last_name' => $last_name,
                    'customer_full_name' => $first_name.' '.$last_name,
                    'customer_email' => $email,
                    'customer_contact_number' => $phone,
                ]);
                if ($prev_email->customer_email != $email) {
                    Subscribers::where('email', $prev_email->customer_email)->delete();
                    //update email_verified status of customer
                    DB::table('customer_info')->where('customer_id', $new_customer_id)
                        ->update(['email_verified' => 0]);
                }

                //TO Update all other customer tables
                (new functionController)->updateCustomerId($prev_customer_id, $new_customer_id, 1);

                DB::table('customer_account')->where('customer_id', $new_customer_id)
                    ->update(['customer_serial_id' => $customer_id_6]);
                DB::table('card_delivery')->where('customer_id', $new_customer_id)
                    ->update(['shipping_address' => $shipping_address]);

                DB::commit(); //to do query rollback
            } catch (\Exception $e) {
                DB::rollBack();

                return Response::json(['results' => 'Something went wrong'], 200);
            }

            return Response::json(['results' => 'Information updated successfully'], 200);
        }
    }

    //function to card delivery customer list
    public function cardDeliveryList()
    {
        if (! $login = JWTAuth::toUser(JWTAuth::getToken())) {
            return response()->json(['user_not_found'], 404);
        } else {
            $card_delivery_list = DB::table('customer_info as ci')
                ->join('b2b2c_user as bu', 'bu.customer_id', '=', 'ci.customer_id')
                ->leftjoin('card_delivery as cd', 'cd.customer_id', '=', 'ci.customer_id')
                ->select('ci.customer_full_name', 'ci.customer_id', 'ci.delivery_status',
                    'ci.customer_contact_number', 'ci.customer_email', 'ci.customer_type', 'ci.member_since', 'cd.delivery_type',
                    'cd.shipping_address', 'cd.order_date', 'paid_amount')
                ->orderBy('cd.id', 'DESC')
                ->where('bu.b2b2c_id', $login->b2b2c_id)
                ->where('cd.delivery_type', DeliveryType::b2b2c_user)
                ->get();

            return Response::json(['results' => $card_delivery_list], 200);
        }
    }

    //function to customer's transactions
    public function transactions()
    {
        if (! $login = JWTAuth::toUser(JWTAuth::getToken())) {
            return response()->json(['user_not_found'], 404);
        } else {
            $customer_id = $_POST['customer_id'];
            $transactions = TransactionTable::where('customer_id', $customer_id)->paginate(20);

            return Response::json(['results' => $transactions], 200);
        }
    }

    //function to create post
    public function createPost()
    {
        if (! $login = JWTAuth::toUser(JWTAuth::getToken())) {
            return response()->json(['user_not_found'], 404);
        } else {
            if ($_POST['caption'] == null) {
                return Response::json(['results' => 'Caption required'], 449);
            } elseif (empty($_FILES['image'])) {
                return Response::json(['results' => 'Image required'], 449);
            }
            $header = $_POST['header'] != null ? $_POST['header'] : null;
            $caption = $_POST['caption'];
            $link = $_POST['link'] != null ? $_POST['link'] : null;

            Storage::disk('s3')->put('dynamic-images/client_post_image'.$_FILES['image']['name'], file_get_contents($_FILES['image']['tmp_name']), 'public');
            $image_url = Storage::disk('s3')->url('dynamic-images/client_post_image'.$_FILES['image']['name']);

            $post = new Post;
            $post->poster_id = $login->b2b2c_id;
            $post->poster_type = PostType::b2b2c;
            $post->header = $header;
            $post->caption = $caption;
            $post->moderate_status = 0;
            $post->image_url = $image_url;
            $post->post_link = $link;
            $post->save();

            return Response::json(['results' => 'Post added successfully'], 200);
        }
    }

    //function to update post
    public function updatePost()
    {
        if (! $login = JWTAuth::toUser(JWTAuth::getToken())) {
            return response()->json(['user_not_found'], 404);
        } else {
            if ($_POST['caption'] == null) {
                return Response::json(['results' => 'Caption required'], 449);
            }
            $postID = $_POST['post_id'];
            $header = $_POST['header'] != null ? $_POST['header'] : null;
            $caption = $_POST['caption'];
            $link = $_POST['link'] != null ? $_POST['link'] : null;

            $post = Post::find($postID);
            if (! empty($_FILES['image'])) {
                Storage::disk('s3')->put('dynamic-images/client_post_image'.$_FILES['image']['name'], file_get_contents($_FILES['image']['tmp_name']), 'public');
                $image_url = Storage::disk('s3')->url('dynamic-images/client_post_image'.$_FILES['image']['name']);

                $post->header = $header;
                $post->caption = $caption;
                $post->image_url = $image_url;
                $post->post_link = $link;
            } else {
                $post->header = $header;
                $post->caption = $caption;
                $post->post_link = $link;
            }
            $post->save();

            return Response::json(['results' => 'Post updated successfully'], 200);
        }
    }

    //function to get all posts
    public function posts()
    {
        if (! $login = JWTAuth::toUser(JWTAuth::getToken())) {
            return response()->json(['user_not_found'], 404);
        } else {
            $posts = Post::where([['poster_type', PostType::b2b2c], ['poster_id', $login->b2b2c_id]])->paginate(20);

            return Response::json(['results' => $posts], 200);
        }
    }

    //function to delete post
    public function deletePost()
    {
        if (! $login = JWTAuth::toUser(JWTAuth::getToken())) {
            return response()->json(['user_not_found'], 404);
        } else {
            $postID = $_POST['post_id'];
            $post = Post::findOrFail($postID);
            $image_path = $post->image_url;
            //at first delete the previous image
            $exploded_path = explode('/', $image_path);
            //remove previous profile image from bucket
            Storage::disk('s3')->delete('dynamic-images/client_post_image/'.end($exploded_path));
            //delete post from count table
            SharePost::where('post_id', $postID)->delete();
            LikePost::where('post_id', $postID)->delete();
            $post->delete();

            return Response::json(['results' => 'Post deleted successfully'], 200);
        }
    }

    //function for logout of admin
    public function logout(Request $request)
    {
        $request->session()->flush();
        Auth::logout();

        return redirect('client/adminDashboard');
    }
}
