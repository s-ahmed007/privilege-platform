<?php

namespace App\Http\Controllers\Search;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class functionController extends Controller
{
    public function getSearched($key)
    {
        return DB::table('partner_info as pi')
            ->join('partner_branch as pb', 'pb.partner_account_id', '=', 'pi.partner_account_id')
            ->join('partner_profile_images as ppi', 'ppi.partner_account_id', '=', 'pi.partner_account_id')
            ->join('partner_account as pa', 'pa.partner_account_id', '=', 'pi.partner_account_id')
            ->select('pb.partner_area as partner_area', 'pb.id as branch_id', 'pi.partner_account_id as id', 'pi.partner_name as name', 'ppi.partner_profile_image as image')
            ->where('pa.active', 1)
            ->where('pb.active', 1)
            ->where('pi.partner_name', 'LIKE', '%'.$key.'%')
            ->orWhereRaw('REPLACE (pi.partner_name," ","") LIKE "%'.str_replace(' ', '%', $key).'%" AND pa.active = 1 AND pb.active = 1')
            ->orWhere([['pb.partner_area', 'LIKE', '%'.$key.'%'], ['pa.active', 1], ['pb.active', 1]])
            ->orWhereRaw('REPLACE (pb.partner_area," ","") LIKE "%'.str_replace(' ', '%', $key).'%" AND pa.active = 1 AND pb.active = 1')
            ->get();
    }

    public function getSoundSearch($keyword)
    {
        return  DB::select("SELECT pi.partner_account_id as id, pi.partner_name as name, ppi.partner_profile_image as image, pb.partner_area as partner_area,pb.id as branch_id
                                    FROM partner_info pi
                                    LEFT JOIN partner_account pa
                                    ON pa.partner_account_id = pi.partner_account_id
                                    LEFT JOIN partner_profile_images ppi
                                    ON pi.partner_account_id = ppi.partner_account_id
                                    LEFT JOIN partner_branch pb
                                    ON pi.partner_account_id = pb.partner_account_id
                                    LEFT JOIN rating rat
                                    ON rat.partner_account_id = pi.partner_account_id
                                    WHERE (pi.partner_name SOUNDS LIKE '%$keyword%' OR pi.partner_name LIKE '%$keyword%' 
                                        OR pb.partner_area SOUNDS LIKE '%$keyword%' OR pb.partner_area LIKE '%$keyword%')
                                    AND pa.active = 1 AND pb.active = 1");
    }
}
