<?php
namespace Mirk\PassportFacebookLogin;

use Illuminate\Http\Request;
use Facebook\Facebook;

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

                $user = User::where('email', $fbUser['email'])->first();
                if (!$user) {
                    // User does not exist, create user automatically
                    $user = new User();
                    $user->first_name = $fbUser['first_name'];
                    $user->last_name = $fbUser['last_name'];
                    $user->email = $fbUser['email'];
                    $user->password = $request->get('fb_token'); // We need to give them a password, use the token
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