<?php

namespace App\Http\Controllers;

use App\Http\Resources\EmployeeResource;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = Employee::latest()->paginate(5);

        return new EmployeeResource(true, 'List Data Employees', $employees);
    }

    public function store(Request $request)
    {
        //define validation rules
        $data = $request->all();
        $validator = Validator::make($data, [
            'image'             => 'mimes:jpg,jpeg,png|max:2048',
            'name'              => 'required',
            'phone'             => 'required',
            'address'           => 'required',
            'status'            => 'required',
        ], [
            'image.mimes'               => 'Format file tidak sesuai. File harus JPG atau PNG.',
            'image.max'                 => 'Ukuran file terlalu besar. Ukuran maksimum file adalah 2MB (2048KB).',
            'name.required'             => 'Nama karyawan harus diisi.',
            'phone.required'            => 'No telepon harus diisi.',
            'address.required'          => 'alamat harus diisi.',
            'status.required'           => 'status harus diisi.'
        ]);

        //check if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //upload image
        if ($request->image) {
            $filename = Storage::disk('uploads')->put('image', $request->image);
            $data['image'] = $filename;
        }

        Employee::create($data);

        //return response
        return new EmployeeResource(true, 'Data Post Berhasil Ditambahkan!', $data);
    }

    public function show($id)
    {
        $employees = Employee::find($id);

        dd('uploads/image/' . basename($employees->image));

        return new EmployeeResource(true, 'Detail data karyawan', $employees);
    }

    public function update(Request $request, $id)
    {
        $data = $request->all();
        $employees = Employee::find($id);

        if (!$employees) {
            return response()->json(['message' => 'Karyawan tidak ditemukan'], 404);
        }

        $validator = Validator::make($data, [
            'image'             => 'mimes:jpg,jpeg,png|max:2048',
            'name'              => 'required',
            'phone'             => 'required',
            'address'           => 'required',
            'status'            => 'required',
        ], [
            'image.mimes'       => 'Format file tidak sesuai. File harus JPG atau PNG.',
            'image.max'         => 'Ukuran file terlalu besar. Ukuran maksimum file adalah 2MB (2048KB).',
            'name.required'     => 'Nama karyawan harus diisi.',
            'phone.required'    => 'No telepon harus diisi.',
            'address.required'  => 'alamat harus diisi.',
            'status.required'   => 'status harus diisi.'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        if ($request->hasFile('image')) {
            $filename = Storage::disk('uploads')->put('image', $request->image);
            $data['image'] = $filename;
            if (@$employees->image) {
                File::delete('./uploads/image/' . basename($employees->image));
            }
        }
        $employees->update($data);

        return new EmployeeResource(true, 'Data karyawan berhasil diubah!', $employees);
    }

    public function destroy($id)
    {
        $employees = Employee::find($id);

        File::delete('uploads/image/' . basename($employees->image));
        $employees->delete();

        return new EmployeeResource(true, 'Data karyawan berhasil dihapus!', $employees);
    }
}
