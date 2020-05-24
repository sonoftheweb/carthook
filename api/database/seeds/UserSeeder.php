<?php

use App\Helpers\DataTransformer;
use App\Helpers\ExternalApiHelper;
use App\Models\Comment;
use App\Models\Post;
use App\Models\UserAsAModel;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserSeeder extends Seeder
{
    protected $model = 'App\Models\UserAsAModel';

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        try {
            DB::beginTransaction();

            $users = ExternalApiHelper::makeGetRequest('users');

            $postPaths = [];
            foreach ($users as &$user) {
                $user['password'] = Hash::make('password');

                //transform the user
                $user = DataTransformer::transform(UserAsAModel::class, $user);
                $postPaths[] = 'user/'.$user['id'].'/posts?_limit=5';
            }

            // insert users into DB
            UserAsAModel::insert($users);

            // Concurrently get the data from api for all user posts
            $postsByUsers = ExternalApiHelper::makeGetRequest($postPaths);
            $posts = call_user_func_array('array_merge', $postsByUsers);

            // insert into post
            Post::insert($posts);

            $commentsPaths = array_map(function ($id) {
                return 'posts/'.$id.'/comments';
            }, array_column($posts, 'id'));

            // Concurrently get the data from api for all posts comments
            $commentsByPosts = ExternalApiHelper::makeGetRequest($commentsPaths);

            $comments = call_user_func_array('array_merge', $commentsByPosts);

            // insert into post
            Comment::insert($comments);

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage().' in ' . $e->getFile() . ' on line ' . $e->getLine());
        }
    }
}
