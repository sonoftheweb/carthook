### Project description

Build an API serving data from JsonPlaceholder.

### Setup
1. In the .env file (copy .env.example to .env) setup your DB.
2. Run `php artisan setup`
3. Run `php artisan serve` to access the api endpoint.


##### Observations
1. Querying JsonPlaceholder's API would result in slow responses. We could avoid this by caching the request vie Guzzle 
(implemented with duration defined in .env file) or caching the data in the local DB and refreshing data with Schedules 
every x duration, depending on how fresh we want data to be.

2. JsonPlaceholder has a some request controls _limit, _start, _page etc that control the amount of requested
data that comes bac to us. We also can make direct queries to resources using item keys like "name" and "email" to filter out 
resource items with the values. I was unable to find a search parameter.

After reviewing the requirements, I deduced that a call to the local API would not need to hit the JsonPlaceholder's api 
directly, but we would want some freshness to the data returned. Direct hits to the API from our request took as much as
5 seconds making it slow for a deployed application. I did think of caching the calls to the API using Laravel's Caching 
system, but that would leave a ton of un-managed cache keys to work with. I decided to cache as a middleware in Guzzle.

##### My thoughts and implementation
I started out by building out the database to hold cached data from JsonPlaceholder. In the `users` table, I added a FullText 
index to make searching for `users.name`, `users.email` and `users.username` much quicker, while having them indexed for filtering, of course with the downside of 
it adding to the overall size of the database. I did the same for `posts` table with `posts` having its fulltext
search pointing to `posts.title`. In the `comment` table, there are no fulltext indexes. In both posts and comments, foreign 
keys are defined so `posts.userId` references `users.id` and set to cascade on delete (if a user is deleted, the posts of the user is deleted too),
`comments.postId` references `posts.id` and set to cascade on delete.

I intended to make the application simple, so I used the Controller resource provided by Laravel. I defined the routes for 
`users`, `posts` and `posts.comments`; one line per route. I thought about having manually defining routes like this:

`Routes::get('{resource}', 'GeneralApiResourceController@getItems)';`
`Routes::get('{resource}/{id}', 'GeneralApiResourceController@getItem)';`
`Routes::get('{resource}/{id}/{relationships}', 'GeneralApiResourceController@getItemRelationsData)';`

The above would map the resource to a controller and model as well as Collection and Resources. While this is a valid approach,
it seems too big for the amount of work that is required in this test. "Keep it as simple as you can" is what I always go by.
In the end I went with this:

`Route::resource('users', 'API\UserController')->only(['index', 'show']);`
`Route::resource('posts', 'API\PostController')->only(['index', 'show']);`
`Route::resource('users.posts', 'API\PostController')->shallow();`
`Route::resource('posts.comments', 'API\CommentController')->shallow();`

I think it's wise not to have the controller directly access the models because if we were to change the model engine 
or even the database engine, we would have to re-work the codes that access the data from the models... In all the files!
So I created repositories that I can inject into the controllers for all models. If any change were to be made in the database engine, 
we would have just one point of reference to make those changes either by swapping the repo for a new repository which 
supports the changes to the engine via `App/Providers/RepositoryServiceProvider.php`.

Let's talk about getting data from JsonPlaceholder's API. We have two ways of getting data:
1. rely on first queries to seed data to our database (in the background via queues).
2. following your recommendation to seed data into the local database before search is done.

I went with the second option as it is the requirement. I implemented a helper class `App/Helpers/ExternalApiHelper.php` that takes data from the JsonPlaceholder 
using Guzzle 6. The method `makeGetRequest` takes either a string, or an array as the path. The reason for an array is because 
we will need to make multiple calls, and I would rather have them concurrent. All calls in the array are simultaneous. This means that the call with 5 requests will only,
be as slow as the slowest request in the bunch.

I decided that it would be best to seed the data from JsonPlaceholder's API when the application's being setup. 

`I should have used docker for this... :(`

To make the application a lot simpler to set up, instead of having to run three commands, I put in place a single command 
that does all at once: `php artisan setup`. This fires a fresh migration (:fresh) and runs the seeder who seeds the tables 
with data from JsonPlaceholder. Note that in place is the `DB::beginTransaction()` and `DB::commit()` to ensure that data
is rolled back if something goes wrong.

##### Recommendations and what to do in the future
1. Add Scheduling to the application. Depending on how fresh you'd want data to be (assuming data is constantly being modified)
I would suggest a Schedule be setup to run every x minutes. This schedule should also be queued. The Schedule may run 
the setup command, or we may create a new command to clear the database and refresh with new data (or introspect data and make updates).

2. Seeing that we are getting part of the data from JsonPlaceholder's Api, I would write a method to find data in JsonPlaceholder's API
if and only if no data is found in the local DB. We can inject this in the repository or on the controller or as part of another action triggered by the user.

3. Indexing's awesome, but has its own issues (size of DB etc).  Let's implement an in-memory data structure store like 
Redis. For Fulltext searches (if the application is that large), we could go with a self hosted Elasticsearch implementation or 
managed ones like Algolia.
