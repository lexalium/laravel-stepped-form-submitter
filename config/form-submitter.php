<?php

return [
    /*
     * --------------------------------------------------------------------------------------
     * Transaction Class
     * --------------------------------------------------------------------------------------
     *
     * Place a Transaction class or instance or service alias if the Form Submitter has to
     * use transaction on the form submit.
     * Place null or remove config to disable transactions.
     *
     * Default: null
     */

    'transaction_class' => null,

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
