<?php

namespace App\Http\Controllers;

use App\Jobs\FetchExchangeRates;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * UserController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['only' => ['update', 'destroy']]);
    }

    /**
     * @param Request $req
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function login(Request $req)
    {
        $credentials = $req->only('email', 'password');
        if (!Auth::once($credentials)) {
            return respond(['error' => 'Invalid credentials.'], 401);
        }

        $user = Auth::user();
        return respond(['user' => $user->toArray()]);
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function index()
    {
        return respond(['error' => 'Not implemented'], 501);
    }

    /**
     * @param int $userId
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function show(int $userId)
    {
$f = new FetchExchangeRates();
$f->handle();

        $user = User::find($userId);
        if ($user) {
            return respond(['user' => $user]);
        }

        return respond(['error' => 'User does not exist'], 404);
    }

    /**
     * @param int $userId
     * @param Request $req
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function update(int $userId, Request $req)
    {
        $authUser = Auth::user();
        if (!$authUser->isAdmin() && $authUser->id !== $userId) {
            return respond(['error' => 'Access forbidden.'], 401);
        }

        $rules = [
            'email' => ['required', 'email:rfc', 'unique:users,email,'. $userId, 'max:255'],
            'password' => ['required', Password::min(8)],
        ];
        $validator = Validator::make($req->all(), $rules);
        if ($validator->fails()) {
            $errors = $validator->errors();
            return respond(['errors' => $errors], 422);
        }

        $user = User::find($userId);
        if (!$user) {
            return respond(['error' => 'Unknown user.'], 404);
        }
        $user->email = $req->email;
        $user->password = Hash::make($req->password);
        $user->save();

        return respond();
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function destroy(int $userId)
    {
        $authUser = Auth::user();
        if (!$authUser->isAdmin() && $authUser->id !== $userId) {
            return respond(['error' => 'Access forbidden.'], 401);
        }

        $user = User::find($userId);
        if (!$user) {
            return respond(['error' => 'Unknown user.'], 404);
        }
        $user->delete();

        return respond();
    }

    /**
     * @param Request $req
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function store(Request $req)
    {
        $rules = [
            'email' => ['required', 'email:rfc', 'unique:users,email', 'max:255'],
            'password' => ['required', Password::min(8)],
        ];
        $validator = Validator::make($req->all(), $rules);
        if ($validator->fails()) {
            $errors = $validator->errors();
            return respond(['errors' => $errors], 422);
        }

        $apiToken = Str::random(80);

        User::create([
            'email' => $req->email,
            'password' => Hash::make($req->password),
            'api_token' => $apiToken,
        ]);

        return respond(['api_token' => $apiToken]);
    }
}
