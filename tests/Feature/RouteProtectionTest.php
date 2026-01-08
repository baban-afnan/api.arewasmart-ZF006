<?php

namespace Tests\Feature;

use Tests\TestCase;

class RouteProtectionTest extends TestCase
{
    /** @test */
    public function landing_page_is_accessible_by_guests()
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }

    /** @test */
    public function dashboard_requires_authentication()
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');
    }

    /** @test */
    public function wallet_requires_authentication()
    {
        $response = $this->get('/wallet');
        $response->assertRedirect('/login');
    }

    /** @test */
    public function bvn_docs_require_authentication()
    {
        $response = $this->get('/developer/bvn');
        $response->assertRedirect('/login');
    }

    /** @test */
    public function nin_docs_require_authentication()
    {
        $response = $this->get('/developer/nin');
        $response->assertRedirect('/login');
    }

    /** @test */
    public function tin_docs_require_authentication()
    {
        $response = $this->get('/developer/tin');
        $response->assertRedirect('/login');
    }
}
