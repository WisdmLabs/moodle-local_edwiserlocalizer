<?php
// This file is part of Moodle - http://moodle.org/
// ... (Add your standard GPL boilerplate here) ...

defined('MOODLE_INTERNAL') || die();

$callbacks = [
    [
        'hook' => \core\hook\output\before_footer_html_generation::class,
        'callback' => \local_edwisertranslate\hook_listener::class . '::before_footer_html_generation',
    ],
];
