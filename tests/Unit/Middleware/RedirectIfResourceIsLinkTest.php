<?php

namespace Tests\Unit\Middleware;

use App\Http\Middleware\RedirectIfResourceIsLink;
use App\Models\Lesson;
use App\Models\Module;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class RedirectIfResourceIsLinkTest extends \TestCase
{
    /**
     * @test
     */
    public function module_redirects()
    {
        $request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();

        $module = new Module;
        $module->type = 'Link';
        $module->link = 'http://google.com';

        $request->expects($this->once())->method('__get')->with('module')->willReturn($module);
        $middleware = new RedirectIfResourceIsLink;
        $result = $middleware->handle($request, function () {});
        $this->assertInstanceOf(RedirectResponse::class, $result);
        $this->assertEquals($result->getTargetUrl(), $module->link);
    }

    /**
     * @test
     */
    public function lesson_redirects()
    {
        $request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();

        $module = new Lesson;
        $module->type = 'Link';
        $module->link = 'http://google.com';

        $request->expects($this->once())->method('__get')->with('module')->willReturn($module);
        $middleware = new RedirectIfResourceIsLink;
        $result = $middleware->handle($request, function () {});
        $this->assertInstanceOf(RedirectResponse::class, $result);
        $this->assertEquals($result->getTargetUrl(), $module->link);
    }
}