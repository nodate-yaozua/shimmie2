<?php

declare(strict_types=1);

namespace Shimmie2;

class AuthTheme extends Themelet
{
    public function display_page(Page $page): void
    {
        $page->set_title("Auth success");
        $page->set_heading("Auth success");
        $page->add_block(new Block("Auth success", "", "main", 20));
    }
    public function display_authorization_required(): void
    {
        $this->display_error(401, "Authorization Required", "Login is required");
    }
}
