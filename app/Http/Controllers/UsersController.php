<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UsersController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth',[
            'except'=>['show','create','store','index']
        ]);

        $this->middleware('guest',[
            'only'=>['create']
        ]);
    }


    //用户列表
    public function index()
    {
        $users = User::All();    
        dd($users);
    }

    //注册页面get
    public function create()
    {
        return view('users.create');
    }


    //用户中心
    public function show(User $user)
    {
        return view('users.show',compact('user'));
    }

    //注册
    public function store(Request $request){

        //验证表单数据
        $this->validate($request,[
            'name'=>'required|max:50',
            'email'=>'required|email|unique:users|max:255',
            'password'=>'required|confirmed|min:6'
        ]);

        $userCreated = User::create([
            'name'=>$request->name,
            'email'=>$request->email,
            'password'=>bcrypt($request->password),
            ]);
            Auth::login($userCreated);
            session()->flash('success','欢迎，您将在这里开启一段新的旅程~');
        return redirect()->route('users.show',[$userCreated->id]);
        
    }

    public function edit(User $user)
    {
        $this->authorize('update',$user);
        return view('users.edit',compact('user'));
    }

    //更新
    public function update(User $user,Request $request)
    {
        $this->authorize('update',$user);
         //验证表单数据
         $this->validate($request,[
            'name'=>'required|max:50',
            'password'=>'nullable|confirmed|min:6'
        ]);    

        $data = [];
        $data['name'] = $request->name;
        if($request->password){
            $data['password'] = bcrypt($request->password);
        }
        $user->update($data);

        session()->flash('success','个人资料更新成功!');

        return redirect()->route('users.show',$user->id);
    }
}
