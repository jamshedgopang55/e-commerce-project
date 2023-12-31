<?php

namespace App\Http\Controllers\Admin;

use Stripe;
use App\Models\discountCoupon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;

class discountCodeController extends Controller
{
    public function index(Request $request)
    {
        $discountCoupons = discountCoupon::orderBy('id' , 'desc')->paginate(10);

        if ($request->get('keyword')) {
            $discountCoupons = discountCoupon::where('name', 'like', '%' . $request->get('keyword') . '%')->paginate(10);

        }
        $data['discountCoupons'] = $discountCoupons;

        return view('admin.coupon.list', $data);
    }
    public function create()
    {
        return view('admin.coupon.create');
    }
    public function store(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'code' => 'required|unique:discount_coupons,code|regex:/\\A[a-zA-Z0-9_\\-]+\\z/',
            'type' => 'required',
            'name' => 'required|unique:discount_coupons,name',
            'discount_amount' => 'required|numeric',
            'status' => 'required',
        ]);
        if ($validator->passes()) {

            ///Starting  Date Must Be Grater than Current Date
            if (!empty($req->start_at)) {
                $now = Carbon::now();
                $start_at = Carbon::createFromFormat('Y-m-d H:i:s', $req->start_at);
                if ($start_at->lte($now) == true) {
                    return response()->json([
                        'status' => false,
                        'errors' => ['start_at' => 'Start Date Can not be less than current date time']
                    ]);
                }
            }



            //expire date must be grater than Start Date

            if (!empty($req->start_at) && !empty($req->expires_at)) {
                $expires_at = Carbon::createFromFormat('Y-m-d H:i:s', $req->expires_at);
                $start_at = Carbon::createFromFormat('Y-m-d H:i:s', $req->start_at);
                if ($expires_at->gt($start_at) == false) {
                    return response()->json([
                        'status' => false,
                        'errors' => ['expires_at' => 'Expiry date must be grater than Start Date']
                    ]);
                }
            }


            $discount = new discountCoupon();
            $discount->code = $req->code;
            $discount->name = $req->name;
            $discount->description = $req->description;
            $discount->max_uses = $req->max_uses;
            $discount->max_uses_user = $req->max_uses_user;
            $discount->type = $req->type;
            $discount->discount_amount = $req->discount_amount;
            $discount->min_amount = $req->min_amount;
            $discount->status = $req->status;
            $discount->start_at = $req->start_at;
            $discount->expires_at = $req->expires_at;
            $discount->save();

            $stripe = new Stripe\StripeClient(env('STRIPE_SECRET'));
            if ($req->type == 'percent') {

                $stripe->coupons->create([
                    'id' => $req->code,
                    "name" => $req->name,
                    'currency' => 'USD',
                    'percent_off' => $req->discount_amount,
                    'duration' => 'once',
                ]);

            } else {

                $stripe->coupons->create([
                    'id' => $req->code,
                    "name" => $req->name,
                    'currency' => 'USD',
                    'amount_off' => $req->discount_amount * 100,
                    'duration' => 'once',
                ]);

            }


            $req->session()->flash('success', 'Discount Coupon added successfully');
            return response()->json([
                'status' => true,
                'message' => 'Discount Coupon added successfully'
            ]);
        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }
    public function edit(Request $req, $id)
    {
        $coupon = discountCoupon::find($id);
        if ($coupon == null) {
            session()->flash('error', 'Record Not Found');
            return redirect()->route('coupons.index');
        }
        $data['coupon'] = $coupon;
        return view('admin.coupon.edit', $data);
    }
    public function update(Request $req, $id)
    {
        $discount = discountCoupon::find($id);
        if ($discount == null) {
            session()->flash('error', 'Record Not Found');
            return redirect()->route('coupons.index');
        }
        $validator = Validator::make($req->all(), [

            'status' => 'required',
        ]);
        if ($validator->passes()) {
            $stripe = new Stripe\StripeClient(env('STRIPE_SECRET'));
            $stripe = new Stripe\StripeClient(env('STRIPE_SECRET'));
            if ($req->type == 'percent') {



                $stripe->coupons->update(
                    $discount->code,
                    [
                        'metadata' => [
                            'id' => $req->code,
                            'name' => $req->name,
                        ],
                        // 'percent_off' => $req->discount_amount,
                    ]
                );


            } else {

                $stripe->coupons->update(
                    $discount->code,
                    [
                        'metadata' => [
                            'id' => $req->code,
                            "name" => $req->name,
                        ],
                        // 'amount_off' => $req->discount_amount * 100,
                    ]
                );

            }
            //expire date must be grater than Start Date

            if (!empty($req->start_at) && !empty($req->expires_at)) {
                $expires_at = Carbon::createFromFormat('Y-m-d H:i:s', $req->expires_at);
                $start_at = Carbon::createFromFormat('Y-m-d H:i:s', $req->start_at);
                if ($expires_at->gt($start_at) == false) {
                    return response()->json([
                        'status' => false,
                        'errors' => ['expires_at' => 'Expiry date must be grater than Start Date']
                    ]);
                }
            }

            $discount->description = $req->description;
            $discount->max_uses = $req->max_uses;
            $discount->max_uses_user = $req->max_uses_user;
            $discount->min_amount = $req->min_amount;
            $discount->status = $req->status;
            $discount->start_at = $req->start_at;
            $discount->expires_at = $req->expires_at;
            $discount->save();
            $req->session()->flash('success', 'Discount Coupon added successfully');
            return response()->json([
                'status' => true,
                'message' => 'Discount Coupon Updated successfully'
            ]);
        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }
    public function destroy(Request $req, $id)
    {
        $discountCoupon = discountCoupon::find($id);

        if (empty($discountCoupon)) {
            session()->flash('error', 'Record Not Found');
            return response()->json([
                'status' => false,
                'errors' => "Record Not Found"
            ]);
        }
        ;

        $discountCoupon->delete();

        $req->session()->flash('success', 'Coupon Deleted successfully');
        return response()->json([
            'status' => true,
            'errors' => "Coupon Deleted successfully"
        ]);
    }
}
