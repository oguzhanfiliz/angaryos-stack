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
    'timestamp' => 'datetime',
    'timestamp without time zone' => 'datetime',
    'date' => 'date',
    'time' => 'time',
    'time without time zone' => 'time',
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
    'multipolygon' => 'multipolygon',
    'timestamp' => 'datetime',
    'timestamp without time zone' => 'datetime',
    'date' => 'date',
    'time' => 'time',
    'time without time zone' => 'time',
];

$column_type_validation_map =
[
    'bigint' => [$column_validations['nullable']->id, $column_validations['numeric']->id],
    'character varying' => NULL,
    'integer' => [$column_validations['nullable']->id, $column_validations['numeric']->id],
    'text' => NULL,
    'jsonb' => [$column_validations['nullable']->id, $column_validations['json']->id],
    'boolean' => [$column_validations['nullable']->id, $column_validations['boolean']->id],
    'timestamp' => [$column_validations['nullable']->id, $column_validations['date_format:"Y-m-d H:i:s"']->id],
    'timestamp without time zone' => [$column_validations['nullable']->id, $column_validations['date_format:"Y-m-d H:i:s"']->id],
    'date' => [$column_validations['nullable']->id, $column_validations['date_format:"Y-m-d"']->id],
    'time' => [$column_validations['nullable']->id, $column_validations['date_format:"H:i:s"']->id],
    'time without time zone' => [$column_validations['nullable']->id, $column_validations['date_format:"H:i:s"']->id],
    'point' => NULL,
    'multipoint' => NULL,
    'linestring' => NULL,
    'multilinestring' => NULL,
    'polygon' => NULL,
    'multipolygon' => NULL
];