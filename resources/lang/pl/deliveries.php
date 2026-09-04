<?php

return [
    'plural_model_label' => 'Dostawy',
    'singular_model_label' => 'Dostawa',

    'number' => 'Numer dostawy',
    'contractor' => 'Kontrahent',
    'contractor_address' => 'Adres kontrahenta',
    'loading_address' => 'Adres załadunku',
    'freight_amount' => 'Kwota frachtu',
    'currency' => 'Waluta',
    'confirm_delete_delivery' => 'Czy na pewno chcesz usunąć dostawę?',
    'confirm_delete_document' => 'Czy na pewno chcesz usunąć ten dokument?',
    'create_title' => 'Utwórz dostawę',
    'basic_info' => 'Informacje podstawowe',
    'calendar_title' => 'Kalendarz dostaw',
    'edit_transport_set' => 'Edycja zestawu transportowego',
    'go_to_edit' => 'Przejdź do edycji',

    'tabs' => [
        'calendar' => 'Kalendarz',
        'planner' => 'Planer',
    ],

    'planner' => [
        'no_resources' => 'Brak aktywnych zasobów: :type',
        'all_drivers' => 'Wszyscy',
        'resource_types' => [
            'driver' => 'Kierowcy',
            'tractor' => 'Ciągniki',
            'trailer' => 'Naczepy',
        ],
    ],

    'status' => [
        'status' => 'Status',
        'planned' => 'Zaplanowana',
        'assigned' => 'Przypisana',
        'in_progress' => 'W trakcie realizacji',
        'completed' => 'Zrealizowana',
        'cancelled' => 'Anulowana',
    ],

    'goods' => [
        'goods' => 'Towary',
        'good' => 'Towar',
        'unit' => 'Jednostka',
        'quantity' => 'Ilość',
        'add' => 'Dodaj towar',
        'remove' => 'Usuń towar',
    ],

    'transport_set' => [
        'transport_sets' => 'Zestawy transportowe',
        'transport_set' => 'Zestaw transportowy',
        'driver' => 'Kierowca',
        'vehicle' => 'Pojazd',
        'trailer' => 'Naczepa',
        'loading_at' => 'Data i godzina załadunku',
        'unloading_at' => 'Data i godzina rozładunku',
        'add' => 'Dodaj zestaw transportowy',
        'remove' => 'Usuń zestaw transportowy',
        'driver_busy' => 'Wybrany kierowca jest już zajęty w tym terminie.',
        'vehicle_busy' => 'Wybrany ciągnik jest już zajęty w tym terminie.',
        'trailer_busy' => 'Wybrana naczepa jest już zajęta w tym terminie.',
        'not_found' => 'Jeden z zestawów transportowych nie istnieje już w tej dostawie. Odśwież stronę i spróbuj ponownie.',
    ],

    'transport_set_status' => [
        'status' => 'Status',
        'draft' => 'Szkic',
        'assigned' => 'Przypisany',
        'loading' => 'Załadunek',
        'unloading' => 'Rozładunek',
        'in_transit' => 'W transporcie',
        'completed' => 'Zrealizowany',
        'cancelled' => 'Anulowany',
    ],

    'documents' => 'Dokumenty',

    'cost_type' => [
        'fuel' => 'Paliwo',
        'driver' => 'Kierowca',
        'toll' => 'Opłaty drogowe',
        'ferry' => 'Prom',
        'parking' => 'Parking',
        'service' => 'Serwis',
        'other' => 'Inne',
    ],

    'cost' => [
        'cost' => 'Koszt',
        'costs' => 'Koszty',
        'type' => 'Kategoria',
        'amount' => 'Kwota',
        'description' => 'Opis',
        'add' => 'Dodaj koszt',
        'edit' => 'Edytuj koszt',
        'remove' => 'Usuń koszt',
        'confirm_delete' => 'Czy na pewno chcesz usunąć ten koszt?',
        'whole_delivery' => 'Cała dostawa',
        'remaining_costs' => 'Koszty pozostałe',
        'empty' => 'Brak kosztów.',
    ],

    'profitability' => [
        'tab' => 'Opłacalność',
        'summary' => 'Opłacalność dostawy',
        'revenue' => 'Przychód',
        'costs' => 'Koszty',
        'profit' => 'Zysk',
        'margin' => 'Marża',
        'total' => 'Razem',
        'total_costs' => 'Łączne koszty',
        'no_freight_amount' => 'Nie podano kwoty frachtu.',
    ],

    'status_history' => [
        'tab' => 'Historia statusów',
        'transport_set' => 'Zestaw transportowy',
        'status' => 'Status',
        'changed_by' => 'Zmienił',
        'changed_at' => 'Data zmiany',
        'system' => 'System',
        'empty' => 'Brak historii zmian statusów.',
    ],
];
