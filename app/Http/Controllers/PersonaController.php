<?php

namespace App\Http\Controllers;

use App\Models\Persona;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PersonaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        
        $personas = Persona::with(['user'])->orderBy('id', 'desc')->get();
        
        return response()->json($personas, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // validacion
        $request->validate([
            "nombres" => "required|string|min:3|max:30",
            "apellidos" => "string|min:3|max:50"
        ]);


        $nombres = $request->nombres;
        $apellidos = $request->apellidos;

        // DB::insert("INSERT INTO personas (nombres, apellidos) values (?,?)", [$nombres, $apellidos]);

        // $persona = DB::table("personas")->insert(["nombres" => $nombres, "apellidos" => $apellidos]);

        $persona = new Persona;
        $persona->nombres = $nombres;
        $persona->apellidos = $apellidos;
        $persona->save();

        return response()->json(["mensaje" => "Persona Registrada"], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
         $persona = Persona::with(['user'])->find($id);

        return response()->json($persona, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
         $request->validate([
            "nombres" => "required",
            "user_id" => "required"
        ]);

        $persona = Persona::find($id);

$       $persona->nombres = $request->nombres;
        $persona->apellidos = $request->apellidos;
        $persona->ci = $request->ci;
        $persona->fecha_nacimiento = $request->fecha_nacimiento;
        $persona->estado = $request->estado;
        $persona->user_id = $request->user_id;
        $persona->update();

        return response()->json(["mensaje" => "Persona Actualizada"], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        
    }

     public function funGuardarPersonaUser(Request $request){
        // validar Datos Personales y Usuario
        $request->validate([
            "nombres" => "required|min:2|max:30",
            "apellidos" => "required|min:2|max:50",
            "email" => "required|email|unique:users",
            "password" => "required|min:6|string"
        ]);

        DB::beginTransaction();

        try {
            // guardar user
            $u = new User();
            $u->name = $request->nombres;
            $u->email = $request->email;
            $u->password = Hash::make($request->password);
            $u->save();       
    
            // guardar persona
            $p = new Persona;
            $p->nombres = $request->nombres;
            $p->apellidos = $request->apellidos;
            $p->user_id = $u->id;
            $p->save();
          
            DB::commit();
            // respuesta correcta
            // all good
            return response()->json(["mensaje" => "Datos registrados correctamente"], 201);

        } catch (\Exception $e) {
            // respuesta de error (revertir)
            DB::rollback();
            // something went wrong
            return response()->json(["mensaje" => "Ocurrió un error al registrar los datos", "error" => $e->getMessage()], 400);
        }
    }

    public function funAddUserPersona(Request $request, $id){

         // validar Datos Personales y Usuario
         $request->validate([
            "email" => "required|email|unique:users",
            "password" => "required|min:6|string"
        ]);

        DB::beginTransaction();

        try {
            $persona = Persona::find($id);

            // guardar user
            $u = new User();
            $u->name = $persona->nombres;
            $u->email = $request->email;
            $u->password = Hash::make($request->password);
            $u->save();   

            // asignamos la cuenta de usuaario a la persona
            $persona->user_id = $u->id;

            $persona->update();

            DB::commit();

            return response()->json(["mensaje" => "cuenta asiganda a la persona"], 201);

        } catch (\Exception $e) {
            // respuesta de error (revertir)
            DB::rollback();
            // something went wrong
            return response()->json(["mensaje" => "Ocurrió un error al asignar cuenta de usuario", "error" => $e->getMessage()], 400);
        }
    }
}
