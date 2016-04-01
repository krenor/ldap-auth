# 1. Create an AuthController

Create your Authentication Controller manually or use
`php artisan make:controller LdapAuthController`


```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;

class AuthController extends Controller
{
    
    /**
     * Show the application login form.
     *
     * @return Response
     */
    public function getLoginForm()
    {
        return view('login');
    }

    /**
     * Handle a login request to the application.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function login(Request $request)
    {
    	// Validate credentials
    	$this->validate($request, [
            'username' => 'required',
            'password' => 'required',
        ]);
        
        if ( auth()->attempt( $request->only('username', 'password') ) ) {
            // Redirect to indented page or fall back to index page
            return redirect()->intended('/');
        }

        return redirect()->back()->withErrors(
             'Username and/or Password are not matching!'
        );
    }

    /**
     * Log the user out of the application.
     *
     * @return Response
     */
    public function getLogout()
    {
        auth()->logout();

        return redirect('/');
    }
}

```

# 2. Configure the routes

Note: It is important to place these views in the 'web' middleware!  
Otherwise sessions will not persist.

```php
Route::get('/', function () {
	return view('home');
});

Route::group(['middleware' => ['web']], function () {
    Route::get('/login', 'AuthController@getLogin');
    Route::post('/login', 'AuthController@postLogin');
    Route::get('/logout', 'AuthController@getLogout');
});


// Register your Routes to be accessed only with authentication
Route::group(['middleware' => 'auth'], function () {
    Route::get('mysite', function () {
        return view('foo');
    });
});
```
You're set to go! Now create some Views and try it out. :)

# (3. Tweak Laravels Authentication)
As I don't like the default path for a login to be `/auth/login` feel free to change
this file at line 41 : `ROOT/app/Http/Middleware/Authenticate.php`

`return redirect()->guest('/login');`
