<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class BasicTest extends TestCase
{
    use TestTrait;
    
    public function testBaseService()
    {       
        $response = $this->get('/');
        $this->controlResponseIsServiceOk($response);
    }
    
    public function testVersion()
    {
        $url = $this->getBaseUrl();        
        $response = $this->get($url);
        $this->controlResponseIsServiceOk($response);
    }
}
