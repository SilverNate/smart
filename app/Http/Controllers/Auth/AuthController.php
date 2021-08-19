<?php
namespace App\Http\Controllers\Auth;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User as ModelsUser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    //
    public function login(Request $request)
    {
        $request->validate(['email' => 'required|string|email', 'password' => 'required|string']);

        $validator = Validator::make(
            ['password' => $request->password], ['password' => 'required|string'], ['email' => $request->email], ['email' => 'required|string|email|unique:users'],);

            if ($validator->fails())
            {
                return response()
                    ->json(['errors' => $validator->errors() ], 406);
            }

        $credentials = request(['email', 'password']);

        if (!Auth::attempt($credentials)) return response()->json(['message' => 'Unauthorized'], 401);

        $user = $request->user();
        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->token;

        if ($request->remember_me) $token->expires_at = Carbon::now()
            ->addWeeks(1);
        $token->save();

        return response()
            ->json(['access_token' => $tokenResult->accessToken, 'token_type' => 'Bearer', 'expires_at' => Carbon::parse($tokenResult
            ->token
            ->expires_at)
            ->toDateTimeString() ]);
    }

    public function registerAdmin(Request $request)
    {
        $validator = Validator::make(
        ['school_name' => $request->school_name], ['school_name' => 'required|string'], ['email' => $request->email], ['email' => 'required|string|email|unique:users'], ['password' => $request->password], ['password' => 'required|string'],);

        if ($validator->fails())
        {
            return response()
                ->json(['errors' => $validator->errors() ], 406);
        }

        $check = ModelsUser::where('email', $request->email)->first();
        if(!empty($check)){
            return response()
            ->json(['message' => 'E-mails already used.'], 400);
        }

        $user = new ModelsUser();
        $user->username = "School Admin";
        $user->school_name = $request->school_name;
        $user->school_id = mt_rand(1,9999);
        $user->email = $request->email;
        $user->role_id = Config::get('constants.roles_id.school_admin');
        $user->role = Config::get('constants.roles.1');
        $user->password = bcrypt($request->password);
        $user->save();
        return response()
            ->json(['message' => 'Successfully created Admin!'], 201);
    }

    public function registerUser(Request $request)
    {
        $roles = $request->user('api')->role_id;
        $schoolIds = $request->user('api')->school_id;
        $schoolName =  $request->user('api')->school_name;

        if($roles == Config::get('constants.roles_id.school_admin')){

            $validator = Validator::make(
                ['username' => $request->username], ['username' => 'required|string'],
                ['email' => $request->email], ['email' => 'required|string|email|unique:users'],
                ['role' => $request->role], ['role' => 'required|string'],);

                if ($validator->fails())
                {
                    return response()
                        ->json(['errors' => $validator->errors() ], 406);
                }

            $check = ModelsUser::where('email', $request->email)->first();

            if(!empty($check)){
                    return response()
                    ->json(['message' => 'E-mails student/teacher already used.'], 400);
            }

            //create random str for pass
            $password = bin2hex(openssl_random_pseudo_bytes(4));

            $user = new ModelsUser();
            $user->username = $request->username;
            $user->email = $request->email;
            $user->role_id = $request->role;
            $user->role = Config::get("constants.roles.{$request->role}");
            $user->school_id = $schoolIds;
            $user->school_name = $schoolName;
            $user->password = bcrypt($password);
            $user->save();

            /**
             * if success we should sent email data to user
             *
             **/

            return response()
                ->json(['message' => 'Successfully created user teacher or student.'], 201);
        }

        return response()
                ->json(['message' => 'Please contact your school admin to register.'], 400);
    }

    public function logout(Request $request)
    {
        $request->user()
            ->token()
            ->revoke();
        return response()
            ->json(['message' => 'Successfully logged out', ]);
    }

    public function user(Request $request)
    {
        return response()->json($request->user());
    }
}

