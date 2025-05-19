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
            'password' => Hash::make($request->password),
            'rol' => 'Administrador', // Asignar rol 
        ]);

        // Redireccionar después del registro
        return redirect()->route('login')->with('success', 'Registro exitoso. Por favor inicia sesión.');
    }

     public function login(Request $request)
    {
        // Validación de los datos del formulario
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Intentar autenticar al usuario
        if (Auth::attempt($credentials)) {
            // Regenerar la sesión para prevenir fixation attacks
            $request->session()->regenerate();

            // Redireccionar al dashboard o página principal después del login
            return redirect()->intended('/');
        }

        // Si la autenticación falla, retornar con errores
        return back()->withErrors([
            'email' => 'Las credenciales proporcionadas no coinciden con nuestros registros.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}