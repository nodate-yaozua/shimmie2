<?php

declare(strict_types=1);

namespace Shimmie2;

class AutoCompleteTest extends ShimmiePHPUnitTestCase
{
    public function testAuth()
    {
        send_event(new UserLoginEvent(User::by_name(self::$anon_name)));
        $page = $this->get_page('api/internal/autocomplete', ["s" => "not-a-tag"]);
        $this->assertEquals(200, $page->code);
        $this->assertEquals(PageMode::DATA, $page->mode);
        $this->assertEquals("[]", $page->data);
    }
}
