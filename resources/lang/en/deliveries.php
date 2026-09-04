<?php

return [
    'plural_model_label' => 'Deliveries',
    'singular_model_label' => 'Delivery',

    'number' => 'Delivery number',
    'contractor' => 'Contractor',
    'contractor_address' => 'Contractor address',
    'loading_address' => 'Loading address',
    'freight_amount' => 'Freight amount',
    'currency' => 'Currency',
    'confirm_delete_delivery' => 'Are you sure you want to delete the delivery?',
    'confirm_delete_document' => 'Are you sure you want to delete this document?',
    'create_title' => 'Create delivery',
    'basic_info' => 'Basic information',
    'calendar_title' => 'Delivery calendar',
    'edit_transport_set' => 'Edit transport set',
    'go_to_edit' => 'Go to edit',

    'tabs' => [
        'calendar' => 'Calendar',
        'planner' => 'Planner',
    ],

    'planner' => [
        'no_resources' => 'No active resources: :type',
        'all_drivers' => 'All',
        'resource_types' => [
            'driver' => 'Drivers',
            'tractor' => 'Tractors',
            'trailer' => 'Trailers',
        ],
    ],

    'status' => [
        'status' => 'Status',
        'planned' => 'Planned',
        'assigned' => 'Assigned',
        'in_progress' => 'In progress',
        'completed' => 'Completed',
        'cancelled' => 'Cancelled',
    ],

    'goods' => [
        'goods' => 'Goods',
        'good' => 'Good',
        'unit' => 'Unit',
        'quantity' => 'Quantity',
        'add' => 'Add good',
        'remove' => 'Remove good',
    ],

    'transport_set' => [
        'transport_sets' => 'Transport sets',
        'transport_set' => 'Transport set',
        'driver' => 'Driver',
        'vehicle' => 'Vehicle',
        'trailer' => 'Trailer',
        'loading_at' => 'Loading date and time',
        'unloading_at' => 'Unloading date and time',
        'add' => 'Add transport set',
        'remove' => 'Remove transport set',
        'driver_busy' => 'The selected driver is already busy at this time.',
        'vehicle_busy' => 'The selected tractor is already busy at this time.',
        'trailer_busy' => 'The selected trailer is already busy at this time.',
        'not_found' => 'One of the transport sets no longer exists in this delivery. Refresh the page and try again.',
    ],

    'transport_set_status' => [
        'status' => 'Status',
        'draft' => 'Draft',
        'assigned' => 'Assigned',
        'loading' => 'Loading',
        'unloading' => 'Unloading',
        'in_transit' => 'In transit',
        'completed' => 'Completed',
        'cancelled' => 'Cancelled',
    ],

    'documents' => 'Documents',

    'cost_type' => [
        'fuel' => 'Fuel',
        'driver' => 'Driver',
        'toll' => 'Toll',
        'ferry' => 'Ferry',
        'parking' => 'Parking',
        'service' => 'Service',
        'other' => 'Other',
    ],

    'cost' => [
        'cost' => 'Cost',
        'costs' => 'Costs',
        'type' => 'Category',
        'amount' => 'Amount',
        'description' => 'Description',
        'add' => 'Add cost',
        'edit' => 'Edit cost',
        'remove' => 'Remove cost',
        'confirm_delete' => 'Are you sure you want to delete this cost?',
        'whole_delivery' => 'Whole delivery',
        'remaining_costs' => 'Remaining costs',
        'empty' => 'No costs.',
    ],

    'profitability' => [
        'tab' => 'Profitability',
        'summary' => 'Delivery profitability',
        'revenue' => 'Revenue',
        'costs' => 'Costs',
        'profit' => 'Profit',
        'margin' => 'Margin',
        'total' => 'Total',
        'total_costs' => 'Total costs',
        'no_freight_amount' => 'No freight amount set.',
    ],

    'status_history' => [
        'tab' => 'Status history',
        'transport_set' => 'Transport set',
        'status' => 'Status',
        'changed_by' => 'Changed by',
        'changed_at' => 'Changed at',
        'system' => 'System',
        'empty' => 'No status change history.',
    ],
];
