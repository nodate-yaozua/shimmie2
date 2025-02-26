<?php

declare(strict_types=1);

namespace Shimmie2;

use MicroHTML\HTMLElement;

use function MicroHTML\{INPUT, LABEL, SMALL, TEXTAREA, TR, TD, TABLE, TH, TBODY, THEAD, DIV, A, BR, emptyHTML, SUP, rawHTML};

class ForumTheme extends Themelet
{
    public function display_thread_list(Page $page, $threads, $showAdminOptions, $pageNumber, $totalPages)
    {
        if (count($threads) == 0) {
            $html = "There are no threads to show.";
        } else {
            $html = $this->make_thread_list($threads, $showAdminOptions);
        }

        $page->set_title(html_escape("Forum"));
        $page->set_heading(html_escape("Forum"));
        $page->add_block(new Block("Forum", $html, "main", 10));

        $this->display_paginator($page, "forum/index", null, $pageNumber, $totalPages);
    }



    public function display_new_thread_composer(Page $page, $threadText = null, $threadTitle = null)
    {
        global $config, $user;
        $max_characters = $config->get_int('forumMaxCharsPerPost');

        $html = SHM_SIMPLE_FORM(
            "forum/create",
            TABLE(
                ["style" => "width: 500px;"],
                TR(
                    TD("Title:"),
                    TD(INPUT(["type" => "text", "name" => "title", "value" => $threadTitle]))
                ),
                TR(
                    TD("Message:"),
                    TD(TEXTAREA(
                        ["id" => "message", "name" => "message"],
                        $threadText
                    ))
                ),
                TR(
                    TD(),
                    TD(SMALL("Max characters allowed: $max_characters."))
                ),
                $user->can(Permissions::FORUM_ADMIN) ? TR(
                    TD(),
                    TD(
                        LABEL(["for" => "sticky"], "Sticky:"),
                        INPUT(["name" => "sticky", "id" => "sticky", "type" => "checkbox", "value" => "Y"])
                    )
                ) : null,
                TR(
                    TD(
                        ["colspan" => 2],
                        INPUT(["type" => "submit", "value" => "Submit"])
                    )
                )
            )
        );

        $blockTitle = "Write a new thread";
        $page->set_title(html_escape($blockTitle));
        $page->set_heading(html_escape($blockTitle));
        $page->add_block(new Block($blockTitle, $html, "main", 120));
    }



    public function display_new_post_composer(Page $page, $threadID)
    {
        global $config;

        $max_characters = $config->get_int('forumMaxCharsPerPost');

        $html = SHM_SIMPLE_FORM(
            "forum/answer",
            INPUT(["type" => "hidden", "name" => "threadID", "value" => $threadID]),
            TABLE(
                ["style" => "width: 500px;"],
                TR(
                    TD("Message:"),
                    TD(TEXTAREA(["id" => "message", "name" => "message"]))
                ),
                TR(
                    TD(),
                    TD(SMALL("Max characters allowed: $max_characters."))
                ),
                TR(
                    TD(
                        ["colspan" => 2],
                        INPUT(["type" => "submit", "value" => "Submit"])
                    )
                )
            )
        );

        $blockTitle = "Answer to this thread";
        $page->add_block(new Block($blockTitle, $html, "main", 130));
    }



    public function display_thread($posts, $showAdminOptions, $threadTitle, $threadID, $pageNumber, $totalPages)
    {
        global $config, $page/*, $user*/;

        $posts_per_page = $config->get_int('forumPostsPerPage');

        $current_post = 0;

        $tbody = TBODY();
        foreach ($posts as $post) {
            $current_post++;

            $post_number = (($pageNumber - 1) * $posts_per_page) + $current_post;
            $tbody->appendChild(
                emptyHTML(
                    TR(
                        ["class" => "postHead"],
                        TD(["class" => "forumSupuser"]),
                        TD(
                            ["class" => "forumSupmessage"],
                            DIV(
                                ["class" => "deleteLink"],
                                $showAdminOptions ? A(["href" => make_link("forum/delete/".$threadID."/".$post['id'])], "Delete") : null
                            )
                        )
                    ),
                    TR(
                        ["class" => "posBody"],
                        TD(
                            ["class" => "forumUser"],
                            A(["href" => make_link("user/".$post["user_name"])], $post["user_name"]),
                            BR(),
                            SUP(["class" => "user_rank"], $post["user_class"]),
                            BR(),
                            rawHTML(User::by_name($post["user_name"])->get_avatar_html()),
                            BR()
                        ),
                        TD(
                            ["class" => "forumMessage"],
                            DIV(["class" => "postDate"], SMALL(rawHTML(autodate($post['date'])))),
                            DIV(["class" => "postNumber"], " #".$post_number),
                            BR(),
                            DIV(["class" => "postMessage"], rawHTML(send_event(new TextFormattingEvent($post["message"]))->formatted))
                        )
                    ),
                    TR(
                        ["class" => "postFoot"],
                        TD(["class" => "forumSubuser"]),
                        TD(["class" => "forumSubmessage"])
                    )
                )
            );
        }

        $html = emptyHTML(
            DIV(
                ["id" => "returnLink"],
                A(["href" => make_link("forum/index/")], "Return")
            ),
            BR(),
            BR(),
            TABLE(
                ["id" => "threadPosts", "class" => "zebra"],
                THEAD(
                    TR(
                        TH(["id" => "threadHeadUser"], "User"),
                        TH("Message")
                    )
                ),
                $tbody
            )
        );

        $this->display_paginator($page, "forum/view/".$threadID, null, $pageNumber, $totalPages);

        $page->set_title(html_escape($threadTitle));
        $page->set_heading(html_escape($threadTitle));
        $page->add_block(new Block($threadTitle, $html, "main", 20));
    }

    public function add_actions_block(Page $page, $threadID)
    {
        $html = A(["href" => make_link("forum/nuke/".$threadID)], "Delete this thread and its posts.");
        $page->add_block(new Block("Admin Actions", $html, "main", 140));
    }

    private function make_thread_list($threads, $showAdminOptions): HTMLElement
    {
        global $config;

        $tbody = TBODY();
        $html = TABLE(
            ["id" => "threadList", "class" => "zebra"],
            THEAD(
                TR(
                    TH("Title"),
                    TH("Author"),
                    TH("Updated"),
                    TH("Responses"),
                    $showAdminOptions ? TH("Actions") : null
                )
            ),
            $tbody
        );

        foreach ($threads as $thread) {
            $titleSubString = $config->get_int('forumTitleSubString');

            if ($titleSubString < strlen($thread["title"])) {
                $title = substr($thread["title"], 0, $titleSubString);
                $title = $title."...";
            } else {
                $title = $thread["title"];
            }

            $tbody->appendChild(
                TR(
                    TD(
                        ["class" => "left"],
                        bool_escape($thread["sticky"]) ? "Sticky: " : "",
                        A(["href" => make_link("forum/view/".$thread["id"])], $title)
                    ),
                    TD(
                        A(["href" => make_link("user/".$thread["user_name"])], $thread["user_name"])
                    ),
                    TD(rawHTML(autodate($thread["uptodate"]))),
                    TD($thread["response_count"]),
                    $showAdminOptions ? TD(A(["href" => make_link("forum/nuke/".$thread["id"])], "Delete")) : null
                )
            );
        }

        return $html;
    }
}
