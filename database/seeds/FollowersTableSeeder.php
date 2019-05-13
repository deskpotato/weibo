<?php

use Illuminate\Database\Seeder;
use App\Models\User;
class FollowersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = User::all();
        $user = $users->first();
        $user_id = $user->id;

        //获取去除掉ID=1的所有用户ID数组
        $followers = $users->slice(1);
        $followers_ids = $followers->pluck('id')->toArray();

        //1号用户关注其他所有用户
        $user->follow($followers_ids);

        //其他用户关注1号用户
        foreach($followers as $follower){
            $follower->follow($user_id);
        }
    }
}
