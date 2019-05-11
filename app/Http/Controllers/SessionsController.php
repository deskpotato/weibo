<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SessionsController extends Controller
{


    public function __construct()
    {
        $this->middleware('guest',[
            'only'=>['create']
        ]);
    }
    //登录页面get
    public function create()
    {
        return view('sessions.create');
    }

    //登录post
    public function store(Request $request)
    {
        $credentials = $this->validate($request,[
            'email'=>'required|email|max:255',
            'password'=>'required'
        ]);
        if(Auth::attempt($credentials,$request->has('remember'))){
            //判断用户邮件是否激活
            if(Auth::user()->activated){
                //登录成功之后的相关操作
                session()->flash('success','欢迎回来!');
                return redirect()->route('users.show',[Auth::user()]);
            }else{
                
                Auth::logout();
                session()->flash('warning','您的账号未激活，请检查邮箱中的注册邮件进行激活');
                return redirect('/');
            }
                
        }else{
            //登陆失败之后的相关操作
            session()->flash('danger','抱歉，您的邮箱和密码不匹配');
            return redirect()->back()->withInput();
        }
    }

    public function destroy()
    {
        Auth::logout();
        session()->flash('success','您已成功退出');
        return redirect('login');
    }
}
