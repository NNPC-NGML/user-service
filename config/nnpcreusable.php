<?php

return [

    'AUTOMATOR_TASK_CREATED' => explode(",", env('AUTOMATOR_TASK_CREATED', '')),
    'AUTOMATOR_TASK_UPDATED' => explode(",", env('AUTOMATOR_TASK_UPDATED', '')),
    'AUTOMATOR_TASK_DELETED' => explode(",", env('AUTOMATOR_TASK_DELETED', '')),

    'CUSTOMER_CREATED' => explode(",", env('CUSTOMER_CREATED', '')),
    'CUSTOMER_UPDATED' => explode(",", env('CUSTOMER_UPDATED', '')),
    'CUSTOMER_DELETED' => explode(",", env('CUSTOMER_DELETED', '')),

    'CUSTOMER_SITE_CREATED' => explode(",", env('CUSTOMER_SITE_CREATED', '')),
    'CUSTOMER_SITE_UPDATED' => explode(",", env('CUSTOMER_SITE_UPDATED', '')),
    'CUSTOMER_SITE_DELETED' => explode(",", env('CUSTOMER_SITE_DELETED', '')),

    'DEPARTMENT_CREATED' => explode(",", env('DEPARTMENT_CREATED', '')),
    'DEPARTMENT_UPDATED' => explode(",", env('DEPARTMENT_UPDATED', '')),
    'DEPARTMENT_DELETED' => explode(",", env('DEPARTMENT_DELETED', '')),

    'DESIGNATION_CREATED' => explode(",", env('DESIGNATION_CREATED', '')),
    'DESIGNATION_UPDATED' => explode(",", env('DESIGNATION_UPDATED', '')),
    'DESIGNATION_DELETED' => explode(",", env('DESIGNATION_DELETED', '')),

    'FORM_BUILDER_CREATED' => explode(",", env('FORM_BUILDER_CREATED', '')),
    'FORM_BUILDER_UPDATED' => explode(",", env('FORM_BUILDER_UPDATED', '')),
    'FORM_BUILDER_DELETED' => explode(",", env('FORM_BUILDER_DELETED', '')),

    'FORM_DATA_CREATED' => explode(",", env('FORM_DATA_CREATED', '')),
    'FORM_DATA_UPDATED' => explode(",", env('FORM_DATA_UPDATED', '')),
    'FORM_DATA_DELETED' => explode(",", env('FORM_DATA_DELETED', '')),

    'NOTIFICATION_TASK_CREATED' => explode(",", env('NOTIFICATION_TASK_CREATED', '')),
    'NOTIFICATION_TASK_UPDATED' => explode(",", env('NOTIFICATION_TASK_UPDATED', '')),
    'NOTIFICATION_TASK_DELETED' => explode(",", env('NOTIFICATION_TASK_DELETED', '')),

    'PROCESSFLOW_CREATED' => explode(",", env('PROCESSFLOW_CREATED', '')),
    'PROCESSFLOW_UPDATED' => explode(",", env('PROCESSFLOW_UPDATED', '')),
    'PROCESSFLOW_DELETED' => explode(",", env('PROCESSFLOW_DELETED', '')),

    'PROCESSFLOW_STEP_CREATED' => explode(",", env('PROCESSFLOW_STEP_CREATED', '')),
    'PROCESSFLOW_STEP_UPDATED' => explode(",", env('PROCESSFLOW_STEP_UPDATED', '')),
    'PROCESSFLOW_STEP_DELETED' => explode(",", env('PROCESSFLOW_STEP_DELETED', '')),


    'PROCESSFLOW_HISTORY_CREATED' => explode(",", env('PROCESSFLOW_HISTORY_CREATED', '')),
    'PROCESSFLOW_HISTORY_UPDATED' => explode(",", env('PROCESSFLOW_HISTORY_UPDATED', '')),
    'PROCESSFLOW_HISTORY_DELETED' => explode(",", env('PROCESSFLOW_HISTORY_DELETED', '')),

    'ROUTE_CREATED' => explode(",", env('ROUTE_CREATED', '')),
    'ROUTE_UPDATED' => explode(",", env('ROUTE_UPDATED', '')),
    'ROUTE_DELETED' => explode(",", env('ROUTE_DELETED', '')),

    'UNIT_CREATED' => explode(",", env('UNIT_CREATED', '')),
    'UNIT_UPDATED' => explode(",", env('UNIT_UPDATED', '')),
    'UNIT_DELETED' => explode(",", env('UNIT_DELETED', '')),

    'USER_CREATED' => explode(",", env('USER_CREATED', '')),
    'USER_UPDATED' => explode(",", env('USER_UPDATED', '')),
    'USER_DELETED' => explode(",", env('USER_DELETED', '')),

    'TAG_CREATED' => explode(",", env('TAG_CREATED', '')),

];
