vcl 4.0;

import directors;
import std;





/*
** Standart Fonksiyonlar
*/



/* Sunucu Öncesi Grubu */

#İlk olarak bu fonksiyon çağırılır
sub vcl_init
{
    call sunucu_nesnelerini_olustur;
    return (ok);
}

#İkinci olarak bu blok çalıştırılır
sub vcl_recv
{
    #std.log("Bilgi: vcl_recv cagirildi");

    set req.http.X-Full-Uri = req.http.host + req.url;

    call sunucu_belirle;
    call onbelleklenebilirlik_kontrolu;
}



/* Sunucu Grubu */

#Cache yok ise sunucuya gitmeden önce bu blok çalıştırılır
sub vcl_backend_fetch
{
    #std.log("Bilgi: vcl_backend_fetch cagirildi");

	return (fetch);
}

#Sunucudan cevap gelince bu blok çalıştırılır. Cevap bu fonksiyonla alınır
sub vcl_backend_response
{
    #std.log("Bilgi: vcl_backend_response cagirildi");

    if (beresp.status == 200)
    {
        set beresp.ttl = 7d;
        set beresp.http.cache-control = "public, max-age=604800";
        return (deliver); 
    }
}

#Sunucuya erişimde yada istek sayısında hata oluşursa bu blok çalıştırılır
sub vcl_backend_error
{
    std.log("Bilgi: be err ile yeni yuklendi");

    set beresp.http.Content-Type = "text/html; charset=utf-8";
    set beresp.http.Retry-After = "5";

    synthetic( {"hata frontendVarnish.kamu.kutahya.gov.tr"} );

    return (deliver);
}


/* Sunucu Sonrası Grubu*/

#Pipe istenirse hiçbir ek fonksiyona dallanmadan direk bu blok çalıştırılır, buradan sonra hash bloğu çalıştırılır
sub vcl_pipe
{
    #std.log("Bilgi: vcl_pipe cagirildi");

    return (pipe);
}

#Sunucu cevabı alındıktan sonra yada ön bellekteki veri alınmadan önce ilk bu blok çalıştırılır
sub vcl_hash
{
    #std.log("Bilgi: vcl_hash cagirildi");

    hash_data(req.url);
	return (lookup);
}

#İstek pass ile geçilirse bu blok çalıştırılır
sub vcl_pass
{
    #std.log("Bilgi: vcl_pass cagirildi");

	#return (fetch);
    return (synth(403, "Access Forbidden (with pass)"));
}

#İstek purge ile geçilirse bu blok çalıştırılır
sub vcl_purge	
{
	#std.log("Bilgi: vcl_purge cagirildi");

    return (synth(403, "Access Forbidden (with purge)"));
}

#Ön belleğe kaydederken bu kod bloğu çalıştırılır
sub vcl_miss
{
	#std.log("Bilgi: vcl_miss cagirildi");

    return (fetch);
}

#Ön belleğe kaydederken bu kod bloğu çalıştırılır
sub vcl_miss
{
	#std.log("Bilgi: vcl_miss cagirildi");

    return (fetch);
}

#Ön bellekteki veri getirilirken bu kod bloğu çalıştırılır
sub vcl_hit
{
    #std.log("Bilgi: vcl_hit cagirildi");

    return (deliver);
}

#Cevap istemciye gönderilmeden önce bu blok çalıştırılır
sub vcl_deliver
{
    #std.log("Bilgi: vcl_deliver cagirildi");

    return (deliver);
}





/*
** Yardımcı Fonksiyonlar
*/



/* Ön tanımlı Fonksiyonlar*/

#İstemciye özel bir cevap göndermek için bu blokdan faydalanılır Ör: return (synth(403, "Forbiddden"));
sub vcl_synth
{
    #std.log("Bilgi: vcl_synth cagirildi");

    set resp.http.Content-Type = "text/html; charset=utf-8";
    set resp.http.Retry-After = "5";

    synthetic({"
        <html>
            <head>
                <title>"} + resp.status + " " + resp.reason + {"</title>
            </head>
            <body>
                <h1>Error "} + resp.status + " " + resp.reason + {"</h1>
                <p>"} + resp.reason + {"</p>
                <h3>Guru Meditation:</h3>
                <p>XID: "} + req.xid + {"</p>
                <hr>
                <p>Kamu Bilgi Sistemi (Geoserver Frontend Varnish)</p>
            </body>
        </html>
    "});
    return (deliver);
}

#En son bu blok çalıştırılır
sub vcl_fini
{
    return (ok);
}



/* Sunucu Nesneleri ve Fonksiyonları */ 

backend frontend1
{
    .host = "frontend";
    .port = "4200";
}

backend frontend2
{
    .host = "frontend";
    .port = "4200";
}

sub frontend_sunucusu_nesnesi
{
    new frontend = directors.round_robin();
	frontend.add_backend(frontend1); 
	frontend.add_backend(frontend2);
}

sub sunucu_nesnelerini_olustur
{
	call frontend_sunucusu_nesnesi;
}

sub sunucu_belirle
{
    set req.backend_hint = frontend.backend();
}

sub onbelleklenebilirlik_kontrolu
{
    if(
        req.url ~ "^/assets/"
        ||
        req.url ~ "^/vendor.js"
        ||
        req.url ~ "^/runtime.js"
        ||
        req.url ~ "^/polyfills.js"
        ||
        req.url ~ "^/styles.js"
        ||
        req.url ~ "^/main.js"
        ||
        req.url ~ "^/scripts.js"
    )
    {
        return(hash);
    }
    else
    {
        return(purge);
    }
}