<?php

namespace App\Http\Controllers;

use App\Admin;
use App\AdminActivityNotification;
use App\AllAmounts;
use App\AllCoupons;
use App\Area;
use App\AssignedCard;
use App\B2b2cInfo;
use App\BranchOwner;
use App\BranchUser;
use App\CardDelivery;
use App\CardPromoCodes;
use App\CardPromoCodeUsage;
use App\Categories;
use App\Contact;
use App\CustomerAccount;
use App\CustomerHistory;
use App\CustomerInfo;
use App\CustomerLoginSession;
use App\CustomerNotification;
use App\CustomizePoint;
use App\Discount;
use App\Division;
use App\Events\user_force_logout;
use App\Helpers\LengthAwarePaginator;
use App\Hotspots;
use App\Http\Controllers\Enum\AdminRole;
use App\Http\Controllers\Enum\Constants;
use App\Http\Controllers\Enum\CustomerType;
use App\Http\Controllers\Enum\DeliveryType;
use App\Http\Controllers\Enum\LoginStatus;
use App\Http\Controllers\Enum\MiscellaneousType;
use App\Http\Controllers\Enum\PlatformType;
use App\Http\Controllers\Enum\PushNotificationType;
use App\Http\Controllers\Enum\SellerRole;
use App\Http\Controllers\Enum\SentMessageType;
use App\InfluencerPayment;
use App\InfluencerRequest;
use App\InfoAtBuyCard;
use App\OpeningHours;
use App\PartnerAccount;
use App\PartnerBranch;
use App\PartnerCategoryRelation;
use App\PartnerGalleryImages;
use App\PartnerInfo;
use App\PartnerJoinForm;
use App\PartnerMenuImages;
use App\PartnerNotification;
use App\PartnerProfileImage;
use App\PartnersInHotspot;
use App\Post;
use App\Press;
use App\PromoTable;
use App\Rating;
use App\RbdCouponPayment;
use App\RbdStatistics;
use App\Review;
use App\ReviewComment;
use App\Rules\unique_if_changed;
use App\ScannerPrizeHistory;
use App\SentMessageHistory;
use App\SslTransactionTable;
use App\Subscribers;
use App\TncForPartner;
use App\TopBrands;
use App\TransactionTable;
use App\TrendingOffers;
use App\UserType;
use App\Wish;
use Auth;
use Carbon\Carbon;
use DateTime;
use DB;
use File;
use function GuzzleHttp\Promise\all;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request as Input;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\Rule;
use Image;
use Khill\Lavacharts\Lavacharts;
use Session;
use Storage;
use Swift_Attachment;
use Swift_Mailer;
use Swift_Message;
use Swift_SmtpTransport;
use View;

class adminController extends Controller
{
    //function to get list of birthday boy/girl
    //    public function birthdayList()
    //    {
    //        //get current month and date only
    //        $current_date = date('m-d');
    //        //find users born today
    //        $users = DB::table('customer_info')
    //            ->select('customer_id', 'customer_first_name', 'customer_dob', 'customer_contact_number')
    //            ->get();
    //
    //        $customer_info = [];
    //        $i=0;
    //        foreach ($users as $key => $value) {
    //            $birthday = substr($value->customer_dob, 5);
    //            if($birthday == $current_date){
    //                $customer_data = DB::table('customer_info')
    //                    ->select('customer_id', 'customer_first_name', 'customer_last_name', 'customer_dob')
    //                    ->where('customer_id', $value->customer_id)
    //                    ->first();
    //                $customer_info[$i] = $customer_data;
    //            }
    //            $i++;
    //        }
    //        $date = new datetime();
    //        $date = date_format($date,"Y-m-d");
    //
    ////        $wish_send_status = DB::table('status')
    ////            ->where('type', 'birthday_wish')
    ////            ->where('date', 'like', $date. '%')
    ////            ->count();
    //
    //        return view('admin/production/birthdates', compact('customer_info'));
    //    }

    //function to get all new partners who want to join with us
    public function newPartners()
    {
        //get all info of new partners
        $newPartners = PartnerJoinForm::orderBy('id', 'desc')->get();

        return view('admin/production/new-partners', compact('newPartners'));
    }

    //function to delete new partner
    public function deleteNewPartner($id)
    {
        PartnerJoinForm::where('id', $id)->delete();

        return Redirect('admin/partner-request')->with('new partner deleted', 'Successfully deleted');
    }

    //Function to check Partner exists in top brands/trending offers before delete
    public function existsInTrendingBrands(Request $request)
    {
        $partner_id = $request->input('partner_id');
        $branch_id = $request->input('branch_id');

        //check if partner has multiple branch
        $branch_number = PartnerBranch::where('partner_account_id', $partner_id)->count();

        if ($branch_number > 1) {
            $result['status'] = 'delete_branch';
            $result['id'] = $branch_id;

            return Response::json($result);
        } else {
            $top_brands_exists = TopBrands::where('partner_account_id', $partner_id)->count();
            $trending_exists = TrendingOffers::where('partner_account_id', $partner_id)->count();

            if ($top_brands_exists > 0 && $trending_exists == 0) {
                $result['status'] = 'exists_in_trending';
                $result['id'] = null;

                return Response::json($result);
            } elseif ($trending_exists > 0 && $top_brands_exists == 0) {
                $result['status'] = 'exists_in_top';
                $result['id'] = null;

                return Response::json($result);
            } elseif ($top_brands_exists > 0 && $trending_exists > 0) {
                $result['status'] = 'exists_in_trending_top';
                $result['id'] = null;

                return Response::json($result);
            } else {
                $result['status'] = 'delete_partner';
                $result['id'] = $partner_id;

                return Response::json($result);
            }
        }
    }

    //function for delete partner branch
    public function deleteBranch($id)
    {
        try {
            DB::beginTransaction(); //to do query rollback
            $tran_exists = TransactionTable::where('branch_id', $id)->count();

            if ($tran_exists == 0) {
                $branch = PartnerBranch::findOrFail($id);
                $branch->delete();
            } else {
                return redirect()->back()->with('try_again', 'Can not delete this branch');
            }

            DB::commit(); //to do query rollback
        } catch (\Exception $e) {
            DB::rollBack(); //rollback all successfully executed queries
            return redirect()->back()->with('try_again', 'Please try again!');
        }

        return redirect()->back()->with('delete branch', 'One branch deleted');
    }

    //function for delete partner(USED BEFORE RBD MIGRATE)
    public function deletePartner($id)
    {
        $partner = PartnerAccount::find($id);
        $notification = PartnerNotification::where('partner_account_id', $id)->get();

        //discount on transactions notifications
        $discount_notification = DB::table('customer_notification as cn')
            ->join('transaction_table as trt', 'cn.source_id', '=', 'trt.id')
            ->join('partner_branch as pb', 'pb.id', '=', 'trt.branch_id')
            ->where('pb.partner_account_id', $id)
            ->where('cn.notification_type', 3)
            ->get();

        //review likes notifications
        $rev_like_notification = DB::table('customer_notification as cn')
            ->join('likes_review as lr', 'cn.source_id', '=', 'lr.id')
            ->join('review as rv', 'lr.review_id', '=', 'rv.id')
            ->where('rv.partner_account_id', $id)
            ->where('cn.notification_type', 1)
            ->get();

        //review reply notifications
        $rev_reply_notification = DB::table('customer_notification as cn')
            ->join('review as rv', 'rv.id', '=', 'cn.source_id')
            ->join('review_comment as rc', 'rc.review_id', '=', 'rv.id')
            ->where('rv.partner_account_id', $id)
            ->where('cn.notification_type', 6)
            ->get();

        //check if this partner has activities then do not allow to delete
        if (count($notification) != 0) {
            return back()->with('can-not-delete', 'This partner has activities');
        } elseif (count($discount_notification) != 0) {
            return back()->with('can-not-delete', 'This partner has activities');
        } elseif (count($rev_like_notification) != 0) {
            return back()->with('can-not-delete', 'This partner has activities');
        } elseif (count($rev_reply_notification) != 0) {
            return back()->with('can-not-delete', 'This partner has activities');
        }

        $branch_ids = PartnerBranch::where('partner_account_id', $partner->partner_account_id)->get();
        //        try {
        //            DB::beginTransaction();//to do query rollback
        foreach ($branch_ids as $value) {
            $branch = PartnerBranch::find($value->id);
            $sql = $branch->delete();
            if ($sql == 0) {
                return redirect('/changePartnerStatus')->with('delete partner', 'Something went wrong. Please contact with IT team');
            }
        }
        $sql = $partner->delete();
        if ($sql == 0) {
            return redirect('/changePartnerStatus')->with('delete partner', 'Something went wrong. Please contact with IT team');
        }
        DB::table('rbd_statistics')->where('partner_id', $id)->delete();
        PartnerCategoryRelation::where('partner_id', $id)->delete();
        //        } catch (\Exception $e) {
        //            DB::rollBack();//rollback all successfully executed queries
        //            dd($e);
        //            return redirect()->back()->with('try_again', 'Please try again!');
        //        }
        //pusher to logout this partner from all browsers
        $pusher = (new pusherController)->initializePusher();
        //Send a message to notify channel with an event name of notify-event
        $pusher->trigger('partnerLogout', 'partnerLogout-event', $id);

        return redirect('/changePartnerStatus')->with('delete partner', 'One partner deleted');
    }

    //function for login of admin
    public function loginView()
    {
        if (session('admin')) {
            return redirect('/dashboard');
        } else {
            return view('admin.production.adminlogin');
        }
    }

    public function loginAdmin(Request $request)
    {
        $username = $request->get('username');
        $password = $request->get('password');

        //        $current_month = date('F');
        //        $day = date('d');
        //        $next_month = Date("F", strtotime($current_month . " next month"));
        //
        //        if ($day > 22) {
        //            $leaderBoard = DB::table('leaderboard_prizes')
        //                ->where('status', 0)
        //                ->where('month_name', $next_month)
        //                ->count();
        //            if ($leaderBoard > 0) {
        //                session(['leaderboard_alert' => 0]);
        //            } else {
        //                session(['leaderboard_alert' => 1]);
        //            }
        //        }

        $encrypted_password = (new functionController())->encrypt_decrypt('encrypt', $password);
        $admin_info = Admin::where('username', $username)->where('password', $encrypted_password)->first();

        if (! empty($admin_info)) {
            session(['admin_username' => $admin_info->username]);
            session(['admin_id' => $admin_info->id]);
            session(['adm_pass_change_stat' => AdminRole::adm_pass_change_stat]);
            if ($admin_info->type == AdminRole::admin) {
                session(['admin' => AdminRole::admin]);
            } elseif ($admin_info->type == AdminRole::superadmin) {
                session(['admin' => AdminRole::superadmin]);
            } elseif ($admin_info->type == AdminRole::internAdmin) {
                session(['admin' => AdminRole::internAdmin]);

                return redirect('/form_upload');
            }

            session(['admin_notification_count' => AdminActivityNotification::where('seen', 0)->count()]);

            return redirect('/dashboard');
        } else {
            return redirect('/adminDashboard')->with('wrong info', 'Login credential invalid.');
        }
    }

    public function dashboard()
    {
        $year = date('Y');
        $month = date('m');

        return view('admin.production.index', compact('year', 'month'));
    }

    public function allAnalytics()
    {
        $from = date('Y-m-d');
        $to = date('Y-m-d');

        return view('admin.production.analytics.index', compact('from', 'to'));
    }

    public function membershipAnalytics()
    {
        return view('admin.production.analytics.membership_analytics');
    }

    //========== function for showing all partners info ============
    public function allPartners()
    {
        //get all partners info for admin panel
        $allPartners = PartnerAccount::with('info.category', 'branches')->orderBy('partner_account_id', 'DESC')->get();
        //get total partner number
        $partner_number = count($allPartners);
        //get all branch owners
        $owners = BranchOwner::all();

        return view('admin/production/allPartners', compact('allPartners', 'partner_number', 'owners'));
    }

    public function allActivatedPartners()
    {
        //get all partners info for admin panel
        $allPartners = PartnerAccount::with('info.category', 'activeBranches')
            ->orderBy('partner_account_id', 'DESC')->get();

        //get total partner number
        $partner_number = count($allPartners);
        //get all branch owners
        $owners = BranchOwner::all();
        $status = 'active';

        return view('admin/production/deactive_branches', compact(
            'allPartners',
            'partner_number',
            'owners',
            'status'
        ));
    }

    public function allDeactivatedPartners()
    {
        //get all partners info for admin panel
        $allPartners = PartnerAccount::with('info.category', 'deactiveBranches')
            ->orderBy('partner_account_id', 'DESC')->get();

        //get total partner number
        $partner_number = count($allPartners);
        //get all branch owners
        $owners = BranchOwner::all();
        $status = 'deactive';

        return view('admin/production/deactive_branches', compact(
            'allPartners',
            'partner_number',
            'owners',
            'status'
        ));
    }

    public function allAboutToExpirePartners()
    {
        //all about to expire partner ids
        $ids = PartnerInfo::whereMonth('expiry_date', Carbon::today()->month)
            ->where('expiry_date', '>=', date('Y-m-d'))->pluck('partner_account_id');
        //get all partners info for admin panel
        $allPartners = PartnerAccount::with('info.category', 'branches')
            ->orderBy('partner_account_id', 'DESC')
            ->whereIn('partner_account_id', $ids)
            ->get();

        //get total partner number
        $partner_number = count($allPartners);
        //get all branch owners
        $owners = BranchOwner::all();

        return view('admin/production/allPartners', compact(
            'allPartners',
            'partner_number',
            'owners'
        ));
    }

    public function allExpirePartners()
    {
        //all expired partner ids
        $ids = PartnerInfo::where('expiry_date', '<', date('Y-m-d'))->pluck('partner_account_id');
        //get all partners info for admin panel
        $allPartners = PartnerAccount::with('info.category', 'branches')
            ->orderBy('partner_account_id', 'DESC')
            ->whereIn('partner_account_id', $ids)
            ->get();

        //get total partner number
        $partner_number = count($allPartners);
        //get all branch owners
        $owners = BranchOwner::all();

        return view('admin/production/allPartners', compact(
            'allPartners',
            'partner_number',
            'owners'
        ));
    }

    //========== function for showing all partners info only (excluding branches) ============
    public function changePartnerStatus(Request $request)
    {
        //get partner via partner_name
        if ($request->has('partnerName')) {
            $partner_name = $request->get('partnerName');
            $allPartners = PartnerAccount::whereHas('info', function ($query) use ($partner_name) {
                $query->where('partner_name', $partner_name);
            })->paginate(20);
            $allPartners[0]->serial = 1;
            if (($allPartners->total()) != 0) {
                $allPartners->load('info');
            }
        } else {
            //get all partners info for admin panel
            $allPartners = PartnerAccount::with('info')->orderBy('partner_account_id', 'DESC')->paginate(20);
            $current_page = $allPartners->currentPage();
            $per_page = $allPartners->perPage();
            $j = ($current_page * $per_page) - $per_page + 1;
            foreach ($allPartners as $profile) {
                $profile->serial = $j;
                $j++;
            }
        }
        $partners = PartnerAccount::all();
        $allPartnersCount = $partners->count();
        $activePartnersCount = $partners->where('active', 1)->count();
        $deactivePartnersCount = $partners->where('active', 0)->count();

        return view('admin/production/changePartnerStatus', compact('allPartners', 'allPartnersCount',
            'activePartnersCount', 'deactivePartnersCount'));
    }

    //========== function for changing active/inactive status of partner ============
    public function partnerChangeStatus($partner_account_id)
    {
        $partner = PartnerAccount::findOrFail($partner_account_id);
        if ($partner->active == 1) {
            $partner->active = 0;
            PartnerBranch::where('partner_account_id', $partner_account_id)->update(['active' => 0]);
        } else {
            PartnerBranch::where('partner_account_id', $partner_account_id)
                ->where('main_branch', 1)->update(['active' => 1]);
            $partner->active = 1;
        }
        $partner->save();
        (new \App\Http\Controllers\AdminNotification\functionController())->partnerStatusChangeNotification($partner);

        $status = 'Status Changed Successfully!';

        return redirect()->back()->with('status', $status);
    }

    //========== function for changing active/inactive status of partner branch ============
    public function partnerBranchChangeStatus($partner_branch_id)
    {
        $partner_branch = PartnerBranch::findOrFail($partner_branch_id);
        if ($partner_branch->main_branch == 1) {
            return \redirect()->back()->with('main_branch_deactivate_msg', 'Main branch can not be deactivated');
        } else {
            if ($partner_branch->active == 1) {
                $partner_branch->active = 0;
            } else {
                $partner_branch->active = 1;
            }
            $partner_branch->save();
            (new \App\Http\Controllers\AdminNotification\functionController())
                ->partnerBranchStatusChangeNotification($partner_branch);
            $status = 'Status Changed Successfully!';

            return redirect()->back()->with('status', $status);
        }
    }

    //============function for showing partner searched by 'name'==================
    public function searchPartner(Request $request)
    {
        $keyword = $request->get('partnerName');
        $attr = explode(' => ', $keyword);

        if (isset($attr[0]) && isset($attr[1])) {
            $partner_info = DB::table('partner_info as pi')
                ->join('partner_branch as pb', 'pb.partner_account_id', '=', 'pi.partner_account_id')
                ->select('pb.partner_email')
                ->where('pi.partner_name', 'like', '%'.$attr[0].'%')
                ->where('pb.partner_address', 'like', '%'.$attr[1].'%')
                ->get();
            $partner_info = json_decode(json_encode($partner_info), true);

            if ($partner_info == null) {
                return redirect()->back();
            }

            if ($request->has('partnerName')) {
                $partnerName = $request->get('partnerName');
                $profileInfo = [];
                $searchResultBranch = PartnerBranch::where('partner_email', $partner_info[0]['partner_email'])->first();
                if ($searchResultBranch == null) {
                    $searchResultPartner = PartnerInfo::with('branches')->where('partner_name', $partnerName)->get();
                    if (count($searchResultPartner) > 0) {
                        foreach ($searchResultPartner as $key => $value) {
                            if (count($value['branches']) > 0) {
                                foreach ($value['branches'] as $key2 => $searchResultBranch) {
                                    $my_arr = [];
                                    $my_arr['partner_account_id'] = $searchResultBranch->info->partner_account_id;
                                    $my_arr['partner_name'] = $searchResultBranch->info->partner_name;
                                    $my_arr['branch_id'] = $searchResultBranch->id;
                                    $my_arr['partner_email'] = $searchResultBranch->partner_email;
                                    $my_arr['partner_mobile'] = $searchResultBranch->partner_mobile;
                                    $my_arr['partner_address'] = $searchResultBranch->partner_address;
                                    $my_arr['partner_area'] = $searchResultBranch->partner_area;
                                    $profileInfo[] = (object) $my_arr;
                                }
                            }
                        }
                    }
                } else {
                    $my_arr = [];
                    $my_arr['partner_account_id'] = $searchResultBranch->info->partner_account_id;
                    $my_arr['partner_name'] = $searchResultBranch->info->partner_name;
                    $my_arr['branch_id'] = $searchResultBranch->id;
                    $my_arr['partner_email'] = $searchResultBranch->partner_email;
                    $my_arr['partner_mobile'] = $searchResultBranch->partner_mobile;
                    $my_arr['partner_address'] = $searchResultBranch->partner_address;
                    $my_arr['partner_area'] = $searchResultBranch->partner_area;
                    $my_arr['active'] = $searchResultBranch->active;
                    $profileInfo[] = (object) $my_arr;
                }
            }

            return view('admin.production.allPartners', compact('profileInfo'));
        } elseif ($keyword == '') {
            return redirect('allPartners');
        } else {
            return redirect()->back();
        }
    }

    //============function to edit partner view page==================
    public function editPartner($branch_id)
    {
        $profileInfo = PartnerBranch::where('id', $branch_id)
            ->with('info.discount', 'info.tnc', 'info.category', 'openingHours')->first();
        $facilities = \App\BranchFacility::whereRaw('JSON_CONTAINS(category_ids, ?)', [json_encode($profileInfo->info->partner_category)])->get();
        $all_areas = Area::all();
        $all_divs = Division::all();

        return view('admin.production.edit_partner', compact('profileInfo', 'facilities', 'all_areas', 'all_divs'));
    }

    //function for insert updated data in partner table
    public function partnerEditDone(Request $request, $partner, $branch)
    {
        $this->validate($request, [
            'partner_mobile' => 'required',
            'partner_name' => 'required',
            'partner_address' => 'required',
            'division' => 'required',
            'area' => 'required',
            'partner_location' => 'required',
            'about' => 'required',
            'longitude' => 'required',
            'latitude' => 'required',
            'type' => 'required',
            'contract_expiry_date' => 'required',
            'sat' => 'required',
            'sun' => 'required',
            'mon' => 'required',
            'tues' => 'required',
            'wed' => 'required',
            'thu' => 'required',
            'fri' => 'required',
        ]);

        $request->flashOnly([
            'partner_email', 'partner_mobile', 'partner_name', 'partner_address', 'partner_location', 'about',
            'contract_expiry_date', 'longitude', 'lattitude', 'type', 'sat', 'sun', 'mon', 'tues', 'wed', 'thu', 'fri',
        ]);

        //get data from edit form
        $partnerName = $request->get('partner_name');
        $partnerEmail = $request->get('partner_email') == null ? '0' : $request->get('partner_email');
        $partnerMobile = $request->get('partner_mobile');
        $partnerAddress = $request->get('partner_address');
        $partnerDivision = $request->get('division');
        $partnerArea = $request->get('area');
        $partnerLocation = $request->get('partner_location');
        $facebookLink = $request->get('facebook') != null ? $request->get('facebook') : '#';
        $websiteLink = $request->get('website') != null ? $request->get('website') : '#';
        $about = $request->get('about');
        $contract_expiry_date = date('Y-m-d', strtotime($request->get('contract_expiry_date')));
        $longitude = $request->get('longitude');
        $latitude = $request->get('latitude');
        $type = $request->get('type');
        $instagramLink = $request->get('instagram') != null ? $request->get('instagram') : '#';
        $main_branch = ($request->get('main_branch')) == 'on' ? 1 : 0;
        //facilities
        $facilities = \App\BranchFacility::all();
        $facility_ids = [];
        foreach ($facilities as $key => $facility) {
            if ($request->input(str_replace(' ', '_', $facility->name)) != null) {
                array_push($facility_ids, $facility->id);
            }
        }
        $facility_ids = count($facility_ids) > 0 ? $facility_ids : null;

        $sat = ($request->get('sat')) != null ? $request->get('sat') : 0;
        $sun = ($request->get('sun')) != null ? $request->get('sun') : 0;
        $mon = ($request->get('mon')) != null ? $request->get('mon') : 0;
        $tue = ($request->get('tues')) != null ? $request->get('tues') : 0;
        $wed = ($request->get('wed')) != null ? $request->get('wed') : 0;
        $thu = ($request->get('thu')) != null ? $request->get('thu') : 0;
        $fri = ($request->get('fri')) != null ? $request->get('fri') : 0;

        DB::beginTransaction(); //to do query rollback
        try {
            //update partner info in database
            PartnerInfo::where('partner_account_id', $partner)->update([
                'partner_name' => $partnerName,
                'partner_type' => $type,
                'facebook_link' => $facebookLink,
                'website_link' => $websiteLink,
                'instagram_link' => $instagramLink,
                'about' => $about,
                'expiry_date' => $contract_expiry_date,
            ]);
            //remove main branch option from other branches
            if ($main_branch == 1) {
                PartnerBranch::where('partner_account_id', $partner)->update([
                    'main_branch' => 0,
                ]);
                //update partner branch info in database
                $partner_branch = PartnerBranch::where('id', $branch)->first();
                $partner_branch->partner_email = $partnerEmail;
                $partner_branch->partner_mobile = $partnerMobile;
                $partner_branch->partner_address = $partnerAddress;
                $partner_branch->partner_location = $partnerLocation;
                $partner_branch->longitude = $longitude;
                $partner_branch->latitude = $latitude;
                $partner_branch->partner_area = $partnerArea;
                $partner_branch->partner_division = $partnerDivision;
                $partner_branch->main_branch = $main_branch;
                $partner_branch->facilities = $facility_ids;
                $partner_branch->save();
            } else {
                //update partner branch info in database
                $partner_branch = PartnerBranch::where('id', $branch)->first();
                $partner_branch->partner_email = $partnerEmail;
                $partner_branch->partner_mobile = $partnerMobile;
                $partner_branch->partner_address = $partnerAddress;
                $partner_branch->partner_location = $partnerLocation;
                $partner_branch->longitude = $longitude;
                $partner_branch->latitude = $latitude;
                $partner_branch->partner_area = $partnerArea;
                $partner_branch->partner_division = $partnerDivision;
                $partner_branch->facilities = $facility_ids;
                $partner_branch->save();
            }

            //update attribute in attribute table
            DB::table('opening_hours')
                ->where('branch_id', $branch)
                ->update([
                    'sat' => $sat,
                    'sun' => $sun,
                    'mon' => $mon,
                    'tue' => $tue,
                    'wed' => $wed,
                    'thurs' => $thu,
                    'fri' => $fri,
                ]);
            DB::commit(); //to do query rollback
        } catch (\Exception $e) {
            DB::rollBack(); //rollback all successfully executed queries

            return redirect()->back()->with('try_again', 'Please try again!');
        }

        return redirect('/allPartners')->with('status', 'Profile updated!');
    }

    //function for logout of admin
    public function adminLogout(Request $request)
    {
        $request->session()->flush();
        Auth::logout();

        return redirect('/adminDashboard');
    }

    //function for admin transaction view
    public function adminTransac($id)
    {
        $transactions = DB::table('transaction_table')
            ->select('customer_id', 'amount_spent', 'date', 'discount_amount')
            ->where('partner_account_id', $id)
            ->get();
        $transactions = json_decode(json_encode($transactions), true);

        $amount_sum = DB::table('transaction_table')
            ->where('partner_account_id', $id)
            ->sum('amount_spent');

        $discount_sum = DB::table('transaction_table')
            ->where('partner_account_id', $id)
            ->sum('discount_amount');

        $profileInfo = DB::table('partner_info')
            ->select('partner_account_id', 'partner_name', 'partner_email', 'partner_mobile', 'partner_address')
            ->get();
        $profileInfo = json_decode(json_encode($profileInfo), true);

        return view('/admin/production/allPartners', compact('transactions', 'amount_sum', 'discount_sum', 'profileInfo'));
    }

    //function to show all coupons in admin panel
    public function allCoupons()
    {
        //get all coupons from database
        $coupons = DB::table('all_coupons')->where('coupon_type', '!=', 2)->get();
        $coupons = json_decode(json_encode($coupons), true);
        $i = 0;
        foreach ($coupons as $coupon) {
            $partner_name = DB::table('partner_info')
                ->select('partner_name')
                ->where('partner_account_id', $coupon['partner_account_id'])
                ->get();
            $partner_name = json_decode(json_encode($partner_name), true);
            $coupons[$i]['partner_name'] = $partner_name[0]['partner_name'];
            $i++;
        }

        return view('admin/production/allCoupons', compact('coupons'));
    }

    //function to add new coupon in database view
    public function couponAdd()
    {
        //get all partners name & id
        $allPartners = PartnerInfo::select('partner_account_id', 'partner_name')->get();
        $allPartners = json_decode(json_encode($allPartners), true);

        return view('admin.production.coupon_add', compact('allPartners'));
    }

    //function to add new coupon in database
    public function addCoupon(Request $request)
    {
        //check validation of coupon fields
        $this->validate($request, [
            'partner_id' => 'required',
            'coupon_type' => [
                'required',
                Rule::in([1, 3]),
            ],
            'reward_text' => 'required',
            'coupon_details' => 'required',
            'coupon_tnc' => 'required',
            'exp_date' => 'required',
            'coupon_count' => 'required',
        ]);
        $request->flashOnly(['partner_id', 'coupon_type', 'coupon_details', 'reward_text', 'coupon_tnc', 'exp_date', 'coupon_count']);

        $partner_id = $request->get('partner_id');
        $coupon_type = $request->get('coupon_type');
        $reward_text = $request->get('reward_text');
        $coupon_details = $request->get('coupon_details');
        $coupon_count = $request->get('coupon_count');
        $coupon_tnc = $request->get('coupon_tnc');
        $exp_date = $request->get('exp_date');

        AllCoupons::insert([
            'partner_account_id' => $partner_id,
            'coupon_type' => $coupon_type,
            'reward_text' => $reward_text,
            'coupon_details' => $coupon_details,
            'stock' => $coupon_count,
            'coupon_tnc' => $coupon_tnc,
            'expiry_date' => $exp_date,
        ]);

        return back()->with('coupon added', 'New coupon added successfully');
    }

    //View to edit coupon page
    public function editCoupon($id)
    {
        //get all coupons from database
        $coupon_info = DB::table('all_coupons')
            ->where('id', $id)
            ->get();
        $coupon_info = json_decode(json_encode($coupon_info), true);

        $partner_name = DB::table('partner_info')
            ->select('partner_name')
            ->where('partner_account_id', $coupon_info[0]['partner_account_id'])
            ->get();
        $partner_name = json_decode(json_encode($partner_name), true);

        return view('admin.production.edit_coupon', compact('coupon_info', 'partner_name'));
    }

    //function to submit edit coupon
    public function editCouponDone(Request $request)
    {
        //check validation of coupon fields
        $this->validate($request, [
            'coupon_type' => [
                'required',
                Rule::in([1, 3]),
            ],
            'reward_text' => 'required',
            'coupon_tnc' => 'required',
            'coupon_details' => 'required',
            'exp_date' => 'required',
            'coupon_count' => 'required',
        ]);
        $request->flashOnly(['partner_id', 'coupon_type', 'reward_text', 'coupon_details', 'coupon_tnc', 'exp_date', 'coupon_count']);

        $coupon_type = $request->get('coupon_type');
        $reward_text = $request->get('reward_text');
        $coupon_count = $request->get('coupon_count');
        $coupon_tnc = $request->get('coupon_tnc');
        $coupon_details = $request->get('coupon_details');
        $exp_date = $request->get('exp_date');
        $coupon_id = $request->get('coupon_id');

        DB::table('all_coupons')
            ->where('id', $coupon_id)
            ->update([
                'coupon_type' => $coupon_type,
                'reward_text' => $reward_text,
                'coupon_details' => $coupon_details,
                'stock' => $coupon_count,
                'coupon_tnc' => $coupon_tnc,
                'expiry_date' => $exp_date,
            ]);

        return redirect('/allCoupons')->with('coupon_update', 'Coupon update successful');
    }

    //View to edit refer bonus page
    public function referBonus()
    {
        //get all coupons from database
        $coupon_info = AllCoupons::where('coupon_type', 2)->select('reward_text')->first();
        $bonus_info = AllAmounts::where('type', 'refer_bonus')->select('price')->first();

        return view('admin.production.edit_refer_bonus', compact('coupon_info', 'bonus_info'));
    }

    //function to edit refer bonus
    public function editReferBonus(Request $request)
    {
        //check validation of coupon fields
        $this->validate($request, [
            'reward_text' => 'required',
            'bonus_amount' => 'required',
        ]);
        $request->flashOnly(['reward_text', 'bonus_amount']);
        $reward_text = $request->get('reward_text');
        $bonus_amount = $request->get('bonus_amount');
        try {
            DB::beginTransaction(); //to do query rollback

            AllCoupons::where('coupon_type', 2)->update(['reward_text' => $reward_text]);
            AllAmounts::where('type', 'refer_bonus')->update(['price' => $bonus_amount]);

            DB::commit(); //to do query rollback
        } catch (\Exception $e) {
            DB::rollBack(); //rollback all successfully executed queries
            return redirect()->back()->with('try_again', 'Please try again!');
        }

        return redirect('refer-bonus')->with('updated', 'Refer bonus updated');
    }

    //function to show to all news in admin panel
    public function allNews()
    {
        //get all news from database
        $allNews = Press::all();

        return view('admin.production.all-news', compact('allNews'));
    }

    //function to add new news
    public function addNews(Request $request)
    {
        $this->validate($request, [
            'press_name' => 'required',
            'sub_title' => 'required',
            'press_details' => 'required',
            'press_link' => 'required',
            'date' => 'required',
            'press_image' => 'required',
        ]);
        $request->flashOnly(['press_name', 'sub_title', 'press_details', 'press_link', 'date', 'press_image']);

        $press_name = $request->get('press_name');
        $sub_title = $request->get('sub_title');
        $press_details = $request->get('press_details');
        $press_link = $request->get('press_link');
        $date = $request->get('date');
        //upload image to aws & save url to DB
        $file = $request->file('press_image');
        //image is being resized & uploaded here
        $image_url = (new functionController)->uploadImageToAWS($file, 'news');

        //save news info in press table
        Press::insert([
            'press_name' => $press_name,
            'sub_title' => $sub_title,
            'press_details' => $press_details,
            'press_image' => $image_url,
            'press_link' => $press_link,
            'date' => $date,
        ]);

        //redirect dashboard after successfully news adding
        return redirect('/allNews')->with('news added', 'One news added successfully');
    }

    public function editNews($id)
    {
        //get specific info of this news
        $news = Press::findOrFail($id);

        return view('admin.production.edit-news', compact('news'));
    }

    //function to save updated info of news
    public function updateNews(Request $request, $id)
    {
        $this->validate($request, [
            'press_name' => 'required',
            'sub_title' => 'required',
            'press_details' => 'required',
            'press_link' => 'required',
            'date' => 'required',
        ]);
        $request->flashOnly(['press_name', 'sub_title', 'press_details', 'press_image', 'press_link', 'date']);

        $press_name = $request->get('press_name');
        $sub_title = $request->get('sub_title');
        $press_details = $request->get('press_details');
        $press_link = $request->get('press_link');
        $date = $request->get('date');

        //get the updating instance
        $press = Press::findOrFail($id);

        if ($_FILES['press_image']['name'] != '') {
            //at first delete the previous image
            $image_path = $press->press_image;
            $exploded_path = explode('/', $image_path);
            //remove previous profile image from bucket
            Storage::disk('s3')->delete('news/'.end($exploded_path));

            //remove previous image from news folder
            if (File::exists($image_path)) {
                File::delete($image_path);
            }
            //upload image to aws & save url to DB
            $file = $request->file('press_image');
            //image is being resized & uploaded here
            $image_url = (new functionController)->uploadImageToAWS($file, 'news');
            //update db instance path
            $press->press_image = $image_url;
        }
        //update the db insatnce
        $press->press_name = $press_name;
        $press->sub_title = $sub_title;
        $press->press_details = $press_details;
        $press->press_link = $press_link;
        $press->date = $date;
        $press->save();

        return Redirect('allNews')->with('news updated', 'News updated successfully');
    }

    //function to delete news
    public function deleteNews($id)
    {
        //at first delete the previous image
        $press = Press::findOrFail($id);
        $image_path = $press->press_image;
        //remove previous image from news folder
        if (File::exists($image_path)) {
            File::delete($image_path);
        }
        //remove specific row with the id
        $press->delete();

        return Redirect('allNews')->with('news deleted', 'News deleted successfully');
    }

    //function to add division area view
    public function addDivisionArea()
    {
        $divisions = Division::all();

        return view('admin/production/addAreaDivision', compact('divisions'));
    }

    //function to store new division
    public function addDivision(Request $request)
    {
        $this->validate($request, [
            'division' => 'required|unique:division,name',
        ]);
        $request->flashOnly(['division']);
        $division = $request->get('division');
        Division::insert(
            [
                'name' => ucfirst($division),
            ]
        );

        return redirect()->back()->with('division_added', 'Division added successfully');
    }

    //Function to update shipping address
    public function selected_area_list(Request $request)
    {
        $division_id = $request->input('division_id');
        $all_areas = Area::where('division_id', $division_id)->get();
        $output = '';

        if ($all_areas) {
            $output .= '<table class="table table-striped projects">';
            $output .= '<tbody>';
            foreach ($all_areas as $area) {
                $output .= '<tr>';
                $output .= '<td>';
                $output .= '<textarea id="area_updated_'.$area['id'].'" rows="1" style="width: 100%"
                placeholder="Area Name" onfocusout="area_update('.$area['id'].')">'.$area['area_name'].'</textarea>';
                $output .= '</td>';
                $output .= '<td><a href="'.url('deleteArea/'.$area['id'])."\">
                    <button class=\"btn btn-danger\" onclick=\"return confirm('Are you sure to delete this Area?')\">Delete</button>";
                $output .= '</a>';
                $output .= '</td>';
                $output .= '</tr>';
            }
            $output .= '</tbody>';
            $output .= '</table>';
        } else {
            $output .= '<div style="font-size: 1.4em; color: red;">';
            $output .= 'No Area Found';
            $output .= '</div>';
        }

        return Response::json($output);
    }

    //Delete a particular Area
    public function delete_area($id)
    {
        //delete review likes notifications
        Area::where('id', $id)->delete();

        return redirect('/add-division-area')->with('area_deleted', 'Area deleted!');
    }

    //Function to update a particular Area Name
    public function update_area_name(Request $request)
    {
        $area_name = $request->input('area_name');
        $area_id = $request->input('area_id');

        Area::where('id', $area_id)->update(['area_name' => $area_name]);

        return Response::json('1');
    }

    //function to store new area
    public function addArea(Request $request)
    {
        $this->validate($request, [
            'division_name' => 'required',
            'area_name' => 'required',
        ]);
        $request->flashOnly(['division_name', 'area_name']);
        $division_id = $request->get('division_name');
        $area_name = $request->get('area_name');

        $area_exists_in_devision = Area::where('division_id', $division_id)
            ->where('area_name', $area_name)
            ->count();

        if ($area_exists_in_devision > 0) {
            return redirect()->back()->with('area_duplicate', 'Area already exists');
        }

        $area = new Area([
            'area_name' => $area_name,
            'division_id' => $division_id,
        ]);
        $area->save();
        (new \App\Http\Controllers\AdminNotification\functionController())->newAreaAddNotification($area);

        return redirect()->back()->with('area_added', 'Area added successfully');
    }

    //function to show all special deals
    public function allSpecialDeals()
    {
        //get all special deals
        $specialDeals = DB::table('special_deals')->get();
        $specialDeals = json_decode(json_encode($specialDeals), true);

        return view('/admin/production/allSpecialDeals', compact('specialDeals'));
    }

    //function to add special deals
    public function addSpecialDeals(Request $request)
    {
        //insert profile image in database
        $file_name = $_FILES['specialDeals']['name']; //get image name
        $file_size = $_FILES['specialDeals']['size']; //size in bytes
        $file_tmp = $_FILES['specialDeals']['tmp_name'];
        $file_type = $_FILES['specialDeals']['type'];
        $path = 'images/specialDeals/'.$file_name;
        //move to the desired folder in server
        move_uploaded_file($file_tmp, 'images/specialDeals/'.$file_name);

        // image path saved to the database
        DB::table('special_deals')->insert([
            'image' => $path,
        ]);

        $specialDeals = DB::table('special_deals')->get();
        $specialDeals = json_decode(json_encode($specialDeals), true);
        //create session message
        $request->session()->flash('specialDealsAdded', 'One special deals added');

        return Redirect('allSpecialDeals');
        // return view('/admin/production/allSpecialDeals', compact('specialDeals'));
    }

    //function to add special deals
    public function allWishes()
    {
        // get all wishes from the database
        $wishes = Wish::wish()->get();
        $wishes = $wishes->where('account', '!=', null);

        //send all data to customer wishes page
        return view('/admin/production/customer-wishes', compact('wishes'));
    }

    public function deleteWish($id)
    {
        $wish = Wish::where('id', $id)->first();
        $wish->delete();

        return \redirect()->back()->with('message', 'Successfully deleted.');
    }

    //function to add special deals
    public function addNewsletter(Request $request)
    {
        //fetch only unique email values
        $subscribed_mails = Subscribers::select('email')->distinct()->get();

        $type = 'all';

        //check for request type parameter
        if ($request->has('type')) {
            $type = $request->type;
            //get the email address
            $all_emails = $subscribed_mails->pluck('email');

            //fetch customer instances existing for these emails
            $customers = CustomerInfo::whereIn('customer_email', $all_emails)->get();

            if ($type == 'non-user') {
                //fetch customer emails
                $customer_emails = $customers->pluck('customer_email');

                //filter to have only non-user emails
                $subscribed_mails = $subscribed_mails->whereNotIn('email', $customer_emails);
            } elseif ($type == 'card_user') {
                //fetch customer emails of cutomer type 2
                $customer_emails = $customers->where('customer_type', 2)->pluck('customer_email');

                //filter to have only platinum customer emails
                $subscribed_mails = $subscribed_mails->whereIn('email', $customer_emails);
            } elseif ($type == 'guest') {
                //fetch customer emails of cutomer type 3
                $customer_emails = $customers->where('customer_type', 3)->pluck('customer_email');

                //filter to have only guest customer emails
                $subscribed_mails = $subscribed_mails->whereIn('email', $customer_emails);
            } elseif ($type == 'expired_trial') {
                $user_type = CustomerType::trial_user;
                //fetch customer emails of expired trial user
                $customers = (new functionController2())->getExpiredCustomers($user_type);
                $customer_emails = collect($customers)->pluck('customer_email');

                //filter to have only guest customer emails
                $subscribed_mails = $subscribed_mails->whereIn('email', $customer_emails);
            } elseif ($type == 'expired_card_user') {
                $user_type = CustomerType::card_holder;
                //fetch customer emails of cutomer type 3
                $customers = (new functionController2())->getExpiredCustomers($user_type);
                $customer_emails = collect($customers)->pluck('customer_email');

                //filter to have only guest customer emails
                $subscribed_mails = $subscribed_mails->whereIn('email', $customer_emails);
            } elseif ($type == 'active') {
                //fetch customer emails of active customers
                $customer_emails = DB::table('transaction_table as tt')
                    ->join('customer_info as ci', 'ci.customer_id', '=', 'tt.customer_id')
                    ->distinct('tt.customer_id')
                    ->pluck('ci.customer_email');

                //filter to have only guest customer emails
                $subscribed_mails = $subscribed_mails->whereIn('email', $customer_emails);
            } elseif ($type == 'inactive') {
                //fetch customer emails of inactive customers
                $tt_ids = DB::table('transaction_table')->distinct('customer_id')->pluck('customer_id');
                $customer_emails = DB::table('customer_info')
                    ->whereNotIn('customer_id', $tt_ids)
                    ->pluck('customer_email');

                //filter to have only guest customer emails
                $subscribed_mails = $subscribed_mails->whereIn('email', $customer_emails);
            }
        }

        //send all data to customer wishes page
        return view('/admin/production/addNewsletter', compact('subscribed_mails', 'type'));
    }

    //push notification send view
    public function sendPushNotificationView($user)
    {
        return view('admin/production/push_noti/send', compact('user'));
    }

    public function sendPushNotification(Request $request)
    {
        $image_url = null;
        if (Input::hasFile('image')) {
            $file = $request->file('image');
            $image_url = (new functionController)->uploadImageToAWS($file, 'dynamic-images/push_notification');
        }
        $customer_type = $request->input('customer_type');

        return \response()->json(['f_tokens'=>$this->getFTokensWithType($customer_type), 'image'=>$image_url]);
    }

    public function getFTokensWithType($customer_type)
    {
        if ($customer_type == 'all' || $customer_type == SentMessageType::ALL_MEMBERS) {//all customers
            $data = CustomerInfo::where('firebase_token', '!=', '0')->pluck('firebase_token');
        } elseif ($customer_type == 'scanner' || $customer_type == SentMessageType::ALL_SCANNERS) {//all scanners
            $data = BranchUser::where('f_token', '!=', '0')->pluck('f_token');
        } else {
            if ($customer_type == 4 || $customer_type == SentMessageType::ALL_EXPIRED) {//all expired customers
                $data = CustomerInfo::where('customer_type', 2)->where('expiry_date', '<', date('Y-m-d'))
                    ->where('firebase_token', '!=', '0')->pluck('firebase_token');
            } elseif ($customer_type == 5 || $customer_type == SentMessageType::ALL_ACTIVE) {//all active customers
                $data = DB::table('transaction_table as tt')
                    ->join('customer_info as ci', 'ci.customer_id', '=', 'tt.customer_id')
                    ->distinct('tt.customer_id')
                    ->where('ci.firebase_token', '!=', '0')
                    ->pluck('ci.firebase_token');
            } elseif ($customer_type == 6 || $customer_type == SentMessageType::ALL_INACTIVE) {//all inactive customers
                $tt_ids = DB::table('transaction_table')->distinct('customer_id')->pluck('customer_id');
                $data = DB::table('customer_info')
                    ->whereNotIn('customer_id', $tt_ids)
                    ->where('firebase_token', '!=', '0')
                    ->pluck('firebase_token');
            } elseif ($customer_type == 7 || $customer_type == SentMessageType::ALL_EXPIRED_TRIAL) {
                $data = DB::table('customer_info as ci')
                    ->join('customer_history', function ($join) {
                        $join->on('customer_history.customer_id', '=', 'ci.customer_id')
                            ->on('customer_history.id', '=', DB::raw('(SELECT max(id) from customer_history WHERE customer_history.customer_id = ci.customer_id)'));
                    })
                    ->where('ci.expiry_date', '<=', date('Y-m-d'))
                    ->where('ci.firebase_token', '!=', '0')
                    ->where('customer_history.type', CustomerType::trial_user)
                    ->pluck('ci.firebase_token');
            } elseif ($customer_type == 8 || $customer_type == SentMessageType::ALL_EXPIRED_PREMIUM) {
                $data = DB::table('customer_info as ci')
                    ->join('customer_history', function ($join) {
                        $join->on('customer_history.customer_id', '=', 'ci.customer_id')
                            ->on('customer_history.id', '=', DB::raw('(SELECT max(id) from customer_history WHERE customer_history.customer_id = ci.customer_id)'));
                    })
                    ->where('ci.expiry_date', '<=', date('Y-m-d'))
                    ->where('ci.firebase_token', '!=', '0')
                    ->where('customer_history.type', CustomerType::card_holder)
                    ->pluck('ci.firebase_token');
            } elseif ($customer_type == 9 || $customer_type == SentMessageType::ALL_EXPIRING_MEMBERS) {
                $data = CustomerInfo::where('customer_type', 2)
                    ->where('expiry_date', '>', date('Y-m-d'))
                    ->where('expiry_date', '<=', date('Y-m-d', strtotime(date('Y-m-d').' + 10 days')))
                    ->where('firebase_token', '!=', '0')
                    ->pluck('firebase_token');
            } else {
                $data = CustomerInfo::where('customer_type', $customer_type)->where('firebase_token', '!=', '0')->pluck('firebase_token');
            }
        }

        return array_chunk($data->toArray(), Constants::notification_chunk);
    }

    public function sendingPushNotification(Request $request)
    {
        $token = $request->input('token');
        $title = $request->input('title');
        $message = $request->input('message');
        $image_url = $request->input('image_url');

        $this->sendCustomerWisePushNotification($title, $message, $token, $image_url);

        return \response()->json(true);
    }

    public function sendCustomerWisePushNotification($title, $message, $firebaseRegIds, $image_url = null)
    {
        $ios_ids = [];
        $android_ids = [];
        foreach ($firebaseRegIds as $firebaseRegId) {
            $session = CustomerLoginSession::where('physical_address', $firebaseRegId)->orderBy('id', 'DESC')->first();
            if ($session && $session->status == LoginStatus::logged_in) {
                if ($session->platform == PlatformType::android) {
                    array_push($android_ids, $firebaseRegId);
                } elseif ($session->platform == PlatformType::ios) {
                    array_push($ios_ids, $firebaseRegId);
                }
            }
        }
        // iOS
        (new jsonController())->sendFirebaseIOSFeedNotification($title, $message, $ios_ids, 0, $image_url, PushNotificationType::FROM_ADMIN);

        // android
        (new jsonController())->sendFirebaseFeedNotification($title, $message, $android_ids, 0, $image_url, PushNotificationType::FROM_ADMIN);
    }

    public function getPushNotiUserType($customer_type)
    {
        if ($customer_type == 'all') {
            $to = 'All Members';
        } elseif ($customer_type == 'scanner') {
            $to = 'All Scanners';
        } else {
            if ($customer_type == 2) {
                $to = 'All Premium Members';
            } elseif ($customer_type == 3) {
                $to = 'All Guest Members';
            } elseif ($customer_type == 4) {
                $to = 'All Expired Member';
            } elseif ($customer_type == 5) {
                $to = 'All Active Member';
            } elseif ($customer_type == 6) {
                $to = 'All Inactive Members';
            }
        }

        return $to;
    }

    public function saveSentMessage(Request $request)
    {
        $customer_type = $request->input('customer_type');
        $title = $request->input('title');
        $message = $request->input('message');
        $image_url = $request->input('image_url');
        $schedule = $request->input('schedule');
        $language = 'english';
        $to = $this->getPushNotiUserType($customer_type);

        return (new functionController2())->saveSentMessage(SentMessageType::push_notification, $title, $message,
            $to, $language, $schedule, $image_url);
    }

    public function scheduledNotifications()
    {
        $notifications = SentMessageHistory::scheduled()->get();

        return view('admin.production.push_noti.scheduled', compact('notifications'));
    }

    public function editScheduledNotification($id)
    {
        $notification = SentMessageHistory::find($id);

        return view('admin.production.push_noti.edit_scheduled', compact('notification'));
    }

    public function updateScheduledNotification(Request $request, $id)
    {
        $this->validate($request, [
            'title' => 'required',
            'body' => 'required',
            'scheduled_at' => 'required',
        ]);
        $request->flashOnly('title', 'body', 'scheduled_at');
        $customer_type = $request->input('customer_type');
        $title = $request->input('title');
        $body = $request->input('body');
        $scheduled_at = $request->input('scheduled_at');
        $cur_hour = date('H');
        $scheduled_hour = date('H', strtotime($scheduled_at));
        if ($scheduled_hour <= $cur_hour) {
            return \redirect()->back()->with('error', 'You can not set past time');
        }
        $to = $this->getPushNotiUserType($customer_type);

        $notification = SentMessageHistory::find($id);
        $notification->title = $title;
        $notification->body = $body;
        $notification->to = $to;
        $notification->scheduled_at = $scheduled_at;
        $notification->save();

        return \redirect('admin/scheduled-notification')->with('success', 'Push notification updated successfully');
    }

    //function to delete special deals
    public function deleteSpecialDeals(Request $request, $id)
    {
        //delete special deals from table
        DB::table('special_deals')->where('id', $id)->delete();

        //get all special deals
        $specialDeals = DB::table('special_deals')->get();
        $specialDeals = json_decode(json_encode($specialDeals), true);
        //create session message
        $request->session()->flash('specialDealsDeleted', 'One special deals deleted!');

        return Redirect('allSpecialDeals');
    }

    //function to fetch branches by partner id
    public function getBranchesByPartner($partner_account_id)
    {
        $partner_branches = PartnerBranch::where('partner_account_id', $partner_account_id)->get();

        return response()->json($partner_branches);
    }

    //function to delete Partner from hotspot
    public function deleteHotspotPartner($branch_id)
    {
        PartnersInHotspot::where('branch_id', $branch_id)->delete();

        return back()->with('partner deleted', 'Partner has been deleted from hotspot');
    }

    //function to add hotspot data view
    public function addToHotspot()
    {
        $allHotspots = (new functionController)->allhotspots();
        //all partners to add in hotspot
        $allPartners = PartnerInfo::with('branches')->orderBy('partner_name', 'ASC')->get();

        return view('admin.production.addHotspot', compact('allHotspots', 'allPartners'));
    }

    //function to add hotspot backend
    public function addHotspot(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'description' => 'required',
            'profile_image' => 'required',
        ]);
        $request->flashOnly('name', 'description', 'profile_image');
        $name = $request->get('name');
        $description = $request->get('description');

        //upload hotspot image to AWS & save path to DB
        $file = $request->file('profile_image');
        //image is being resized & uploaded here
        $image_url = (new functionController)->uploadImageToAWS($file, 'hotspot_images');
        //image path saved to the database
        Hotspots::insert(
            [
                'name' => $name,
                'image_link' => $image_url,
                'description' => $description,
            ]
        );

        return redirect('/allHotspots')->with('added', 'New Hotspot Added');
    }

    //function to delete hotspot
    public function deleteHotspot($id)
    {
        $get_current_image_name = Hotspots::select('image_link')
            ->where('id', $id)
            ->get();
        $get_current_image_name = json_decode(json_encode($get_current_image_name), true);
        $get_current_image_name = $get_current_image_name[0];
        $image_path = $get_current_image_name['image_link'];
        //remove image from folder
        if (File::exists($image_path)) {
            File::delete($image_path);
        }
        //delete hotspot from table
        Hotspots::where('id', $id)->delete();

        return redirect('/allHotspots')->with('deleted', 'One hotspot deleted');
    }

    //function to add partner to a hotspot
    public function addPartnerToHotspot(Request $request)
    {
        $this->validate($request, [
            'hotspot' => 'required',
            'partner_branch' => 'required|unique:partners_in_hotspot,branch_id',
        ]);
        $request->flashOnly('hotspot', 'partner_branch');

        $hotspot_id = $request->get('hotspot');
        $branch_id = $request->get('partner_branch');
        //store new branch in DB
        PartnersInHotspot::insert([
            'hotspot_id' => $hotspot_id,
            'branch_id' => $branch_id,
        ]);

        return back()->with('partner_added_to_hotspot', 'New partner added to a hotspot');
    }

    //function to view Blog Categories
    public function blogCategories()
    {
        $allCategories = blogCategories::select('partner_account_id', 'partner_name')->get();

        return view('admin.production.blogCategories', compact('allPartners'));
    }

    //function to add promo code
    public function addPromoCode(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'category' => 'required',
            'discount' => 'required',
            'promo_code' => 'required',
            'terms' => 'required',
            'profile' => 'required',
        ]);
        $request->flashOnly('name', 'category', 'discount', 'promo_code', 'terms', 'profile');
        $name = $request->get('name');
        $category = $request->get('category');
        $discount = $request->get('discount');
        $promo_code = $request->get('promo_code');
        $terms = $request->get('terms');
        $website = ($request->get('website')) != null ? $request->get('website') : '#';

        //upload hotspot image to AWS & save path to DB
        $file = $request->file('profile');
        //image is being resized & uploaded here
        $image_url = (new functionController)->uploadImageToAWS($file, 'online_partner');

        PromoTable::insert([
            'partner_name' => $name,
            'image_link' => $image_url,
            'category' => $category,
            'discount_percentage' => $discount,
            'partner_website' => $website,
            'promo_code' => $promo_code,
            'term&condition' => $terms,
        ]);

        return redirect('allPromo')->with('promo_added', 'Promo code added successfully');
    }

    //function to show all promo codes
    public function allPromoCodes()
    {
        $allPromo = PromoTable::all();
        //get all promo partner names
        $promoPartners = $allPromo->pluck('partner_name');

        //$promoPartners = json_decode(json_encode($promoPartners),true);
        return view('admin.production.all_promo', compact('allPromo', 'promoPartners'));
    }

    //function to search promo by partner name
    public function searchPromo(Request $request)
    {
        $allPromo = PromoTable::where('partner_name', $request->get('partnerName'))->get();
        //get all promo partner names
        $promoPartners = $allPromo->pluck('partner_name');

        return view('admin.production.all_promo', compact('allPromo', 'promoPartners'));
    }

    //function to edit promo code view
    public function editPromoCode($id)
    {
        $promo = PromoTable::findOrFail($id);

        return view('admin.production.edit_promo', compact('promo'));
    }

    //function to edit promo code backend
    public function edit_promo_code(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required',
            'category' => 'required',
            'discount' => 'required',
            'promo_code' => 'required',
            'terms' => 'required',
        ]);
        $request->flashOnly('name', 'category', 'discount', 'promo_code', 'terms');
        $name = $request->get('name');
        $category = $request->get('category');
        $discount = $request->get('discount');
        $promo_code = $request->get('promo_code');
        $terms = $request->get('terms');
        $website = ($request->get('website')) != null ? $request->get('website') : '#';

        PromoTable::where('id', $id)
            ->update([
                'partner_name' => $name,
                'category' => $category,
                'discount_percentage' => $discount,
                'partner_website' => $website,
                'promo_code' => $promo_code,
                'term&condition' => $terms,
            ]);

        return back()->with('promo updated', 'Promo code updated successfully');
    }

    //function to delete promo code from database
    public function deletePromo($id)
    {
        //delete promo code
        PromoTable::where('id', $id)->delete();

        return back()->with('deleted', 'One promo deleted');
    }

    public function getSingleTranInfo($row)
    {
        return [
            'id' => $row->id,
            'partner_name' => $row->branch->info->partner_name,
            'partner_area' => $row->branch->partner_area,
            'partner_address' => $row->branch->partner_address,
            'customer_id' => $row->customer->customer_id,
            'customer_name' => $row->customer->customer_full_name,
            'customer_expired' => new DateTime($row->customer->expiry_date) <= new DateTime(date('y-m-d')) ? true : false,
            'customer_phone' => $row->customer->customer_contact_number,
            'point' => $row->transaction_point,
            'offer_details' => $row->offer['offer_description'] ?? 'Discount',
            'posted_on' => $row->posted_on,
            'partner_active' => $row->branch->info->account->active,
            'branch_active' => $row->branch->active,
            'partner_expiry' => $row->branch->info->expiry_date,
            'branch_user_id' => $row->branch_user_id,
            'transaction_request_id' => $row->transaction_request_id,
            'delivery_type' => $row->customer->cardDeliveries->last()->delivery_type ?? null,
            'ssl_tran_date' => $row->customer->cardDeliveries->last()->sslTransaction->tran_date ?? date('Y-m-d'),
            'platform' => $row->transactionRequest != null ? $row->transactionRequest->platform : null,
            'transaction_platform' => $row->platform,
        ];
    }

    //function to show all transactions
    public function AllTransactions($status)
    {
        if ($status == 'deleted') {
            $transactions = TransactionTable::onlyTrashed()
                ->with('branch.info.account', 'customer.cardDeliveries.sslTransaction', 'offer', 'transactionRequest')
                ->orderBy('posted_on', 'DESC')->get();
            $tab_title = 'Deleted Transactions';
        } else {
            $transactions = TransactionTable::with('branch.info.account', 'customer.cardDeliveries.sslTransaction', 'offer', 'transactionRequest')
                ->orderBy('posted_on', 'DESC')->get();
            $tab_title = 'Active Partners';
        }

        $transactions = collect($transactions)->where('offer.selling_point', null);
        if ($status == 'active' || $status == 'deleted') {
            $allTransactions = $transactions->map(function ($row) {
                if ($row->branch->info->account->active == 1) {
                    if (! empty($row->transactionRequest)) {
                        if ($row->transactionRequest['status'] == 1) {
                            return $this->getSingleTranInfo($row);
                        }
                    } else {
                        return $this->getSingleTranInfo($row);
                    }
                }
            })->filter();
        } elseif ($status == 'inactive') {
            $allTransactions = $transactions->map(function ($row) {
                if ($row->branch->info->account->active == 0 && $row->transactionRequest['status'] == 1) {
                    if (! empty($row->transactionRequest)) {
                        if ($row->transactionRequest['status'] == 1) {
                            return $this->getSingleTranInfo($row);
                        }
                    } else {
                        return $this->getSingleTranInfo($row);
                    }
                }
            })->filter();
            $tab_title = 'Inactive Partners';
        } elseif ($status == 'expired') {
            $today = date('Y-m-d H:i:s');
            $allTransactions = $transactions->map(function ($row) use ($today) {
//                if ($row->branch->info->expiry_date < $today && $row->transactionRequest['status'] == 1) {
                if ($row->branch->info->expiry_date < $today) {
                    if (! empty($row->transactionRequest)) {
                        if ($row->transactionRequest['status'] == 1) {
                            return $this->getSingleTranInfo($row);
                        }
                    } else {
                        return $this->getSingleTranInfo($row);
                    }
                }
            })->filter();
            $tab_title = 'Expired Partners';
        } else {
            $allTransactions = $transactions->map(function ($row) {
                if (! empty($row->transactionRequest)) {
                    if ($row->transactionRequest['status'] == 1) {
                        return $this->getSingleTranInfo($row);
                    }
                } else {
                    return $this->getSingleTranInfo($row);
                }
            });
            $tab_title = 'All Partners';
        }

//        //custom pagination to apply on an array variable
//        $currentPage = LengthAwarePaginator::resolveCurrentPage();
//        $col = new Collection($allTransactions);
//        $perPage = 500;
//        $currentPageSearchResults = $col->slice(($currentPage - 1) * $perPage, $perPage)->all();
//        $allTransactions = new LengthAwarePaginator($currentPageSearchResults, count($col), $perPage, $currentPage,['path' => LengthAwarePaginator::resolveCurrentPath()] );
//        //custom pagination ends
        return view('admin.production.all-transactions', compact('allTransactions', 'tab_title'));
    }

    //function to show all payment for coupon
    public function couponPayment()
    {
        $allBranches = PartnerBranch::with('info', 'couponPayment')->get();
        //get partner with at least 1 branch and active status 1
        $paymentInfo = [];
        for ($i = 0; $i < count($allBranches); $i++) {
            if ($allBranches[$i]->couponPayment != null) {
                $paymentInfo[$i] = $allBranches[$i];
            }
        }

        return view('admin.production.coupon_payment', compact('paymentInfo'));
    }

    //function to update coupon payment data in DB
    public function payPartnerForCoupon(Request $request)
    {
        $branch_id = $request->input('branch_id');
        $totalAmount = $request->input('totalDue');
        $prev_paid = $request->input('prevPaid');
        $paid_amount = $request->input('paid');

        if ($paid_amount == $totalAmount) { //if someone pay exact amount
            $due = $paid = $totalAmount;
            $total_due = $totalAmount - $paid_amount;
        } elseif ($paid_amount < $totalAmount) {
            $total_paid = $prev_paid + $paid_amount;
            if ($total_paid == $totalAmount) { //if someone pay full amount with previous payment
                $due = $paid = $totalAmount;
                $total_due = $totalAmount - $total_paid;
            } elseif ($total_paid < $totalAmount) { //if someone pay less than due
                $due = $totalAmount;
                $paid = $total_paid;
                $total_due = $totalAmount - $total_paid;
            } else { //if someone pay more
                $result['branch_id'] = $branch_id;
                $result['status'] = false;
                $result['due'] = 'undefined';
                $result['paid'] = 'undefined';

                return Response::json($result);
            }
        } else { //if someone pay more
            $result['branch_id'] = $branch_id;
            $result['status'] = false;
            $result['due'] = 'undefined';
            $result['paid'] = 'undefined';

            return Response::json($result);
        }

        //check if this branch already exists or not in payment table
        $exists = RbdCouponPayment::where('branch_id', $branch_id)->count();
        if ($exists > 0) { //if exists then update
            RbdCouponPayment::where('branch_id', $branch_id)->update([
                'total_amount' => $due,
                'paid_amount' => $paid,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        } else { //if doesn't exist then insert
            RbdCouponPayment::insert([
                'branch_id' => $branch_id,
                'total_amount' => $due,
                'paid_amount' => $paid,
            ]);
        }
        $posted_on = date('Y-M-d H:i:s');
        $created = \Carbon\Carbon::createFromTimeStamp(strtotime($posted_on));

        $result['branch_id'] = $branch_id;
        $result['status'] = true;
        $result['due'] = $due;
        $result['paid'] = $paid;
        $result['last_paid'] = date_format($created, 'd-m-y h:i A');
        $result['total_due'] = $total_due;

        return Response::json($result);
    }

    public function cardUsers()
    {
//        $profileInfo = CustomerInfo::with('account', 'customerHistory.sellerInfo',
//            'customerLastActivitySession', 'customerReferrer', 'latestSSLTransaction')->get();
        $profileInfo = CustomerInfo::with('account', 'customerLastActivitySession', 'customerReferrer',
            'latestSSLTransaction')->get();
        $profileInfo = collect($profileInfo)->sortByDesc(function ($item) {
            if ($item->latestSSLTransaction) {
                if ((new functionController2())->daysRemaining($item->latestSSLTransaction->tran_date)
                    > (new functionController2())->daysRemaining($item->member_since)) {
                    return $item->latestSSLTransaction->tran_date;
                } else {
                    return $item->member_since;
                }
            } else {
                return $item->member_since;
            }
        });
        $emails_to_print = $profileInfo->pluck('customer_email');
        $profileInfo = (new functionController2())->getPaginatedData($profileInfo, 10);
        $customer_type = '';
        $tab_title = 'All Members';

        return view('admin.production.allCustomers', compact('profileInfo', 'customer_type', 'tab_title', 'emails_to_print'));
    }

    //function for showing all card holders info
    public function cardHolders()
    {
//        $profileInfo = CustomerInfo::with('account', 'customerHistory.sellerInfo',
//            'customerLastActivitySession', 'customerReferrer', 'latestSSLTransaction')
//            ->where('expiry_date', '>', date('Y-m-d'))->get();
        $profileInfo = CustomerInfo::with('account', 'customerLastActivitySession', 'customerReferrer',
            'latestSSLTransaction')
            ->where('expiry_date', '>', date('Y-m-d'))->get();
        $profileInfo = collect($profileInfo)->sortByDesc('latestSSLTransaction.tran_date');
        $profileInfo = collect($profileInfo)->where('customerHistory.type', CustomerType::card_holder);
        $emails_to_print = $profileInfo->pluck('customer_email');
        $profileInfo = (new functionController2())->getPaginatedData($profileInfo, 10);
        $customer_type = '(Only card holders)';
        $tab_title = 'Premium Members';

        return view('admin.production.allCustomers', compact('profileInfo', 'customer_type', 'tab_title', 'emails_to_print'));
    }

    //function for showing all card holders info
    public function upgradedMembers()
    {
        $profileInfo = CustomerInfo::with('account', 'customerHistory.sellerInfo',
            'customerLastActivitySession', 'customerReferrer', 'latestSSLTransaction')
            ->where('expiry_date', '>', date('Y-m-d'))->get();
        $profileInfo = collect($profileInfo)->sortByDesc('latestSSLTransaction.tran_date');
        $profileInfo = collect($profileInfo)->where('customerHistory.type', CustomerType::card_holder);
        $profileInfo = collect($profileInfo)->where('latestSSLTransaction.cardDelivery.delivery_type', DeliveryType::home_delivery);
        $profileInfo = $profileInfo->reject(function ($item) {
            return $item->isUpgrade() != true;
        });
        $emails_to_print = $profileInfo->pluck('customer_email');
        $profileInfo = (new functionController2())->getPaginatedData($profileInfo, 10);
        $customer_type = '(Only card holders)';
        $tab_title = 'Upgraded Members';

        return view('admin.production.allCustomers', compact('profileInfo', 'customer_type', 'tab_title', 'emails_to_print'));
    }

    //function for showing all card holders info
    public function renewedMembers()
    {
        $profileInfo = CustomerInfo::with('account', 'customerHistory.sellerInfo',
            'customerLastActivitySession', 'customerReferrer', 'latestSSLTransaction')
            ->where('expiry_date', '>', date('Y-m-d'))->get();
        $profileInfo = collect($profileInfo)->sortByDesc('latestSSLTransaction.tran_date');
        $profileInfo = collect($profileInfo)->where('customerHistory.type', CustomerType::card_holder);
        $profileInfo = collect($profileInfo)->where('latestSSLTransaction.cardDelivery.delivery_type', DeliveryType::renew);
        $emails_to_print = $profileInfo->pluck('customer_email');
        $profileInfo = (new functionController2())->getPaginatedData($profileInfo, 10);
        $customer_type = '(Only card holders)';
        $tab_title = 'Renewed Members';

        return view('admin.production.allCustomers', compact('profileInfo', 'customer_type', 'tab_title', 'emails_to_print'));
    }

    //function for showing all card holders info
    public function getAllTrialUsers()
    {
        $profileInfo = CustomerInfo::with('account', 'customerHistory.sellerInfo',
            'customerLastActivitySession', 'customerReferrer', 'latestSSLTransaction')->get();
        $profileInfo = collect($profileInfo)->sortByDesc('latestSSLTransaction.tran_date');
        $profileInfo = collect($profileInfo)->where('customerHistory.type', CustomerType::trial_user)
            ->where('expiry_date', '>', date('Y-m-d'));
        $emails_to_print = $profileInfo->pluck('customer_email');
        $profileInfo = (new functionController2())->getPaginatedData($profileInfo, 10);
        $customer_type = '(Trial users)';
        $tab_title = 'Trial Members';

        return view('admin.production.allCustomers', compact('profileInfo', 'customer_type', 'tab_title', 'emails_to_print'));
    }

    //function to show all guest customers
    public function guestCustomers()
    {
        $profileInfo = CustomerInfo::with('account', 'customerHistory.sellerInfo',
            'customerLastActivitySession', 'customerReferrer', 'latestSSLTransaction')->where('customer_type', 3)->get();
        $profileInfo = collect($profileInfo)->sortByDesc('member_since');
        $emails_to_print = $profileInfo->pluck('customer_email');
        $profileInfo = (new functionController2())->getPaginatedData($profileInfo, 10);
        $customer_type = '(Guest)';
        $tab_title = 'Guest';

        return view('admin.production.allCustomers', compact('profileInfo', 'customer_type', 'tab_title', 'emails_to_print'));
    }

    //function to show all spot purchased customers
    public function spotCustomers()
    {
        $profileInfo = CustomerInfo::with('account', 'customerHistory.sellerInfo',
            'customerLastActivitySession', 'customerReferrer', 'latestSSLTransaction')->get();
        $profileInfo = collect($profileInfo)->sortByDesc('latestSSLTransaction.tran_date');
        $profileInfo = collect($profileInfo)->where('latestSSLTransaction.cardDelivery.delivery_type', DeliveryType::spot_delivery);
        $emails_to_print = $profileInfo->pluck('customer_email');
        $profileInfo = (new functionController2())->getPaginatedData($profileInfo, 10);
        $customer_type = '(Spot/Manual)';
        $tab_title = 'Spot Purchase/Manual Registration';

        return view('admin.production.allCustomers', compact('profileInfo', 'customer_type', 'tab_title', 'emails_to_print'));
    }

    //function to show all expired customers
    public function expiredTrialCustomers()
    {
        $profileInfo = CustomerInfo::with('account', 'customerHistory.sellerInfo',
            'customerLastActivitySession', 'customerReferrer', 'latestSSLTransaction')
            ->where('customer_type', '!=', 3)->get();
        $profileInfo = collect($profileInfo)->sortByDesc('latestSSLTransaction.tran_date');
        $profileInfo = collect($profileInfo)
            ->where('expiry_date', '<=', date('Y-m-d'))
            ->where('latestSSLTransaction.cardDelivery.delivery_type', DeliveryType::virtual_card)
            ->where('customerHistory', '!=', null);
        $emails_to_print = $profileInfo->pluck('customer_email');
        $profileInfo = (new functionController2())->getPaginatedData($profileInfo, 10);
        $customer_type = '(Expired User)';
        $tab_title = 'Expired Trial Members';

        return view('admin.production.allCustomers', compact('profileInfo', 'customer_type', 'tab_title', 'emails_to_print'));
    }

    //function to show all expired customers
    public function expiredPremiumCustomers()
    {
        $profileInfo = CustomerInfo::with('account', 'customerHistory.sellerInfo',
            'customerLastActivitySession', 'customerReferrer', 'latestSSLTransaction')
            ->where('customer_type', '!=', 3)->get();
        $profileInfo = collect($profileInfo)->sortByDesc('latestSSLTransaction.tran_date');
        $profileInfo = collect($profileInfo)
            ->where('expiry_date', '<=', date('Y-m-d'))
            ->where('latestSSLTransaction.cardDelivery.delivery_type', '!=', DeliveryType::virtual_card)
            ->where('customerHistory', '!=', null);
        $emails_to_print = $profileInfo->pluck('customer_email');
        $profileInfo = (new functionController2())->getPaginatedData($profileInfo, 10);
        $customer_type = '(Expired User)';
        $tab_title = 'Expired Premium Members';

        return view('admin.production.allCustomers', compact('profileInfo', 'customer_type', 'tab_title', 'emails_to_print'));
    }

    //function to show all expiring soon customers
    public function expiringCustomers()
    {
        $profileInfo = CustomerInfo::with('account', 'customerHistory.sellerInfo',
            'customerLastActivitySession', 'customerReferrer', 'latestSSLTransaction')->get();
        $profileInfo = collect($profileInfo)->sortByDesc('latestSSLTransaction.tran_date');
        $profileInfo = collect($profileInfo)
            ->where('expiry_date', '>', date('Y-m-d'))
            ->where('expiry_date', '<=', date('Y-m-d', strtotime(date('Y-m-d').' + 10 days')))
            ->where('customerHistory', '!=', null);
        $emails_to_print = $profileInfo->pluck('customer_email');
        $profileInfo = (new functionController2())->getPaginatedData($profileInfo, 10);
        $customer_type = '(Expired User)';
        $tab_title = 'Expiring Soon';

        return view('admin.production.allCustomers', compact('profileInfo', 'customer_type', 'tab_title', 'emails_to_print'));
    }

    //get members of today & yesterday
    public function recentMembershipOrders()
    {
        $profileInfo = CustomerInfo::with('account', 'customerHistory.sellerInfo',
            'customerLastActivitySession', 'customerReferrer', 'latestSSLTransaction')
            ->where('customer_type', 2)->get();
        $profileInfo = collect($profileInfo)->where('latestSSLTransaction.tran_date', '>=', Carbon::yesterday())
            ->sortByDesc('latestSSLTransaction.tran_date');
        $emails_to_print = $profileInfo->pluck('customer_email');
        $profileInfo = (new functionController2())->getPaginatedData($profileInfo, 10);
        $customer_type = '';
        $tab_title = 'Recent Members';

        return view('admin.production.allCustomers', compact('profileInfo', 'customer_type', 'tab_title', 'emails_to_print'));
    }

    public function getSellerInfo($profileInfo)
    {
        $current_page = $profileInfo->currentPage();
        $per_page = $profileInfo->perPage();
        $i = ($current_page * $per_page) - $per_page + 1;
        foreach ($profileInfo as $list) {
            $list->serial = $i;
            $i++;
        }
        $i = 0;
        foreach ($profileInfo as $info) {
            $card_delivery = CardDelivery::where('customer_id', $info->customer_id)->orderBy('id', 'DESC')->first();
            $profileInfo[$i]->delivery_type = $card_delivery->delivery_type;
            if ($card_delivery->delivery_type == 9) {
                $seller = AssignedCard::where('card_number', $info->customer_id)->with('seller.info')->first();
                $profileInfo[$i]->seller_name = $seller['seller']['info']['first_name'].' '.$seller['seller']['info']['last_name'];
                $profileInfo[$i]->seller_phone = $seller['seller']['phone'];
            }
            $i++;
        }

        return $profileInfo;
    }

    //function to show all active customers
    public function activeCustomers()
    {
//        $profileInfo = CustomerInfo::with('account', 'customerHistory.sellerInfo', 'latestCheckout',
//            'customerLastActivitySession', 'customerReferrer', 'latestSSLTransaction')->get();
        $profileInfo = CustomerInfo::with('account', 'latestCheckout',
            'customerLastActivitySession', 'customerReferrer', 'latestSSLTransaction')->get();
        $profileInfo = collect($profileInfo)->sortByDesc('latestSSLTransaction.tran_date');
        $profileInfo = collect($profileInfo)->where('latestCheckout', '!=', null);
        $emails_to_print = $profileInfo->pluck('customer_email');
        $profileInfo = (new functionController2())->getPaginatedData($profileInfo, 10);
        $customer_type = '(Active)';
        $tab_title = 'Active Members';

        return view('admin.production.allCustomers', compact('profileInfo', 'customer_type', 'tab_title', 'emails_to_print'));
    }

    //function to show all inactive customers
    public function inactiveTrialCustomers()
    {
        $profileInfo = CustomerInfo::with('account', 'customerHistory.sellerInfo', 'latestCheckout',
            'customerLastActivitySession', 'customerReferrer', 'latestSSLTransaction')->where('customer_type', '!=', 3)->get();
        $profileInfo = collect($profileInfo)->sortByDesc('latestSSLTransaction.tran_date');
        $profileInfo = collect($profileInfo)->where('latestCheckout', null)
            ->where('latestSSLTransaction.cardDelivery.delivery_type', DeliveryType::virtual_card)
            ->where('expiry_date', '>', date('Y-m-d'));
        $emails_to_print = $profileInfo->pluck('customer_email');
        $profileInfo = (new functionController2())->getPaginatedData($profileInfo, 10);
        $customer_type = '(Inactive)';
        $tab_title = 'Inactive Trial Members';

        return view('admin.production.allCustomers', compact('profileInfo', 'customer_type', 'tab_title', 'emails_to_print'));
    }

    public function inactivePremiumCustomers()
    {
//        $profileInfo = CustomerInfo::with('account', 'customerHistory.sellerInfo', 'latestCheckout',
//            'customerLastActivitySession', 'customerReferrer', 'latestSSLTransaction')->where('customer_type', '!=', 3)->get();
        $profileInfo = CustomerInfo::with('account', 'latestCheckout', 'customerLastActivitySession',
            'customerReferrer', 'latestSSLTransaction')->where('customer_type', '!=', 3)->get();
        $profileInfo = collect($profileInfo)->sortByDesc('latestSSLTransaction.tran_date');
        $profileInfo = collect($profileInfo)->where('latestCheckout', null)
            ->where('latestSSLTransaction.cardDelivery.delivery_type', '!=', DeliveryType::virtual_card);
        $emails_to_print = $profileInfo->pluck('customer_email');
        $profileInfo = (new functionController2())->getPaginatedData($profileInfo, 10);
        $customer_type = '(Inactive)';
        $tab_title = 'Inactive Premium Members';

        return view('admin.production.allCustomers', compact('profileInfo', 'customer_type', 'tab_title', 'emails_to_print'));
    }

    //function to show all influencer
    public function influencerCustomers()
    {
        //get all customers info for admin panel
        $profileInfo = DB::table('customer_info as ci')
            ->join('customer_account as ca', 'ca.customer_id', '=', 'ci.customer_id')
            ->join('card_promo as cp', 'cp.influencer_id', '=', 'ci.customer_id')
            ->select(
                'ci.*',
                'ca.platform',
                'cp.id as promo_id',
                'cp.code as promo_code',
                'cp.active as promo_active',
                'cp.expiry_date as promo_expiry'
            )
            ->orderBy('ci.approve_date', 'DESC')
            ->orderBy('ci.id', 'DESC')
            ->get();

        $i = 0;
        foreach ($profileInfo as $list) {
            $exp_status = (new functionController2())->getExpStatusOfCustomer($list->expiry_date);
            $profileInfo[$i]->exp_status = $exp_status;
            $profileInfo[$i]->referrar = (new functionController2)->getReferrar($list->customer_id);
            //get tran date
            $stt = SslTransactionTable::where([['customer_id', $list->customer_id], ['status', 1]])->orderBy('id', 'desc')->first();
            $profileInfo[$i]->tran_date = $stt->tran_date;
            $promo_used = DB::table('customer_card_promo_usage as ccpu')
                ->join('customer_info as ci', 'ci.customer_id', '=', 'ccpu.customer_id')
                ->where('ccpu.promo_id', $list->promo_id)
                ->get();

            if (count($promo_used) == 0) {
                $list->total_promo_used = 0;
                $list->promo_used['platinum'] = 0;
            } else {
                $list->total_promo_used = count($promo_used);
                $list->promo_used['platinum'] = 0;
                foreach ($promo_used as $item) {
                    if ($item->customer_type == 2) {
                        $list->promo_used['platinum'] += 1;
                    }
                }
            }
            $list->serial = $i;
            $i++;
        }
        $all_clients = B2b2cInfo::orderBy('id', 'DESC')->get();
        $emails_to_print = $profileInfo->pluck('customer_email');
        $customer_type = '(Influencer)';
        $tab_title = 'Influencers with promo codes';

        return view('admin.production.allInfluencer', compact('profileInfo', 'all_clients', 'customer_type', 'tab_title', 'emails_to_print'));
    }

    //function to show influencer payment page
    public function influencerPayment()
    {
        //get influencer payment info
        $data = InfluencerPayment::with('userInfo')->get();
        $paymentInfo = $data->map(function ($item) {
            return [
                'id' => $item['id'],
                'influencer_id' => $item['influencer_id'],
                'influencer_name' => $item['userInfo']['customer_full_name'],
                'total_amount' => $item['total_amount'],
                'paid_amount' => $item['paid_amount'],
                'updated_at' => $item['updated_at'],
            ];
        });

        return view('admin.production.influencerPayment', compact('paymentInfo'));
    }

    //function to update influencer payment info
    public function payInfluencer(Request $request)
    {
        $influencer_id = $request->input('influencer_id');
        $totalAmount = $request->input('totalDue');
        $prev_paid = $request->input('prevPaid');
        $paid_amount = $request->input('paid');

        if ($paid_amount == $totalAmount) { //if someone pay exact amount
            $due = $paid = $totalAmount;
            $total_due = $totalAmount - $paid_amount;
        } elseif ($paid_amount < $totalAmount) {
            $total_paid = $prev_paid + $paid_amount;
            if ($total_paid == $totalAmount) { //if someone pay full amount with previous payment
                $due = $paid = $totalAmount;
                $total_due = $totalAmount - $total_paid;
            } elseif ($total_paid < $totalAmount) { //if someone pay less than due
                $due = $totalAmount;
                $paid = $total_paid;
                $total_due = $totalAmount - $total_paid;
            } else { //if someone pay more
                $result['influencer_id'] = $influencer_id;
                $result['status'] = false;
                $result['due'] = 'undefined';
                $result['paid'] = 'undefined';

                return Response::json($result);
            }
        } else { //if someone pay more
            $result['influencer_id'] = $influencer_id;
            $result['status'] = false;
            $result['due'] = 'undefined';
            $result['paid'] = 'undefined';

            return Response::json($result);
        }

        //check if this influencer already exists or not in payment table
        $exists = InfluencerPayment::where('influencer_id', $influencer_id)->count();
        if ($exists > 0) { //if exists then update
            InfluencerPayment::where('influencer_id', $influencer_id)->update([
                'total_amount' => $due,
                'paid_amount' => $paid,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        } else { //if doesn't exist then insert
            InfluencerPayment::insert([
                'influencer_id' => $influencer_id,
                'total_amount' => $due,
                'paid_amount' => $paid,
            ]);
        }
        $posted_on = date('Y-M-d H:i:s');
        $created = \Carbon\Carbon::createFromTimeStamp(strtotime($posted_on));

        $result['influencer_id'] = $influencer_id;
        $result['status'] = true;
        $result['due'] = $due;
        $result['paid'] = $paid;
        $result['last_paid'] = date_format($created, 'd-m-y h:i A');
        $result['total_due'] = $total_due;

        return Response::json($result);
    }

    //function to show all B2B2C customers
    public function b2b2cCustomers($client_id)
    {
        //get all customers info for admin panel
        $profileInfo = DB::table('customer_info as ci')
            ->join('customer_account as ca', 'ca.customer_id', '=', 'ci.customer_id')
            ->join('b2b2c_user as b2u', 'b2u.customer_id', '=', 'ci.customer_id')
            ->select('ci.*', 'ca.moderator_status', 'ca.isSuspended')
            ->where('b2u.b2b2c_id', $client_id)
            ->orderBy('ci.approve_date', 'DESC')
            ->orderBy('ci.id', 'DESC')
            ->paginate(20);
        $current_page = $profileInfo->currentPage();
        $per_page = $profileInfo->perPage();
        $i = ($current_page * $per_page) - $per_page + 1;
        foreach ($profileInfo as $list) {
            $list->serial = $i;
            $i++;
        }
        $i = 0;
        foreach ($profileInfo as $info) {
            $card_delivery = CardDelivery::where('customer_id', $info->customer_id)->orderBy('id', 'DESC')->first();
            $profileInfo[$i]->delivery_type = $card_delivery->delivery_type;
            $seller = CustomerHistory::where('customer_id', $info->customer_id)->orderBy('id', 'DESC')->first();
            if ($seller->seller_id) {
                $profileInfo[$i]->seller_name = $seller->sellerInfo->first_name.' '.$seller->sellerInfo->last_name;
                $profileInfo[$i]->seller_phone = $seller->sellerInfo->account->phone;
            }
            $i++;
        }
        $all_clients = B2b2cInfo::orderBy('id', 'DESC')->get();
        $selected_client = B2b2cInfo::orderBy('id', 'DESC')->where('id', $client_id)->first();
        $customer_type = '(B2B2C)';

        return view('admin.production.allCustomers', compact(
            'profileInfo',
            'all_clients',
            'selected_client',
            'customer_type'
        ));
    }

    //============function for showing user searched by 'ID'==================
    public function searchCustomerByKey(Request $request)
    {
        $search_keyword = $request->get('customerSearchKey');
        $keyword = explode('=>', $search_keyword);

        if (count($keyword) != 2) {
            return redirect()->back()->with('try_again', 'Please select user from dropdown.');
        }
        $profileInfo = CustomerInfo::with('account', 'customerHistory.sellerInfo',
            'customerLastActivitySession', 'customerReferrer', 'latestSSLTransaction')->where('customer_contact_number', $keyword[1])->get();
        $profileInfo = collect($profileInfo)->sortByDesc('latestSSLTransaction.tran_date');
        $emails_to_print = $profileInfo->pluck('customer_email');
        $profileInfo = (new functionController2())->getPaginatedData($profileInfo, 10);
        $customer_type = $profileInfo[0]->customer_type == 3 ? '(Guest)' : '';
        $tab_title = '';

        return view('admin.production.allCustomers', compact('profileInfo', 'customer_type', 'tab_title', 'emails_to_print'));
    }

    public function searchCustomerByKeyForPurchaseHistory(Request $request)
    {
        $search_keyword = $request->get('customerSearchKey');
        $keyword = explode('=>', $search_keyword);

        $history = CustomerHistory::with('customerInfo', 'sslInfo.cardDelivery', 'sellerInfo')
            ->withCount('customerPaymentHistory')->get();
        $history = collect($history)
            ->where('sslInfo.cardDelivery.delivery_type', '!=', DeliveryType::virtual_card)
            ->where('sslInfo.cardDelivery.delivery_type', '!=', DeliveryType::guest_user)
            ->where('sslInfo.cardDelivery.delivery_type', '!=', DeliveryType::b2b2c_user)
            ->where('customerInfo.customer_contact_number', $keyword[1]);

        $history = collect($history)->sortByDesc('sslInfo.tran_date');
        $data = (new functionController2())->getPaginatedData($history, 20);
        $tab_title = 'All';

        return view('admin.production.PurchaseHistory.all', compact('data', 'tab_title'));
    }

    //function for showing all customers info
    public function smsExistingCustomers($type = null)
    {
        if ($type) {
            //get all customers info for admin panel
            if ($type == 'guests') {
                $profileInfo = DB::select('select ci.*, ch.type as user_type, ca.moderator_status
                                            from customer_info as ci
                                                     join customer_account ca on ci.customer_id = ca.customer_id
                                                     left join customer_history ch on ci.customer_id = ch.customer_id
                                            where ci.customer_type = 3');
            } else {
                $profileInfo = DB::select("SELECT ca.moderator_status, ci.* , ch.type as user_type
                                FROM customer_history as ch
                                     join customer_account as ca on ch.customer_id = ca.customer_id
                                     join customer_info as ci on ch.customer_id = ci.customer_id
                                WHERE ch.id IN (
                                    SELECT MAX(id)
                                    FROM customer_history
                                    GROUP BY customer_id
                                ) and ch.type = '$type'");
            }
        } else {
            //get all customers info for admin panel
            $profileInfo = DB::select('select ci.*, ch.type as user_type, ca.moderator_status
                                    from customer_info as ci
                                     join customer_account ca on ci.customer_id = ca.customer_id
                                     left join customer_history ch on ci.customer_id = ch.customer_id
                                      AND ch.id = 
                                        (
                                           SELECT MAX(id) 
                                           FROM customer_history c 
                                           WHERE c.customer_id = ch.customer_id
                                        )');
        }
        //custom pagination to apply on an array variable
//        $currentPage = LengthAwarePaginator::resolveCurrentPage();
//        $col = new Collection($profileInfo);
//        $perPage = 20;
//        $currentPageSearchResults = $col->slice(($currentPage - 1) * $perPage, $perPage)->all();
//        $profileInfo = new LengthAwarePaginator($currentPageSearchResults, count($col), $perPage, $currentPage, ['path' => LengthAwarePaginator::resolveCurrentPath()]);
//        //custom pagination ends
//
//        $current_page = $profileInfo->currentPage();
//        $per_page = $profileInfo->perPage();
//        $i = ($current_page * $per_page) - $per_page + 1;
//        foreach ($profileInfo as $list) {
//            $list->serial = $i;
//            $i++;
//        }

        $profileInfo = (new functionController2())->getPaginatedData($profileInfo, 20);

        return view('admin.production.sms.existing_customers_sms', compact('profileInfo'));
    }

    //function for showing all scanner info to send sms
    public function smsExistingScanners()
    {
        //get all scanners info for admin panel
        $profileInfo = DB::table('branch_user as bu')
            ->join('branch_scanner as bs', 'bs.branch_user_id', '=', 'bu.id')
            ->join('partner_branch as pb', 'pb.id', '=', 'bs.branch_id')
            ->join('partner_info as pi', 'pi.partner_account_id', '=', 'pb.partner_account_id')
            ->select('bu.phone', 'bs.full_name', 'pi.partner_name', 'pb.partner_area')
            ->orderBy('bu.id', 'DESC')
            ->get();

        return view('admin.production.sms.existing_scanners_sms', compact('profileInfo'));
    }

    //function for showing all customers info
    public function allCODCustomers()
    {
        //payable amount
        $card_prices = AllAmounts::all();
        $gold_card_price = $card_prices[0]->price;
        $platinum_card_price = $card_prices[1]->price;
        $delivery_charge = $card_prices[3]->price;
        $customization_charge = $card_prices[4]->price;
        $lost_card_charge = $card_prices[5]->price;

        $delivery_types = [3, 4, 6, 7];
        //get all customers info for admin panel
        $profileInfo = DB::table('info_at_buy_card as temp')
            ->join('customer_account as ca', 'ca.customer_username', '=', 'temp.customer_username')
            ->join('customer_info as ci', 'ci.customer_id', '=', 'ca.customer_id')
            ->select(
                'temp.customer_id',
                'temp.paid_amount',
                'temp.delivery_type',
                'temp.customer_type',
                'temp.card_promo_id',
                'temp.shipping_address',
                'temp.order_date',
                'temp.moderator_status',
                'temp.member_since',
                'ci.customer_profile_image',
                'ci.customer_full_name',
                'ci.customer_email',
                'ci.customer_contact_number'
            )
            ->whereIn('temp.delivery_type', $delivery_types)
            ->orderBy('temp.id', 'DESC')
            ->paginate(20);

        $i = 0;
        foreach ($profileInfo as $customerInfo) {
            //Fetch card price
            if ($profileInfo[$i]->paid_amount != null) {
                $profileInfo[$i]->total_payable = $profileInfo[$i]->paid_amount;
            } else {
                $promo_discount = 0;
                if ($customerInfo->delivery_type == 3 || $customerInfo->delivery_type == 4) {
                    if ($customerInfo->customer_type == 1) {
                        $actual_card_price = $gold_card_price;
                    } elseif ($customerInfo->customer_type == 2) {
                        $actual_card_price = $platinum_card_price;
                    }
                    if ($customerInfo->card_promo_id != 0) {
                        $promo_details = CardPromoCodes::where('id', $customerInfo->card_promo_id)->first();
                        if ($promo_details->type == 1) {
                            $promo_discount = $promo_details->flat_rate;
                        } elseif ($promo_details->type == 2) {
                            $promo_discount = round(($actual_card_price * $promo_details->percentage) / 100);
                        }
                    }
                    $total_card_price = $actual_card_price - $promo_discount + $delivery_charge;
                } elseif ($customerInfo->delivery_type == 6) {
                    $total_card_price = $lost_card_charge + $delivery_charge;
                } elseif ($customerInfo->delivery_type == 7) {
                    $total_card_price = $lost_card_charge + $customization_charge + $delivery_charge;
                }

                $profileInfo[$i]->total_payable = $total_card_price;
            }
            $i++;
        }
        $current_page = $profileInfo->currentPage();
        $per_page = $profileInfo->perPage();
        $i = ($current_page * $per_page) - $per_page + 1;
        foreach ($profileInfo as $list) {
            $list->serial = $i;
            $i++;
        }

        return view('admin.production.cod_customers', compact('profileInfo'));
    }

    //============function for edit COD customer view==================
    public function editCODUser($id)
    {
        $profileInfo = DB::table('info_at_buy_card')
            ->where('customer_id', $id)
            ->get();
        $profileInfo = json_decode(json_encode($profileInfo), true);
        $profileInfo = $profileInfo[0];

        return view('admin.production.edit_cod_user', compact('profileInfo'));
    }

    //============function for insert updated data in Info_At_Buy_Card table for pre order==================
    public function CODEditDone(Request $request, $id)
    {
        if ($request->get('customer_id') == $id) {
            $this->validate($request, [
                'customer_id' => 'required',
                'shipping_address' => 'required',
            ]);
        } elseif ($request->get('customer_id') != $id) {
            $this->validate($request, [
                'customer_id' => 'required|unique:customer_account,customer_id|unique:info_at_buy_card,customer_id',
                'shipping_address' => 'required',
            ]);
        }
        $request->flashOnly(['customer_id', 'shipping_address']);

        //get data from edit form
        $customerId = $request->get('customer_id');
        $customerShipAddress = $request->get('shipping_address');
        $customerType = $request->get('customer_type');
        DB::table('info_at_buy_card')
            ->where('customer_id', $id)
            ->update([
                'customer_id' => $customerId,
                'customer_type' => $customerType,
                'shipping_address' => $customerShipAddress,
                'moderator_status' => ($request->get('is_approve')) == 'on' ? 1 : 2,
            ]);

        return redirect('/allCOD')->with('info updated', 'Customer info updated!');
    }

    //function for showing all customers info of Temporary buy card
    public function allTempCustomers()
    {
        //get all customers info for admin panel
        $profileInfo = DB::table('info_at_buy_card')
            ->Where('delivery_type', '!=', '3')
            ->Where('delivery_type', '!=', '4')
            ->orderBy('id', 'DESC')
            ->paginate(20);

        $current_page = $profileInfo->currentPage();
        $per_page = $profileInfo->perPage();
        $i = ($current_page * $per_page) - $per_page + 1;
        foreach ($profileInfo as $list) {
            $list->serial = $i;
            $i++;
        }

        return view('admin.production.temp_buy_customers', compact('profileInfo'));
    }

    //function to delete Temporary buy card info
    public function deleteTempCustomer($customerId)
    {
        $types = [DeliveryType::cod, DeliveryType::guest_user, DeliveryType::lost_card_without_customization, DeliveryType::lost_card_with_customization];
        $cod_exists = DB::table('info_at_buy_card')
            ->where('customer_id', $customerId)
            ->whereIn('delivery_type', $types)
            ->count();
        if ($cod_exists > 0) {
            return Redirect()->back()->with('cod_exists', 'First approve from COD then delete');
        } else {
            DB::table('info_at_buy_card')
                ->where('customer_id', $customerId)
                ->delete();

            return redirect('/tempBuyCard')->with('delete customer', 'One customer deleted.');
        }
    }

    public function searchTempCustomer(Request $request)
    {
        $search_keyword = $request->get('customerSearchKey');
        $keyword = explode('=>', $search_keyword);
        //get all customers info for admin panel
        $profileInfo = DB::table('info_at_buy_card')
            ->where('customer_email', $keyword[0])
            ->Where('delivery_type', '!=', '3')
            ->orderBy('id', 'DESC')
            ->paginate(20);

        $current_page = $profileInfo->currentPage();
        $per_page = $profileInfo->perPage();
        $i = ($current_page * $per_page) - $per_page + 1;
        foreach ($profileInfo as $list) {
            $list->serial = $i;
            $i++;
        }

        return view('admin.production.temp_buy_customers', compact('profileInfo'));
    }

    //function for showing all customers info
    public function customerByTemp(Request $request)
    {
        $keyword = $request->term;
        $customers = DB::table('info_at_buy_card')
            ->select('customer_id', 'customer_full_name', 'customer_profile_image', 'customer_email', 'customer_contact_number')
            ->where('customer_id', 'like', '%'.$keyword.'%')
            ->orWhere('customer_full_name', 'like', '%'.$keyword.'%')
            ->orWhere('customer_email', 'like', '%'.$keyword.'%')
            ->orWhere('customer_contact_number', 'like', '%'.$keyword.'%')
            ->Where('delivery_type', '!=', '3')
            ->distinct('customer_id')
            ->orderBy('id', 'DESC')
            ->get();
        if (count($customers) == 0) {
            $result[] = 'No customer found';
        } else {
            foreach ($customers as $key => $value) {
                $result[] = $value->customer_email.'=>'.$value->customer_contact_number;
            }
        }

        return $result;
    }

    //function for showing all customers info
    public function customerByCod(Request $request)
    {
        $keyword = $request->term;
        $type = [3, 4];
        $customers = DB::table('info_at_buy_card')
            ->select('customer_id', 'customer_full_name', 'customer_profile_image', 'customer_email', 'customer_contact_number')
            ->where('customer_id', 'like', '%'.$keyword.'%')
            ->orWhere('customer_full_name', 'like', '%'.$keyword.'%')
            ->orWhere('customer_email', 'like', '%'.$keyword.'%')
            ->orWhere('customer_contact_number', 'like', '%'.$keyword.'%')
            ->whereIn('delivery_type', $type)
            ->orderBy('id', 'DESC')
            ->get();
        if (count($customers) == 0) {
            $result[] = 'No customer found';
        } else {
            foreach ($customers as $key => $value) {
                $result[] = $value->customer_email.'=>'.$value->customer_contact_number;
            }
        }

        return $result;
    }

    public function searchCustomerByCard(Request $request)
    {
        $search_keyword = $request->get('customerSearchKey');
        $keyword = explode('=>', $search_keyword);

        //payable amount
        $card_prices = AllAmounts::all();
        $gold_card_price = $card_prices[0]->price;
        $platinum_card_price = $card_prices[1]->price;
        $delivery_charge = $card_prices[3]->price;
        $customization_charge = $card_prices[4]->price;
        $lost_card_charge = $card_prices[5]->price;

        //get all customers info for admin panel
        $profileInfo = DB::table('info_at_buy_card')
            ->where([['customer_email', $keyword[0]], ['delivery_type', '3']])
            ->orWhere([['customer_email', $keyword[0]], ['delivery_type', '4']])
            ->orWhere([['customer_email', $keyword[0]], ['delivery_type', '6']])
            ->orWhere([['customer_email', $keyword[0]], ['delivery_type', '7']])
            ->orderBy('id', 'DESC')
            ->paginate(20);

        $i = 0;
        foreach ($profileInfo as $customerInfo) {
            //Fetch card price
            if ($profileInfo[$i]->paid_amount != null) {
                $profileInfo[$i]->total_payable = $profileInfo[$i]->paid_amount;
            } else {
                $promo_discount = 0;
                if ($customerInfo->delivery_type == 3 || $customerInfo->delivery_type == 4) {
                    if ($customerInfo->customer_type == 1) {
                        $actual_card_price = $gold_card_price;
                    } elseif ($customerInfo->customer_type == 2) {
                        $actual_card_price = $platinum_card_price;
                    }
                    if ($customerInfo->card_promo_id != 0) {
                        $promo_details = CardPromoCodes::where('id', $customerInfo->card_promo_id)->first();
                        if ($promo_details->type == 1) {
                            $promo_discount = $promo_details->flat_rate;
                        } elseif ($promo_details->type == 2) {
                            $promo_discount = round(($actual_card_price * $promo_details->percentage) / 100);
                        }
                    }
                    $total_card_price = $actual_card_price - $promo_discount + $delivery_charge;
                } elseif ($customerInfo->delivery_type == 6) {
                    $total_card_price = $lost_card_charge + $delivery_charge;
                } elseif ($customerInfo->delivery_type == 7) {
                    $total_card_price = $lost_card_charge + $customization_charge + $delivery_charge;
                }

                $profileInfo[$i]->total_payable = $total_card_price;
            }
            $i++;
        }

        $current_page = $profileInfo->currentPage();
        $per_page = $profileInfo->perPage();
        $i = ($current_page * $per_page) - $per_page + 1;
        foreach ($profileInfo as $list) {
            $list->serial = $i;
            $i++;
        }

        return view('admin.production.cod_customers', compact('profileInfo'));
    }

    //function to send SMS to specific scanner
    public function sendCustomSMS(Request $request)
    {
        $language = $request->input('language');
        $phone = $request->input('phone');
        $text_message = $request->input('text_message');

//        if ($language == 'english') {
//            $stake_holder = 'RoyaltybdMasking';
//        } else {
//            $stake_holder = 'royaltybangla';
//            $text_message = strtoupper(bin2hex(iconv('UTF-8', 'UCS-2BE', $text_message)));
//        }

        if (substr($phone, 0, 4) === '+880') {
            $full_number = $phone;
        } elseif (substr($phone, 0, 1) === '0') {
            $full_number = '+88'.$phone;
        } else {
            $full_number = '+880'.$phone;
        }

//        $user = 'Royaltybd';
//        $pass = '66A6Q13d';
//        $sid = $stake_holder;
//        $url = 'http://sms.sslwireless.com/pushapi/dynamic/server.php';
//        $param = "user=$user&pass=$pass&sms[0][0]= $full_number &sms[0][1]=".urlencode($text_message)."&sms[0][2]=123456789&sid=$sid";

        $username = env('BOOMCAST_USERNAME');
        $password = env('BOOMCAST_PASSWORD');
        $url = 'http://api.boom-cast.com/boomcast/WebFramework/boomCastWebService/externalApiSendTextMessage.php';
        $param = "masking=NOMASK&userName=$username&password=$password&MsgType=TEXT&receiver=$full_number&message=".$text_message;

        $crl = curl_init();
        curl_setopt($crl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($crl, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($crl, CURLOPT_URL, $url);
        curl_setopt($crl, CURLOPT_HEADER, 0);
        curl_setopt($crl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($crl, CURLOPT_POST, 1);
        curl_setopt($crl, CURLOPT_POSTFIELDS, $param);
        $response = curl_exec($crl);
        curl_close($crl);

        $to = $phone;
        if ($request->has('user_type')) {
            $to .= ' ('.$request->input('user_type').')';
        }
        (new functionController2())->saveSentMessage(
            SentMessageType::sms,
            null,
            $request->input('text_message'),
            $to,
            $language,
            null
        );

        $response = json_decode($response, true)[0];
        if ($response['success'] == 1) {
            return Redirect()->back()->with('sms successful', 'Send SMS successful');
        } else {
            return Redirect()->back()->with('sms failed', 'Failed : Invalid Mobile No');
        }

//        $xmlob = simplexml_load_string($response) or die('Error: Cannot create object');
//        $MSISDNSTATUS = (string) $xmlob->SMSINFO->MSISDNSTATUS; //return receiver number
//        if ($MSISDNSTATUS == 'Invalid Mobile No') {
//            return Redirect()->back()->with('sms failed', 'Failed : Invalid Mobile No');
//        } else {
//            return Redirect()->back()->with('sms successful', 'Send SMS successful');
//        }
    }

    //function to send SMS to All Customers
    public function sendCustomSMSToAll(Request $request)
    {
        $language = $request->input('language');
        $user_type = $request->input('customer_type');
        $text_message = $request->input('text_message');
        $date = $request->input('date');

//        if ($language == 'english') {
//            $stake_holder = 'RoyaltybdMasking';
//        } else {
//            $stake_holder = 'royaltybangla';
//            $text_message = strtoupper(bin2hex(iconv('UTF-8', 'UCS-2BE', $text_message)));
//        }

        $users = (new functionController)->getSMSCustomerList($user_type, $date);

        foreach ($users['users'] as $user) {
            $phone = $user['customer_contact_number'];

            if (substr($phone, 0, 4) === '+880') {
                $full_number = $phone;
            } elseif (substr($phone, 0, 1) === '0') {
                $full_number = '+88'.$phone;
            } else {
                $full_number = '+880'.$phone;
            }

//            $user = 'Royaltybd';
//            $pass = '66A6Q13d';
//            $sid = $stake_holder;
//            $url = 'http://sms.sslwireless.com/pushapi/dynamic/server.php';
//            $param = "user=$user&pass=$pass&sms[0][0]= $full_number &sms[0][1]=".urlencode($text_message)."&sms[0][2]=123456789&sid=$sid";

            $username = env('BOOMCAST_USERNAME');
            $password = env('BOOMCAST_PASSWORD');
            $url = 'http://api.boom-cast.com/boomcast/WebFramework/boomCastWebService/externalApiSendTextMessage.php';
            $param = "masking=NOMASK&userName=$username&password=$password&MsgType=TEXT&receiver=$full_number&message=".$text_message;

            $crl = curl_init();
            curl_setopt($crl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($crl, CURLOPT_SSL_VERIFYHOST, 2);
            curl_setopt($crl, CURLOPT_URL, $url);
            curl_setopt($crl, CURLOPT_HEADER, 0);
            curl_setopt($crl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($crl, CURLOPT_POST, 1);
            curl_setopt($crl, CURLOPT_POSTFIELDS, $param);
            $response = curl_exec($crl);
            curl_close($crl);
        }

        (new functionController2())->saveSentMessage(
            SentMessageType::sms,
            null,
            $request->input('text_message'),
            $users['to'],
            $language,
            null
        );

        return Redirect()->back()->with('operation complete', 'Operation Completed');
    }

    //function to change customer approval
    public function customerApproval()
    {
        $userId = $_POST['userId'];
        $status = $_POST['status'];
        $miscellaneous_id = $_POST['misc'];
        $result[0] = $status;
        $result[1] = $userId;

        if ($status == 1) { //make user active
            DB::table('customer_account')
                ->where('customer_id', $userId)
                ->update(['moderator_status' => 2]);

            //pusher to logout this user from all browsers
            $pusher = (new pusherController)->initializePusher();
            //Send a message to notify channel with an event name of notify-event
            $pusher->trigger('userLogout', 'userLogout-event', $userId);

            return Response::json($result);
        } else { //make user deactive
            DB::table('customer_account')
                ->where('customer_id', $userId)
                ->update(['moderator_status' => 1]);

            $customer_exists_in_miscellaneous_type = DB::table('customer_miscellaneous')
                ->where('customer_id', $userId)
                ->count();

            if ($customer_exists_in_miscellaneous_type > 0) {
                DB::table('customer_miscellaneous')
                    ->where('customer_id', $userId)
                    ->update([
                        'miscellaneous_id' => $miscellaneous_id,
                        'deactive_date' => date('Y-m-d'),
                    ]);
            } else {
                DB::table('customer_miscellaneous')->insert(
                    [
                        'customer_id' => $userId,
                        'miscellaneous_id' => $miscellaneous_id,
                        'deactive_date' => date('Y-m-d'),
                    ]
                );
            }
            $exist = CustomerLoginSession::where('customer_id', $userId)->where('status', LoginStatus::logged_in)->orderBy('id', 'DESC')->first();

            if ($exist) {
                $exist->status = LoginStatus::kicked;
                $exist->save();
            }

            $data['customer_id'] = $userId;
            //trigger event to user force logout
            event(new user_force_logout($data));

            return Response::json($result);
        }
    }

    //function to change customer approval
    public function postApproval()
    {
        $ids = $_POST['id'];
        $ids = explode('_', $ids);
        if ($ids[0] == 0) {
            Post::where('id', $ids[1])
                ->update(['moderate_status' => 0]);

            return Response::json($ids);
        } else {
            Post::where('id', $ids[1])
                ->update(['moderate_status' => 1]);

            return Response::json($ids);
        }
    }

    //function to get info of unapproved accounts or posts
    public function underModeration($param)
    {
        if ($param == 'customer') {
            $info = DB::table('customer_account as ca')
                ->join('customer_info as ci', 'ci.customer_id', '=', 'ca.customer_id')
                ->select('ci.customer_id', 'ci.customer_first_name', 'ci.customer_last_name', 'ci.customer_email', 'ci.customer_contact_number')
                ->where('ca.moderator_status', 0)
                ->get();
            $info = json_decode(json_encode($info), true);

            return view('admin.production.unapprovedAccounts', compact('info'));
        } else {
            dd('Please go back :: Nothing to do');
        }
    }

    //function to show all approved posts of partners
    public function allApprovedPosts()
    {
        //get all approved posts
        $posts = Post::with('postHeader', 'partnerInfo')->where('moderate_status', 1)->get();
        //all names of partners
        $allPartners = PartnerInfo::select('partner_name')
            ->orderBy('partner_name')
            ->get();

        return view('admin.production.allPosts', compact('allPartners', 'posts'));
    }

    //function to show all reviews by customers
    public function allCustomerReviews()
    {
        $allReviews = Review::with('customer', 'partnerInfo', 'transaction', 'comments')
            ->where('moderation_status', 1)->orderBy('id', 'DESC')->get();
        $delete = true;

        return view('admin.production.reviews.allCustomerReviews', compact('allReviews', 'delete'));
    }

    //function to show all deleted reviews
    public function allCustomerDeletedReviews()
    {
        $allReviews = Review::onlyTrashed()->with('customer', 'partnerInfo', 'transaction', 'comments')->orderBy('id', 'DESC')->get();
        $delete = false;

        return view('admin.production.reviews.allCustomerReviews', compact('allReviews', 'delete'));
    }

    //function to show all pending reviews
    public function allCustomerPendingReviews()
    {
        $allReviews = Review::with('customer', 'partnerInfo', 'transaction', 'comments')
            ->where('moderation_status', 0)
            ->orderBy('id', 'DESC')
            ->get();

        return view('admin.production.reviews.pendingReviews', compact('allReviews'));
    }

    //function to approve review
    public function approveReview($id)
    {
        (new \App\Http\Controllers\Review\functionController())->acceptReviewModeration($id, true);

        return \redirect()->back()->with('success', 'Review Approved');
    }

    //function to reject review
    public function rejectReview($id)
    {
        (new \App\Http\Controllers\Review\functionController())->rejectReviewModeration($id);

        return \redirect()->back()->with('success', 'Review Rejected');
    }

    //function to show all pending review replies
    public function allPendingReviewReplies()
    {
        $allReviewReplies = ReviewComment::pending()->with('review.customer', 'review.partnerInfo', 'review.transaction')
            ->orderBy('id', 'DESC')
            ->get();

        return view('admin.production.reviews.pendingReviewReplies', compact('allReviewReplies'));
    }

    //function to edit reply view
    public function editReviewReply($id)
    {
        $reply = ReviewComment::find($id);

        return view('admin.production.reviews.editReply', compact('reply'));
    }

    //update reply
    public function updateReviewReply(Request $request, $id)
    {
        $reply = $request->post('review_reply');
        (new \App\Http\Controllers\Review\functionController())->editReviewReply($id, $reply);

        return \redirect('admin/pending_review_replies')->with('success', 'Reply edited');
    }

    //function to approve review
    public function approveReviewReply($id)
    {
        (new \App\Http\Controllers\Review\functionController())->acceptReviewReplyModeration($id);

        return \redirect()->back()->with('success', 'Review Reply Approved');
    }

    //function to reject review
    public function rejectReviewReply($id)
    {
        (new \App\Http\Controllers\Review\functionController())->rejectReviewReplyModeration($id);

        return \redirect()->back()->with('success', 'Review Reply Rejected');
    }

    //function to search review (not using right now)
    //    public function searchReview(Request $request)
    //    {
    //        $customerSearchKey = $request->get('customerSearchKey');
    //        $customer_full_name = DB::table('customer_info')
    //            ->select('customer_full_name', 'customer_profile_image', 'customer_email', 'customer_contact_number')
    //            ->where('customer_contact_number', 'like', '%'. $customerSearchKey .'%')
    //            ->orWhere('customer_full_name', 'like', '%'. $customerSearchKey .'%')
    //            ->orWhere('customer_email', 'like', '%'. $customerSearchKey .'%')
    //            ->get();
    //        if(count($customer_full_name) != 0) {
    //            $customerName = $customer_full_name[0]->customer_full_name;
    //        } else {
    //            return redirect()->back();
    //        }
    //        $partnerName = $request->get('partnerSearchKey');
    //        $date = $request->get('date');
    //        if($customerName == null && $partnerName == null && $date == null) {
    //            return redirect()->back();
    //        }
    //        if($date != null){
    //            //make it as date format
    //            $date = date_create($date);
    //            $newDate =  date_format($date,"Y-m-d");
    //        }
    //
    //        if($customerName != null && $partnerName == null && $date == null) {
    //            //get review by customer name
    //            $reviews = DB::table('review as rv')
    //                ->join('review_comment as rc', 'rc.review_id', '=', 'rv.id')
    //                ->join('customer_info as ci', 'ci.customer_id', '=', 'rv.customer_id')
    //                ->join('partner_info as pi', 'pi.partner_account_id', '=', 'rv.partner_account_id')
    //                ->select('ci.customer_id', 'ci.customer_full_name', 'ci.customer_email', 'ci.customer_contact_number',
    //                    'ci.review_deleted', 'pi.partner_name', 'rc.comment', 'rv.heading', 'rv.id', 'rv.posted_on')
    //                ->where('ci.customer_full_name', $customerName)
    //                ->where('rc.comment_type', 'reviewer')
    //                ->get();
    //            $reviews = json_decode(json_encode($reviews), true);
    //        }elseif($customerName == null && $partnerName != null && $date == null) {
    //            //get review by partner name
    //            $reviews = DB::table('review as rv')
    //                ->join('review_comment as rc', 'rc.review_id', '=', 'rv.id')
    //                ->join('customer_info as ci', 'ci.customer_id', '=', 'rv.customer_id')
    //                ->join('partner_info as pi', 'pi.partner_account_id', '=', 'rv.partner_account_id')
    //                ->select('ci.customer_id', 'ci.customer_full_name', 'ci.customer_email', 'ci.customer_contact_number',
    //                    'ci.review_deleted', 'pi.partner_name', 'rc.comment', 'rv.heading', 'rv.id', 'rv.posted_on')
    //                ->orWhere('pi.partner_name', $partnerName)
    //                ->where('rc.comment_type', 'reviewer')
    //                ->get();
    //            $reviews = json_decode(json_encode($reviews), true);
    //        }elseif($customerName == null && $partnerName == null && $date != null) {
    //            //get review by date
    //            $reviews = DB::table('review as rv')
    //                ->join('review_comment as rc', 'rc.review_id', '=', 'rv.id')
    //                ->join('customer_info as ci', 'ci.customer_id', '=', 'rv.customer_id')
    //                ->join('partner_info as pi', 'pi.partner_account_id', '=', 'rv.partner_account_id')
    //                ->select('ci.customer_id', 'ci.customer_full_name', 'ci.customer_email', 'ci.customer_contact_number',
    //                    'ci.review_deleted', 'pi.partner_name', 'rc.comment', 'rv.heading', 'rv.id', 'rv.posted_on')
    //                ->where('rv.posted_on', 'like', $newDate. '%')
    //                ->where('rc.comment_type', 'reviewer')
    //                ->get();
    //            $reviews = json_decode(json_encode($reviews), true);
    //        }elseif($customerName != null && $partnerName != null && $date == null) {
    //            //get review by customer & partner name
    //            $reviews = DB::table('review as rv')
    //                ->join('review_comment as rc', 'rc.review_id', '=', 'rv.id')
    //                ->join('customer_info as ci', 'ci.customer_id', '=', 'rv.customer_id')
    //                ->join('partner_info as pi', 'pi.partner_account_id', '=', 'rv.partner_account_id')
    //                ->select('ci.customer_id', 'ci.customer_full_name', 'ci.customer_email', 'ci.customer_contact_number',
    //                    'ci.review_deleted', 'pi.partner_name', 'rc.comment', 'rv.heading', 'rv.id', 'rv.posted_on')
    //                ->where('ci.customer_full_name', $customerName)
    //                ->where('pi.partner_name', $partnerName)
    //                ->where('rc.comment_type', 'reviewer')
    //                ->get();
    //            $reviews = json_decode(json_encode($reviews), true);
    //        }elseif($customerName != null && $partnerName == null && $date != null){
    //            //get review by customer name & date
    //            $reviews = DB::table('review as rv')
    //                ->join('review_comment as rc', 'rc.review_id', '=', 'rv.id')
    //                ->join('customer_info as ci', 'ci.customer_id', '=', 'rv.customer_id')
    //                ->join('partner_info as pi', 'pi.partner_account_id', '=', 'rv.partner_account_id')
    //                ->select('ci.customer_id', 'ci.customer_full_name', 'ci.customer_email', 'ci.customer_contact_number',
    //                    'ci.review_deleted', 'pi.partner_name', 'rc.comment', 'rv.heading', 'rv.id', 'rv.posted_on')
    //                ->where('ci.customer_full_name', $customerName)
    //                ->where('rv.posted_on', 'like', $newDate. '%')
    //                ->where('rc.comment_type', 'reviewer')
    //                ->get();
    //            $reviews = json_decode(json_encode($reviews),true);
    //        }elseif($customerName == null && $partnerName != null && $date != null){
    //            //get review by partner name & date
    //            $reviews = DB::table('review as rv')
    //                ->join('review_comment as rc', 'rc.review_id', '=', 'rv.id')
    //                ->join('customer_info as ci', 'ci.customer_id', '=', 'rv.customer_id')
    //                ->join('partner_info as pi', 'pi.partner_account_id', '=', 'rv.partner_account_id')
    //                ->select('ci.customer_id', 'ci.customer_full_name', 'ci.customer_email', 'ci.customer_contact_number',
    //                    'ci.review_deleted', 'pi.partner_name', 'rc.comment', 'rv.heading', 'rv.id', 'rv.posted_on')
    //                ->where('pi.partner_name', $partnerName)
    //                ->where('rv.posted_on', 'like', $newDate. '%')
    //                ->where('rc.comment_type', 'reviewer')
    //                ->get();
    //            $reviews = json_decode(json_encode($reviews),true);
    //        }else{
    //            //get review by customer, partner & date
    //            $reviews = DB::table('review as rv')
    //                ->join('review_comment as rc', 'rc.review_id', '=', 'rv.id')
    //                ->join('customer_info as ci', 'ci.customer_id', '=', 'rv.customer_id')
    //                ->join('partner_info as pi', 'pi.partner_account_id', '=', 'rv.partner_account_id')
    //                ->select('ci.customer_id', 'ci.customer_full_name', 'ci.customer_email', 'ci.customer_contact_number',
    //                    'ci.review_deleted', 'pi.partner_name', 'rc.comment', 'rv.heading', 'rv.id', 'rv.posted_on')
    //                ->where('ci.customer_full_name', $customerName)
    //                ->where('pi.partner_name', $partnerName)
    //                ->where('rv.posted_on', 'like', $newDate. '%')
    //                ->where('rc.comment_type', 'reviewer')
    //                ->get();
    //            $reviews = json_decode(json_encode($reviews),true);
    //        }
    //        $i=0;
    //        foreach ($reviews as $review) {
    //            $reviews[$i]['serial_number'] = $i+1;
    //            $i++;
    //        }
    //        //custom pagination to apply on an array variable
    //        $currentPage = LengthAwarePaginator::resolveCurrentPage();
    //        $col = new Collection($reviews);
    //        $perPage = 15;
    //        $currentPageSearchResults = $col->slice(($currentPage - 1) * $perPage, $perPage)->all();
    //        $allReviews = new LengthAwarePaginator($currentPageSearchResults, count($col), $perPage, $currentPage,['path' => LengthAwarePaginator::resolveCurrentPath()] );
    //        //custom pagination ends
    //        return view('admin.production.allCustomerReviews', compact('allReviews'));
    //    }

    //View to edit a particular review
    public function editReview($id)
    {
        $reviewInfo = Review::findOrFail($id);
        $reviewInfo->load(['comments' => function ($query) {
            $query->where('comment_type', 'reviewer');
        }]);

        return view('admin.production.edit_review', compact('reviewInfo'));
    }

    //edit submit of a particular review
    public function reviewEditDone(Request $request, $id)
    {
        //get data from edit form
        $review_heading = $request->get('review_heading');
        $review_comment = $request->get('review_comment');
        if ($review_heading == null) {
            $review_heading = 'n/a';
        }
        if ($review_comment == null) {
            $review_comment = 'n/a';
        }

        try {
            DB::beginTransaction(); //to do query rollback

            $review = Review::findOrFail($id);
            //update review heading
            $review->heading = $review_heading;
            $review->body = $review_comment;
            $review->save();

            DB::commit(); //to do query rollback
        } catch (\Exception $e) {
            DB::rollBack(); //rollback all successfully executed queries
            return redirect()->back()->with('try_again', 'Please try again!');
        }

        return redirect($request->get('prev_url'))->with('review_edited', 'Review updated!');
    }

    //Delete Review
    public function deleteReview($id)
    {
        try {
            DB::beginTransaction(); //to do query rollback

            //delete review
            (new functionController)->deleteReview($id, session('admin_id'));

            DB::commit(); //to do query rollback
        } catch (\Exception $e) {
            DB::rollBack(); //rollback all successfully executed queries
            return redirect()->back()->with('try_again', 'Please try again!');
        }

        return redirect('admin/allCustomerReviews')->with('review_deleted', 'Review deleted!');
    }

    //function to search customer in review search
    public function customerByKey(Request $request)
    {
        $keyword = $request->term;
        $customers = DB::table('customer_info as ci')
            ->join('customer_account as ca', 'ca.customer_id', '=', 'ci.customer_id')
            ->select('ci.customer_id', 'ci.customer_full_name', 'ci.customer_profile_image', 'ci.customer_email', 'ci.customer_contact_number')
            ->where('ci.customer_id', 'like', '%'.$keyword.'%')
            ->orWhere('ci.customer_full_name', 'like', '%'.$keyword.'%')
            ->orWhere('ci.customer_email', 'like', '%'.$keyword.'%')
            ->orWhere('ci.customer_contact_number', 'like', '%'.$keyword.'%')
            ->orWhere('ca.customer_username', 'like', '%'.$keyword.'%')
            ->get();
        if (count($customers) == 0) {
            $result[] = 'No customer found';
        } else {
            foreach ($customers as $key => $value) {
                $result[] = $value->customer_email.'=>'.$value->customer_contact_number;
            }
        }

        return $result;
    }

    //function to search customer in review search
    public function customerNameByKey(Request $request)
    {
        $keyword = $request->term;
        $customers = DB::table('customer_info')
            ->select('customer_id', 'customer_full_name', 'customer_profile_image', 'customer_email', 'customer_contact_number')
            ->where('customer_id', 'like', '%'.$keyword.'%')
            ->orWhere('customer_full_name', 'like', '%'.$keyword.'%')
            ->orWhere('customer_email', 'like', '%'.$keyword.'%')
            ->orWhere('customer_contact_number', 'like', '%'.$keyword.'%')
            ->get();
        if (count($customers) == 0) {
            $result[] = 'No customer found';
        } else {
            foreach ($customers as $key => $value) {
                $result[] = $value->customer_full_name;
            }
        }

        return $result;
    }

    //function to search partner in review search
    public function partnerByKey(Request $request)
    {
        $keyword = $request->term;
        $partners = DB::table('partner_info as pi')
            ->join('partner_branch as pb', 'pb.partner_account_id', '=', 'pi.partner_account_id')
            ->select('pi.partner_account_id', 'pi.partner_name', 'pb.partner_area', 'pb.partner_address', 'pb.id')
            ->where('pi.partner_name', 'like', '%'.$keyword.'%')
            ->orWhere('pb.partner_email', 'like', '%'.$keyword.'%')
            ->get();

        $partners = json_decode(json_encode($partners), true);
        if (count($partners) == 0) {
            $result[] = 'No partner found';
        } else {
            foreach ($partners as $partner) {
                $result[] = $partner['partner_name'].' => '.$partner['partner_address'].'=>'.$partner['id'];
            }
        }

        return $result;
    }

    //function to search partner in review search
    public function partnerNameByKey(Request $request)
    {
        $keyword = $request->term;
        $partners = DB::table('partner_info as pi')
            ->join('partner_branch as pb', 'pb.partner_account_id', '=', 'pi.partner_account_id')
            ->select('pi.partner_account_id', 'pi.partner_name', 'pb.partner_area', 'pb.partner_address')
            ->where('pi.partner_name', 'like', '%'.$keyword.'%')
            ->orWhere('pb.partner_email', 'like', '%'.$keyword.'%')
            ->get();

        $partners = json_decode(json_encode($partners), true);
        if (count($partners) == 0) {
            $result[] = 'No partner found';
        } else {
            foreach ($partners as $partner) {
                $result[] = $partner['partner_name'];
            }
        }

        return $result;
    }

    //function to search partner in review search
    public function partnerByKeyName(Request $request)
    {
        $keyword = $request->term;
        $partners = DB::table('partner_info')
            ->select('partner_name')
            ->where('partner_name', 'like', '%'.$keyword.'%')
            ->get();

        $partners = json_decode(json_encode($partners), true);
        if (count($partners) == 0) {
            $result[] = 'No partner found';
        } else {
            foreach ($partners as $partner) {
                $result[] = $partner['partner_name'];
            }
        }

        return $result;
    }

    //function to search post partner wise
    public function searchPartnerPost(Request $request)
    {
        if ($request->has('partnerName')) {
            $partnerName = $request->get('partnerName');

            $posts = Post::whereHas('partnerInfo', function ($query) use ($partnerName) {
                $query->where('partner_name', $partnerName);
            })->where('moderate_status', 1)->get();

            if (count($posts) > 0) {
                $posts->load('postHeader', 'partnerInfo');
            }
        } else {
            $partnerEmail = $request->get('partnerEmail');
            //get all approved posts
            $posts = DB::table('partner_post as pp')
                ->join('partner_post_header as pph', 'pph.post_id', '=', 'pp.id')
                ->join('partner_info as pi', 'pi.partner_account_id', '=', 'pp.partner_account_id')
                ->select('pi.partner_name', 'pp.*', 'pph.header')
                ->where('pp.moderate_status', 1)
                ->where('pi.partner_email', $partnerEmail)
                ->get();
            $posts = json_decode(json_encode($posts), true);
        }
        //get all partner names & email id
        $allPartners = PartnerInfo::select('partner_name')
            ->orderBy('partner_name')
            ->get();

        return view('admin.production.allPosts', compact('posts', 'allPartners'));
    }

    //function to get posts by partner name
    public function PostByPartnerName(Request $request)
    {
        $name = $request->get('partnerName');

        $allPosts = Post::whereHas('partnerInfo', function ($query) use ($name) {
            $query->where('partner_name', $name);
        })->get();

        if (count($allPosts) > 0) {
            $allPosts->load('postHeader', 'partnerInfo');
        }

        //all partners name
        $partnerName = PartnerInfo::select('partner_name')->get();

        return view('admin.production.unapprovedPosts', compact('allPosts', 'partnerName'));
    }

    //function to get card prices & show in admin panel
    public function cardPrice()
    {
        $card_prices = AllAmounts::all();

        return view('admin.production.card-price', compact('card_prices'));
    }

    //function to add card price
//    public function addCardPrice(Request $request)
//    {
//        $this->validate($request, [
//            "card_type" => 'required',
//            "platform" => 'required',
//            "card_price" => 'required|numeric',
//            "card_duration" => 'required|numeric',
//        ]);
//        $request->flashOnly(['card_type', 'platform', 'card_price', 'card_duration']);
//        $month_list = ['12', '6', '3', '1'];
//
//        //store values in variable
//        $card_type = $request->get('card_type');
//        $platform = $request->get('platform');
//        $card_price = $request->get('card_price');
//        $card_duration = $request->get('card_duration');
//        if (!in_array($card_duration, $month_list)) { //month not in the list
//            return redirect('prices')->with('price_add_error', 'Month selection error');
//        }
//        $price_exist = AllAmounts::where([['type', $card_type . '_' . $platform], ['month', $card_duration]])->count();
//        if ($price_exist > 0) {
//            return redirect('prices')->with('price_add_error', 'Card price already added');
//        }
//
//        //add prices in database
//        $allAmounts = new AllAmounts();
//        $allAmounts->type = $card_type . '_' . $platform;
//        $allAmounts->price = $card_price;
//        $allAmounts->month = $card_duration;
//        $allAmounts->save();
//
//        return redirect('prices')->with('price_added', 'Card price added');
//    }

    //function to change card prices
//    public function changeCardPrice(Request $request)
//    {
//        //store values in variable
//        // $gold_web_prices = $request->get('gold_web_prices');
//        // $gold_web_validity = $request->get('gold_web_validity');
//        $platinum_web_prices = $request->get('platinum_web_prices');
//        $platinum_web_validity = $request->get('platinum_web_validity');
//        $platinum_web_renew_prices = $request->get('platinum_web_renew_prices');
//        $platinum_web_renew_validity = $request->get('platinum_web_renew_validity');
//
//        // $gold_android_prices = $request->get('gold_android_prices');
//        // $gold_android_validity = $request->get('gold_android_validity');
//        $platinum_android_prices = $request->get('platinum_android_prices');
//        $platinum_android_validity = $request->get('platinum_android_validity');
//        $platinum_android_renew_prices = $request->get('platinum_android_renew_prices');
//        $platinum_android_renew_validity = $request->get('platinum_android_renew_validity');
//
//        // $gold_ios_prices = $request->get('gold_ios_prices');
//        // $gold_ios_validity = $request->get('gold_ios_validity');
//        $platinum_ios_prices = $request->get('platinum_ios_prices');
//        $platinum_ios_validity = $request->get('platinum_ios_validity');
//        $platinum_ios_renew_prices = $request->get('platinum_ios_renew_prices');
//        $platinum_ios_renew_validity = $request->get('platinum_ios_renew_validity');
//
//        //update web card prices
//        // $i = 0;
//        // foreach ($gold_web_prices as $value) {
//        //     AllAmounts::where([['type', 'gold_web'], ['month', $gold_web_validity[$i]]])->update(['price' => $value, 'month' => $gold_web_validity[$i]]);
//        //     $i++;
//        // }
//        $i = 0;
//        foreach ($platinum_web_prices as $value) {
//            AllAmounts::where([['type', 'platinum_web'], ['month', $platinum_web_validity[$i]]])->update(['price' => $value]);
//            $i++;
//        }
//        $i = 0;
//        foreach ($platinum_web_renew_prices as $value) {
//            AllAmounts::where([['type', 'renew_web'], ['month', $platinum_web_renew_validity[$i]]])->update(['price' => $value]);
//            $i++;
//        }
//        //update android card prices
//        // $i = 0;
//        // foreach ($gold_android_prices as $value) {
//        //     AllAmounts::where([['type', 'gold_android'], ['month', $gold_android_validity[$i]]])->update(['price' => $value, 'month' => $gold_android_validity[$i]]);
//        //     $i++;
//        // }
//        $i = 0;
//        foreach ($platinum_android_prices as $value) {
//            AllAmounts::where([['type', 'platinum_android'], ['month', $platinum_android_validity[$i]]])->update(['price' => $value]);
//            $i++;
//        }
//        $i = 0;
//        foreach ($platinum_android_renew_prices as $value) {
//            AllAmounts::where([['type', 'renew_android'], ['month', $platinum_android_renew_validity[$i]]])->update(['price' => $value]);
//            $i++;
//        }
//        //update IOS card prices
//        // $i = 0;
//        // foreach ($gold_ios_prices as $value) {
//        //     AllAmounts::where([['type', 'gold_ios'], ['month', $gold_ios_validity[$i]]])->update(['price' => $value, 'month' => $gold_ios_validity[$i]]);
//        //     $i++;
//        // }
//        $i = 0;
//        foreach ($platinum_ios_prices as $value) {
//            AllAmounts::where([['type', 'platinum_ios'], ['month', $platinum_ios_validity[$i]]])->update(['price' => $value]);
//            $i++;
//        }
//        $i = 0;
//        foreach ($platinum_ios_renew_prices as $value) {
//            AllAmounts::where([['type', 'renew_ios'], ['month', $platinum_ios_renew_validity[$i]]])->update(['price' => $value]);
//            $i++;
//        }
//
//        return redirect('prices')->with('updated', 'Card details updated');
//    }

    //function to change other card prices
    public function changeOtherPrices(Request $request)
    {
        $this->validate(
            $request,
            [
                'delivery_charge' => 'required|numeric',
//                'customization_charge' => 'required|numeric',
//                'lost_card_charge' => 'required|numeric',
                'refer_bonus' => 'required|numeric',
                'per_card_scan' => 'required|numeric',
                'per_card_sell' => 'required|numeric',
                'min_card_sell_redeem' => 'required|numeric',
                'rating' => 'required|numeric',
                'review' => 'required|numeric',
                'daily_point_limit' => 'required|numeric',
            ]
        );
        $request->flashOnly(['delivery_charge', 'refer_bonus', 'per_card_scan',
            'per_card_sell', 'min_card_sell_redeem', 'rating', 'review', ]);
        //store values in variable
        $delivery_charge = $request->get('delivery_charge');
//        $customization_charge = $request->get('customization_charge');
//        $lost_card_charge = $request->get('lost_card_charge');
        $refer_bonus = $request->get('refer_bonus');
        $per_card_scan = $request->get('per_card_scan');
        $per_card_sell = $request->get('per_card_sell');
        $min_card_sell_redeem = $request->get('min_card_sell_redeem');
        $rating = $request->get('rating');
        $review = $request->get('review');
        $daily_point_limit = $request->get('daily_point_limit');

        //update prices in database
        AllAmounts::where('type', 'delivery_charge')->update(['price' => $delivery_charge]);
//        AllAmounts::where('type', 'card_customization')->update(['price' => $customization_charge]);
//        AllAmounts::where('type', 'lost_card')->update(['price' => $lost_card_charge]);
        AllAmounts::where('type', 'refer_bonus')->update(['price' => $refer_bonus]);
        AllAmounts::where('type', 'per_card_scan')->update(['price' => $per_card_scan]);
        AllAmounts::where('type', 'per_card_sell')->update(['price' => $per_card_sell]);
        AllAmounts::where('type', 'min_card_sell_redeem')->update(['price' => $min_card_sell_redeem]);
        AllAmounts::where('type', 'rating')->update(['price' => $rating]);
        AllAmounts::where('type', 'review')->update(['price' => $review]);
        AllAmounts::where('type', 'daily_point_limit')->update(['price' => $daily_point_limit]);

        return redirect('admin/other_prices')->with('updated', 'Extra charge updated');
    }

    //function to delete a Price item
//    public function deletePriceItem($itemId)
//    {
//        dd($itemId);
//        DB::table('all_amounts')
//            ->where('id', $itemId)
//            ->delete();
//
//        return redirect()->back();
//    }

    //function to get customize points
    public function customizePoints($partner_id)
    {
        $customize_point = DB::table('point_customize as pc')
            ->join('discount as ds', 'ds.point_customize_id', '=', 'pc.id')
            ->select('pc.*')
            ->where('ds.partner_account_id', $partner_id)
            ->get();
        $time_duration_count = 0;
        $time_to_array = [];
        $time_from_array = [];
        if (isset($customize_point[0]->time_duration)) {
            $time_duration_count = count(json_decode($customize_point[0]->time_duration));
            $point_details = json_decode($customize_point[0]->time_duration);
            if ($time_duration_count > 0) {
                for ($i = 0; $i < $time_duration_count; $i++) {
                    $time_to_array[$i] = $point_details[$i]->to;
                    $time_from_array[$i] = $point_details[$i]->from;
                }
            }
        }
        $partner_info = PartnerInfo::where('partner_account_id', $partner_id)->first();

        return view('admin.production.customize_points', compact(
            'customize_point',
            'partner_info',
            'time_duration_count',
            'time_to_array',
            'time_from_array'
        ));
    }

    //function for insert updated data for checkout points
    public function updatePoints(Request $request, $partner_id)
    {
        $this->validate($request, [
            'point_customize_type' => 'required|numeric',
            'points' => 'required|numeric',
            'date_from' => 'required',
            'date_to' => 'required',
        ]);

        $request->flashOnly(['point_customize_type', 'points', 'date_from', 'date_to']);

        //get data from edit form
        $point_type = $request->get('point_customize_type');
        $date_from = $request->get('date_from');
        $date_to = $request->get('date_to');
        $points = $request->get('points');
        $description = $request->get('description');

        //weekdays
        $sat = ($request->get('sat')) == null ? 0 : 1;
        $sun = ($request->get('sun')) == null ? 0 : 1;
        $mon = ($request->get('mon')) == null ? 0 : 1;
        $tue = ($request->get('tue')) == null ? 0 : 1;
        $wed = ($request->get('wed')) == null ? 0 : 1;
        $thu = ($request->get('thu')) == null ? 0 : 1;
        $fri = ($request->get('fri')) == null ? 0 : 1;

        //create json variable
        $week[] = [
            'sat' => $sat.'',
            'sun' => $sun.'',
            'mon' => $mon.'',
            'tue' => $tue.'',
            'wed' => $wed.'',
            'thu' => $thu.'',
            'fri' => $fri.'',
        ];
        $date[] = [
            'from' => $date_from,
            'to' => $date_to,
        ];

        $hour = [];
        if (isset($_POST['time_duration_from']) && isset($_POST['time_duration_to'])) {
            for ($i = 0; $i < count($_POST['time_duration_from']); $i++) {
                if ($_POST['time_duration_from'][$i] != '' && $_POST['time_duration_to'][$i] != '') {
                    $hour[] = [
                        'from' => $_POST['time_duration_from'][$i],
                        'to' => $_POST['time_duration_to'][$i],
                    ];
                }
            }
        } else {
            $hour = [];
        }

        $week = json_encode($week);
        $date = json_encode($date);
        $hour = json_encode($hour);

        try {
            DB::beginTransaction(); //to do query rollback

            $customize_point_exists = DB::table('point_customize as pc')
                ->join('discount as ds', 'ds.point_customize_id', '=', 'pc.id')
                ->select('pc.id')
                ->where('ds.partner_account_id', $partner_id)
                ->get();

            if (count($customize_point_exists) > 0) {
                //update point customization info in database
                DB::table('point_customize')
                    ->where('id', $customize_point_exists[0]->id)
                    ->update([
                        'point_type' => $point_type,
                        'date_duration' => $date,
                        'weekdays' => $week,
                        'time_duration' => $hour,
                        'point_multiplier' => $points,
                        'description' => $description,
                    ]);
            } else {
                $id = DB::table('point_customize')
                    ->insertGetId([
                        'point_type' => $point_type,
                        'date_duration' => $date,
                        'weekdays' => $week,
                        'time_duration' => $hour,
                        'point_multiplier' => $points,
                        'description' => $description,
                    ]);

                //point_customize id to be inserted into discount table
                DB::table('discount')
                    ->where('partner_account_id', $partner_id)
                    ->update([
                        'point_customize_id' => $id,
                    ]);
            }

            DB::commit(); //to do query rollback
        } catch (\Exception $e) {
            DB::rollBack(); //rollback all successfully executed queries

            return redirect()->back()->with('try_again', 'Please try again!');
        }

        return redirect('/allPartners')->with('status', 'Points updated!');
    }

    //function to delete-customized-point
    public function deleteCustomizedPoint($partner_id)
    {
        $discount = Discount::where('partner_account_id', $partner_id)->first();
        if ($discount && $discount->point_customize_id != null) {
            CustomizePoint::where('id', $discount->point_customize_id)->delete();
            Discount::where('partner_account_id', $partner_id)->update(['point_customize_id' => null]);

            return redirect('/allPartners')->with('status', 'Points deleted!');
        } else {
            return redirect()->back()->with('try_again', 'Nothing to delete');
        }
    }

    //function to card delivery customer list
    public function cardDeliveryList($status)
    {
        $card_prices = AllAmounts::all();
        $delivery_charge = $card_prices[3]->price;
        $card_delivery_list = (new functionController2)->cardDeliveryData($status);

        $current_page = $card_delivery_list->currentPage();
        $per_page = $card_delivery_list->perPage();
        $i = ($current_page * $per_page) - $per_page + 1;
        foreach ($card_delivery_list as $list) {
            $list->serial = $i;
            $i++;
        }
        if ($status == 'free_trial') {
            return view('admin.production.freeTrialUsers', compact('card_delivery_list'));
        }

        $i = 0;
        foreach ($card_delivery_list as $customerInfo) {
            if ($card_delivery_list[$i]->paid_amount != null) {
                $card_delivery_list[$i]->total_payable = $card_delivery_list[$i]->paid_amount;
            } else {
                //This portion is for Temporary Fix to avoid to show wrong payable amount
                if ($customerInfo->delivery_type == (DeliveryType::office_pickup || DeliveryType::spot_delivery)) {
                    $card_delivery_list[$i]->total_payable = $customerInfo->amount;
                } elseif ($customerInfo->amount == 599 || $customerInfo->amount == 799 || $customerInfo->amount == 999 || $customerInfo->amount == 1199) {
                    $card_delivery_list[$i]->total_payable = $customerInfo->amount + $delivery_charge;
                } else {
                    $card_delivery_list[$i]->total_payable = $customerInfo->amount;
                }
            }
            $i++;
        }

        return view('admin.production.card-delivery-list', compact('card_delivery_list'));
    }

    //Function to change card delivery status
    public function change_delivery_status(Request $request)
    {
        $current_status = $request->input('current_status');
        $customer_id = $request->input('customer_id');
        CustomerInfo::where('customer_id', $customer_id)->update(['delivery_status' => $current_status]);

        return Response::json(1);
    }

    //Function to update delivery type
    public function update_delivery_type(Request $request)
    {
        $delivery_type = $request->input('delivery_type');
        $customer_id = $request->input('customer_id');

        $customer_exists_in_delivery = DB::table('card_delivery')->where('customer_id', $customer_id)->count();

        if ($customer_exists_in_delivery > 0) {
            DB::table('card_delivery')->where('customer_id', $customer_id)->update(['delivery_type' => $delivery_type]);
        } else {
            DB::table('card_delivery')->insert([
                'customer_id' => $customer_id,
                'delivery_type' => $delivery_type,
                'shipping_address' => '',
            ]);
        }

        return Response::json('1');
    }

    //Function to update actual card price
    public function update_actual_price(Request $request)
    {
        $updated_price = $request->input('updated_price');
        $customer_id = $request->input('customer_id');

        $is_updated = DB::table('card_delivery')->where('customer_id', $customer_id)->update(['paid_amount' => $updated_price]);

        return Response::json('1');
    }

    public function searchDeliveryCustomerByKey(Request $request)
    {
        //payable amounts
        $card_prices = AllAmounts::all();
        $delivery_charge = $card_prices[3]->price;

        $search_keyword = $request->get('customerSearchKey');
        $keyword = explode('=>', $search_keyword);
        $card_delivery_list = DB::table('customer_info as ci')
            ->leftjoin('card_delivery', function ($join) {
                $join->on('card_delivery.customer_id', '=', 'ci.customer_id')
                    ->on('card_delivery.id', '=', DB::raw('(SELECT max(id) from card_delivery WHERE card_delivery.customer_id = ci.customer_id)'));
            })
            ->join('ssl_transaction_table as stt', 'stt.id', '=', 'card_delivery.ssl_id')
            ->select(
                'ci.customer_full_name',
                'ci.customer_id as cid',
                'ci.delivery_status',
                'ci.customer_contact_number',
                'ci.customer_email',
                'ci.customer_type',
                'ci.month',
                'card_delivery.*',
                'stt.tran_date',
                'stt.tran_id',
                'stt.id as stt_id',
                'stt.amount',
                'stt.platform'
            )
            ->orderBy('card_delivery.id', 'DESC')
            ->where('ci.delivery_status', '!=', 0)
            ->where('ci.customer_type', '!=', 3)
            ->where('stt.status', 1)
            ->where('ci.customer_email', $keyword[0])
            ->paginate(20);

        $i = 0;
        foreach ($card_delivery_list as $customerInfo) {
            if ($card_delivery_list[0]->paid_amount != null) {
                $card_delivery_list[$i]->total_payable = $card_delivery_list[0]->paid_amount;
            } else {
                //This portion is for Temporary Fix to avoid to show wrong payable amount
                if ($customerInfo->delivery_type == (DeliveryType::office_pickup || DeliveryType::spot_delivery)) {
                    $card_delivery_list[$i]->total_payable = $customerInfo->amount;
                } elseif ($customerInfo->amount == 599 || $customerInfo->amount == 799 || $customerInfo->amount == 999 || $customerInfo->amount == 1199) {
                    $card_delivery_list[$i]->total_payable = $customerInfo->amount + $delivery_charge;
                } else {
                    $card_delivery_list[$i]->total_payable = $customerInfo->amount;
                }
            }
            $i++;
        }

        $current_page = $card_delivery_list->currentPage();
        $per_page = $card_delivery_list->perPage();
        $i = ($current_page * $per_page) - $per_page + 1;
        foreach ($card_delivery_list as $list) {
            $list->serial = $i;
            $i++;
        }

        return view('admin.production.card-delivery-list', compact('card_delivery_list'));
    }

    public function searchFreeTrialUserByKey(Request $request)
    {
        $search_keyword = $request->get('customerSearchKey');
        $keyword = explode('=>', $search_keyword);
        $date = date('2019-10-17');
        $card_delivery_list = DB::table('customer_info as ci')
            ->leftjoin('card_delivery', function ($join) {
                $join->on('card_delivery.customer_id', '=', 'ci.customer_id')
                    ->on('card_delivery.id', '=', DB::raw('(SELECT max(id) from card_delivery WHERE card_delivery.customer_id = ci.customer_id)'));
            })
            ->join('ssl_transaction_table as stt', 'stt.id', '=', 'card_delivery.ssl_id')
            ->select(
                'ci.customer_full_name',
                'ci.customer_id as cid',
                'ci.delivery_status',
                'ci.customer_contact_number',
                'ci.customer_email',
                'ci.customer_type',
                'ci.month',
                'card_delivery.*',
                'stt.tran_date',
                'stt.tran_id',
                'stt.id as stt_id',
                'stt.amount',
                'stt.platform'
            )
            ->orderBy('card_delivery.id', 'DESC')
            ->where('card_delivery.delivery_type', 11)
            ->where('stt.status', 1)
            ->where('stt.tran_date', '>', $date)
            ->where('ci.customer_type', 2)
            ->where('ci.customer_email', $keyword[0])
            ->paginate(20);

        return view('admin.production.freeTrialUsers', compact('card_delivery_list'));
    }

    public function searchCustomerForSMS(Request $request)
    {
        $search_keyword = $request->get('customerSearchKey');
        $keyword = explode('=>', $search_keyword);
        //get partner by id
        $profileInfo = DB::select("select ci.*, ch.type as user_type, ca.moderator_status
                                    from customer_info as ci
                                     join customer_account ca on ci.customer_id = ca.customer_id
                                     left join customer_history ch on ci.customer_id = ch.customer_id
                                      AND ch.id = 
                                        (
                                           SELECT MAX(id) 
                                           FROM customer_history c 
                                           WHERE c.customer_id = ch.customer_id
                                        ) 
                                        where ci.customer_email='$keyword[0]'");
        $profileInfo = (new functionController2())->getPaginatedData($profileInfo, 20);

        return view('admin.production.sms.existing_customers_sms', compact('profileInfo'));
    }

    //============function for showing Partners searched by 'Partner name'==================
    public function searchPartnerByName(Request $request)
    {
        $search_keyword = $request->get('searchPartner');
        //get partner by id
        $coupons = DB::table('all_coupons as ac')
            ->join('partner_info as pi', 'pi.partner_account_id', '=', 'ac.partner_account_id')
            ->select('ac.*')
            ->where('pi.partner_name', 'LIKE', '%'.$search_keyword.'%')
            ->get();
        $coupons = json_decode(json_encode($coupons), true);
        $i = 0;
        foreach ($coupons as $coupon) {
            $partner_name = DB::table('partner_info')
                ->select('partner_name')
                ->where('partner_account_id', $coupon['partner_account_id'])
                ->get();
            $partner_name = json_decode(json_encode($partner_name), true);
            $coupons[$i]['partner_name'] = $partner_name[0]['partner_name'];
            $i++;
        }
        $coupons = json_decode(json_encode($coupons), true);

        return view('admin.production.allCoupons', compact('coupons'));
    }

    //============function for edit user view==================
    public function editUser($customerID)
    {
        $profileInfo = DB::table('customer_info as ci')
            ->join('customer_account as ca', 'ca.customer_id', '=', 'ci.customer_id')
            ->select('ci.*', 'ca.moderator_status')
            ->where('ci.customer_id', $customerID)
            ->first();
        $profileInfo->reference_used = CustomerInfo::where('referrer_id', $customerID)->count();

        return view('admin.production.edit_user', compact('profileInfo'));
    }

    //function to crop user image in admin dashboard
    public function editUserImage()
    {
        $data = $_POST['customerProfileImage'];
        list($type, $data) = explode(';', $data);
        list(, $data) = explode(',', $data);
        $data = base64_decode($data);
        $imageName = time().'.jpg';
        Session::put('user_profile_image_name', $imageName);
        Session::put('user_profile_image', $data);

        echo 'Image Uploaded';
    }

    //============function for insert updated data in customer info table for pre order==================
    public function editDone(Request $request, $id)
    {
        $contactInfo = DB::table('customer_info')
            ->select('customer_email', 'customer_contact_number', 'expiry_date')
            ->where('customer_id', $id)
            ->first();

        if ($request->get('customer_id') == $id && $request->get('customer_email') != $contactInfo->customer_email
            && $request->get('mobile') != $contactInfo->customer_contact_number) {
            $this->validate($request, [
                'customer_id' => 'required',
                'customer_email' => 'required|email|unique:customer_info,customer_email',
                'mobile' => 'required|unique:customer_info,customer_contact_number',
            ]);
        } elseif ($request->get('customer_id') == $id && $request->get('customer_email') != $contactInfo->customer_email
            && $request->get('mobile') == $contactInfo->customer_contact_number) {
            $this->validate($request, [
                'customer_id' => 'required',
                'customer_email' => 'required|email|unique:customer_info,customer_email',
                'mobile' => 'required',
            ]);
        } elseif ($request->get('customer_id') == $id && $request->get('customer_email') == $contactInfo->customer_email
            && $request->get('mobile') != $contactInfo->customer_contact_number) {
            $this->validate($request, [
                'customer_id' => 'required',
                'customer_email' => 'required|email',
                'mobile' => 'required|unique:customer_info,customer_contact_number',
            ]);
        } elseif ($request->get('customer_id') != $id && $request->get('customer_email') != $contactInfo->customer_email
            && $request->get('mobile') != $contactInfo->customer_contact_number) {
            $this->validate($request, [
                'customer_id' => 'required|unique:customer_account,customer_id',
                'customer_email' => 'required|email|unique:customer_info,customer_email',
                'mobile' => 'required|unique:customer_info,customer_contact_number',
            ]);
        } elseif ($request->get('customer_id') != $id && $request->get('customer_email') == $contactInfo->customer_email
            && $request->get('mobile') != $contactInfo->customer_contact_number) {
            $this->validate($request, [
                'customer_id' => 'required|unique:customer_account,customer_id',
                'customer_email' => 'required|email',
                'mobile' => 'required|unique:customer_info,customer_contact_number',
            ]);
        } elseif ($request->get('customer_id') != $id && $request->get('customer_email') != $contactInfo->customer_email
            && $request->get('mobile') == $contactInfo->customer_contact_number) {
            $this->validate($request, [
                'customer_id' => 'required|unique:customer_account,customer_id',
                'customer_email' => 'required|email|unique:customer_info,customer_email',
                'mobile' => 'required',
            ]);
        } elseif ($request->get('customer_id') != $id && $request->get('customer_email') == $contactInfo->customer_email
            && $request->get('mobile') == $contactInfo->customer_contact_number) {
            $this->validate($request, [
                'customer_id' => 'required|unique:customer_account,customer_id',
                'customer_email' => 'required|email',
                'mobile' => 'required',
            ]);
        } else {
            $this->validate($request, [
                'customer_id' => 'required',
                'customer_email' => 'required|email',
                'mobile' => 'required',
            ]);
        }

        $request->flashOnly(['customer_id', 'customer_email', 'mobile']);

        $this->validate($request, [
            'customer_full_name' => 'required',
            'refer_code' => ['required', new unique_if_changed($id, 'customer_info', 'referral_number',
                'customer_id', 'Refer code has already been taken')],
        ]);

        $request->flashOnly(['customer_full_name', 'refer_code']);

        //get data from edit form
        $customerId = $request->get('customer_id');
        $customerFullName = $request->get('customer_full_name');
        $customerEmail = $request->get('customer_email');
        $customerMobile = $request->get('mobile');
        $customerAddress = $request->get('address');
        $customerGender = $request->get('customer_gender');
        $customerDOB = $request->get('dob') != null ? date('Y-m-d', strtotime($request->get('dob'))) : null;
        $expiry_date = date('Y-m-d', strtotime($request->get('expiry_date')));
        $email_verify = $request->get('email_verify');
        $refer_code = $request->get('refer_code');

        //$approval = $request->get('approve');
        if (! $expiry_date) {
            $expiry_date = $contactInfo->expiry_date;
        }
        try {
            DB::beginTransaction(); //to do query rollback
            //when new image is selected
            if (Session::has('user_profile_image_name')) {
                //get current image name
                $get_current_image_name = DB::table('customer_info')
                    ->select('customer_profile_image')
                    ->where('customer_id', $id)
                    ->first();
                $image_path = $get_current_image_name->customer_profile_image;
                $exploded_path = explode('/', $image_path);

                if (strpos($image_path, 'https://royalty-bd.s3.ap-southeast-1.amazonaws.com/') !== false) {
                    //remove previous profile image from folder
                    Storage::disk('s3')->delete('dynamic-images/users/'.end($exploded_path));
                    //update new image info
                    Storage::disk('s3')->put('dynamic-images/users/'.Session::get('user_profile_image_name'), Session::get('user_profile_image'), 'public');
                    $image_url = Storage::disk('s3')->url('dynamic-images/users/'.Session::get('user_profile_image_name'));
                } else {
                    //just update the new image info
                    Storage::disk('s3')->put('dynamic-images/users/'.Session::get('user_profile_image_name'), Session::get('user_profile_image'), 'public');
                    $image_url = Storage::disk('s3')->url('dynamic-images/users/'.Session::get('user_profile_image_name'));
                }
                if ($customerEmail != $contactInfo->customer_email) {
                    DB::table('social_id')
                        ->where('customer_id', $id)
                        ->where('customer_social_type', 'google')
                        ->delete();
                    DB::table('subscribers')->where('email', $contactInfo->customer_email)->delete();
                }

                //update customer info in database with new image
                DB::table('customer_info')
                    ->where('customer_id', $id)
                    ->update([
                        'customer_id' => $customerId,
                        'customer_full_name' => $customerFullName,
                        'customer_email' => $customerEmail,
                        'customer_contact_number' => $customerMobile,
                        'customer_address' => $customerAddress,
                        'customer_gender' => $customerGender,
                        'customer_dob' => $customerDOB,
                        'expiry_date' => $expiry_date,
                        'email_verified' => $email_verify,
                        'referral_number' => $refer_code,
                    ]);

                (new functionController)->update_profile_image_link($image_url, $id);

                //remove session of cropped image
                $request->session()->forget('user_profile_image_name');
                $request->session()->forget('user_profile_image');
            //when only info is selected
            } else {
                //update customer info in database
                DB::table('customer_info')
                    ->where('customer_id', $id)
                    ->update([
                        'customer_id' => $customerId,
                        'customer_full_name' => $customerFullName,
                        'customer_email' => $customerEmail,
                        'customer_contact_number' => $customerMobile,
                        'customer_gender' => $customerGender,
                        'customer_dob' => $customerDOB,
                        'customer_address' => $customerAddress,
                        'expiry_date' => $expiry_date,
                        'email_verified' => $email_verify,
                        'referral_number' => $refer_code,
                    ]);
            }

            //update email_verified status of customer
            if ($customerEmail != $contactInfo->customer_email) {
                DB::table('customer_info')
                    ->where('customer_id', $id)
                    ->update([
                        'email_verified' => 0,
                    ]);
                DB::table('subscribers')->where('email', $contactInfo->customer_email)->delete();
            }

            //TO Update all other customer tables
            (new functionController)->updateCustomerId($id, $customerId, 1);
            DB::commit(); //to do query rollback
        } catch (\Exception $e) {
            DB::rollback(); //rollback all successfully executed queries
            return redirect()->back()->with('try_again', 'Please try again with valid values.');
        }
        if ($request->get('prev_url') == url('/customerById') || $request->get('prev_url') == url('/edit-user/'.$id)) {
            return redirect('customers/card_users')->with('status', 'Profile updated!');
        } else {
            return redirect($request->get('prev_url'))->with('status', 'Profile updated!');
        }
    }

    //function for editing user who lost his card
    public function editLostUser($id)
    {
        $profileInfo = DB::table('customer_info as ci')
            ->join('customer_account as ca', 'ca.customer_id', '=', 'ci.customer_id')
            ->join('customer_miscellaneous as cm', 'cm.customer_id', '=', 'ci.customer_id')
            ->join('card_delivery as cd', 'cd.customer_id', '=', 'ci.customer_id')
            ->select('ci.customer_id', 'cd.shipping_address', 'ca.moderator_status', 'cm.*')
            ->where('ci.customer_id', $id)
            ->get();
        $profileInfo = json_decode(json_encode($profileInfo), true);
        if (isset($profileInfo[0]['miscellaneous_id'])) {
            $profileInfo = $profileInfo[0];
            if ($profileInfo['moderator_status'] == 1 && $profileInfo['miscellaneous_id'] == 1) {
                return view('admin.production.edit_lost_user', compact('profileInfo'));
            } else {
                return redirect()->back()->with('user_active', 'User did not lost card');
            }
        } else {
            return redirect()->back()->with('user_active', 'User is not deactivated');
        }
    }

    //============function for insert lost card data in Info_At_Buy_Card table for COD==================
    public function lostUserEditDone(Request $request, $old_customer_id)
    {
        $this->validate($request, [
            'new_customer_id' => 'required|unique:customer_account,customer_id',
            'shipping_address' => 'required',
        ]);
        $request->flashOnly(['new_customer_id', 'shipping_address']);

        //payable amount
        $card_prices = AllAmounts::all();
        $lost_card_price = $card_prices[5]->price;
        $delivery_charge = $card_prices[3]->price;
        $customization_charge = $card_prices[4]->price;

        //get data from edit form
        $new_customer_id = $request->get('new_customer_id');
        $customer_ship_address = $request->get('shipping_address');
        $customization = ($request->get('customization')) == null ? 0 : 1;
        if ($customization == 1) {
            $delivery_type = DeliveryType::lost_card_with_customization;
            $payable_amount = $lost_card_price + $delivery_charge + $customization_charge;
        } else {
            $delivery_type = DeliveryType::lost_card_without_customization;
            $payable_amount = $lost_card_price + $delivery_charge;
        }

        //get customer info from old customer id
        $customer_info = CustomerInfo::where('customer_id', $old_customer_id)->with('account')->first();

        $exists_in_cod = InfoAtBuyCard::where('customer_username', $customer_info->account->customer_username)
            ->where('delivery_type', DeliveryType::lost_card_with_customization)
            ->orWhere('delivery_type', DeliveryType::lost_card_without_customization)
            ->count();
        if ($exists_in_cod > 0) {
            return redirect()->back()->with('exists_in_cod', 'Customer already exists in COD');
        }

        A: //come back to regenerate tran id again if exists
        //generate random text for transaction id
        $random_text = '';
        $codeAlphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $codeAlphabet .= 'abcdefghijklmnopqrstuvwxyz';
        $codeAlphabet .= '0123456789';
        $max = strlen($codeAlphabet); // edited
        for ($i = 0; $i < 15; $i++) {
            $random_text .= $codeAlphabet[random_int(0, $max - 1)];
        }

        $random_text = 'ROYALTYBD'.$random_text;
        $tran_id_exists = DB::table('info_at_buy_card')->where('tran_id', $random_text)->count();
        //regenerate tran id if already exists
        if ($tran_id_exists > 0) {
            goto A;
        }

        try {
            DB::beginTransaction(); //to do query rollback

            DB::table('info_at_buy_card')->insert([
                'customer_id' => $new_customer_id,
                'tran_id' => $random_text,
                'customer_serial_id' => $customer_info->account->customer_serial_id,
                'customer_username' => $customer_info->account->customer_username,
                'password' => $customer_info->account->password,
                'moderator_status' => 1,
                'customer_first_name' => $customer_info->customer_first_name,
                'customer_last_name' => $customer_info->customer_last_name,
                'customer_full_name' => $customer_info->customer_full_name,
                'customer_email' => $customer_info->customer_email,
                'customer_dob' => $customer_info->customer_dob,
                'customer_gender' => $customer_info->customer_gender,
                'customer_contact_number' => $customer_info->customer_contact_number,
                'customer_profile_image' => $customer_info->customer_profile_image,
                'customer_type' => $customer_info->customer_type,
                'month' => 0,
                'card_active' => 0,
                'card_activation_code' => 0,
                'firebase_token' => 0,
                'expiry_date' => '1971-03-26',
                'member_since' => $customer_info->member_since,
                'referral_number' => '0',
                'delivery_type' => $delivery_type,
                'shipping_address' => $customer_ship_address,
                'order_date' => date('Y-m-d'),
                'paid_amount' => $payable_amount,
            ]);
            DB::commit(); //to do query rollback
        } catch (\Exception $e) {
            DB::rollBack(); //rollback all successfully executed queries
        }

        return redirect('/allCOD')->with('info updated', 'Customer info updated!');
    }

    //function for delete customer
    public function deleteCustomer($customerId)
    {
        $cus_info = CustomerAccount::where('customer_id', $customerId)->first();
        $temp_info = InfoAtBuyCard::where('customer_username', $cus_info->customer_username)->first();
        $email = $cus_info->info->customer_email;
        if ($temp_info != null) {
            return redirect('customers/guest')->with('cod exists', 'User already exists in temporary list. First delete from there.');
        } else {
            //First delete all notifications
            DB::beginTransaction(); //to do query rollback
            try {
                //delete like review notification from customer_notification table
                DB::table('customer_notification as cn')
                    ->join('likes_review as lr', 'cn.source_id', '=', 'lr.id')
                    ->where('lr.liker_id', $customerId)
                    ->where('cn.notification_type', 1)
                    ->delete();
                //delete follow customer notification from customer_notification table
                DB::table('customer_notification as cn')
                    ->join('follow_customer as fc', 'fc.id', '=', 'cn.source_id')
                    ->where('fc.follower', $customerId)
                    ->where('cn.notification_type', 8)
                    ->delete();
                //delete Accept follow request notification from customer_notification table
                DB::table('customer_notification as cn')
                    ->join('follow_customer as fc', 'fc.id', '=', 'cn.source_id')
                    ->where('fc.following', $customerId)
                    ->where('cn.notification_type', 9)
                    ->delete();

                //delete follow partner notification from partner_notification table
                DB::table('partner_notification as pn')
                    ->join('follow_partner as fp', 'fp.id', '=', 'pn.source_id')
                    ->where('fp.follower', $customerId)
                    ->where('pn.notification_type', 4)
                    ->delete();
                //delete like post notification from like_post table
                DB::table('partner_notification as pn')
                    ->join('likes_post as lp', 'lp.id', '=', 'pn.source_id')
                    ->where('lp.liker_id', $customerId)
                    ->where('pn.notification_type', 7)
                    ->delete();
                //delete review notification from review table
                DB::table('partner_notification as pn')
                    ->join('review as rev', 'rev.id', '=', 'pn.source_id')
                    ->where('rev.customer_id', $customerId)
                    ->where('pn.notification_type', 2)
                    ->delete();
                //delete refer notification from notification table
                DB::table('customer_notification')
                    ->where('user_id', $customerId)
                    ->where('notification_type', 10)
                    ->delete();
                //get current image name
                $get_current_image_name = CustomerInfo::where('customer_id', $customerId)->select('customer_profile_image')->first();
                $image_path = $get_current_image_name->customer_profile_image;

                //find customer with id
                $customer = CustomerAccount::find($customerId);
                //delete customer from DB Tables using Model
                $customer->delete();
                DB::table('subscribers')->where('email', $email)->delete();
                DB::commit(); //to do query rollback
            } catch (\Exception $e) {
                DB::rollBack(); //rollback all successfully executed queries
                return redirect('customers/guest')->with('try_again', 'Please try again!');
            }

            if (strpos($image_path, 'https://royalty-bd.s3.ap-southeast-1.amazonaws.com/') !== false) {
                $exploded_path = explode('/', $image_path);
                //remove previous profile image from folder
                Storage::disk('s3')->delete('dynamic-images/users/'.end($exploded_path));
            }

            //pusher to logout this user from all browsers
            $pusher = (new pusherController)->initializePusher();
            //Send a message to notify channel with an event name of notify-event
            $pusher->trigger('userLogout', 'userLogout-event', $customerId);

            return redirect('customers/guest')->with('delete customer', 'One customer deleted');
        }
    }

    //function to delete COD customer
    public function deleteCODCustomer($customerId)
    {
        DB::table('info_at_buy_card')
            ->where('customer_id', $customerId)
            ->delete();

        return redirect('/allCOD')->with('delete customer', 'One COD customer deleted.');
    }

    //function to approve guest cod
    public function approveCOD($customerId)
    {
        $cus_info = DB::table('info_at_buy_card')
            ->where('customer_id', $customerId)
            ->where('delivery_type', DeliveryType::guest_user)
            ->orWhere('delivery_type', DeliveryType::lost_card_without_customization)
            ->orWhere('delivery_type', DeliveryType::lost_card_with_customization)
            ->first();

        $mail_info = CustomerAccount::where('customer_username', $cus_info->customer_username)->with('info')->first();

        $date = date('Y-m-d');

        if ($cus_info->paid_amount == null) {
            $card_prices = AllAmounts::all();
            if ($cus_info->delivery_type == DeliveryType::guest_user) {
                $amount = $cus_info->customer_type == 1 ? $card_prices[0]->price : $card_prices[1]->price;
            } elseif ($cus_info->delivery_type == DeliveryType::lost_card_without_customization) {
                $amount = $card_prices[5]->price;
            } elseif ($cus_info->delivery_type == DeliveryType::lost_card_with_customization) {
                $amount = $card_prices[5]->price + $card_prices[4]->price;
            }

            //card promo code checking
            $card_promo_exists = CardPromoCodes::where('id', $cus_info->card_promo_id)->first();
            if ($cus_info->card_promo_id != 0) {
                if ($card_promo_exists->type == 1) {
                    $amount = $amount - $card_promo_exists->flat_rate;
                } elseif ($card_promo_exists->type == 2) {
                    $promo_discount = ($amount * $card_promo_exists->percentage) / 100;
                    $amount -= $promo_discount;
                }
            }
            //delivery charge add
            $amount += $card_prices[3]->price;
        } else {
            $amount = $cus_info->paid_amount;
        }

        try {
            DB::beginTransaction(); //to do query rollback

            //insert into ssl transaction table
            DB::table('ssl_transaction_table')
                ->insert([
                    'customer_id' => $cus_info->customer_id,
                    'status' => 1,
                    'tran_date' => $date,
                    'tran_id' => $cus_info->tran_id,
                    'val_id' => '',
                    'amount' => $amount,
                    'store_amount' => '0.00',
                    'card_type' => 'CASH',
                    'card_no' => '',
                    'currency' => 'BDT',
                    'bank_tran_id' => '',
                    'card_issuer' => '',
                    'card_brand' => '',
                    'card_issuer_country' => '',
                    'card_issuer_country_code' => '',
                    'currency_amount' => '0.00',
                ]);

            $prev_id = DB::table('customer_account')->select('customer_id')->where('customer_username', $cus_info->customer_username)->first();
            //insert into customer info table
            CustomerInfo::where('customer_id', $prev_id->customer_id)->update([
                'customer_id' => $customerId,
                'customer_type' => $cus_info->customer_type,
                'card_active' => 1,
                'delivery_status' => 3,
                'approve_date' => date('Y-m-d H:i:s'),
            ]);

            CustomerAccount::where('customer_id', $prev_id->customer_id)->update(['moderator_status' => 2]);

            if ($cus_info->delivery_type == DeliveryType::lost_card_without_customization || $cus_info->delivery_type == DeliveryType::lost_card_with_customization) {
                //update customer card delivery details
                CardDelivery::where('customer_id', $prev_id->customer_id)->update([
                    'customer_id' => $cus_info->customer_id,
                    'delivery_type' => $cus_info->delivery_type,
                    'shipping_address' => $cus_info->shipping_address,
                    'order_date' => $cus_info->order_date,
                    'paid_amount' => $cus_info->paid_amount,
                ]);
            } else {
                //save delivery address
                DB::table('card_delivery')->insert([
                    'customer_id' => $cus_info->customer_id,
                    'delivery_type' => $cus_info->delivery_type,
                    'shipping_address' => $cus_info->shipping_address,
                    'order_date' => $cus_info->order_date,
                    'paid_amount' => $cus_info->paid_amount,
                ]);
            }

            //TO Update all other customer tables
            (new functionController)->updateCustomerId($prev_id->customer_id, $customerId, 0);

            //TODO: need to be changed if this function will be used anytime

            //            if ($cus_info->referral_number != '0') {
            //                if ($cus_info->customer_type == 1) {
            //                    DB::table('customer_reward as cr')
            //                        ->join('customer_info as ci', 'ci.customer_id', '=', 'cr.customer_id')
            //                        ->where('ci.referral_number', $cus_info->referral_number)
            //                        ->increment('cr.refer_bonus', 25);
            //                    DB::table('customer_info')
            //                        ->where('referral_number', $cus_info->referral_number)
            //                        ->increment('reference_used', 1);
            //                    //add tk to own account
            //                    DB::table('customer_reward as cr')
            //                        ->join('customer_info as ci', 'ci.customer_id', '=', 'cr.customer_id')
            //                        ->where('ci.customer_id', $customerId)
            //                        ->increment('cr.refer_bonus', 25);
            //                    $refer_user_notification_text = "You have received a referral bonus of BDT 25.";
            //                    $referrar_notification_text = $cus_info->customer_full_name . " has joined Royalty. You have received a referral bonus of BDT 25.";
            //                } else {
            //                    DB::table('customer_reward as cr')
            //                        ->join('customer_info as ci', 'ci.customer_id', '=', 'cr.customer_id')
            //                        ->where('ci.referral_number', $cus_info->referral_number)
            //                        ->increment('cr.refer_bonus', 50);
            //                    DB::table('customer_info')
            //                        ->where('referral_number', $cus_info->referral_number)
            //                        ->increment('reference_used', 1);
            //                    //add tk to own account
            //                    DB::table('customer_reward as cr')
            //                        ->join('customer_info as ci', 'ci.customer_id', '=', 'cr.customer_id')
            //                        ->where('ci.customer_id', $customerId)
            //                        ->increment('cr.refer_bonus', 50);
            //                    $refer_user_notification_text = "You have received a referral bonus of BDT 50.";
            //                    $referrar_notification_text = $cus_info->customer_full_name . " has joined Royalty. You have received a referral bonus of BDT 50.";
            //                }
            //                $refer_bonus_from_db = DB::table('all_amounts')->select('price')->where('type', 'refer_bonus')->first();
            //                $cash_coupon_point_notification_text = 'Congratulations! You have earned a cash coupon worth BDT 250.';
            //                //referrar
            //                $referrar = CustomerInfo::where('referral_number', $cus_info->referral_number)->first();
            //                $customer_new_reward = CustomerReward::where('customer_id', $referrar->customer_id)->first();
            //                $refer_bonus_counter = $customer_new_reward->bonus_counter;
            //                $new_refer_bonus = $customer_new_reward->refer_bonus;
            //                //check if refer bonus is greater than 250 or not
            //                while ($new_refer_bonus >= $refer_bonus_from_db->price) {
            //                    $new_refer_bonus -= $refer_bonus_from_db->price;
            //                    $refer_bonus_counter++;
            //                }
            //
            //                //update referrar's reward table
            //                CustomerReward::where('customer_id', $referrar->customer_id)
            //                    ->update(['refer_bonus' => $new_refer_bonus, 'bonus_counter' => $refer_bonus_counter]);
            //                //send refer notification to referrar
            //                CustomerNotification::insert([
            //                    'user_id' => $referrar->customer_id,
            //                    'image_link' => $cus_info->customer_profile_image,
            //                    'notification_text' => $referrar_notification_text,
            //                    'notification_type' => 10,
            //                    'source_id' => $cus_info->customer_id,
            //                    'seen' => 0
            //                ]);
            //                if ($refer_bonus_counter > $customer_new_reward->bonus_counter) { //when completes 250tk limit
            //                    CustomerNotification::insert([
            //                        'user_id' => $referrar->customer_id,
            //                        'image_link' => 'https://s3-ap-southeast-1.amazonaws.com/royalty-bd/static-images/notification/reward.png',
            //                        'notification_text' => $cash_coupon_point_notification_text,
            //                        'notification_type' => 11,
            //                        'source_id' => $customer_new_reward->id,
            //                        'seen' => 0
            //                    ]);
            //                }
            //
            //                //refer user
            //                $refer_user_new_reward = CustomerReward::where('customer_id', $customerId)->first();
            //                $refer_user_point_bonus_counter = $refer_user_new_reward->bonus_counter;
            //                $refer_user_new_refer_bonus = $refer_user_new_reward->refer_bonus;
            //                //check if refer bonus is greater than 250 or not
            //                while ($refer_user_new_refer_bonus >= $refer_bonus_from_db->price) {
            //                    $refer_user_new_refer_bonus -= $refer_bonus_from_db->price;
            //                    $refer_user_point_bonus_counter++;
            //                }
            //                //update refer user's reward table
            //                CustomerReward::where('customer_id', $customerId)
            //                    ->update(['refer_bonus' => $refer_user_new_refer_bonus, 'bonus_counter' => $refer_user_point_bonus_counter]);
            //                //send refer notification to refer user
            //                CustomerNotification::insert([
            //                    'user_id' => $customerId,
            //                    'image_link' => 'https://s3-ap-southeast-1.amazonaws.com/royalty-bd/static-images/notification/refer.png',
            //                    'notification_text' => $refer_user_notification_text,
            //                    'notification_type' => 10,
            //                    'source_id' => $referrar->customer_id,
            //                    'seen' => 0
            //                ]);
            //                if ($refer_user_point_bonus_counter > $refer_user_new_reward->bonus_counter) { //when completes 250tk limit
            //                    CustomerNotification::insert([
            //                        'user_id' => $customerId,
            //                        'image_link' => 'https://s3-ap-southeast-1.amazonaws.com/royalty-bd/static-images/notification/reward.png',
            //                        'notification_text' => $cash_coupon_point_notification_text,
            //                        'notification_type' => 11,
            //                        'source_id' => $refer_user_new_reward->id,
            //                        'seen' => 0
            //                    ]);
            //                }
            //            }
            //save card promo usage data if exists
            if ($cus_info->card_promo_id != 0) {
                $ssl_id = DB::table('ssl_transaction_table')
                    ->select('id')
                    ->where('tran_id', $cus_info->tran_id)
                    ->first();
                CardPromoCodeUsage::insert([
                    'customer_id' => $cus_info->customer_id,
                    'promo_id' => $cus_info->card_promo_id,
                    'ssl_id' => $ssl_id->id,
                ]);
            }

            DB::table('info_at_buy_card')->where('tran_id', $cus_info->tran_id)->delete();

            //TODO: need to be changed if this function will be used anytime

            //            if ($cus_info->referral_number != '0') { //send push notification
            //                //send live push notification to referrar
            //                (new pusherController)->liveCustomerReferNotification($referrar->customer_id);
            //                //send live push notification to refer user
            //                (new pusherController)->liveCustomerReferNotification($customerId);
            //
            //                if ($refer_bonus_counter > $customer_new_reward->bonus_counter) { //when completes 250tk limit
            //                    //send live push notification to referrar
            //                    (new pusherController)->liveCustomerReferCouponNotification($referrar->customer_id);
            //                    (new jsonController)->functionSendGlobalPushNotification($cash_coupon_point_notification_text, $referrar->firebase_token);
            //                }
            //                if ($refer_user_point_bonus_counter > $refer_user_new_reward->bonus_counter) { //when completes 250tk limit
            //                    //send live push notification to refer user
            //                    (new pusherController)->liveCustomerReferCouponNotification($customerId);
            //                    (new jsonController)->functionSendGlobalPushNotification($cash_coupon_point_notification_text, $cus_info->firebase_token);
            //                }
            //
            //                //send notification to app for referrar
            //                (new jsonController)->functionSendGlobalPushNotification($referrar_notification_text, $referrar->firebase_token);
            //                //send notification to app for refer user
            //                (new jsonController)->functionSendGlobalPushNotification($refer_user_notification_text, $cus_info->firebase_token);
            //            }

            if ($cus_info->delivery_type == DeliveryType::guest_user) {
                $this->CODApprovalMail($mail_info->info->customer_full_name, $mail_info->info->customer_email, $cus_info);
            } elseif ($cus_info->delivery_type == DeliveryType::lost_card_without_customization || $cus_info->delivery_type == DeliveryType::lost_card_with_customization) {
                $this->LostCardApprovalMail($cus_info->customer_full_name, $cus_info->customer_email, $cus_info);
            }

            DB::commit(); //to do query rollback
        } catch (\Exception $e) {
            DB::rollBack(); //rollback all successfully executed queries
            return redirect()->back()->with('cod_approval_error', 'Something went wrong! Please contact with IT team.');
        }

        return redirect('allCustomers')->with('codPaymentClear', 'Payment successful');
    }

    public function CODApprovalMail($name, $email, $exist)
    {
        $exp_date = date_create($exist->expiry_date);
        $exp_date = date_format($exp_date, 'M d, Y');
        //send mail
        $to = $email;
        $subject = 'Welcome To The Privileged Club';

        $message_text = 'Dear '.$name.','.'<br><br>';

        $message_text .= 'Congratulations!'.'<br><br>';

        $message_text .= 'Welcome to Royalty - Your Lifestyle Partner.'.'<br><br>';

        $message_text .= 'The payment for your Royalty Premium Membership has been confirmed. We are delighted to inform you that you are now amongst the privileged members on our platform.'.'<br><br>';

        $message_text .= 'You may log into your Royalty user account using the phone number that you have registered with. Your membership will expire on '.$exp_date.'.'.'<br>'.'<br>';

        $message_text .= 'Activation of your card can be done from your user account via our app or website in 24-48 hours upon receiving your card.
                            In case you do not activate your card in 5 to 7 working days, it will be automatically activated. 
                            Please note that your card will have a validity of one year from the date it is activated.'.'<br><br>';

        $message_text .= 'Stay tuned by following our social accounts to find out more about the new exciting partners we are bringing to you!'.'<br><br>';

        $message_text .= 'Please find your receipt attached to this mail.'.'<br><br>';

        $message_text .= 'Should you have any queries, please feel free to contact us at support@royaltybd.com or call us
                            at 096-3862-0202 during our business hours which are Saturday through Thursday [11 AM – 6 PM].'.'<br><br>';

        $message_text .= 'Thank you,'.'<br><br>';
        $message_text .= 'Team Royalty'.'<br>';

        //using zoho mail service
        $smtpAddress = 'smtp.zoho.com';
        $port = 465;
        $encryption = 'ssl';
        $yourEmail = 'support@royaltybd.com';
        $yourPassword = 'SUp963**';

        // Prepare transport
        $transport = new Swift_SmtpTransport($smtpAddress, $port, $encryption);
        $transport->setUsername($yourEmail);
        $transport->setPassword($yourPassword);
        $mailer = new Swift_Mailer($transport);

        $pdf = new \App\Invoice;
        $output = $pdf->generate($exist);
        $attachment = new Swift_Attachment($output, 'invoice.pdf', 'application/pdf');

        $message = new Swift_Message($subject);
        $message->attach($attachment);
        $message->setFrom(['support@royaltybd.com' => 'Royalty']);
        $message->setTo([$to => $name]);
        // If you want plain text instead, remove the second parameter of setBody
        $message->setBody($message_text, 'text/html');

        if ($mailer->send($message)) {
            return Response::json(['result' => 'E-mail sent successfully!']);
        } else {
            return Response::json(['result' => 'Internal Server Error']);
        }
    }

    public function LostCardApprovalMail($name, $email, $exist)
    {
        //send mail
        $to = $email;
        $subject = 'Replacement for your Royalty Membership';

        $message_text = 'Dear '.$name.','."\r\n"."\r\n";

        $message_text .= 'Thank you for contacting us regarding your request for replacing your Royalty Membership. The payment for your replacement card has been confirmed.'."\r\n"."\r\n";
        $message_text .= 'Upon receiving your card, please activate your card by entering the new card number through your user account via our app or website within 24-48 hours. In case you do not activate your card in 5 to 7 working days, it will get activated automatically.'."\r\n"."\r\n";
        $message_text .= 'You will receive your Royalty card within 3-5 working days. Till then, stay tuned by following our social accounts to find out more about the new exciting partners we are bringing to you!'."\r\n"."\r\n";
        $message_text .= 'Please find your receipt attached to this mail.'."\r\n"."\r\n";
        $message_text .= 'Should you have any queries, please feel free to contact us at support@royaltybd.com or call us at 096-3862-0202 during our business hours which are from Saturday through Thursday [11 AM – 6 PM].'."\r\n"."\r\n";

        $message_text .= 'Best Regards,'."\r\n"."\r\n";

        $message_text .= 'Team Royalty'."\r\n";

        //using zoho mail service
        $smtpAddress = 'smtp.zoho.com';
        $port = 465;
        $encryption = 'ssl';
        $yourEmail = 'support@royaltybd.com';
        $yourPassword = 'SUp963**';

        // Prepare transport
        $transport = new Swift_SmtpTransport($smtpAddress, $port, $encryption);
        $transport->setUsername($yourEmail);
        $transport->setPassword($yourPassword);
        $mailer = new Swift_Mailer($transport);

        $pdf = new \App\Invoice;
        $output = $pdf->generate($exist);
        $attachment = new Swift_Attachment($output, 'invoice.pdf', 'application/pdf');

        $message = new Swift_Message($subject);
        $message->attach($attachment);
        $message->setFrom(['support@royaltybd.com' => 'Royalty']);
        $message->setTo([$to => $name]);
        // If you want plain text instead, remove the second paramter of setBody
        $message->setBody($message_text, 'text');

        if ($mailer->send($message)) {
            return Response::json(['result' => 'E-mail sent successfully!']);
        } else {
            return Response::json(['result' => 'Internal Server Error']);
        }
    }

    public function OnlinePaymentMail($name, $email, $exist, $validity)
    {
        $exp_date = CustomerInfo::where('customer_id', $exist->customer_id)->select('expiry_date')->first();
        $exp_date = date_format(date_create($exp_date->expiry_date), 'M d, Y');
        //send mail
        $subject = 'Welcome To The Privileged Club';

        $pdf = new \App\Invoice;
        $output = $pdf->generate($exist);

        $data = [];
        $data['exp_date'] = $exp_date;
        $data['name'] = $name;
        try {
            $mg = (new functionController2())->getMailGun();
            $mg->messages()->send('mail.royaltybd.com', [
                'from' => 'Royalty no-reply@royaltybd.com',
                'to' => $email,
                'subject' => $subject,
                'html' => view('emails.buy_card', ['data' => $data])->render(),
                'attachment' => [
                    ['fileContent' => $output, 'filename' => 'invoice.pdf'],
                ],
            ]);
            return Response::json(['result' => 'E-mail sent successfully!']);
        } catch (\Exception $e) {
            return Response::json(['result' => 'Internal Server Error']);
        }
    }

    public function VirtualUserMail($name, $email, $exist, $validity)
    {
        $exp_date = CustomerInfo::where('customer_id', $exist->customer_id)->select('expiry_date')->first();
        $exp_date = date_format(date_create($exp_date->expiry_date), 'M d, Y');
        $data = [];
        $data['exp_date'] = $exp_date;
        $data['name'] = $name;
        try {
            $mg = (new functionController2())->getMailGun();
            $mg->messages()->send('mail.royaltybd.com', [
                'from' => 'Royalty no-reply@royaltybd.com',
                'to' => $email,
                'subject' => 'Welcome To The Privileged Club',
                'html' => view('emails.welcome', ['data' => $data])->render(),
            ]);
        } catch (\Exception $e) {
        }

//        if ($mg) {
//            return Response::json(array('result' => 'E-mail sent successfully!'));
//        } else {
//            return Response::json(array('result' => 'Internal Server Error'));
//        }
    }

    public function RenewPaymentMail($name, $email, $exist, $validity, $isUpgrade)
    {
        $exp_date = CustomerInfo::where('customer_id', $exist->customer_id)->select('expiry_date')->first();
        $exp_date = date_format(date_create($exp_date->expiry_date), 'M d, Y');

        if ($isUpgrade) {
            $subject = 'Royalty Membership Upgraded';
            $email_cover_image = 'https://royalty-bd.s3-ap-southeast-1.amazonaws.com/static-images/email/UPGRADE.png';
            $email_body = 'The payment for your Royalty Premium Membership has been confirmed. Please find your receipt attached to this email.'."\r\n".
                'Your membership will have a validity of '.$validity.' from today. The membership
                                        will end on '.$exp_date.'.';
        } else {
            $subject = 'Royalty Membership Renewal';
            $email_cover_image = 'https://royalty-bd.s3-ap-southeast-1.amazonaws.com/static-images/email/RENEW.png';
            $email_body = 'The payment for your Royalty Premium Membership renewal has been confirmed. Please find your receipt attached to this email.'."\r\n".
                'Your membership will have a validity of '.$validity.' from today. The membership
                                        will end on '.$exp_date.'.';
        }

        $pdf = new \App\Invoice;
        $output = $pdf->generate($exist);

        $data = [];
        $data['exp_date'] = $exp_date;
        $data['email_cover_image'] = $email_cover_image;
        $data['email_body'] = $email_body;
        $data['name'] = $name;

        try {
            $mg = (new functionController2())->getMailGun();
            $mg->messages()->send('mail.royaltybd.com', [
                'from' => 'Royalty no-reply@royaltybd.com',
                'to' => $email,
                'subject' => $subject,
                'html' => view('emails.upgrade', ['data' => $data])->render(),
                'attachment' => [
                    ['fileContent' => $output, 'filename' => 'invoice.pdf'],
                ],
            ]);
            return Response::json(['result' => 'E-mail sent successfully!']);
        } catch (\Exception $exception) {
            return Response::json(['result' => 'Internal Server Error']);
        }
    }

    public function userExpiryMail($emails, $status)
    {
        $email_body = '';
        $email_cover_image = 'https://royalty-bd.s3-ap-southeast-1.amazonaws.com/static-images/email/RENEW.png';

        $data = [];
        $data['email_cover_image'] = $email_cover_image;
        $data['email_body'] = $email_body;

        if ($status == 'expiring') {
            $subject = 'Your Royalty Membership EXPIRES in 10 days';
            $blade_file = 'emails.user_expiring';
        } elseif ($status == 'expiry') {
            $subject = 'Your Royalty Membership has EXPIRED!';
            $blade_file = 'emails.user_expiry';
        } else {
            $subject = 'Your Royalty Membership has EXPIRED!';
            $blade_file = 'emails.user_expired';
        }

        try {
            $mg = (new functionController2())->getMailGun();
            $mg->messages()->send('mail.royaltybd.com', [
                'from' => 'Royalty no-reply@royaltybd.com',
                'to' => $emails,
                'recipient-variables' => $this->getRecipientJson($emails),
                'subject' => $subject,
                'html' => view($blade_file, ['data' => $data])->render(),
            ]);
        } catch (\Exception $exception) {
            //
        }
    }

    public function sendAttemptedUserEmail($emails)
    {
        try {
            $mg = (new functionController2())->getMailGun();
            $mg->messages()->send('mail.royaltybd.com', [
                'from' => 'Royalty no-reply@royaltybd.com',
                'to' => $emails,
                'recipient-variables' => $this->getRecipientJson($emails),
                'subject' => 'Getting Royalty Membership is Very Easy!',
                'html' => view('emails.send_attempted_user_email')->render(),
            ]);
        } catch (\Exception $exception) {
            //
        }
    }

    public function getRecipientJson($emails, $with_dynamic_content = false)
    {
        $recipients = [];
        foreach ($emails as $email) {
            if ($with_dynamic_content) {
                $recipients[$email['email']]['unique_id'] = (new JsonControllerV2())->getSSLTransactionId();
                $recipients[$email['email']]['total_scan'] = $email['total_scan'];
                $recipients[$email['email']]['outlet_visited'] = $email['outlet_visited'];
                $recipients[$email['email']]['total_review'] = $email['total_review'];
                $recipients[$email['email']]['earned_point'] = $email['earned_point'];
            } else {
                $recipients[$email]['unique_id'] = (new JsonControllerV2())->getSSLTransactionId();
            }
        }

        return json_encode($recipients);
    }

    //function to Update COD
    public function updateCOD(Request $request)
    {
        $card_no = $request->input('card_no');
        $info_id = $request->input('info_id');

        //update info at buy card
        $is_updated = InfoAtBuyCard::where('id', $info_id)
            ->update([
                'customer_id' => $card_no,
                'moderator_status' => 1,
            ]);
        if ($is_updated == 1) {
            return Redirect()->back()->with('info updated', 'Card Number Updated Successfully');
        } else {
            return Redirect()->back()->with('try_again', 'Please Try Again');
        }
    }

    //function to Customer card activation
    public function cardActiveByAdmin($customerId)
    {
        $contactInfo = DB::table('customer_info')
            ->select('customer_contact_number', 'customer_full_name', 'month', 'expiry_date')
            ->where('customer_id', $customerId)
            ->first();
        $phone_number = $contactInfo->customer_contact_number;
        $name = $contactInfo->customer_full_name;
        $months = $contactInfo->month;

        $date = date_create(date('Y-m-d'));
        $expiry_date = date_add($date, date_interval_create_from_date_string($months.' month'));
        $expiry_date = $expiry_date->format('Y-m-d');
        //Checking if lost card customer is being activated
        $deliveryInfo = DB::table('card_delivery')
            ->select('delivery_type')
            ->where('customer_id', $customerId)
            ->first();
        $delivery_type = $deliveryInfo->delivery_type;

        if ($delivery_type == DeliveryType::lost_card_with_customization || $delivery_type == DeliveryType::lost_card_without_customization) {
            $deactiveInfo = DB::table('customer_miscellaneous')
                ->select('deactive_date')
                ->where('customer_id', $customerId)
                ->where('miscellaneous_id', MiscellaneousType::lost_card)
                ->first();
            $deactive_date = date_create($deactiveInfo->deactive_date);
            $todays_date = date_create(date('Y-m-d'));
            $days_difference = date_diff($deactive_date, $todays_date)->format('%a days');
            $new_expiry_date = date('Y-m-d', strtotime($contactInfo->expiry_date.' + '.$days_difference));
            $expiry_date = $new_expiry_date;
        }

        DB::table('customer_info')
            ->where('customer_id', $customerId)
            ->update([
                'expiry_date' => $expiry_date,
                'card_active' => 2,
            ]);

        (new customerController)->activeSuccessSMS($phone_number, $name);

        return redirect('allCustomers')->with('cardActivated', 'Card Activation successful');
    }

    //Function to view add partner page
    public function partnerFormUpload()
    {
        $all_categories = Categories::orderBy('priority', 'DESC')->get();

        return view('admin.production.form_upload', compact('all_categories'));
    }

    public function mainCatWiseSubCats($main_cat_id)
    {
        $sub_cats = DB::table('category_relation as cr')
            ->join('sub_cat_1 as sc1', 'sc1.id', '=', 'cr.sub_cat_1_id')
            ->where('cr.main_cat', $main_cat_id)
            ->distinct('sc1.id')
            ->select('sc1.*')
            ->get();
        foreach ($sub_cats as $sub_cat) {
            $sub_cats_2 = DB::table('category_relation as cr')
                ->join('sub_cat_2 as sc2', 'sc2.id', '=', 'cr.sub_cat_2_id')
                ->where('cr.sub_cat_1_id', $sub_cat->id)
                ->where('cr.main_cat', $main_cat_id)
                ->select('sc2.*', 'cr.*', 'cr.id as cat_rel_id')
                ->get();
            $sub_cat->sub_cat_2 = $sub_cats_2;
        }
        if (count($sub_cats->first()->sub_cat_2) <= 0) {
            $sub_cats = DB::table('category_relation as cr')
                ->join('sub_cat_1 as sc1', 'sc1.id', '=', 'cr.sub_cat_1_id')
                ->where('cr.main_cat', $main_cat_id)
                ->distinct('sc1.id')
                ->select('sc1.*', 'cr.*', 'cr.id as cat_rel_id')
                ->get();
        }
        $sub_cats = $sub_cats->sortBy('cat_name');

        return $sub_cats;
    }

    public function loadSubCats(Request $request)
    {
        $cat_id = $request->post('category_id');
        $sub_cats = $this->mainCatWiseSubCats($cat_id);

        return Response::json(['sub_cats' => $sub_cats, 'first_obj' => $sub_cats->first()]);
    }

    //function for add partner
    public function addPartner(Request $request)
    {
        //check validation of partner's all fields
        $this->validate($request, [
            'category' => 'required',
            'type' => 'required',
            'name' => 'required|unique:partner_info,partner_name',
            'about' => 'required',
            'gallery' => 'required',
            'contract_expiry_date' => 'required',
        ]);

        $request->flashOnly([
            'category', 'type', 'name', 'about', 'contract_expiry_date',
        ]);

        //get all data from table
        $category = $request->get('category');
        $type = $request->get('type');
        $name = $request->get('name');
        $owner = $request->get('owner');
        $owner_contact = $request->get('ownerContact');
        $username = $request->get('username');
        $admin_code = $request->get('admin_code') != null ? $request->get('admin_code') : 'n/a';
        $facebook = $request->get('facebook') != null ? $request->get('facebook') : '#';
        $website = $request->get('website') != null ? $request->get('website') : '#';
        $instagram = $request->get('instagram') != null ? $request->get('instagram') : '#';
        $about = $request->get('about');
        $expiry = date('Y-m-d', strtotime($request->get('contract_expiry_date')));
        $password = $request->get('password') != null ? $request->get('password') : 'n/a';
//        $encrypted_password = (new functionController)->encrypt_decrypt('encrypt', $password);
        $cat_rel_ids = $request->get('cat_rel_ids');
        $cat_rel_ids = json_decode($cat_rel_ids);

        try {
            DB::beginTransaction(); //to do query rollback
            //insert data into partner account table
            $partner_account = new PartnerAccount([
                'username' => $username,
                'password' => $password,
                'admin_code' => $admin_code,
            ]);
            $partner_account->save();
            $partner_id = $partner_account->partner_account_id;

            //insert data into partner info table
            $partner_info = new PartnerInfo([
                'partner_account_id' => $partner_id,
                'partner_name' => $name,
                'owner_name' => $owner,
                'owner_contact' => $owner_contact,
                'partner_category' => $category,
                'partner_type' => $type,
                'facebook_link' => $facebook,
                'website_link' => $website,
                'instagram_link' => $instagram,
                'about' => $about,
                'expiry_date' => $expiry,
            ]);
            $partner_info->save();
            //insert data into partner attribute table of a specific category

            foreach ($cat_rel_ids as $key => $value) {
                $part_cat_rel = new PartnerCategoryRelation([
                    'cat_rel_id' => $value,
                    'partner_id' => $partner_id,
                ]);
                $part_cat_rel->save();
            }

            //insert data into rating table
            Rating::insert([
                'partner_account_id' => $partner_id,
                '1_star' => '0.00',
                '2_star' => '0.00',
                '3_star' => '0.00',
                '4_star' => '0.00',
                '5_star' => '0.00',
                'average_rating' => '0.00',
            ]);

            //upload profile image to AWS & save path to DB
            if ($request->hasFile('profile')) {
                $file = $request->profile;
                //image is being resized & uploaded here
                $image_url = (new functionController)->uploadProPicToAWS($file, 'dynamic-images/partner_pro_pic');
                // image path saved to the database
                PartnerProfileImage::insert([
                    'partner_account_id' => $partner_id,
                    'partner_profile_image' => $image_url,
                ]);
            }
            //upload profile cover Photo to AWS & save path to DB
            if ($request->hasFile('cover_pic')) {
                $file = $request->cover_pic;
                //image is being resized & uploaded here
                $image_url = (new functionController)->uploadImageToAWS($file, 'dynamic-images/partner_cover_pics');
                // image path saved to the database
                PartnerProfileImage::where('partner_account_id', $partner_id)
                    ->update([
                        'partner_cover_photo' => $image_url,
                    ]);
            }
            //upload Thumbnail image to AWS & save path to DB
            // if ($request->hasFile('thumb_pic')) {
            //     $file = $request->file('thumb_pic');
            //     //image is being resized & uploaded here
            //     $image_url = (new functionController)->uploadImageToAWS($file, 'dynamic-images/partner_thumb_pics');
            //     // image path saved to the database
            //     PartnerProfileImage::where('partner_account_id', $partner_id)
            //         ->update([
            //             'partner_thumb_image' => $image_url
            //         ]);
            // }
            //upload profile image to AWS & save path to DB
            if ($request->hasFile('menu')) {
                $files = $request->menu;
                foreach ($files as $file) {
                    //image is being resized & uploaded here
                    $image_url = (new functionController)->uploadImageToAWS($file, 'dynamic-images/partner_menu_image');
                    //image path saved to the database
                    PartnerMenuImages::insert([
                        'partner_account_id' => $partner_id,
                        'partner_menu_image' => $image_url,
                    ]);
                }
            }
            //upload gallery image to AWS & save path to DB
            if ($request->hasFile('gallery')) {
                $files = $request->gallery;
                foreach ($files as $key => $file) {
                    //image is being resized & uploaded here
                    $image_url = (new functionController)
                        ->uploadImageToAWS($file, 'dynamic-images/partner_gallery_image');
                    //image path saved to the database
                    if ($key == 0) {
                        PartnerGalleryImages::insert([
                            'partner_account_id' => $partner_id,
                            'partner_gallery_image' => $image_url,
                            'pinned' => 1,
                        ]);
                    } else {
                        PartnerGalleryImages::insert([
                            'partner_account_id' => $partner_id,
                            'partner_gallery_image' => $image_url,
                        ]);
                    }
                }
            }
            (new \App\Http\Controllers\AdminNotification\functionController())
                ->newPartnerAddNotification($partner_info);

            DB::commit(); //to do query rollback
        } catch (\Exception $e) {
            DB::rollBack(); //rollback all successfully executed queries
            return redirect()->back()->with('try_again', 'Please try again!');
        }

        //redirect to all partner page after successfully adding new partner
        return redirect('/admin/add-branch/'.$partner_id)
            ->with('partner_basic_info_added', 'Basic info of a partner added. Please complete the task by inserting at
             least one branch!');
    }

    //function to get Thumbnail Pic of partner
    public function proPic($partner_id)
    {
        $proPic = PartnerProfileImage::where('partner_account_id', $partner_id)->first();

        return view('/admin/production/proPic', compact('proPic'));
    }

    //function to update pro pic of partner
    public function updateProPic(Request $request, $partner_id)
    {
        $this->validate($request, [
            'proPic' => 'required',
        ]);
        $image_file = $request->file('proPic');
        //image is being resized & uploaded here
        $image_url = (new functionController)->uploadProPicToAWS($image_file, 'dynamic-images/partner_pro_pic');

        $get_current_image_name = PartnerProfileImage::where('partner_account_id', $partner_id)->first();
        $image_path = $get_current_image_name->partner_profile_image;
        $exploded_path = explode('/', $image_path);

        try {
            DB::beginTransaction(); //to do query rollback

            //image path saved to the database
            PartnerProfileImage::where('partner_account_id', $partner_id)->update([
                'partner_profile_image' => $image_url,
            ]);
            //update pro pic in notification table
            (new functionController)->updateImgLinkInNotification($image_path, $image_url);
            //remove previous profile image from bucket
            Storage::disk('s3')->delete('dynamic-images/partner_pro_pic/'.end($exploded_path));

            DB::commit(); //to do query rollback
        } catch (\Exception $e) {
            $image_url = explode('/', $image_url);
            Storage::disk('s3')->delete('dynamic-images/partner_pro_pic/'.end($image_url));
            DB::rollBack(); //rollback all successfully executed queries
            return redirect()->back()->with('try_again', 'Please try again!');
        }

        return redirect('edit_pro_pic/'.$partner_id)->with('updated', 'Successful');
    }

    //function to show gallery images
    public function galleryImage($partner_id)
    {
        $galleryImages = PartnerGalleryImages::where('partner_account_id', $partner_id)->get();
        $partner_name = PartnerInfo::where('partner_account_id', $partner_id)->select('partner_name')->first();

        return view('admin.production.partnerGalleryImages', compact('galleryImages', 'partner_id', 'partner_name'));
    }

    //function to add new gallery image
    public function addGalleryImage(Request $request, $partner_id)
    {
        $this->validate($request, [
            'gallery' => 'required',
        ]);
        //insert gallery images in database
        if ($request->hasFile('gallery')) {
            $files = $request->file('gallery');
            $countPreviousImages = PartnerGalleryImages::where('partner_account_id', $partner_id)->count();

            if ($countPreviousImages + count($files) <= 20) {
                foreach ($files as $file) {
                    try {
                        DB::beginTransaction(); //to do query rollback
                        //image is being resized & uploaded here
                        $image_url = (new functionController)->uploadImageToAWS($file, 'dynamic-images/partner_gallery_image');
                        //image path saved to the database
                        PartnerGalleryImages::insert([
                            'partner_account_id' => $partner_id,
                            'partner_gallery_image' => $image_url,
                        ]);
                        DB::commit(); //to do query rollback
                    } catch (\Exception $e) {
                        $image_url = explode('/', $image_url);
                        Storage::disk('s3')->delete('dynamic-images/partner_gallery_image/'.end($image_url));
                        DB::rollBack(); //rollback all successfully executed queries
                        return redirect()->back()->with('try_again', 'Please try again!');
                    }
                }
            } else {
                return redirect()->back()->with('try_again', 'Number of images exceeds the limit!');
            }
        } else {
            dd('image not set');
        }

        return redirect('partner-gallery-images/'.$partner_id)->with('updated', 'Successful');
    }

    //function to add gallery image caption
    public function addGalleryCaption(Request $request)
    {
        //collect caption & image id from ajax request
        $imageId = $request->input('id');
        $caption = $request->input('caption');
        //update image caption
        PartnerGalleryImages::where('id', $imageId)->update(['image_caption' => $caption]);

        $updated[0] = 'updated';
        $updated[1] = $imageId;

        return Response::json($updated);
    }

    //function to add gallery image caption
    public function pinGalleryImage($partner_id, $img_id)
    {
        try {
            DB::beginTransaction(); //to do query rollback
            //update pinned image
            PartnerGalleryImages::where('partner_account_id', $partner_id)
                ->update(['pinned' => 0]);
            PartnerGalleryImages::where('id', $img_id)
                ->update(['pinned' => 1]);
            DB::commit(); //to do query rollback
        } catch (\Exception $e) {
            DB::rollBack(); //rollback all successfully executed queries
            return redirect()->back()->with('try_again', 'Please try again!');
        }

        return redirect()->back()->with('pinned_img_changed', 'Pinned image changed.');
    }

    //function to delete gallery image of partner
    public function deleteGalleryImage($id)
    {
        $partner_gallery_image = PartnerGalleryImages::findOrFail($id);

        $get_current_image_path = $partner_gallery_image->partner_gallery_image;
        $exploded_path = explode('/', $get_current_image_path);
        try {
            DB::beginTransaction(); //to do query rollback
            //remove image path from partner_gallery_images table
            $partner_gallery_image->delete();
            //remove gallery image from bucket
            Storage::disk('s3')->delete('dynamic-images/partner_gallery_image/'.end($exploded_path));
            DB::commit(); //to do query rollback
        } catch (\Exception $e) {
            DB::rollBack(); //rollback all successfully executed queries
            return redirect()->back()->with('try_again', 'Please try again!');
        }

        return redirect('partner-gallery-images/'.$partner_gallery_image->partner_account_id)->with('updated', 'Successfully deleted');
    }

    //function to add branch
    public function addBranch($id)
    {
        $partner = PartnerInfo::where('partner_account_id', $id)->first();
        $facilities = \App\BranchFacility::whereRaw('JSON_CONTAINS(category_ids, ?)', [json_encode($partner->partner_category)])->get();
        $all_areas = Area::all();
        $all_divs = Division::all();

        return view('admin.production.addBranch', compact('partner', 'facilities', 'all_areas', 'all_divs'));
    }

    //function to store branch
    public function storeBranch(Request $request)
    {
        $this->validate($request, [
            'contact' => 'required',
        ]);
        $request->flashOnly(['contact']);
        //get all data from table
        $partner_id = $request->get('partner');
        // $username = $request->get('username');
        // $password = $request->get('password');
        // $encrypted_password = (new functionController)->encrypt_decrypt('encrypt', $password);
        $email = $request->get('email') == null ? '0' : $request->get('email');
        $contact = $request->get('contact');
        $address = $request->get('address');
        $location = $request->get('location');
        $longitude = $request->get('longitude');
        $latitude = $request->get('latitude');
        $zip = $request->get('zipCode');
        $area = $request->get('area');
        $division = $request->get('division');

        //check if this partner has any branch or not
        $main_branch_exists = PartnerBranch::where('partner_account_id', $partner_id)->count();
        if ($main_branch_exists > 0) {
            $main_branch = 0;
        } else {
            $main_branch = 1;
        }

        //Opening hours
        $sat = $request->get('sat');
        $sun = $request->get('sun');
        $mon = $request->get('mon');
        $tues = $request->get('tues');
        $wed = $request->get('wed');
        $thu = $request->get('thu');
        $fri = $request->get('fri');
        //facilities
        $facilities = \App\BranchFacility::all();
        $facility_ids = [];
        foreach ($facilities as $key => $facility) {
            if ($request->input(str_replace(' ', '_', $facility->name)) != null) {
                array_push($facility_ids, $facility->id);
            }
        }

        try {
            DB::beginTransaction(); //to do query rollback
            //insert data into partner branch table
            $partner_branch = new PartnerBranch();
            $partner_branch->username = 0;
            $partner_branch->password = 0;
            $partner_branch->partner_account_id = $partner_id;
            $partner_branch->partner_email = $email;
            $partner_branch->partner_mobile = $contact;
            $partner_branch->partner_address = $address;
            $partner_branch->partner_location = $location;
            $partner_branch->longitude = $longitude;
            $partner_branch->latitude = $latitude;
            $partner_branch->zip_code = $zip;
            $partner_branch->partner_area = $area;
            $partner_branch->partner_division = $division;
            $partner_branch->main_branch = $main_branch;
            $partner_branch->facilities = $facility_ids;
            $partner_branch->save();

            //insert data into all_coupons table (Refer Bonus)
//            AllCoupons::insert(
//                [
//                    'branch_id' => $last_inserted_id[0]['id'],
//                    'coupon_type' => 2,
//                    'reward_text' => '250 Tk. Off',
//                    'coupon_details' => '250 Tk. Off',
//                    'coupon_tnc' => 'Terms and Conditions are applied as per refer bonus policy by Royalty',
//                    'stock' => 'unlimited',
//                    'expiry_date' => DB::raw("( SELECT `expiry_date` FROM `partner_info`
//                                            WHERE `partner_account_id` = $partner_id)"),
//                ]
//            );
            //insert data into opening hours table
            OpeningHours::insert([
                'branch_id' => $partner_branch->id,
                'sat' => $sat,
                'sun' => $sun,
                'mon' => $mon,
                'tue' => $tues,
                'wed' => $wed,
                'thurs' => $thu,
                'fri' => $fri,
            ]);
            (new \App\Http\Controllers\AdminNotification\functionController())
                ->newBranchAddNotification($partner_branch);

            DB::commit(); //to do query rollback
        } catch (\Exception $e) {
            DB::rollback(); //rollback all successfully executed queries
            return Redirect()->back()->with('try_again', 'Something went wrong. Please contact with IT Team');
        }

        return Redirect()->back()->with('branch added', 'New Branch Added Successfully');
    }

    //Card Promo Active/Deactive
    public function activate_card_promo($id)
    {
        DB::table('card_promo')->where('id', $id)->update(['active' => 1]);

        return redirect()->back();
    }

    public function deactivate_card_promo($id)
    {
        DB::table('card_promo')->where('id', $id)->update(['active' => 0]);

        return redirect()->back();
    }

    //function to show all contacts
    public function allContacts()
    {
        $contacts = Contact::orderBy('id', 'DESC')->get();

        return view('admin.production.all-contacts', compact('contacts'));
    }

    //function to delete contact
    public function deleteContact($id)
    {
        //find contact with id
        Contact::findOrFail($id)->delete();

        return \redirect()->back()->with('contact-deleted', 'Contact deleted successfully');
    }

    //function to influencer requests
    public function influencerRequests()
    {
        $influencer_requests = InfluencerRequest::orderBy('id', 'DESC')->get();

        return view('admin.production.influencerRequests', compact('influencer_requests'));
    }

    //function to delete influencer requests
    public function deleteInfluencerRequest($id)
    {
        InfluencerRequest::findOrFail($id)->delete();

        return redirect('admin/influencer-requests')->with('deleted', 'Request deleted');
    }
}
