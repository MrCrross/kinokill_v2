<?php

return [

    /*
     * Role assigned to a self-registered account (see App\Services\Auth\RegistrationService).
     * Kept here, out of business logic, so no service has to hardcode a role name.
     */
    'default_customer_role' => 'customer',

];
