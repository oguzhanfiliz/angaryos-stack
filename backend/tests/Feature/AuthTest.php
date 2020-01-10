<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use TestTrait;
    
    public function testLogin()
    {
        $url = '/api/v1/login';
        $params = 
        [
            'email' => 'iletisim@omersavas.com', 
            'password' => '1234Aa.'
        ];
        $response = $this->json('POST', $url, $params);
        
        $this->controlResponseIsSuccess($response);
        $this->controlResponseOrDataHasAttributes($response, 'token');
        
        $this->assertEquals(strlen($response->getData()->data->token), 16+1+1);//random(16)+'d'+$user->id
    }
    
    public function testTokenValidation()
    {
        $url = $this->getBaseUrlWithToken();
        $response = $this->get($url);
        
        $this->controlResponseIsServiceOk($response);
    }
    
    public function testFailToken()
    {
        $url = $this->getBaseUrl();
        $response = $this->get($url . '2222222222222222d1');
        
        $this->controlResponseIsFailToken($response);
    }
    
    public function testGetLoggedInUserInfo()
    {
        $url = $this->getBaseUrlWithToken().'getLoggedInUserInfo';
        
        $response = $this->get($url);
        
        $this->controlResponseIsSuccess($response);
        $this->controlResponseOrDataHasAttributes($response, ['user', 'menu', 'auths']);
    }
}
