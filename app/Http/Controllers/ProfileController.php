<?php

namespace App\Http\Controllers;
use Auth;
use Hash;
use Storage;

//use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    //
    public function __construct(){
      $this->middleware('auth');
    }

    public function index() {
      return view('user.profile');
    }

    public function update(Request $request){
      $rules = [
        'name'  => 'required|string|min:3|max:191',
        'email' => 'required|email|min:3|max:191',
        'password' => 'nullable|string|min:8|max:191',
        'image'=> 'nullable|image|max:1999'
      ];

      $request->validate($rules);

      $user = Auth::user();
      $user->name = $request->name;
      $user->email = $request->email;

      if($request->hasFile('image')){
        //get image file
        $image = $request->image;
        //get just extension
        $ext = $image->getClientOriginalExtension();
        //make unique name
        $filename = uniqid().'.'.$ext;
        //upload the image
        $image->storeAs("public/images/{$user->image}");
        //delete the previous image
        Storage::delete("public/images/{$user->image}");
        //this column has a default value so don't need to set it empty
        $user->image = $filename;
      }

      if($request->password){
        $user->password = Hash::make($request->password);
      }

      $user->save();
      return redirect()
        ->route('profile.index')
        ->with('status','Your profile has been updated!');
    }

}
