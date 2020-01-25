export const types = [
    
        {
            source: 'list',
            display: 'Liste',
            table: 'column_arrays',
            in_form_column: 'column_array_ids',
            description: 'Kullanıcıların kayıtları listelerken hengi kolonları göreceğini seçiniz. Bu yetkiyi boş bırakırsanız kullanıclar kayıtları <span class="badge badge-warning">listeleyemezler</span>',
            search: 'tableName:list'
        },
        {
            source: 'create',
            display: 'Ekleme',
            table: 'column_sets',
            in_form_column: 'column_set_ids',
            description: 'Kayıt ekleme formunu ve görünecek kolonları burada belirleyebilirsiniz. Ekleme yetkisi <span class="badge badge-warning">vermemek</span> için boş geçiniz',
            search: 'tableName:create'
        },
        {
            source: 'edit',
            display: 'Güncelleme',
            table: 'column_sets',
            in_form_column: 'column_set_ids',
            description: 'Kullanıcı kaydı düzenlerken göreceği formu ve kolonları burada belirleyebilirsiniz. Düzenleme yetkisi <span class="badge badge-warning">vermemek</span> için boş geçiniz.',
            search: 'tableName:edit'
        },
        {
            source: 'delete',
            display: 'Silme',
            table: 'data_filters',
            in_form_column: 'data_filter_ids',
            description: 'Kullanıcıların varsayılan olarak kayıt <span class="badge badge-danger">silme yetkisi vardır</span>. Burada, bu yetkiyi kaldırabilir yada kısıtlayabilirsiniz.',
            search: 'filters:tableName:delete:'
        },        
        {
            source: 'show',
            display: 'Bilgi Kartı',
            table: 'column_sets',
            in_form_column: 'column_set_ids',
            description: 'Burada kullanıcılar için, zengin bilgi kartları tasarlayabilirsniz. Bilgi kartında farklı tablolardan veriler yada tümüyle tablo olabilir.',
            search: 'tableName:show'
        },
        {
            source: 'querie',
            display: 'Sorgu',
            table: 'column_arrays',
            in_form_column: 'column_array_ids',
            description: 'Kullanıcıların listeleme esnasında görüdğü kolonlardan sorgu yapma yetkisi zaten vardır. Özel olarak sorgu yapabilmesini istediğiniz kolonlar varsa buradan ekleyebilirsiniz.',
            search: 'tableName:querie'
        },             
        {
            source: 'restore',
            display: 'Geri Yükleme',
            table: 'data_filters',
            in_form_column: 'data_filter_ids',
            description: 'Kullanıcıların varsayılan olarak kayıt <span class="badge badge-danger">geri yükleme yetkisi vardır</span>. Burada, bu yetkiyi kaldırabilir yada kısıtlayabilirsiniz.',
            search: 'filters:tableName:restore:'
        },
        {
            source: 'deleted',
            display: 'Silinmiş Kayıtlar',
            table: 'column_arrays',
            in_form_column: 'column_array_ids',
            description: 'Kullanıcıların silinmiş kayıtları geri getirebilmesi için bu ekrandan yetki verebilirsiniz.',
            search: 'tableName:deleted'
        },
        {
            source: 'export',
            display: 'Dışa aktarma',
            table: 'data_filters',
            in_form_column: 'data_filter_ids',
            description: 'Kaydı dışa aktarma varsayılan olarak izinlidir. İsterseniz kaldırılabilir.',
            search: 'filters:tableName:export:'
        },
        {
            source: 'filters',
            display: 'Diğer Filtreler',
            table: 'data_filters',
            in_form_column: 'data_filter_ids',
            description: 'İsterseniz diğer işlemler için filtreler oluşturabilirsiniz',
            search: 'filters:tableName:'
        },
        {
            source: 'other',
            display: 'Diğer Yetkiler',
            table: '',
            in_form_column: '',
            description: 'İsterseniz diğer yetkileri de ekleyebilirsiniz',
            search: 'tableName:'
        }
];