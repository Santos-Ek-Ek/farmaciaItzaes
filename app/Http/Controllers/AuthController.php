<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
        public function verInicioSesion(){
        return view('auth.login');
    }
    public function verRegistro(){
        return view('auth.register');
    }
public function register(Request $request)
    {
        // Validación de los datos del formulario
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Si la validación falla, retornar los errores
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Crear el nuevo usuario
        $user = User::create([
            'nombre' => $request->nombre,
            'apellidos' => $request->apellido,
            'email' => $request->email,
            'telefono'=>$request->telefono,
            'password' => Hash::make($request->password),
            'rol' => 'Administrador', // Asignar rol 
            'activo' => 1
        ]);

        // Redireccionar después del registro
        return redirect()->route('login')->with('success', 'Registro exitoso. Por favor inicia sesión.');
    }

public function login(Request $request)
{
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    // Primero, verificar si el usuario existe y está activo
    $user = User::where('email', $request->email)->where('activo', 1)->first();

    if ($user && Hash::check($request->password, $user->password)) {
        Auth::login($user);
        $request->session()->regenerate();
        return redirect()->intended('inicio');
    }

    // Mensaje de error específico
    $errorMsg = !User::where('email', $request->email)->exists()
        ? 'El correo no está registrado.'
        : ($user ? 'Contraseña incorrecta.' : 'La cuenta está inactiva.');

    return back()->withErrors(['email' => $errorMsg])->onlyInput('email');
}

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    public function verEmpleados(){
        return view('content.empleados');
    }
}