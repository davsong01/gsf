<?php

namespace App\Http\Controllers;

use App\Models\Field;
use App\Models\Stakeholder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class FieldController extends Controller
{
    public function index()
    {
        $count = 1;
        $fields = Field::with(['zones', 'chapters', 'stakeholder'])->get();
        
        return view('admin.fields.index', compact('fields', 'count'));
    }

    public function create()
    {
        return view('admin.fields.create');
    }

    public function edit(Field $field)
    {
        return view('admin.fields.edit', compact('field'));
    }

    public function store(Request $request)
    {
        $data = $this->validate($request, [
            'name' => 'required|unique:fields,name',
        ]);

        $field = Field::create([
            'name' => $data['name'],
        ]);
        
        return redirect(route('fields.index'))->with('message', 'Field succesfully created');
    }

    public function update(Request $request, Field $field)
    {
        $field->update($request->all());
    
        return redirect()->back()->with('message', 'Update successful!');
    }

    public function destroy($id)
    {
        $field = Field::findOrFail($id);
        $field->delete(); 
        return redirect()->back()->with('message', 'delete successful!');
    }
    
}
