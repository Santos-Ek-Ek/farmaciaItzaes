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

    // verificar si el usuario existe y está activo
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
        $usuarios = User::where('activo', 1)->get();
        return view('content.empleados',compact('usuarios'));
    }

        // Función para registrar un nuevo empleado
 public function agregarEmpleado(Request $request)
{
    try {
        // Validación de datos
        $validated = $request->validate([
            'nombre' => 'required|string|max:50',
            'apellido' => 'required|string|max:50',
            'email' => 'required|email|unique:users,email',
            'telefono' => 'nullable|string|digits:10|regex:/^[0-9]{10}$/',
            'rol' => 'required|in:admin,empleado',
            'password' => [
                'required',
                'confirmed',
            ]
        ]);

        // Crear el usuario
        $user = User::create([
            'nombre' => $validated['nombre'],
            'apellidos' => $validated['apellido'],
            'email' => $validated['email'],
            'telefono' => $validated['telefono'] ?? null,
            'rol' => $validated['rol'],
            'password' => Hash::make($validated['password']),
            'activo' => 1
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Empleado registrado exitosamente',
            'data' => $user
        ], 201);

    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error de validación',
            'errors' => $e->errors()
        ], 422);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error al procesar la solicitud',
            'error' => $e->getMessage()
        ], 500);
    }
}

public function editarEmpleado($id)
{
    $empleado = User::findOrFail($id);
    return response()->json([
        'success' => true,
        'empleado' => $empleado
    ]);
}

public function actualizarEmpleado(Request $request, $id)
{
    $empleado = User::findOrFail($id);
    
    $validated = $request->validate([
        'nombre' => 'required|string|max:50',
        'apellidos' => 'required|string|max:50',
        'email' => 'required|email|unique:users,email,'.$id,
        'telefono' => 'nullable|string|digits:10',
        'rol' => 'required|in:Administrador,Empleado',
        'password' => 'nullable|min:8|confirmed'
    ]);

    // Solo actualizar la contraseña si se proporcionó
    $data = $request->only(['nombre', 'apellidos', 'email', 'telefono', 'rol']);
    if ($request->filled('password')) {
        $data['password'] = Hash::make($request->password);
    }

    $empleado->update($data);
    
    return response()->json([
        'success' => true,
        'message' => 'Empleado actualizado correctamente'
    ]);
}

public function eliminarEmpleado($id)
{
    try {
        $empleado = User::findOrFail($id);
        $empleado->activo = 0;
        $empleado->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Empleado desactivado correctamente'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error al desactivar empleado',
            'error' => $e->getMessage()
        ], 500);
    }
}

}