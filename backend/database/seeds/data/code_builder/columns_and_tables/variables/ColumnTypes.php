<?php

$colmn_db_type_map = 
[
    'bigint' => 'integer',
    'character varying' => 'string',
    'integer' => 'integer',
    'text' => 'text',
    'jsonb' => 'jsonb',
    'json' => 'json',
    'boolean' => 'boolean',
    'point' => 'point',
    'multipoint' => 'multipoint',
    'linestring' => 'linestring',
    'multilinestring' => 'multilinestring',
    'polygon' => 'polygon',
    'multipolygon' => 'multipolygon',
    'timestamp without time zone' => 'datetime'
];

$colmn_gui_type_map = 
[
    'bigint' => 'numeric',
    'character varying' => 'string',
    'integer' => 'numeric',
    'text' => 'text',
    'jsonb' => 'jsonb',
    'json' => 'json',
    'boolean' => 'boolean',
    'point' => 'point',
    'multipoint' => 'multipoint',
    'linestring' => 'linestring',
    'multilinestring' => 'multilinestring',
    'polygon' => 'polygon',
    'timestamp without time zone' => 'datetime'
];

$column_type_validation_map =
[
    'bigint' => [$column_validations['nullable']->id, $column_validations['numeric']->id],
    'character varying' => NULL,
    'integer' => [$column_validations['nullable']->id, $column_validations['numeric']->id],
    'text' => NULL,
    'jsonb' => [$column_validations['nullable']->id, $column_validations['json']->id],
    'boolean' => [$column_validations['nullable']->id, $column_validations['boolean']->id],
    'timestamp without time zone' => [$column_validations['nullable']->id, $column_validations['date']->id],
    'point' => NULL,
    'multipoint' => NULL,
    'linestring' => NULL,
    'multilinestring' => NULL,
    'polygon' => NULL
];