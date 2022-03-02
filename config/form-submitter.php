<?php

return [
    /*
     * --------------------------------------------------------------------------------------
     * Use Transactional Form Submitter
     * --------------------------------------------------------------------------------------
     *
     * Will the Form Submitter use transaction (true) or not (false) on the form submit?
     *
     * Default: false
     */

    'use_transactional' => false,

    /*
     * --------------------------------------------------------------------------------------
     * Form Submitters
     * --------------------------------------------------------------------------------------
     *
     * Specify at least one form submitter that the stepped form will use to submit entity on
     * FormFinished event.
     */

    'submitters' => [],
];
