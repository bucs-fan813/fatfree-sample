<?php

class User extends \DB\SQL\Mapper
{

    private $uid;

    private $name;

    private $mail;

    private $pass;

    private $f3;
    
    private $args;

    public function __construct()
    {
        //$f3 = \Base::instance();
        $this->f3 = \Base::instance();
        //$f3 = $this->f3;
        $prefix = $this->f3->get('schema.prefix');
        //k($this);
        parent::__construct(\Base::instance()->get('DB'), $prefix . 'user');
        
    }

    static function create_password($password)
    {
        // Prevent DoS attacks by refusing to hash large passwords.
        if (strlen($password) > 512) {
            return FALSE;
        }
        
        // We rely on the password_hash() function being available in PHP 5.5+.
        return password_hash($password, PASSWORD_DEFAULT);
    }

    function login($username, $password = null)
    {
        // F3 sync the 'POST' hive array variable with the $_POST array
        $f3->get('user')->copyfrom('POST');
        k($f3);
        $user = self::load("name = $username");
        k($user);
    }

    function authorize($args = array())
    {
        // $args['auth_domain'] = isset($args['auth_domain']) ? $args['auth_domain'] : ''
        // Create a new (and much lighter) OAuth2 client with no external dependencies!!
        $oauth2 = new \Web\OAuth2();
        
        // Get the Geoaxis/OAuth2 creds from the SSO tile and set the oauth2 client defaults
        // foreach ($p_identity as $key => $value)
        // $oauth2->set($key,$value);
        
        // $auth_domain = $oauth2->get('auth_domain');
        $auth_domain = 'https://stackoverflow.com';
        $redirect_uri = "{$this->f3->get('SCHEME')}://{$this->f3->get('HOST')}{$this->f3->get('BASE')}";
        //$redirect_uri = $this->f3->get('REALM');
        
        // TODO: Revise authorization logic.
        // 1) Check for valid token (base case)
        // Else request/refresh token
        // 2) Check for valid code and state in URL
        // Request token
        // 3) Catch all
        // Something must be wrong with geoaxis... but can we gurantee that we can properly route them? May need "health monitor(s)" for geoaxis access points
        //k($_GET);
        // Check to make sure there is not an existing code argument
        if ($this->f3->get('token')) {
            // TODO: Handle existing valid tokens
            k($this);
        }
        elseif ($this->f3->get('VERB') == 'GET' && !isset($_GET['code'])) {
            k($this);
            // Set the endpoint's required params
            $oauth2->set('redirect_uri', $redirect_uri);
            $oauth2->set('state', $this->getRandomState());
            $oauth2->set('response_type', 'code');
            //$oauth2->set('scope', 'openid');
            $oauth2->set('client_id', '12256');
            
            // Generate GeoAxis Authorize URI
            // TODO: Is there a PCF api call to get the endpoints so we dont have to hard code them? The endpoints are not in VCAP_SERVICES
            $uri = $oauth2->uri($auth_domain . '/oauth');
            //k($uri);
            // Redirect to SSO Tile/GeoAxis Authorize Endpoint with required oauth parameters
            header('Location: ' . $uri);
        } // Get a new GeoAxis/OAuth token
        elseif (isset($_GET['code'])) {
            // Set the endpoint's required params
            //k('test');
            $oauth2->set('redirect_uri', $redirect_uri);
            $oauth2->set('client_id', '12256');
            
            $oauth2->set('state', $_GET['state']);
            $oauth2->set('client_secret', '6J4fm2Ok73o20Q5dXO8ZhA((');
            $oauth2->set('code', $_GET['code']);
            
            // Generate GeoAxis token URI
            $uri = $oauth2->uri($auth_domain . '/oauth/access_token');
            // TODO: Need error handling
            $request = $oauth2->request($uri, 'POST');
            k($request);
            //if ($request = $oauth2->request($uri, 'POST'))
            {
                //$this->f3->set('token', $oauth2->jwt($request['access_token']));
                //F3::set('COOKIE.f3token', $request['access_token']);
                
            }
            //k($request);
            
                k($this->f3);
                
        } // Use an existing GeoAxis/OAuth token

    }

    /**
     * Returns a new random string to use as the state parameter in an
     * authorization flow.
     *
     * @param int $length
     *            Length of the random string to be generated.
     * @return string https://github.com/thephpleague/oauth2-client/blob/64c6acd2730a4d698b9dae6de518196b0774719a/src/Provider/AbstractProvider.php#L265
     *         http://docs.cloudfoundry.org/api/uaa/version/4.11.0/#api-flow
     */
    protected function getRandomState($length = 32)
    {
        // Converting bytes to hex will always double length. Hence, we can reduce
        // the amount of bytes by half to produce the correct length.
        // BUG: random_bytes() requires PHP7. For now use openssl_random_pseudo_bytes which is included in openssl PHP extentions for >= PHP5.3
        // return bin2hex(random_bytes($length / 2));
        return bin2hex(openssl_random_pseudo_bytes($length / 2));
    }
}

// $user = new User();
// $user->load("name = $username");
// k($user);
// etc.