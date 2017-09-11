<?php
namespace Mirk\PassportFacebookLogin;

use Facebook\Facebook;
use Illuminate\Http\Request;
use League\OAuth2\Server\Exception\OAuthServerException;

trait FacebookLoginTrait {
    /**
     * Logs a App\User in using a Facebook token via Passport
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     * @throws \League\OAuth2\Server\Exception\OAuthServerException
     */
    public function loginFacebook(Request $request)
    {
        try {
            if ($request->get('fb_token')) { // Check that a token was passed

                // Init facebook SDK
                $fb = new Facebook([
                    'app_id' => env('FACEBOOK_APP_ID'),
                    'app_secret' => env('FACEBOOK_APP_SECRET'),
                    'default_graph_version' => 'v2.5',
                ]);
                $fb->setDefaultAccessToken($request->get('fb_token'));

                // Attempt to get the user object from Facebook
                $response = $fb->get('/me?locale=en_AU&fields=first_name,last_name,email');
                $fbUser = $response->getDecodedBody();

                // Check that we have an existing user matching the email address
                $userModel = config('auth.providers.users.model');

                $user = $userModel::where('email', $fbUser['email'])->first();
                if (!$user) {
                    // User does not exist, create user automatically
                    $user = new $userModel();
                    $user->name = $fbUser['first_name'].' '.$fbUser['last_name'];
                    $user->email = $fbUser['email'];
                    $user->password = uniqid('fb_', true); // We need to give them a password, generate a random one
                    $user->save();
                }
                return $user;
            }
        } catch (\Exception $e) {
            throw OAuthServerException::accessDenied($e->getMessage());
        }
        return null;
    }
}
