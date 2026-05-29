<?php

return [
    'exports' => [
        'chunk_size'       => 1000,
        'pre_calculate_formulas' => false,
        'strict_null_comparison' => false,
        'csv' => [
            'delimiter'              => ',',
            'enclosure'              => '"',
            'line_ending'            => PHP_EOL,
            'use_bom'                => false,
            'include_separator_line' => false,
            'excel_compatibility'    => false,
            'output_encoding'        => '',
        ],
    ],
    'imports' => [
        'read_only'            => true,
        'ignore_empty'         => false,
        'heading_row'          => ['formatter' => 'slug'],
        'csv' => [
            'delimiter'        => ',',
            'enclosure'        => '"',
            'escape_character' => '\\',
            'contiguous'       => false,
            'input_encoding'   => 'UTF-8',
        ],
        'cells' => [
            'trim_whitespace' => false,
        ],
    ],
    'extension_detector' => [
        'xlsx'     => \Maatwebsite\Excel\Excel::XLSX,
        'xlsm'     => \Maatwebsite\Excel\Excel::XLSX,
        'xls'      => \Maatwebsite\Excel\Excel::XLS,
        'slk'      => \Maatwebsite\Excel\Excel::SLK,
        'xml'      => \Maatwebsite\Excel\Excel::XML,
        'gnumeric' => \Maatwebsite\Excel\Excel::GNUMERIC,
        'htm'      => \Maatwebsite\Excel\Excel::HTML,
        'html'     => \Maatwebsite\Excel\Excel::HTML,
        'csv'      => \Maatwebsite\Excel\Excel::CSV,
        'tsv'      => \Maatwebsite\Excel\Excel::TSV,
        'pdf'      => \Maatwebsite\Excel\Excel::MPDF,
        'ods'      => \Maatwebsite\Excel\Excel::ODS,
    ],
    'value_binder' => [
        'default' => \Maatwebsite\Excel\DefaultValueBinder::class,
    ],
    'cache' => [
        'enable'   => env('EXCEL_CACHE_ENABLE', false),
        'driver'   => env('EXCEL_CACHE_DRIVER', 'memory'),
    ],
    'transactions' => [
        'handler' => 'db',
    ],
    'temporary_files' => [
        'local_path'            => sys_get_temp_dir(),
        'remote_disk'           => null,
        'remote_prefix'         => null,
        'force_resync_remote'   => null,
    ],
];