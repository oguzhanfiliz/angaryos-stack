<?php

$columns_validations =
[
    //'id' => 'ID',
    'name_basic' => [$column_validations['required']->id ],
    'name' => [$column_validations['required']->id, $column_validations['unique']->id, $column_validations['name_not_start_deleted']->id ],
    'surname' =>  [ $column_validations['required']->id ],
    'email' => [ $column_validations['required']->id, $column_validations['email']->id, $column_validations['unique']->id  ],
    'tc' => [ $column_validations['required']->id , $column_validations['unique']->id ],
    //'email_verified_at' => 'Mail doğrulanma zamanı',
    'password' =>  [$column_validations['nullable']->id, $column_validations['min:5']->id ],
    //'tokens' => 'Jetonlar',
    'auths' => [$column_validations['nullable']->id, $column_validations['no_self']->id ],
    'srid' => [ $column_validations['nullable']->id, $column_validations['numeric']->id ],
    //'remember_token' => 'Şifre Hatırlatma Jetonu',
    //'state' => 'Durum',
    //'user_id' => 'Kaydı Güncelleyen',
    //'own_id' => 'Kaydın Sahibi',
    //'created_at' => 'Eklenme Zamanı',
    //'updated_at' => 'Güncellenme Zamanı',
    //'display_name' => 'Görüntüleme Adı',
    'php_code' => [$column_validations['nullable']->id ],
    //'description' => 'Açıklama',
    //'subscriber_type_id' => 'Takipçi Tipi',
    'data_filter_type_id' => [ $column_validations['required']->id, $column_validations['numeric']->id, $column_validations['no_change']->id ],
    'sql_code' => [$column_validations['required']->id ],
    'value' => [$column_validations['required']->id ],
    'schema_code' => [$column_validations['required']->id ],
    //'relation_table_id' => 'İlişkili Tablo',
    //'relation_source_column_id' => 'İlişkili Kaynak Kolon',
    //'relation_display_column_id' => 'İlişkili Görüntüleme Kolonu',
    //'relation_sql' => 'İlişki Sql \'i',
    //'relation_source_column' => 'İlişkili Kaynak Kolon',
    //'relation_display_column' => 'İlişkili Görüntüleme Kolonu',
    //'up_column_id' => 'Üst Kolon',
    'validation_with_params' => [$column_validations['required']->id, $column_validations['valid_validations']->id ],
    'column_db_type_id' => [ $column_validations['required']->id, $column_validations['numeric']->id ],
    'column_gui_type_id' => [ $column_validations['required']->id, $column_validations['numeric']->id ],
    'column_table_relation_id' => [ $column_validations['column_table_relation_control']->id ],
    //'subscriber_ids' => 'Takipçi(ler)',
    //'column_validation_ids' => 'column_validation_ids',
    //'default' => 'Varsayılan',
    //'column_ids' => 'Kolon(lar)',
    //'column_id' => 'Kolon',
    'control_column_ids' => [$column_validations['required']->id ],
    //'table_id' => 'Tablo',
    //'join_table' => 'Bağlı Tablo',
    //'join_columns' => 'Bağlı Kolon',
    'table_id' => [ $column_validations['required']->id, $column_validations['numeric']->id ],
    'column_array_ids' => [ $column_validations['required']->id ],
    'column_set_type_id' => [ $column_validations['required']->id, $column_validations['numeric']->id ],
    'column_array_type_id' => [ $column_validations['required']->id, $column_validations['numeric']->id ],
    'color_id' => [ $column_validations['required']->id, $column_validations['numeric']->id ],
    //'column_group_ids' => [$column_validations['required']->id ],
    'join_table_alias' => [$column_validations['required']->id, $column_validations['unique']->id ],
    'profile_picture' => [ $column_validations['files_type:image']->id, $column_validations['files_count:1']->id ],
    'image' => [ $column_validations['files_type:image']->id, $column_validations['files_count:1']->id ],
    'layer_base_url ' => [$column_validations['required']->id, $column_validations['url']->id ]
];
