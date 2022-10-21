How to add custom POST and file filtering parameters to wp native media uploader.
Use case: You need several media upload modals on the front-end, each with potentially different files mime types and/or sizes allowance.
Adding a custom POST parameter allows to then apply different file restrictions in the wp_handle_upload_prefilter hook for each page/case.