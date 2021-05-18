<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;
use Spatie\Geocoder\Geocoder;
use Carbon\Carbon;
use App\Models\Client;
use App\Models\User;

class ApiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    public function register(Request $request){
        $confirmPassowrd = empty($request->input('user.passwordConfirmation')) ? '' : $request->input('user.passwordConfirmation');
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:100',
            'address1' => 'required',
            'city' => 'required|max:100',
            'state' => 'required|max:100',
            'country' => 'required|max:100',
            'zipcode' => 'required|max:20',
            'phoneNo1' => 'required|max:20',
            'user.firstName' => 'required|max:50',
            'user.lastName' => 'required|max:50',
            'user.password' => [
                            'required',
                            'max:256',
                            function ($attribute, $value, $fail) use($confirmPassowrd) {
                                if ($value !== $confirmPassowrd) {
                                    $fail('The password confirmation does not match.');
                                }
                            }],
            'user.passwordConfirmation' => 'required',
            'user.phone' => 'required|max:20',
        ]);

        if ($validator->fails()) {    
            return response()->json($validator->messages(), Response::HTTP_BAD_REQUEST);
        }

        $client = [];
        $client['client_name'] = $request->input('name');
        $client['address1'] = $request->input('address1');
        $client['address2'] = $request->input('address2', '');
        $client['city'] = $request->input('city');
        $client['state'] = $request->input('state');
        $client['country'] = $request->input('country');
        $client['zip'] = $request->input('zipcode');
        $client['phone_no1'] = $request->input('phoneNo1');
        $client['phone_no2'] = empty($request->input('phoneNo2')) ? '' : $request->input('phoneNo2');
        $client['start_validity'] = Carbon::now();
        $client['end_validity'] = Carbon::now()->addDays(15);

        $address = $client['address1']. ' ' . $client['address2'] . ', ' . $client['city'] . ', '. $client['state']. ' '. $client['zip']. ', '. $client['country'];

        $cache_address = $client['address1']. ' ' . $client['address2'] . ' ' . $client['city'] . ' '. $client['state']. ' '. $client['zip']. ' '. $client['country'];
        $cache_address = str_replace(' ', '_', $cache_address);
        
        $coodinates = Cache::get($cache_address, '');

        if(empty($coodinates)){
            $ct = new \GuzzleHttp\Client();
            $geocoder = new Geocoder($ct);
            $geocoder->setApiKey(config('geocoder.key'));
            $geocoder->setCountry(config('geocoder.country', 'US'));
            $coodinates = $geocoder->getCoordinatesForAddress($address);
            Cache::store('redis')->put($cache_address, ['lat' => $coodinates['lat'], 'lng' => $coodinates['lng']]);
        }
        
        $client['latitude'] = $coodinates['lat'];
        $client['longitude'] = $coodinates['lng'];

        $client = Client::create($client);

        $user = [];
        $user['client_id'] = $client->id;
        $user['first_name'] = $request->input('user.firstName');
        $user['last_name'] = $request->input('user.lastName');
        $user['password'] = $request->input('user.password');
        $user['phone'] = $request->input('user.phone');
        $user = User::create($user);

        return response()->json([
            'message' => 'Successfully registered!'
        ]);
    }

    public function accounts(Request $request){
        $clients = Client::paginate(10)->toArray();
        $response = [];
        $response['data'] = $clients['data'];
        $response['links']['path'] = $clients['path'];
        $response['links']['firstPageUrl'] = $clients['first_page_url'];
        $response['links']['lastPageUrl'] = $clients['last_page_url'];
        $response['links']['nextPageUrl'] = $clients['next_page_url'];
        $response['links']['prevPageUrl'] = $clients['prev_page_url'];
        $response['meta']['currentPage'] = $clients['current_page'];
        $response['meta']['from'] = $clients['from'];
        $response['meta']['lastPage'] = $clients['last_page'];
        $response['meta']['perPage'] = $clients['per_page'];
        $response['meta']['to'] = $clients['to'];
        $response['meta']['total'] = $clients['total'];
        $response['meta']['count'] = count($response['data']);
        return response()->json($response);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
