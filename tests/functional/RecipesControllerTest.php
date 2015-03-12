<?php

use Mockery as m;

class RecipesControllerTest extends TestCase {

    public function setUp() {
        parent::setUp();

        // Mock the Appointment model
        $this->mock = m::mock(App::make('App\Models\Recipe'));
        // Bind the mock to our application,
        // so it gets injected into the controller
        $this->app->instance('App\Models\Recipe', $this->mock);
    }

    public function testIndexWithoutFilters() {
        $this->mock->shouldReceive('paginate')
            ->once()->andReturn([]); // An empty array is sufficient, since we don't want to hit the db

        $response = $this->action('GET', 'RecipesController@index');

		// $this->assertEquals(200, $response->getStatusCode());
        $this->assertViewHas('recipes');
    }

}
