<?php

declare(strict_types=1);

namespace Shimmie2;

class Auth extends Extension
{
    /** @var AuthTheme */
    protected Themelet $theme;

    public function onPageRequest(PageRequestEvent $event)
    {
        global $user, $page;

        if ($event->page_matches("auth")) {
            if ($user->is_anonymous()) {
                $this->theme->display_authorization_required();
            } else {
                $this->theme->display_page($page);
            }
        } else {
        }
    }
}
