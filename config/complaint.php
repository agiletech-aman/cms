<?php

return [
    // Comma separated list of emails that get a mail when a complaint is Resolved
    'resolved_mail_to' => array_filter(array_map('trim', explode(',', env('RESOLVED_COMPLAINT_MAIL_TO', '')))),

    // Resolved complaints are auto closed after these many hours
    'auto_close_hours' => (int) env('COMPLAINT_AUTO_CLOSE_HOURS', 72),
];
