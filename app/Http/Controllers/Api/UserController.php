<?php

namespace App\Http\Controllers\Api;

use App\User;
use App\Mail\UserCreated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\ApiController;

class UserController extends ApiController
{
    // public function __construct()
    // {
    //     $this->middleware('auth:api');
    // }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::all();

        return $this->showAll($users);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
        ];

        $this->validate($request, $rules);

        $data = $request->all();
        $data['password'] = bcrypt($request->password);
        $data['verified'] = User::USUARIO_NO_VERIFICADO;
        $data['verification_token'] = User::generateVerificationToken();
        $data['admin'] = User::USUARIO_REGULAR;

        $user = User::create($data);

        return $this->showOne($user, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        return $this->showOne($user);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        $rules = [
            'email' => 'email|unique:users,email,' . $user->id,
            'password' => 'min:6|confirmed',
            'admin' => 'in:'.User::USUARIO_ADMINISTRADOR.','.User::USUARIO_REGULAR,
        ];

        $this->validate($request, $rules);

        if($request->has('name')) $user->name = $request->name;

        if($request->has('email') && $user->email != $request->email)
        {
            $user->verified = User::USUARIO_NO_VERIFICADO;
            $user->verification_token = User::generateVerificationToken();
            $user->email = $request->email;
        }

        if($request->has('password')) $user->password = bcrypt($request->password);

        if($request->has('admin'))
        {
            if(!$user->esVerificado())
            {
                return $this->errorResponse('Únicamento los usuarios verificados pueden cambiar su valor de administrador', 409);
            }

            $user->admin = $request->admin;
        }

        if(!$user->isDirty())
        {
            return $this->errorResponse('Se debe especificar al menos un valor diferente para actualizar.', 422);
        }

        $user->save();

        return $this->showOne($user, 201);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $user->delete();

        return $this->showOne($user);
    }

    public function verify($token)
    {
        $user = User::where(['verification_token' => $token])->firstOrFail();

        $user->verified = User::USUARIO_VERIFICADO;
        $user->verification_token = null;
        $user->save();

        return $this->showMessage('La cuenta ha sido verificada');
    }

    public function resend(User $user)
    {
        if($user->esVerificado())
        {
            return $this->errorResponse('Este usuario ya ha sido verificado', 409);
        }

        retry(5, function() use($user) {
            Mail::to($user)->send(new UserCreated($user));
        }, 100);

        return $this->showMessage('El correo de verificaión se ha reenviado');
    }
}
