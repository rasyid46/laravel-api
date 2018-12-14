<?php

namespace App\Http\Controllers;

    use App\User;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Hash;
    use Illuminate\Support\Facades\Validator;
    use JWTAuth;
    use Tymon\JWTAuth\Exceptions\JWTException;
    use DB;
    class UserController extends Controller

 
    {
        public function authenticate(Request $request)
        {
            $credentials = $request->only('email', 'password');
 
            try {
                if (! $token = JWTAuth::attempt($credentials)) {
                    return response()->json(['error' => 'invalid_credentials'], 400);
                }
            } catch (JWTException $e) {
                return response()->json(['error' => 'could_not_create_token'], 500);
            }

              $data = DB::table('users as a')
                  ->select('a.id', 'a.first_name', 'a.last_name', 'a.email', 'c.slug as role', 'c.name as role_name')
                  ->leftjoin('role_users as b', 'b.user_id', '=', 'a.id')
                  ->leftjoin('roles as c', 'c.id', '=', 'b.role_id')
                  ->where('email', $request->email)
                  ->get()->first();


                            
       $users = [
            'token' => 'Bearer '.$token, 
            'user_id' => $data->id,
            'name' => $data->first_name.' '.$data->last_name,
            'email' => $data->email, 
            'role' => $data->role,
            'role_name' => $data->role_name
       ];
        // all good so return the token
        return response()->json([ 
            'message' => 'Login Berhasil', 
            'contents'=> $users,
            'code' =>200
        ],200);
 


        }

       


        public function register(Request $request)
        {
                $validator = Validator::make($request->all(), [
                'first_name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:6|confirmed',
            ]);

            if($validator->fails()){
                    return response()->json($validator->errors()->toJson(), 400);
            }

            $user = User::create([
                'name' => $request->get('name'),
                'email' => $request->get('email'),
                'password' => Hash::make($request->get('password')),
            ]);

            $token = JWTAuth::fromUser($user);

            return response()->json(compact('user','token'),201);
        }

        public function getAuthenticatedUser()
            {

                    try {

                        if (! $user = JWTAuth::parseToken()->authenticate()) {
                                return response()->json(['user_not_found'], 404);
                        }

                    } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

                            return response()->json(['token_expired'], $e->getStatusCode());

                    } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {

                            return response()->json(['token_invalid'], $e->getStatusCode());

                    } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {

                            return response()->json(['token_absent'], $e->getStatusCode());

                    }

                    return response()->json(compact('user'));
            }

            public function me()
		    {
                $token = JWTAuth::getToken();
                $user = JWTAuth::parseToken()->authenticate();

                dd($user);
		    }

            public function destroy( Request $request )
            {
                $logout = JWTAuth::invalidate();
                 return response()->json(['message' => 'logout success'], 200);
            }

    }