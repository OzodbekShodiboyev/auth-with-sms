<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use GuzzleHttp\Client;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\RedirectResponse;
use Illuminate\Auth\Events\Registered;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Session;
use GuzzleHttp\Exception\RequestException;


class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */

    public function create(): View
    {
        return view('auth.register');
    }



    public function code(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'phone' => 'required|unique:users',
        ], [
            'phone.unique' => 'Ushbu raqam avval ro\'yxatdan o\'tgan!',
        ]);
    
        $code = rand(1000, 9999);
        $name = $request->name;
        $phone = $request->phone;
        $curl = curl_init();
    
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'notify.eskiz.uz/api/auth/login',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array('email' => 'ozodbekonline1@gmail.com', 'password' => 'rxM2y4kO3rXbTzflpnZWgXFg9CjVyMUOAfeC9Y04'),
        ));
    
        $response = curl_exec($curl);
    
        curl_close($curl);
        $responseData = json_decode($response, true);
    
        if (isset($responseData['data']['token'])) {
            $token = $responseData['data']['token'];
    
            // Make the subsequent request with the token
            $curl = curl_init();
    
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'notify.eskiz.uz/api/message/sms/send',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => array('mobile_phone' => $phone, 'message' => "$name, sizning kodingiz: $code", 'from' => '4546'),
                CURLOPT_HTTPHEADER => array(
                    'Authorization: Bearer ' . $token // Pass the token in the Authorization header
                )
            ));
    
            $response = curl_exec($curl);
            curl_close($curl);
            // echo $phone;
            return view('auth.code', compact('name', 'phone', 'code'));
        } else {
            return redirect()->back()->withErrors(['phone' => 'Error: Failed to retrieve the token.']);
        }
    }
    

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => 'required',
            'code' => 'required'
        ]);
        $name = $request->name;
        $phone = $request->phone;
        $sms = $request->sms;
        $code = $request->code;
        if ($sms == $code) {
            $curl = curl_init();
            $user = User::create([
                'name' => $request->name,
                'phone' => $request->phone,
            ]);
            event(new Registered($user));

            Auth::login($user);
            return redirect(RouteServiceProvider::HOME);
        } else {
            Session::put('flash_message', 'Kod xato kiritildi!');
            return view('auth.code',compact('name','phone','code'));
        }
    }
}
