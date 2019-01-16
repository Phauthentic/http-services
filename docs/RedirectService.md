# Redirect Service

## Creating new redirect responses

Redirect with new response instances, basic example:

```php
$redirect = new Redirect(new ResponseFactory());
$response = $redirect->create('/home');
```

The example above is not showing much context, usually you want to pass the redirect service to you controller or command handler via dependency injection.

To give you a more real world example take a look at this:

```php
namespace App\Http\Controller;

use Phauthentic\Http\Services\RedirectInterface;
use Psr\Http\Message\ResponseInterface;

class UsersController extends SomeBaseController
{
    protected $redirect;

    public function __construct(
        RedirectInterface $redirect
    ) {
        $this->redirect = $redirect;
    }

    public function completeRegistration(): ResponseInterface
    {
        // Some other code before

        return $this->redirect->create('/login');
    }
}
```

The `create()` method takes the status as thrid argument in the case you need to change it to something else like `permanently moved`. 

## Applying redirects to existing response objects

Static call, applying the redirect to an existing response object:

```php
$response = Redirect::to($response, '/home');
```

The status is by default 302, you can pass it as third argument:

```php
$response = Redirect::to($response, '/home', Redirect::STATUS_MOVED_PERMANENTLY);
```
