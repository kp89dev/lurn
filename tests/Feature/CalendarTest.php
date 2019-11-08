<?php
namespace Tests\Feature;

class CalendarTest extends \LoggedInTestCase
{
    /**
     * @test
     */
    public function page_is_available()
    {
        $response = $this->get(route('calendar'));
            
        $response->assertStatus(200)
            ->assertSee('Calendar Events');
    }
    
    /**
     * @test
     */
    public function calendar_js_loaded()
    {
        $response = $this->get(route('calendar'));
        
        $this->assertContains('id="event-details"', $response->getContent());
    }
    
    /**
     * @test
     */
    public function calendar_template_present()
    {
        $response = $this->get(route('calendar'));
        
        $this->assertContains(
            "<strong v-if=\"events.length\" v-cloak>{{ events[0].start.format('MMMM YYYY') }}</strong>",
            $response->getContent()
        );
    }
}
