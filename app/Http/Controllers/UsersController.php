<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class UsersController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth',[
            'except'=>['show','create','store','index','confirmEmail']
        ]);

        $this->middleware('guest',[
            'only'=>['create']
        ]);
    }


    //用户列表
    public function index()
    {
        // $users = User::All();    
        $users = User::paginate(10);
        return view('users.index',compact('users'));
    }

    //注册页面get
    public function create()
    {
        return view('users.create');
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
            // Auth::login($userCreated);
            // session()->flash('success','欢迎，您将在这里开启一段新的旅程~');
            // return redirect()->route('users.show',[$userCreated->id]);

            $this->sendEmailConfirmationTo($userCreated);
            session()->flash('success','邮件已发送，请登录邮箱进行激活~');
            return redirect('/');
        
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

    /**
     * 删除用户
     */
    public function destroy(User $user)
    {
        //授权
        $this->authorize('destroy',$user);
        $user->delete();
        session()->flash('success','成功删除用户!');
        return back();
    }


    /**
     * 邮件激活
     */
    public function confirmEmail($token)
    {
        $user = User::where('activation_token',$token)->firstOrFail();
        $user->activated = true;
        $user->activation_token = null;
        $user->save();
        Auth::login($user);
        session()->flash('success','恭喜你，激活成功');
        return redirect()->route('users.show',[$user]);
    }

    /**
     * 发送邮件
     */
    protected function sendEmailConfirmationTo($user)
    {
        $view = "emails.confirm";
        $data = compact('user');
        $to = $user->email;
        $subject = '感谢您注册 weibo 应用!请确认您的邮箱';
        Mail::send($view, $data, function ($message)use($to,$subject) {
            $message->to($to);
            $message->subject($subject);
        });
    }

    //用户中心
    public function show(User $user)
    {
        $statuses = $user->statuses()->orderBy('created_at','desc')->paginate(10);    
        return view('users.show',compact('user','statuses'));
    }

    //用户关注列表
    public function followings(User $user)
    {
        $users = $user->followings()->paginate(30);
        $title = $user->name.'关注的人';
        return view('users.show_follow',compact('users','title'));
    }

    //用户粉丝列表
    public function follwers(User $user)
    {
        $users = $user->followers()->paginate(30);
        $title = $user->name.'的粉丝';
        return view('users.show_follow',compact('users','title'));

    }

}
