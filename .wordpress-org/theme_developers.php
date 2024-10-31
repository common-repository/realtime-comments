<?php

/*
How to reverse page numbering in Theme (from left-to-right to right-to-left)?

Theme probably uses function paginate_links() or paginate_comments_links() function
for generating comments page number links.

https://codex.wordpress.org/Function_Reference/paginate_links
https://codex.wordpress.org/Function_Reference/paginate_comments_links

To support descending order, first it must be amended so that this function does not echo 
but returns "array" (and not "plain", which is default)

$args = array(
...
'type' => 'array',
'echo' => 'false',
...
);

Wordpress default (global) comments order value is get_option('comment_order'): {asc|desc}
For comments reversing in some particular page, Realtime Comments uses 'wp_list_comments_args' filter


FOLLOWING IS WRITTEN FROM SCRATCH AS ONE POSSIBLE SOLUTION, NOT TESTED
*/

// get links
$links_array = paginate_comments_links(
    array( 
        /* ... existing values ...,*/ 
        'echo' => 'false', // do not echo
        'type' => 'array', // instead, return array
        )
    );

// start links generation only if links_array exists
if (is_array($links_array) && count($links_array)>0) {

    // get default top level order
    // 'reverse_top_level' is true|false, but get_option(comment_order) returns asc|desc
    // asc = false, desc = true
    $r = array('reverse_top_level' => ( 'desc' == get_option('comment_order')));

    // if any plugin is changing 'reverse_top_level' args with filter (like Realtime Comments does), discover that
    $r = apply_filters('wp_list_comments_args', $r); // you may want to play with priority here
    
    // now, if top level is reversed order, reverse also links array
    if ($r['reverse_top_level']) {
        $links_array = array_reverse($links_array);
        // If link text is containing arrows/image etc "visualized" direction, must reverse those also, currently not done
        // Its better to add such visualization to first and last li node with CSS, examples: 
        // li.classdef:first-child::before {content: '« '}  
        // a.prev.page-numbers::before {content: '« '}
        // li.classdef:last-child::after {content: ' »'}
        // a.next.page-numbers::after {content: ' »'}
    }

    // echo links
    echo implode("\n", $links_array);
}

?> 